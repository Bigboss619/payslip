<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

// Clean any prior output
if (ob_get_level()) {
    ob_clean();
}

$month = $_GET['month'] ?? '';
$limit = (int)($_GET['limit'] ?? 10);
$offset = (int)($_GET['offset'] ?? 0);
$name = $_GET['name'] ?? '';
$dept = $_GET['dept'] ?? '';

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}

$where = ['1=1'];
$params = [];

if ($month) {
    $where[] = "pb.month = ?";
    $params[] = $month;
}
if ($name) {
    $where[] = "(u.name LIKE ? OR u.staff_id LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
}
if ($dept) {
    $where[] = "u.department = ?";
    $params[] = $dept;
}

$whereClause = implode(' AND ', $where);

try {
    // Data query
    $stmt = $conn->prepare("
    SELECT COALESCE(u.staff_id, p.user_id) as staff_id, 
           COALESCE(u.name, 'Unknown') as name, 
           COALESCE(u.department, 'Unknown') as department, 
           p.gross_salary, p.net_salary, 
           COALESCE(pb.month, 'Unknown') as month, 
           COALESCE(pb.year, YEAR(NOW())) as year
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE $whereClause
        ORDER BY COALESCE(pb.created_at, p.created_at) DESC, COALESCE(u.name, p.user_id)
        LIMIT ? OFFSET ?
    ");
    $params[] = $limit;
    $params[] = $offset;
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Data query failed: " . $e->getMessage());
    $data = [];
}

try {
    // Count for pagination
    $countStmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE $whereClause
    ");
    $countStmt->execute(array_slice($params, 0, -2));
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    error_log("Count query failed: " . $e->getMessage());
    $total = 0;
}

try {
    // Summary
    $sumStmt = $conn->prepare("
        SELECT 
            COUNT(p.id) as total_employees,
            COALESCE(SUM(p.gross_salary), 0) as total_gross,
            COALESCE(SUM(p.net_salary), 0) as total_net
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE $whereClause
    ");
    $sumStmt->execute(array_slice($params, 0, -2));
    $summary = $sumStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Summary query failed: " . $e->getMessage());
    $summary = ['total_employees' => 0, 'total_gross' => 0, 'total_net' => 0];
}

try {
    // Months
    $monthsStmt = $conn->query("SELECT DISTINCT DATE_FORMAT(p.created_at, '%M %Y') as month_year FROM payslip p ORDER BY p.created_at DESC LIMIT 12");
    $months = $monthsStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Months query failed: " . $e->getMessage());
    $months = [];
}

ob_clean(); // Final clean before output
echo json_encode([
    'success' => true,
    'data' => $data,
    'total' => $total,
    'summary' => $summary,
    'months' => $months
]);
?>


