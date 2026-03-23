<?php
session_start();
header('Content-Type: application/json');

require '../config/config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

$month = $_POST['month'] ?? '';
$year = (int)$_POST['year'] ?? date('Y');
$uploaded_by = $_SESSION['user_id'] ?? null;

if (empty($month)) {
    echo json_encode(['success' => false, 'error' => 'Month required']);
    exit;
}

if (!isset($_FILES['payroll_file']) || $_FILES['payroll_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No valid file uploaded']);
    exit;
}

$file = $_FILES['payroll_file'];
$allowedExtensions = ['xlsx', 'xls'];
$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['success' => false, 'error' => 'Invalid file type. Use XLSX/XLS']);
    exit;
}

if ($file['size'] > 10 * 1024 * 1024) { // 10MB
    echo json_encode(['success' => false, 'error' => 'File too large (max 10MB)']);
    exit;
}

$uploadDir = '../uploads/excel/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileName = time() . '_' . basename($file['name']);
$filePath = $uploadDir . $fileName;

if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    echo json_encode(['success' => false, 'error' => 'Failed to save file']);
    exit;
}

// Check if month/year already exists
$checkStmt = $conn->prepare("SELECT id FROM payroll_batches WHERE month = ? AND year = ?");
$checkStmt->execute([$month, $year]);
if ($checkStmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Payroll for ' . $month . ' ' . $year . ' already exists']);
    unlink($filePath);
    exit;
}

// Parse Excel for preview
$previewData = [];
try {
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray(null, true, true, true);

    // Skip first 2 header rows
    array_shift($rows);
    array_shift($rows);

    foreach ($rows as $rowIndex => $row) {
        if (count($row) < 16 || empty($row[1])) continue; // Skip empty/invalid

        $previewData[] = [
            'staff_id' => $row[0] ?? '',
            'name' => $row[1] ?? '',
            'department' => $row[2] ?? '',
            'gross' => (float)($row[3] ?? 0),
            'days_worked' => (int)($row[5] ?? 0),
            'basic' => (float)($row[6] ?? 0),
            'housing' => (float)($row[7] ?? 0),
            'transport' => (float)($row[8] ?? 0),
            'net' => (float)($row[15] ?? 0),
            'row' => $rowIndex + 1
        ];
    }

    if (empty($previewData)) {
        echo json_encode(['success' => false, 'error' => 'No valid data in Excel']);
        unlink($filePath);
        exit;
    }

    // Create batch
    $stmt = $conn->prepare("INSERT INTO payroll_batches (month, year, uploaded_by, file_path, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$month, $year, $uploaded_by, $filePath]);
    $batch_id = $conn->lastInsertId();

    // Process and insert payslips
    $inserted = 0;
    $skipped = 0;
    foreach ($previewData as $data) {
        // Find user by staff_id or name + dept
        $userStmt = $conn->prepare("SELECT id FROM users WHERE (staff_id = ? OR name = ?) AND department = ? LIMIT 1");
        $userStmt->execute([$data['staff_id'], $data['name'], $data['department']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $skipped++;
            continue;
        }

        $payslipStmt = $conn->prepare("
            INSERT INTO payslips (
                user_id, batch_id, gross_salary, days_worked, basic_salary, housing_allowance,
                transport_allowance, net_salary
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $payslipStmt->execute([
            $user['id'], $batch_id, $data['gross'], $data['days_worked'],
            $data['basic'], $data['housing'], $data['transport'], $data['net']
        ]);
        $inserted++;
    }

    echo json_encode([
        'success' => true,
        'message' => "Processed: {$inserted} inserted, {$skipped} skipped.",
        'batch_id' => $batch_id,
        'month' => $month,
        'year' => $year,
        'preview_count' => count($previewData),
        'inserted' => $inserted
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Processing failed: ' . $e->getMessage()]);
    if (isset($filePath)) unlink($filePath);
}
?>

