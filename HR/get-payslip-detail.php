<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // ✅ CORS
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Payslip ID required']);
    exit;
}

try {
    // ✅ FIXED SQL - Proper JOIN for department
    $stmt = $conn->prepare("
        SELECT 
            p.id, p.deductions, p.gross_salary AS grossSalary, p.net_salary AS netSalary,
            p.basic_salary, p.housing, p.transport, p.medical, p.utility,
            p.paye, p.pension, p.days_worked, p.pro_rata,
            COALESCE(u.name, 'Unknown') AS employeeName,
            COALESCE(u.staff_id, 'N/A') AS employeeId,
            COALESCE(d.name, 'Unknown') AS department,  -- ✅ FIXED: JOIN department
            COALESCE(pb.month, DATE_FORMAT(p.created_at, '%M')) AS month,
            COALESCE(pb.year, YEAR(p.created_at)) AS year,
            COALESCE(pb.status, 'Paid') AS status,
            DATE_FORMAT(COALESCE(pb.created_at, p.created_at), '%d %M %Y') AS generatedDate
        FROM payslip p
        LEFT JOIN users u ON p.user_id = u.id
        LEFT JOIN departments d ON u.department_id = d.id  -- ✅ JOIN departments
        LEFT JOIN payroll_batches pb ON p.batch_id = pb.id
        WHERE p.id = ?
    ");
    
    $stmt->execute([$id]);
    $payslip = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$payslip) {
        echo json_encode(['success' => false, 'error' => 'Payslip not found']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'data' => $payslip
    ]);
    
} catch (PDOException $e) {
    error_log("Payslip detail error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
?>