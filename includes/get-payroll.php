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
$limit = (int)($_GET['limit'] ?? 1000);
$offset = (int)($_GET['offset'] ?? 0);

// Month mapping: dropdown "01" → DB "January"
$monthMap = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

$filterParams = [];
$whereConditions = [];

// 🔥 HR FILTER - Each HR sees only THEIR uploads
$hrFilter = ($_SESSION['role'] === 'HR') ? "AND pb.uploaded_by = ?" : "AND p.user_id = ?";
$hrParam = ($_SESSION['role'] === 'HR') ? $_SESSION['user_id'] : $_SESSION['user_id'];
$filterParams[] = $hrParam;  // First param is always HR/STAFF filter

$name = trim($_GET['name'] ?? '');
if (!empty($name)) {
    $whereConditions[] = "COALESCE(u.name, '') LIKE ?";
    $filterParams[] = "%$name%";
}

if (!empty($month)) {
    $targetMonth = $monthMap[$month] ?? $month;
    $whereConditions[] = "pb.month = ?";
    $filterParams[] = $targetMonth;
}

if (!empty($year)) {
    $whereConditions[] = "pb.year = ?";
    $filterParams[] = $year;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : 'WHERE 1=1';
error_log("🔍 Params: " . json_encode($filterParams));

$dataQuery = "
    SELECT 
        p.id, p.deductions, p.pension, p.paye, p.gross_salary AS grossSalary, p.net_salary AS netSalary,
        COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
        COALESCE(u.staff_id, p.user_id) AS employeeId,
        pb.month, pb.year, pb.status, pb.file_path, pb.created_at AS batch_date,
        pb.uploaded_by  -- 🔥 Show who uploaded
    FROM payslip p
    INNER JOIN payroll_batches pb ON p.batch_id = pb.id
    LEFT JOIN users u ON p.user_id = u.id
    $whereClause $hrFilter
    ORDER BY pb.created_at DESC
    LIMIT $limit OFFSET $offset
";

$stmt = $conn->prepare($dataQuery);
$stmt->execute($filterParams);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// TOTAL COUNT QUERY
$countSql = "
    SELECT COUNT(*) as total
    FROM payslip p
    INNER JOIN payroll_batches pb ON p.batch_id = pb.id
    LEFT JOIN users u ON p.user_id = u.id
    $whereClause $hrFilter
";

$countStmt = $conn->prepare($countSql);
$countStmt->execute($filterParams);
$total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// 📅 UNIQUE MONTHS FOR FILTERS (HR-specific)
$monthsSql = "
    SELECT DISTINCT pb.year, pb.month
    FROM payroll_batches pb
    INNER JOIN payslip p ON pb.id = p.batch_id
    $hrFilter
";
$monthsStmt = $conn->prepare($monthsSql);
$monthsStmt->execute([$hrParam]);  // 👈 HR filter here too
$months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'data' => $data,
    'total' => (int)$total,
    'months' => $months,
    'debug' => [
        'role' => $_SESSION['role'],
        'user_id' => $_SESSION['user_id'],
        'hr_filter' => $hrFilter,
        'where_clause' => $whereClause,
        'months_count' => count($months),
        'params_count' => count($filterParams),
        'record_count' => count($data),
        'total_count' => $total
    ]
]);
?>