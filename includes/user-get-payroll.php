<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

error_log("👤 USER PAYROLL API - Role: " . ($_SESSION['role'] ?? 'none'));

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'STAFF') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'User access required']);
    exit;
}

require '../config/config.php';

// 🔑 GET USER ID & STAFF_ID (NEW!)
$userId = $_SESSION['user_id'] ?? null;
$userStaffId = $_SESSION['staff_id'] ?? null;  // 🔥 NEW: Get staff_id from session

if (!$userId) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'User ID missing']);
    exit;
}

error_log("👤 Loading payslips for user_id: $userId, staff_id: $userStaffId");

// 📊 GET FILTERS (KEEP SAME)
$month = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');
$limit = max(1, min((int)($_GET['limit'] ?? 10), 100));
$offset = max(0, (int)($_GET['offset'] ?? 0));

// 📅 MONTH MAP (KEEP SAME)
$monthMap = [
    '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
    '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
    '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
];

// 🧠 BUILD WHERE CONDITIONS - 🔥 FIXED!
$whereConditions = ["(p.user_id = ? OR p.staff_id = ?)"];  // 🔥 NOW MATCHES BY staff_id TOO!
$params = [$userId, $userStaffId];  // 🔥 Two params now

if (!empty($month)) {
    $whereConditions[] = "pb.month = ?";
    $params[] = $month;
}

if (!empty($year)) {
    $whereConditions[] = "pb.year = ?";
    $params[] = $year;
}

$whereClause = 'WHERE ' . implode(' AND ', $whereConditions);

try {
    // 🔍 MAIN QUERY - 🔥 FIXED JOIN
    $sql = "
        SELECT 
            p.id,
            p.pension, 
            p.paye,
            p.deductions,
            p.gross_salary AS grossSalary,
            p.net_salary AS netSalary,
            COALESCE(u.name, 'Unknown') AS employeeName,
            COALESCE(u.staff_id, p.staff_id, p.user_id) AS employeeId,
            pb.month,
            pb.year,
            pb.created_at AS batch_date
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON (p.user_id = u.id OR p.staff_id = u.staff_id)  -- 🔥 FIXED: Match both ways
        $whereClause
        ORDER BY pb.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 📊 TOTAL COUNT - 🔥 FIXED
    $countSql = "
        SELECT COUNT(*) as total
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        $whereClause
    ";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // 📅 UNIQUE MONTHS - 🔥 FIXED
    $monthsSql = "
        SELECT DISTINCT pb.year, pb.month
        FROM payroll_batches pb
        INNER JOIN payslip p ON p.batch_id = pb.id
        WHERE p.user_id = ? OR p.staff_id = ?
        ORDER BY pb.year DESC, pb.month DESC
    ";
    $monthsStmt = $conn->prepare($monthsSql);
    $monthsStmt->execute([$userId, $userStaffId]);
    $months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'total' => (int)$total,
        'months' => $months,
        'debug' => ['user_id' => $userId, 'staff_id' => $userStaffId, 'count' => count($data)]
    ]);

} catch (Exception $e) {
    error_log("❌ ERROR: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>