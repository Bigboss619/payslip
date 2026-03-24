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

    $month = $_GET['month'] ?? '';
    $year = $_GET['year'] ?? '';
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = (int)($_GET['offset'] ?? 0);
    $name = $_GET['name'] ?? '';

    // WHERE conditions for filters
    $whereConditions = ['1=1'];
    $filterParams = [];

    if ($month) {
        $whereConditions[] = "pb.month = ?";
        $filterParams[] = $month;
    }
    if ($year) {
        $whereConditions[] = "pb.year = ?";
        $filterParams[] = $year;
    }
    if ($name) {
        $whereConditions[] = "(u.name LIKE ? OR u.staff_id LIKE ?)";
        $filterParams[] = "%$name%";
        $filterParams[] = "%$name%";
    }

    $whereClause = implode(' AND ', $whereConditions);

    // MAIN QUERY - Perfect match for your structure
    $stmt = $conn->prepare("
        SELECT 
            p.id,
            p.deductions,
            p.gross_salary AS grossSalary,
            p.net_salary AS netSalary,
            p.basic_salary,
            p.housing,
            p.transport,
            p.medical,
            p.utility,
            p.paye,
            p.pension,
            p.days_worked,
            p.pro_rata,
            COALESCE(u.name, CONCAT('ID-', p.user_id)) AS employeeName,
            COALESCE(u.staff_id, p.user_id) AS employeeId,
            
            pb.month,
            pb.year,
            pb.status,
            pb.file_path,
            pb.created_at AS batch_date,
            p.created_at AS payslip_date
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE $whereClause
        ORDER BY pb.created_at DESC, u.name ASC
        LIMIT :limit OFFSET :offset
    ");

    // Bind filter parameters (positional)
    $paramIndex = 1;
    foreach ($filterParams as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    // Bind pagination (named)
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // COUNT QUERY
    $countStmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        LEFT JOIN users u ON p.user_id = u.id
        WHERE $whereClause
    ");
    $paramIndex = 1;
    foreach ($filterParams as $param) {
        $countStmt->bindValue($paramIndex++, $param);
    }
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // SUMMARY
    $summaryStmt = $conn->prepare("
        SELECT 
            COUNT(p.id) as total_employees,
            COALESCE(SUM(p.gross_salary), 0) as total_gross,
            COALESCE(SUM(p.net_salary), 0) as total_net,
            COALESCE(SUM(p.deductions), 0) as total_deductions
        FROM payslip p
        INNER JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE $whereClause
    ");
    $paramIndex = 1;
    foreach ($filterParams as $param) {
        $summaryStmt->bindValue($paramIndex++, $param);
    }
    $summaryStmt->execute();
    $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

    // MONTHS/YEARS for filters
    $monthsStmt = $conn->query("
        SELECT DISTINCT pb.month, pb.year, pb.status
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
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>