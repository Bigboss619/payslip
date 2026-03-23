<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

$month = $_GET['month'] ?? '';
$limit = (int)($_GET['limit'] ?? 10);
$offset = (int)($_GET['offset'] ?? 0);
$name = $_GET['name'] ?? '';
$dept = $_GET['dept'] ?? '';

$where = ['1=1'];
$params = [];

if ($month) {
    $where[] = "pb.month = ?";
    $params[] = $month;
}
if ($name) {
    $where[] = "u.name LIKE ?";
    $params[] = "%$name%";
}
if ($dept) {
    $where[] = "u.department = ?";
    $params[] = $dept;
}

$whereClause = implode(' AND ', $where);

// Data query
$stmt = $conn->prepare("
    SELECT u.name, u.department, p.gross_salary, p.net_salary, pb.month, pb.year
    FROM payslip p
    JOIN users u ON p.user_id = u.id
    JOIN payroll_batches pb ON p.batch_id = pb.id
    WHERE $whereClause
    ORDER BY pb.created_at DESC, u.name
    LIMIT ? OFFSET ?
");
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count for pagination
$countStmt = $conn->prepare("
    SELECT COUNT(*) as total
    FROM payslip p
    JOIN users u ON p.user_id = u.id
    JOIN payroll_batches pb ON p.batch_id = pb.id
    WHERE $whereClause
");
$countStmt->execute(array_slice($params, 0, -2));
$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Summary
$sumStmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_employees,
        SUM(p.gross_salary) as total_gross,
        SUM(p.net_salary) as total_net
    FROM payslip p
    JOIN users u ON p.user_id = u.id
    JOIN payroll_batches pb ON p.batch_id = pb.id
    WHERE $whereClause
");
$sumStmt->execute(array_slice($params, 0, -2));
$summary = $sumStmt->fetch(PDO::FETCH_ASSOC);

// Months
$monthsStmt = $conn->query("SELECT DISTINCT CONCAT(pb.month, ' ', pb.year) as month_year FROM payroll_batches pb ORDER BY pb.year DESC, pb.month DESC");
$months = $monthsStmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode([
    'success' => true,
    'data' => $data,
    'total' => $total,
    'summary' => $summary,
    'months' => $months
]);
?>

