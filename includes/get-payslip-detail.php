<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Payslip ID required']);
    exit;
}

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT p.id,
               p.deductions,
               p.gross_salary AS grossSalary,
               p.net_salary AS netSalary,
               p.basic_salary,
               p.housing,
               p.transport,
               p.medical,
               p.utility,
               p.paye,
               p.pension,
               p.days_worked,
               p.pro_rata,
               COALESCE(u.name, 'Unknown') AS employeeName,
               COALESCE(u.staff_id, p.user_id) AS employeeId,
               COALESCE(u.department, 'Unknown') AS department,
               COALESCE(u.position, 'N/A') AS position,
               COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')) AS month,
               COALESCE(pb.year, YEAR(p.created_at)) AS year,
               COALESCE(pb.status, 'Paid') AS status,
               COALESCE(pb.created_at, p.created_at) AS date,
               DATE_FORMAT(COALESCE(pb.created_at, p.created_at), '%d %M %Y') AS generatedDate
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE p.id = ?
    ");
    $stmt->execute([$id]);
    $payslip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payslip) {
        echo json_encode(['success' => false, 'error' => 'Payslip not found']);
        exit;
    }

    // Calculate breakdowns if not stored
    $grossSalary = $payslip['grossSalary'] ?? 0;
    $payslip['basic_salary'] = $payslip['basic_salary'] ?? round($grossSalary * 0.4);
    $payslip['housing'] = $payslip['housing'] ?? round($grossSalary * 0.25);
    $payslip['transport'] = $payslip['transport'] ?? round($grossSalary * 0.2);
    $payslip['medical'] = $payslip['medical'] ?? round($grossSalary * 0.15);
    $payslip['totalEarnings'] = $grossSalary;

    $deductions = $payslip['deductions'] ?? 0;
    $payslip['tax'] = $payslip['paye'] ?? round($deductions * 0.5);
    $payslip['pension'] = $payslip['pension'] ?? round($grossSalary * 0.08);
    $payslip['payrollDeductions'] = ($payslip['deductions'] ?? 0) - ($payslip['tax'] ?? 0) - ($payslip['pension'] ?? 0);
    $payslip['totalDeductions'] = $deductions;

    echo json_encode([
        'success' => true,
        'data' => $payslip
    ]);
} catch (PDOException $e) {
    error_log("Detail query failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Query failed']);
}
?>

