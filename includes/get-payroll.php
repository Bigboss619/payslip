<?php
session_start();
header('Content-Type: application/json');

// 🔍 FORCE DEBUG - Log ALL GET params
error_log("🔍 ALL GET PARAMS: " . json_encode($_GET));

if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

require '../config/config.php';

$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');
$limit = (int)($_GET['limit'] ?? 1000);
$offset = (int)($_GET['offset'] ?? 0);

error_log("🔍 RAW INPUT - Month: '$month', Year: '$year'");

// Month mapping: dropdown "01" → DB "January"
$monthMap = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

$filterParams = [];
$whereConditions = [];

if (!empty($month)) {
    $targetMonth = $monthMap[$month] ?? $month;
    $whereConditions[] = "pb.month = ?";
    $filterParams[] = $targetMonth;
    error_log("🔍 Month mapped: '$month' → '$targetMonth'");
}

if (!empty($year)) {
    $whereConditions[] = "pb.year = ?";
    $filterParams[] = $year;
    error_log("🔍 Year filter: '$year'");
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
error_log("🔍 Final WHERE: $whereClause");
error_log("🔍 Params: " . json_encode($filterParams));

$userFilter = ($_SESSION['role'] === 'HR') ? '' : "AND p.user_id = {$_SESSION['user_id']}";

$dataQuery = "
    SELECT 
        p.id, p.deductions, p.gross_salary AS grossSalary, p.net_salary AS netSalary,
        COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
        COALESCE(u.staff_id, p.user_id) AS employeeId,
        pb.month, pb.year, pb.status, pb.file_path, pb.created_at AS batch_date
    FROM payslip p
    INNER JOIN payroll_batches pb ON p.batch_id = pb.id
    LEFT JOIN users u ON p.user_id = u.id
    $whereClause $userFilter
    ORDER BY pb.created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($dataQuery);
$stmt->execute($filterParams);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $data,
    'debug' => [
        'raw_month' => $month,
        'mapped_month' => $monthMap[$month] ?? 'unknown',
        'raw_year' => $year,
        'where_clause' => $whereClause,
        'params_count' => count($filterParams),
        'record_count' => count($data)
    ]
]);
?>