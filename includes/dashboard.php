<?php
session_start();
header('Content-Type: application/json');

require '../config/config.php';

$userRole = $_SESSION['role'] ?? 'Employee';
$userId = $_SESSION['user_id'] ?? 1;

try {
    // STATS
    $stats = [
        'total_payslips' => getSafeCount($conn, 'payslip'),
        'total_employees' => getSafeCount($conn, 'users'),
        'last_salary' => getLastSalary($conn, $userId),
        'current_month' => date('F Y'),
        'user_name' => $_SESSION['name'] ?? 'Emmanuel'
    ];

    // RECENT PAYSLIPS
    $recentPayslips = getRecentPayslips($conn, $userId, $userRole);

    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_payslips' => $recentPayslips
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getSafeCount($conn, $table) {
    try {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM `$table`");
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    } catch (Exception $e) {
        return 0;
    }
}

function getLastSalary($conn, $userId) {
    try {
        $stmt = $conn->prepare("SELECT net_salary FROM payslip WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (float)$result['net_salary'] : 0;
    } catch (Exception $e) {
        return 0;
    }
}

function getRecentPayslips($conn, $userId, $role) {
    try {
        if ($role === 'HR') {
            // HR: Latest batches
            $stmt = $conn->query("
                SELECT pb.month, pb.year, 
                       COALESCE(SUM(p.gross_salary), 0) as gross_salary,
                       COALESCE(SUM(p.net_salary), 0) as net_salary,
                       pb.status, pb.id as batch_id
                FROM payroll_batches pb 
                LEFT JOIN payslip p ON p.batch_id = pb.id
                GROUP BY pb.id 
                ORDER BY pb.year DESC, pb.month DESC 
                LIMIT 5
            ");
        } else {
            // Employee: Personal payslips
            $stmt = $conn->prepare("
                SELECT pb.month, pb.year, p.gross_salary, p.net_salary, 
                       COALESCE(pb.status, 'Paid') as status, p.id
                FROM payslip p 
                LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
                WHERE p.user_id = ? 
                ORDER BY COALESCE(pb.year, YEAR(p.created_at)) DESC, pb.month DESC 
                LIMIT 5
            ");
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}
?>