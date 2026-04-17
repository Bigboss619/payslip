<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

require '../config/config.php';

$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');
$name = trim($_GET['name'] ?? '');
$limit = (int)($_GET['limit'] ?? 10);
$offset = (int)($_GET['offset'] ?? 0);

$hrParam = $_SESSION['user_id'];
$hrFilter = ($_SESSION['role'] === 'HR') ? "AND pb.uploaded_by = ?" : "AND p.user_id = ?";

// 🔥 PERFECT SEARCH LOGIC - ONE CONDITION, 3 FIELDS
$whereConditions = [];
$params = [$hrParam]; // Start with HR param

if (!empty($name)) {
    $likeTerm = "%$name%";
    $whereConditions[] = "(u.name LIKE ? OR u.staff_id LIKE ? OR p.user_id LIKE ?)";
    $params[] = $likeTerm;
    $params[] = $likeTerm;
    $params[] = $likeTerm;
}

if (!empty($month)) {
    $whereConditions[] = "pb.month = ?";
    $params[] = $month;
}

if (!empty($year)) {
    $whereConditions[] = "pb.year = ?";
    $params[] = $year;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

$dataQuery = "
    SELECT 
        p.id, COALESCE(p.deductions, 0) as deductions, 
        COALESCE(p.pension, 0) as pension, COALESCE(p.paye, 0) as paye,
        p.gross_salary AS grossSalary, p.net_salary AS netSalary,
        COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
        COALESCE(u.staff_id, p.user_id) AS employeeId,
        pb.month, pb.year, COALESCE(pb.status, 'Paid') as status, 
        pb.file_path, pb.created_at AS batch_date
    FROM payslip p
    INNER JOIN payroll_batches pb ON p.batch_id = pb.id
    LEFT JOIN users u ON p.user_id = u.id
    $whereClause $hrFilter
    ORDER BY pb.created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($dataQuery);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$countQuery = "
    SELECT COUNT(*) as total
    FROM payslip p
    INNER JOIN payroll_batches pb ON p.batch_id = pb.id
    LEFT JOIN users u ON p.user_id = u.id
    $whereClause $hrFilter
";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute($params);
$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

$monthsQuery = "
    SELECT DISTINCT pb.year, pb.month 
    FROM payroll_batches pb 
    INNER JOIN payslip p ON pb.id = p.batch_id 
    $hrFilter 
    ORDER BY pb.year DESC, pb.month
";
$monthsStmt = $conn->prepare($monthsQuery);
$monthsStmt->execute([$hrParam]);
$months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $data,
    'total' => (int)$total,
    'months' => $months
]);
?>