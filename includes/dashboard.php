<?php
session_start();
header('Content-Type: application/json');

require '../config/config.php';

$userRole = $_SESSION['role'] ?? 'Employee';
$userId = $_SESSION['user_id'] ?? 1;

try {
    // STATS - DYNAMIC (Role-aware)
    $stats = [
'total_payslips' => getTotalPayslips($conn, $userId, $userRole),
        'total_employees' => getSafeCount($conn, 'users'),
        'last_salary' => getLastSalary($conn, $userId, $userRole),
        'current_month' => getLatestMonth($conn, $userId, $userRole),
        'user_name' => $_SESSION['name'] ?? 'Emmanuel'
    ];

    // RECENT PAYSLIPS - NEWEST FIRST
    $recentPayslips = getRecentPayslips($conn, $userId, $userRole);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_payslips' => $recentPayslips
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ================= NEW FUNCTIONS =================
function getLatestMonth($conn, $userId, $role) {
    try {
        if ($role === 'HR') {
            // HR: Latest company batch
            $stmt = $conn->query("
                SELECT CONCAT(month, ' ', year) as period
                FROM payroll_batches 
                ORDER BY year DESC, FIELD(month, 'January','February','March','April','May','June','July','August','September','October','November','December') DESC
                LIMIT 1
            ");
        } else {
            // Employee: Latest personal payslip
            $stmt = $conn->prepare("
                SELECT CONCAT(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), ' ', COALESCE(pb.year, YEAR(p.created_at))) as period
                FROM payslip p 
                LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
                WHERE p.user_id = ?
                ORDER BY COALESCE(pb.year, YEAR(p.created_at)) DESC, 
                         FIELD(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), 
                               'January','February','March','April','May','June','July','August','September','October','November','December') DESC
                LIMIT 1
            ");
            $stmt->execute([$userId]);
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['period'] ?? date('F Y');
    } catch (Exception $e) {
        return date('F Y');
    }
}

function getLastSalary($conn, $userId, $userRole) {
    try {
        if ($userRole === 'STAFF') {
            $monthDetails = getLastMonthDetails($conn, $userId, $userRole);
            $stmt = $conn->prepare("
                SELECT net_salary 
                FROM payslip 
                WHERE user_id = ? AND (
                    (batch_id IN (SELECT id FROM payroll_batches WHERE month=? AND year=?)) OR 
                    (MONTH(created_at)=? AND YEAR(created_at)=?)
                )
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([$userId, $monthDetails['month'], $monthDetails['year'], $monthDetails['month_num'], $monthDetails['year']]);
        } else {
            // HR: latest overall
            $stmt = $conn->prepare("
                SELECT net_salary 
                FROM payslip ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute();
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['net_salary'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getRecentPayslips($conn, $userId, $role) {
    try {
        if ($role === 'HR') {
            // HR: Latest batches (April first!)
            $stmt = $conn->query("
                SELECT pb.month, pb.year, 
                       COALESCE(SUM(p.gross_salary), 0) as gross_salary,
                       COALESCE(SUM(p.net_salary), 0) as net_salary,
                       pb.status, pb.id as batch_id, pb.created_at
                FROM payroll_batches pb 
                LEFT JOIN payslip p ON p.batch_id = pb.id
                GROUP BY pb.id 
                ORDER BY pb.year DESC, 
                         FIELD(pb.month, 'December','November','October','September','August','July','June','May','April','March','February','January') ASC,
                         pb.created_at DESC
                LIMIT 5
            ");
        } else {
            // Employee: Personal (newest first)
            $stmt = $conn->prepare("
                SELECT pb.month, pb.year, p.gross_salary, p.net_salary, 
                       COALESCE(pb.status, 'Paid') as status, p.id, p.created_at
                FROM payslip p 
                LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
                WHERE p.user_id = ? 
                ORDER BY COALESCE(pb.year, YEAR(p.created_at)) DESC, 
                         FIELD(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), 
                               'December','November','October','September','August','July','June','May','April','March','February','January') ASC,
                         p.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function getSafeCount($conn, $table) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (Exception $e) {
        return 0;
    }
}

function getMonthDetails($conn, $userId, $role, $isLastMonth = false) {
    try {
        if ($role === 'HR') {
            $order = $isLastMonth ? 'LIMIT 2' : 'LIMIT 1';
            $stmt = $conn->query("
                SELECT month, year, MONTHNAME(STR_TO_DATE(CONCAT(month, ' 1 ', year), '%M %d %Y')) as month_name, 
                       MONTH(STR_TO_DATE(CONCAT(month, ' 1 ', year), '%M %d %Y')) as month_num
                FROM payroll_batches 
                ORDER BY year DESC, FIELD(month, 'January','February','March','April','May','June','July','August','September','October','November','December') DESC
                $order
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($isLastMonth && count($results) >= 2) {
                return $results[1]; // Second latest
            }
            return $results[0] ?? [
                'month' => date('F'), 
                'year' => date('Y'), 
                'month_name' => date('F'), 
                'month_num' => (int)date('n')
            ];
        } else {
            // STAFF: from their payslips
            $order = $isLastMonth ? 'LIMIT 2' : 'LIMIT 1';
            $stmt = $conn->prepare("
                SELECT COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')) as month, 
                       COALESCE(pb.year, YEAR(p.created_at)) as year,
                       MONTHNAME(STR_TO_DATE(CONCAT(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), ' 1 ', COALESCE(pb.year, YEAR(p.created_at))), '%M %d %Y')) as month_name,
                       MONTH(STR_TO_DATE(CONCAT(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), ' 1 ', COALESCE(pb.year, YEAR(p.created_at))), '%M %d %Y')) as month_num
                FROM payslip p LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
                WHERE p.user_id = ?
                ORDER BY COALESCE(pb.year, YEAR(p.created_at)) DESC, FIELD(COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')), 'January','February','March','April','May','June','July','August','September','October','November','December') DESC
                $order
            ");
            $stmt->execute([$userId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($isLastMonth && count($results) >= 2) {
                return $results[1];
            }
            return $results[0] ?? [
                'month' => date('F'), 
                'year' => date('Y'), 
                'month_name' => date('F'), 
                'month_num' => (int)date('n')
            ];
        }
    } catch (Exception $e) {
        return [
            'month' => date('F'), 
            'year' => date('Y'), 
            'month_name' => date('F'), 
            'month_num' => (int)date('n')
        ];
    }
}

function getCurrentMonthDetails($conn, $userId, $userRole) {
    return getMonthDetails($conn, $userId, $userRole, false);
}

function getLastMonthDetails($conn, $userId, $userRole) {
    return getMonthDetails($conn, $userId, $userRole, true);
}

function getTotalPayslips($conn, $userId, $userRole) {
    try {
        $monthDetails = $userRole === 'STAFF' ? getLastMonthDetails($conn, $userId, $userRole) : getCurrentMonthDetails($conn, $userId, $userRole);
        
        if ($userRole === 'STAFF') {
            // STAFF: count their payslips in last month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count
                FROM payslip 
                WHERE user_id = ? AND (
                    (batch_id IN (SELECT id FROM payroll_batches WHERE month=? AND year=?)) OR 
                    (MONTH(created_at)=? AND YEAR(created_at)=?)
                )
            ");
            $stmt->execute([$userId, $monthDetails['month'], $monthDetails['year'], $monthDetails['month_num'], $monthDetails['year']]);
        } else {
            // HR: count all payslips in current month
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count
                FROM payslip 
                WHERE (
                    (batch_id IN (SELECT id FROM payroll_batches WHERE month=? AND year=?)) OR 
                    (MONTH(created_at)=? AND YEAR(created_at)=?)
                )
            ");
            $stmt->execute([$monthDetails['month'], $monthDetails['year'], $monthDetails['month_num'], $monthDetails['year']]);
        }
        
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (Exception $e) {
        return 0;
    }
}
?>
