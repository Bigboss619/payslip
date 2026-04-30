<?php
// ============================================================================
// BULLETPROOF JSON API - NO OUTPUT BEFORE THIS LINE!
// ============================================================================

// 1. NO SPACE, NO CHARACTERS BEFORE <?php
// 2. Start session FIRST
session_start();

// 3. NO OUTPUT - Set headers IMMEDIATELY
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// 4. BUFFER ALL OUTPUT - Catch any accidental output
ob_start();

// 5. AUTH CHECK - NO REDIRECTS, JSON ONLY
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    ob_end_clean();
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'HR access required',
        'debug' => ['session_role' => $_SESSION['role'] ?? 'none']
    ]);
    exit;
}

// 6. LOAD CONFIG
require_once '../config/config.php';

$monthNum = trim($_GET['month'] ?? '');
$year = trim($_GET['year'] ?? '');

// 7. VALIDATE INPUTS
if (empty($monthNum) || empty($year)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Missing month/year',
        'received' => ['month' => $monthNum, 'year' => $year]
    ]);
    exit;
}

// 8. MONTH MAPPING
$monthNames = [
    '01' => 'January', '1' => 'January',
    '02' => 'February', '2' => 'February',
    '03' => 'March', '3' => 'March',
    '04' => 'April', '4' => 'April',
    '05' => 'May', '5' => 'May',
    '06' => 'June', '6' => 'June',
    '07' => 'July', '7' => 'July',
    '08' => 'August', '8' => 'August',
    '09' => 'September', '9' => 'September',
    '10' => 'October', '11' => 'November', '12' => 'December'
];

$monthName = $monthNames[$monthNum] ?? $monthNum;

if (empty($monthName)) {
    ob_end_clean();
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid month']);
    exit;
}

// 9. EXECUTE QUERY
try {
    $sql = "SELECT 
        p.id, 
        p.gross_salary, 
        p.net_salary, 
        COALESCE(u.name, 'Unknown') as employeeName,
        pb.month, 
        pb.year
        FROM payslip p 
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE pb.month = ? AND pb.year = ?
        ORDER BY u.name ASC
        LIMIT 50";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$monthName, $year]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 10. CLEAN BUFFER & SEND JSON
    ob_end_clean();
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($data),
        'debug' => [
            'month_num' => $monthNum,
            'month_name' => $monthName,
            'year' => $year,
            'records_found' => count($data)
        ]
    ], JSON_THROW_ON_ERROR);

} catch (PDOException $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    ob_end_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'details' => $e->getMessage()
    ]);
}

// 11. NOTHING AFTER THIS - END OF FILE
