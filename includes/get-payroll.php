<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

session_start();
header('Content-Type: application/json');
ob_clean();

try {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
        throw new Exception('Unauthorized');
    }
    

    require '../config/config.php';
// ✅ ADD THIS at top of get-payroll.php (after session check)
if (isset($_GET['get_latest'])) {
    $latestStmt = $conn->query("
        SELECT month, year FROM payroll_batches 
        ORDER BY created_at DESC LIMIT 1
    ");
    $latest = $latestStmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        // ✅ Change this line (around line 25):
'latest' => $latest ?: ['month' => sprintf('%02d', date('m')), 'year' => date('Y')]
    ]);
    exit;
}
    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    $name = $_GET['name'] ?? '';

    // Filter params
    $filterParams = [];
    $whereConditions = [];

    if ($month) {
        $whereConditions[] = "pb.month = ?";
        $filterParams[] = $month;
    }
    if ($year) {
        $whereConditions[] = "pb.year = ?";
        $filterParams[] = $year;
    }
    if ($name) {
        $whereConditions[] = "(COALESCE(u.name, '') LIKE ? OR COALESCE(u.staff_id, '') LIKE ? OR CAST(p.user_id AS CHAR) LIKE ?)";
        $filterParams[] = "%$name%";
        $filterParams[] = "%$name%";
        $filterParams[] = "%$name%";
    }

    $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

    // MAIN QUERY - LIMIT/OFFSET as raw integers (MariaDB fix)
    $dataQuery = "
        SELECT 
            p.id,
            p.deductions,
            p.gross_salary AS grossSalary,
            p.net_salary AS netSalary,
            COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
            COALESCE(u.staff_id, p.user_id) AS employeeId,
            pb.month,
            pb.year,
            pb.status,
            pb.file_path,
            pb.created_at AS batch_date
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        $whereClause
        ORDER BY pb.created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $conn->prepare($dataQuery);
    $stmt->execute($filterParams);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // COUNT QUERY
    $countQuery = "
        SELECT COUNT(*) as total
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        $whereClause
    ";

    $countStmt = $conn->prepare($countQuery);
    $countStmt->execute($filterParams);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // SUMMARY
    $summaryQuery = "
        SELECT 
            COUNT(*) as total_employees,
            COALESCE(SUM(p.gross_salary), 0) as total_gross,
            COALESCE(SUM(p.net_salary), 0) as total_net
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        $whereClause
    ";

    $summaryStmt = $conn->prepare($summaryQuery);
    $summaryStmt->execute($filterParams);
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // MONTHS
    $monthsStmt = $conn->query("
        SELECT DISTINCT pb.month, pb.year
        FROM payroll_batches pb 
        INNER JOIN payslip p ON pb.id = p.batch_id
        ORDER BY pb.year DESC, pb.month ASC
        LIMIT 24
    ");
    $months = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data,
        'total' => (int)$total,
        'summary' => $summary,
        'months' => $months
    ]);

} catch (Exception $e) {
    error_log("Payslip error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>