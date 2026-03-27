<?php
session_start();

// 🔥 SHOW ERRORS (REMOVE IN PRODUCTION)
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// 🔍 DEBUG LOG
error_log("👤 USER PAYROLL API - Role: " . ($_SESSION['role'] ?? 'none'));

// 🛡️ ONLY STAFF CAN ACCESS
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STAFF') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'User access required']);
    exit;
}

require '../config/config.php';

// 🔑 GET USER ID
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User ID missing']);
    exit;
}

error_log("👤 Loading payslips for user_id: $userId");

// 📊 GET FILTERS
$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');
$limit = max(1, min((int)($_GET['limit'] ?? 10), 100));
$offset = max(0, (int)($_GET['offset'] ?? 0));

// 📅 MONTH MAP
$monthMap = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

// 🧠 BUILD WHERE CONDITIONS
$whereConditions = ["p.user_id = ?"];
$params = [$userId];

if (!empty($month) && isset($monthMap[$month])) {
    $whereConditions[] = "pb.month = ?";
    $params[] = $monthMap[$month];
}

if (!empty($year)) {
    $whereConditions[] = "pb.year = ?";
    $params[] = $year;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

try {

    // 🔍 MAIN QUERY
    $sql = "
        SELECT 
            p.id,
            p.deductions,
            p.gross_salary AS grossSalary,
            p.net_salary AS netSalary,
            COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
            COALESCE(u.staff_id, p.user_id) AS employeeId,
            pb.month,
            pb.year,
            pb.created_at AS batch_date
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        $whereClause
        ORDER BY pb.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 📊 TOTAL COUNT
    $countSql = "
        SELECT COUNT(*) as total
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        $whereClause
    ";

    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // ✅ RESPONSE
    echo json_encode([
        'success' => true,
        'data' => $data,
        'total' => (int)$total
    ]);

} catch (Exception $e) {

    error_log("❌ ERROR: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage() // show real error for now
    ]);
}
?>