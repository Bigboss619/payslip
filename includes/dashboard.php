<?php
session_start();
header('Content-Type: application/json');
require '../config/config.php';

$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['role'] ?? 'STAFF';
$hrType = $_SESSION['hr_type'] ?? null;
$staffId = $_SESSION['staff_id'] ?? null;

function isStaffLikeRole($role) {
    return in_array($role, ['STAFF', 'USER'], true);
}

if (!$userId) {
    exit(json_encode(['success' => false, 'error' => 'Login required']));
}

try {
    $response = [
        'success' => true,
        'user' => [
            'name' => $_SESSION['name'] ?? 'User',
            'role' => $userRole,
            'hr_type' => $hrType
        ],
        'stats' => getSmartStats($conn, $userId, $userRole, $hrType),
        'payslips' => getSmartPayslips($conn, $userId, $userRole, $hrType, $staffId)
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getSmartStats($conn, $userId, $role, $hrType) {
    if (isStaffLikeRole($role)) {
        $stmt = $conn->prepare("
            SELECT COUNT(DISTINCT CONCAT(COALESCE(b.year, YEAR(p.created_at)), '-', COALESCE(b.month, DATE_FORMAT(p.created_at, '%M')))) as total_payslips,
                   COALESCE(SUM(net_salary), 0) as total_earned
            FROM payslip p
            LEFT JOIN payroll_batches b ON p.batch_id = b.id
            WHERE p.user_id = ? OR p.staff_id = ?
        ");
        $stmt->execute([$userId, $_SESSION['staff_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'total_payslips' => (int)$row['total_payslips'],
            'total_earned' => (float)$row['total_earned'],
            'last_salary' => getLastSalary($conn, $userId),
            'current_month' => getCurrentMonth($conn, $userId)
        ];
    }
    
    // HR Stats
    $params = [$_SESSION['user_id']];
    $hrFilter = $hrType ? "AND b.hr_type = ?" : "";
    if ($hrType) $params[] = $hrType;
    
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT CONCAT(b.year, '-', b.month)) as total_payslips,
               COUNT(DISTINCT u.id) as total_employees
        FROM payslip p 
        INNER JOIN payroll_batches b ON p.batch_id = b.id  /* 🔥 Fixed alias */
        LEFT JOIN users u ON p.user_id = u.id
        WHERE b.uploaded_by = ? $hrFilter
    ");
    $stmt->execute($params);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'total_payslips' => (int)$row['total_payslips'],
        'total_employees' => (int)$row['total_employees'],
        'last_salary' => getLastSalary($conn, $userId),
        'current_month' => getCurrentMonth($conn, $userId, 'HR')
    ];
}

function getSmartPayslips($conn, $userId, $role, $hrType, $staffId) {
    if (isStaffLikeRole($role)) {
        $stmt = $conn->prepare("
            SELECT b.month, b.year, p.gross_salary, p.net_salary, 
                   COALESCE(b.status, 'Paid') as status, p.id, p.created_at
            FROM payslip p 
            LEFT JOIN payroll_batches b ON p.batch_id = b.id  /* 🔥 Fixed alias */
            WHERE p.user_id = ? OR p.staff_id = ?
            ORDER BY b.year DESC, b.created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$userId, $staffId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // HR Payslips
    $params = [$_SESSION['user_id']];
    $hrFilter = $hrType ? "AND b.hr_type = ?" : "";
    if ($hrType) $params[] = $hrType;
    
    $stmt = $conn->prepare("
        SELECT b.month, b.year, 
               SUM(p.gross_salary) as gross_salary,
               SUM(p.net_salary) as net_salary,
               b.status, b.id as batch_id, b.created_at,
               COUNT(p.id) as employees
        FROM payroll_batches b  /* 🔥 Clear alias */
        LEFT JOIN payslip p ON p.batch_id = b.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE b.uploaded_by = ? $hrFilter
        GROUP BY b.id 
        ORDER BY b.year DESC, b.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getLastSalary($conn, $userId) {
    $stmt = $conn->prepare("
        SELECT net_salary FROM payslip 
        WHERE user_id = ? OR staff_id = ?
        ORDER BY created_at DESC LIMIT 1
    ");
    $stmt->execute([$userId, $_SESSION['staff_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? (float)$result['net_salary'] : 0;
}

function getCurrentMonth($conn, $userId, $role = 'STAFF') {
    if (isStaffLikeRole($role)) {
        $stmt = $conn->prepare("
            SELECT CONCAT(COALESCE(b.month, DATE_FORMAT(p.created_at, '%M')), ' ', 
                          COALESCE(b.year, YEAR(p.created_at))) as period
            FROM payslip p
            LEFT JOIN payroll_batches b ON p.batch_id = b.id
            WHERE p.user_id = ? OR p.staff_id = ?
            ORDER BY COALESCE(b.year, YEAR(p.created_at)) DESC LIMIT 1
        ");
        $stmt->execute([$userId, $_SESSION['staff_id']]);
    } else {
        // HR version - use payroll_batches directly
        $stmt = $conn->prepare("
            SELECT CONCAT(b.month, ' ', b.year) as period
            FROM payroll_batches b
            WHERE b.uploaded_by = ?
            ORDER BY b.year DESC, b.created_at DESC LIMIT 1
        ");
        $stmt->execute([$_SESSION['user_id']]);
    }
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['period'] ?? date('F Y');
}
?>