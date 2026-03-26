<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['HR', 'Employee'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'] ?? null;

try {
    // 1. STATS
    if ($userRole === 'HR') {
        // HR: Company-wide stats
        $stats = [
            'total_payslips' => getTotalPayslips($conn),
            'total_employees' => getTotalEmployees($conn),
            'total_gross' => getTotalGross($conn),
            'pending_payslips' => getPendingPayslips($conn)
        ];
    } else {
        // Employee: Personal stats
        $stats = [
            'total_payslips' => getUserPayslipsCount($conn, $userId),
            'last_salary' => getLastSalary($conn, $userId),
            'current_month' => getCurrentMonth($conn, $userId)
        ];
    }

    // 2. RECENT PAYSLIPS (Last 5 for user/company)
    $recentPayslips = getRecentPayslips($conn, $userId, $userRole);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_payslips' => $recentPayslips,
        'user_name' => $_SESSION['name'] ?? 'User'
    ]);

} catch (Exception $e) {
    error_log("Dashboard error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Server error']);
}

// ================= HELPER FUNCTIONS =================
function getTotalPayslips($conn) {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM payslip");
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getTotalEmployees($conn) {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getTotalGross($conn) {
    $stmt = $conn->query("SELECT COALESCE(SUM(gross_salary), 0) as total FROM payslip WHERE MONTH(created_at) = MONTH(CURDATE())");
    return (float)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

function getPendingPayslips($conn) {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM payroll_batches WHERE status = 'preview'");
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getUserPayslipsCount($conn, $userId) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM payslip WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

function getLastSalary($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT net_salary 
        FROM payslip 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? (float)$result['net_salary'] : 0;
}

function getCurrentMonth($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT CONCAT(pb.month, ' ', pb.year) as period
        FROM payslip p
        JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE p.user_id = ? 
        ORDER BY pb.year DESC, pb.month DESC
        LIMIT 1
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['period'] : date('F Y');
}

function getRecentPayslips($conn, $userId, $role) {
    if ($role === 'HR') {
        $sql = "
            SELECT 
                pb.month, pb.year, 
                p.gross_salary, p.net_salary,
                pb.status,
                pb.id as batch_id
            FROM payslip p
            JOIN payroll_batches pb ON p.batch_id = pb.id
            GROUP BY pb.id
            ORDER BY pb.year DESC, pb.month DESC
            LIMIT 5
        ";
        $stmt = $conn->query($sql);
    } else {
        $sql = "
            SELECT 
                pb.month, pb.year, 
                p.gross_salary, p.net_salary,
                pb.status,
                p.id
            FROM payslip p
            JOIN payroll_batches pb ON p.batch_id = pb.id
            WHERE p.user_id = ?
            ORDER BY pb.year DESC, pb.month DESC
            LIMIT 5
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$userId]);
    }
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>