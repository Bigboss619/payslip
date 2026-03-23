<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require '../config/config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Suppress notices/warnings for clean JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

$mode = $_POST['mode'] ?? 'preview';
$batch_id = $_POST['batch_id'] ?? null;
$month = $_POST['month'] ?? '';
$year = (int)($_POST['year'] ?? date('Y'));
$uploaded_by = $_SESSION['user_id'] ?? null;

switch ($mode) {
    case 'preview':
        if (empty($month)) {
            echo json_encode(['success' => false, 'error' => 'Month required for preview']);
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

        if ($file['size'] > 10 * 1024 * 1024) {
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
            echo json_encode(['success' => false, 'error' => 'Failed to save uploaded file']);
            exit;
        }

        // Check if month/year already exists
        $checkStmt = $conn->prepare("SELECT id FROM payroll_batches WHERE month = ? AND year = ?");
        $checkStmt->execute([$month, $year]);
        if ($checkStmt->fetch()) {
            unlink($filePath);
            echo json_encode(['success' => false, 'error' => 'Payroll for ' . $month . ' ' . $year . ' already exists']);
            exit;
        }

        // Parse Excel
        $previewData = [];
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            array_shift($rows); // Skip header

            foreach ($rows as $rowIndex => $row) {
                if (count($row) < 16 || empty($row[1]) || trim($row[0]) === 'STAFF ID') continue;

                $previewData[] = [
                    'staff_id' => $row[0] ?? '',
                    'name' => $row[1] ?? '',
                    'department' => $row[2] ?? '',
                    'gross_salary' => (float)($row[3] ?? 0),
                    'pro_rata' => (float)($row[4] ?? 0),
                    'days_worked' => (int)($row[5] ?? 0),
                    'basic_salary' => (float)($row[6] ?? 0),
                    'housing' => (float)($row[7] ?? 0),
                    'transport' => (float)($row[8] ?? 0),
                    'medical' => (float)($row[9] ?? 0),
                    'utility' => (float)($row[10] ?? 0),
                    'paye' => (float)($row[12] ?? 0),
                    'deductions' => (float)($row[13] ?? 0),
                    'pension' => (float)($row[14] ?? 0),
                    'net_salary' => (float)($row[15] ?? 0)
                ];
            }

            if (empty($previewData)) {
                unlink($filePath);
                echo json_encode(['success' => false, 'error' => 'No valid data found in Excel']);
                exit;
            }

            // Create preview batch
            $stmt = $conn->prepare("INSERT INTO payroll_batches (month, year, uploaded_by, file_path, status, created_at) VALUES (?, ?, ?, ?, 'preview', NOW())");
            $stmt->execute([$month, $year, $uploaded_by, $filePath]);
            $batch_id = $conn->lastInsertId();

            // Store in session
            $_SESSION['preview_' . $batch_id] = $previewData;

            echo json_encode([
                'success' => true,
                'message' => count($previewData) . ' rows ready for preview.',
                'batch_id' => $batch_id,
                'preview_data' => $previewData,
                'preview_count' => count($previewData)
            ]);
        } catch (Exception $e) {
            if (isset($filePath)) unlink($filePath);
            echo json_encode(['success' => false, 'error' => 'Excel parse error: ' . $e->getMessage()]);
        }
        break;

    case 'save':
        if (!$batch_id) {
            echo json_encode(['success' => false, 'error' => 'Batch ID required']);
            exit;
        }

        $previewData = $_SESSION['preview_' . $batch_id] ?? [];
        if (empty($previewData)) {
            echo json_encode(['success' => false, 'error' => 'Preview data not found for batch ' . $batch_id]);
            exit;
        }

        // Update batch to completed
        $stmt = $conn->prepare("UPDATE payroll_batches SET status = 'completed' WHERE id = ? AND status = 'preview'");
        $stmt->execute([$batch_id]);

        // Insert payslips
        $conn->beginTransaction();
        $inserted = 0;
        $skipped = 0;
        try {
                foreach ($previewData as $data) {
                    $userStmt = $conn->prepare("SELECT id FROM users WHERE staff_id = ? OR name LIKE ? LIMIT 1");
                    $userStmt->execute([$data['staff_id'], '%' . $data['name'] . '%']);
                    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$user) {
                        $skipped++;
                        continue;
                    }

                $payslipStmt = $conn->prepare("
                    INSERT INTO payslip (
                        user_id, batch_id, gross_salary, basic_salary, housing, transport, 
                        medical, utility, paye, deductions, pension, net_salary, 
                        days_worked, pro_rata
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $payslipStmt->execute([
                    $user['id'], $batch_id,
                    $data['gross_salary'], $data['basic_salary'], $data['housing'], 
                    $data['transport'], $data['medical'], $data['utility'], 
                    $data['paye'], $data['deductions'], $data['pension'], 
                    $data['net_salary'], $data['days_worked'], $data['pro_rata']
                ]);
                $inserted++;
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Database error during save: ' . $e->getMessage()]);
            exit;
        }

        // Cleanup
        unset($_SESSION['preview_' . $batch_id]);

        echo json_encode([
            'success' => true,
            'message' => "Saved! $inserted payslips created ($skipped skipped - no matching staff).",
            'inserted' => $inserted,
            'skipped' => $skipped
        ]);
        break;

    case 'cancel':
        if (!$batch_id) {
            echo json_encode(['success' => false, 'error' => 'Batch ID required']);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM payroll_batches WHERE id = ? AND status = 'preview'");
        $stmt->execute([$batch_id]);
        unset($_SESSION['preview_' . $batch_id]);
        
        echo json_encode(['success' => true, 'message' => 'Preview cancelled and cleaned up']);
        break;

    default:
        echo json_encode(['success' => false, 'error' => "Unknown mode: $mode"]);
}
?>

