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
$year = $_GET['year'] ?? '';
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
if ($year) {
    $where[] = "pb.year = ?";
    $params[] = $year;
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
        SELECT p.id AS id,
               p.deductions AS deductions,
               p.gross_salary AS grossSalary, 
               p.net_salary AS netSalary,
               COALESCE(u.name, 'Unknown') AS employeeName,
               COALESCE(u.staff_id, p.user_id) AS employeeId,
               COALESCE(u.department, 'Unknown') AS department,
               COALESCE(u.position, 'N/A') AS position,
               COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')) AS month,
               COALESCE(pb.year, YEAR(p.created_at)) AS year,
               COALESCE(pb.status, 'Paid') AS status,
               COALESCE(pb.created_at, p.created_at) AS date
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE $whereClause
        ORDER BY date DESC, employeeName
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
    // Months for filter (distinct month/year pairs)
    $monthsStmt = $conn->prepare("SELECT DISTINCT month, year FROM (
        SELECT COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')) AS month, 
               COALESCE(pb.year, YEAR(p.created_at)) AS year
        FROM payslip p LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
    ) m ORDER BY year DESC, FIELD(month, 'January','February','March','April','May','June','July','August','September','October','November','December') LIMIT 24");
    $monthsStmt->execute();
    $months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);
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


