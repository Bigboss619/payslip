<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit(0);

require '../config/config.php';
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$hrType = $_SESSION['hr_type'] ?? 'MAIN'; // 🔥 MAIN or RETAIL

// 🔥 HEADER MAPPINGS FOR BOTH FORMATS
$headerMappings = [
    'MAIN' => [
    'staff_id' => ['staff id', 'staff_id', 'staffid', 'id'],
    'name' => ['employee names', 'names', 'name', 'employee_name'],
    'gross_salary' => ['monthly gross', 'gross_salary', 'gross'],
    'deductions' => ['monthly payroll deductions', 'payroll deductions', 'deductions'],
    'net_salary' => ['monthly take home', 'take home', 'net_salary', 'net'],
    'days_worked' => ['no of days worked', 'days_worked', 'days'],
    'basic_salary' => ['basic salary', 'basic'],
    'housing_allowance' => ['housing allowance', 'housing'],           // ✅ Maps to housing
    'transport_allowance' => ['transport allowance', 'transport'],     // ✅ Maps to transport
    'medical' => ['medical'],
    'utility' => ['utility'],
    'monthly_paye' => ['monthly paye', 'paye'],                        // ✅ Maps to paye
    'pension' => ['pension'],
    ],
    'RETAIL' => [
    'staff_id' => ['staff id', 'staff_id', 'staffid', 'retail_id', 'emp_code'],
    'name' => ['names', 'name', 'employee_name', 'staff_name'],
    'gross_salary' => ['monthly gross', 'gross_salary', 'gross', 'retail_gross'],
    'deductions' => ['deduction', 'deductions', 'payroll deductions', 'cuts'],
    'net_salary' => ['monthly net', 'net_salary', 'net', 'net_pay', 'take_home'],
    // Retail extras (flexible)
    'annual_gross' => ['annual gross', 'annual_gross'],
    'taxable_income' => ['taxable income', 'taxable'],
    'annual_tax' => ['annual tax'],
    'monthly_tax' => ['monthly tax'],
    'stations' => ['stations', 'station', 'branch', 'location']
    ]
];

function findHeaderIndex($headers, $possibleNames) {
    foreach ($possibleNames as $name) {
        $index = array_search(strtolower(trim($name)), array_map('strtolower', $headers));
        if ($index !== false) return $index;
    }
    return -1;
}


// 🔥 FIXED: Smart parsing - MAIN uses columns, RETAIL uses JSON
// 🔥 SIMPLIFIED: Direct column mapping for MAIN HR (14 fixed columns)
function parseExcelRow($row, $headers, $hrType, $headerMappings) {
    if ($hrType === 'MAIN' && count($row) >= 14) {
        // 🔥 MAIN HR: Exact column positions!
        return [
            'staff_id' => trim($row[0] ?? ''),
            'name' => trim($row[1] ?? ''),
            'gross_salary' => (float)($row[2] ?? 0),           // Col 3
            'days_worked' => (int)($row[3] ?? 0),              // Col 4
            'basic_salary' => (float)($row[4] ?? 0),            // Col 5
            'housing_allowance' => (float)($row[5] ?? 0),       // Col 6 → housing
            'transport_allowance' => (float)($row[6] ?? 0),     // Col 7 → transport
            'medical' => (float)($row[7] ?? 0),                 // Col 8
            'utility' => (float)($row[8] ?? 0),                 // Col 9
            'monthly_paye' => (float)($row[10] ?? 0),           // Col 11 → paye (skip col 10)
            'deductions' => (float)($row[11] ?? 0),             // Col 12
            'pension' => (float)($row[12] ?? 0),                // Col 13
            'net_salary' => (float)($row[13] ?? 0),             // Col 14
            'extra_data' => null  // 🔥 MAIN HR = NO extras
        ];
    }
    
    // RETAIL HR: Keep dynamic mapping
    if ($hrType === 'RETAIL') {
        $mapping = $headerMappings['RETAIL'];
        $data = [];
        $extraData = [];
        foreach ($mapping as $dbField => $possibleHeaders) {
            $index = findHeaderIndex($headers, $possibleHeaders);
            if ($index !== -1 && isset($row[$index])) {
                $value = trim($row[$index]);
                if (!empty($value)) {
                    $extraData[$dbField] = match($dbField) {
                        'gross_salary', 'net_salary', 'deductions' => (float)$value,
                        default => $value
                    };
                }
            }
        }
        $data['staff_id'] = $extraData['staff_id'] ?? '';
        $data['name'] = $extraData['name'] ?? '';
        $data['gross_salary'] = $extraData['gross_salary'] ?? 0;
        $data['net_salary'] = $extraData['net_salary'] ?? 0;
        $data['deductions'] = $extraData['deductions'] ?? 0;

        // 🔥 NEW: ONLY Retail extras for extra_data
    $retailExtras = array_intersect_key($extraData, [
        'annual_gross' => true,
        'taxable_income' => true,
        'annual_tax' => true,
        'stations' => true
    ]);
    $data['extra_data'] = !empty($retailExtras) ? json_encode($retailExtras) : null;
    
        return $data;
    }
    
    return [];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['mode']) && $_GET['mode'] === 'get_excel') {
    $monthNum = $_GET['month'] ?? '';
    $year = (int)($_GET['year'] ?? date('Y'));
    
    if (empty($monthNum)) {
        echo json_encode(['success' => false, 'error' => 'Month required']);
        exit;
    }

    $monthNames = [
        '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
        '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
        '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
    ];
    $month = $monthNames[$monthNum] ?? $monthNum;
    
    try {
        $stmt = $conn->prepare("SELECT id, file_path FROM payroll_batches WHERE month = ? AND year = ? AND status = 'completed'");
        $stmt->execute([$month, $year]);
        $batch = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$batch || !file_exists($batch['file_path'])) {
            echo json_encode(['success' => false, 'error' => 'Excel file not found']);
            exit;
        }
        
        $spreadsheet = IOFactory::load($batch['file_path']);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        
        if (empty($rows) || count($rows[0]) < 5) {
            echo json_encode(['success' => false, 'error' => 'Invalid Excel format']);
            exit;
        }
        
        $headers = array_map('trim', $rows[0]);
        $mapping = $headerMappings[$hrType];
        
        // Validate required common fields
        $required = ['staff_id', 'name', 'gross_salary', 'deductions', 'net_salary'];
        foreach ($required as $field) {
            if (findHeaderIndex($headers, $mapping[$field]) === -1) {
                echo json_encode(['success' => false, 'error' => "Missing required header: {$field}"]);
                exit;
            }
        }
        
        $excelData = [];
        array_shift($rows); // Skip header
        
        foreach ($rows as $rowIndex => $row) {
            if (count($row) < 5 || empty(trim($row[0] ?? ''))) continue;
            
          
            // Replace everywhere:  
            $parsed = parseExcelRow($row, $headers, $hrType, $headerMappings);  // Remove $mapping param
            if (!empty($parsed['staff_id']) && !empty($parsed['name'])) {
                $excelData[] = [
                    'row_index' => $rowIndex + 2,
                    'staff_id' => $parsed['staff_id'] ?? '',
                    'name' => $parsed['name'] ?? '',
                    'gross_salary' => $parsed['gross_salary'] ?? 0,
                    'net_salary' => $parsed['net_salary'] ?? 0,
                    'deductions' => $parsed['deductions'] ?? 0,
                    // 🔥 FIXED: Show ALL MAIN fields in preview + extras only for Retail
                    'days_worked' => $parsed['days_worked'] ?? 0,
                    'basic_salary' => $parsed['basic_salary'] ?? 0,
                    'housing' => $parsed['housing_allowance'] ?? 0,        // Map to preview
                    'transport' => $parsed['transport_allowance'] ?? 0,    // Map to preview
                    'medical' => $parsed['medical'] ?? 0,
                    'utility' => $parsed['utility'] ?? 0,
                    'paye' => $parsed['monthly_paye'] ?? 0,                // Map to preview
                    'pension' => $parsed['pension'] ?? 0,
                    'extra_data' => $hrType === 'RETAIL' ? $parsed['extra_data'] : null,  // 🔥 MAIN = NULL
                    'hr_type' => $hrType,
                    'raw_row' => $row
            ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'excel_data' => $excelData,
            'total_rows' => count($excelData),
            'hr_type' => $hrType,
            'batch_id' => $batch['id'],
            'file_path' => $batch['file_path'],
            'headers_detected' => $headers
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Excel parse error: ' . $e->getMessage()]);
    }
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
$uploaded_by = $_SESSION['user_id'];

switch ($mode) {
    case 'preview':
        if (empty($month)) {
            echo json_encode(['success' => false, 'error' => 'Month required']);
            exit;
        }

        if (!isset($_FILES['payroll_file']) || $_FILES['payroll_file']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'error' => 'No valid file']);
            exit;
        }

        $file = $_FILES['payroll_file'];
        $allowedExtensions = ['xlsx', 'xls'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            echo json_encode(['success' => false, 'error' => 'Use XLSX/XLS only']);
            exit;
        }

        $uploadDir = '../uploads/excel/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = time() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['success' => false, 'error' => 'Failed to save file']);
            exit;
        }

        // Check duplicates
        $checkStmt = $conn->prepare("SELECT id FROM payroll_batches WHERE month = ? AND year = ? AND hr_type = ?");
        $checkStmt->execute([$month, $year, $hrType]);
        if ($checkStmt->fetch()) {
            unlink($filePath);
            echo json_encode(['success' => false, 'error' => "Payroll for $month $year already exists"]);
            exit;
        }

        // 🔥 PARSE EXCEL with DYNAMIC MAPPING
        $previewData = [];
        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
            
            if (empty($rows) || count($rows[0]) < 5) {
                unlink($filePath);
                echo json_encode(['success' => false, 'error' => 'Invalid Excel - needs at least 5 columns']);
                exit;
            }
            
            $headers = array_map('trim', $rows[0]);
            $mapping = $headerMappings[$hrType];
            
            // Validate common fields
            $required = ['staff_id', 'name', 'gross_salary', 'deductions', 'net_salary'];
            foreach ($required as $field) {
                if (findHeaderIndex($headers, $mapping[$field]) === -1) {
                    unlink($filePath);
                    echo json_encode(['success' => false, 'error' => "Missing: " . implode('/', $mapping[$field])]);
                    exit;
                }
            }
            
            array_shift($rows);
            foreach ($rows as $rowIndex => $row) {
                if (count($row) < 5 || empty(trim($row[0] ?? ''))) continue;
                
                // Replace everywhere:
                $parsed = parseExcelRow($row, $headers, $hrType, $headerMappings);  // Remove $mapping param
                if (!empty($parsed['staff_id']) && !empty($parsed['name'])) {
                    $previewData[] = [
                        'staff_id' => $parsed['staff_id'] ?? '',
                        'name' => $parsed['name'] ?? '',
                        'gross_salary' => $parsed['gross_salary'] ?? 0,
                        'net_salary' => $parsed['net_salary'] ?? 0,
                        'deductions' => $parsed['deductions'] ?? 0,
                        // 🔥 FIXED: Include ALL MAIN fields for preview table
                        'days_worked' => $parsed['days_worked'] ?? 0,
                        'basic_salary' => $parsed['basic_salary'] ?? 0,
                        'housing_allowance' => $parsed['housing_allowance'] ?? 0,
                        'transport_allowance' => $parsed['transport_allowance'] ?? 0,
                        'medical' => $parsed['medical'] ?? 0,
                        'utility' => $parsed['utility'] ?? 0,
                        'monthly_paye' => $parsed['monthly_paye'] ?? 0,
                        'pension' => $parsed['pension'] ?? 0,
                        'extra_data' => $hrType === 'RETAIL' ? $parsed['extra_data'] : null,  // 🔥 MAIN = NULL
                        'hr_type' => $hrType
                ];
                }
            }

            if (empty($previewData)) {
                unlink($filePath);
                echo json_encode(['success' => false, 'error' => 'No valid data rows found']);
                exit;
            }

            // Create preview batch
            $stmt = $conn->prepare("INSERT INTO payroll_batches (month, year, uploaded_by, file_path, status, hr_type, created_at) VALUES (?, ?, ?, ?, 'preview', ?, NOW())");
            $stmt->execute([$month, $year, $uploaded_by, $filePath, $hrType]);
            $batch_id = $conn->lastInsertId();

            $_SESSION['preview_' . $batch_id] = $previewData;

            echo json_encode([
                'success' => true,
                'message' => count($previewData) . " rows parsed ($hrType format)",
                'batch_id' => $batch_id,
                'preview_data' => $previewData,
                'preview_count' => count($previewData),
                'hr_type' => $hrType
            ]);
        } catch (Exception $e) {
            if (isset($filePath)) unlink($filePath);
            echo json_encode(['success' => false, 'error' => 'Parse error: ' . $e->getMessage()]);
        }
        break;

    case 'save':
    if (!$batch_id) {
        echo json_encode(['success' => false, 'error' => 'Batch ID required']);
        exit;
    }

    $previewData = $_SESSION['preview_' . $batch_id] ?? [];
    if (empty($previewData)) {
        echo json_encode(['success' => false, 'error' => 'Preview expired']);
        exit;
    }

    // 🔥 Update batch status
    $stmt = $conn->prepare("UPDATE payroll_batches SET status = 'completed' WHERE id = ? AND status = 'preview'");
    $stmt->execute([$batch_id]);

    $conn->beginTransaction();
    $inserted = 0;
    
    try {
        foreach ($previewData as $data) {
            $userStmt = $conn->prepare("SELECT id FROM users WHERE staff_id = ? LIMIT 1");
            $userStmt->execute([$data['staff_id']]);
            $user = $userStmt->fetch();
            $user_id = $user ? $user['id'] : null;

            // 🔥 FULL INSERT - Uses ALL your existing columns + extra_data
            $payslipStmt = $conn->prepare("
                INSERT INTO payslip (
                    user_id, staff_id, batch_id, 
                    gross_salary, basic_salary, housing, transport, medical, utility, 
                    paye, deductions, pension, net_salary, days_worked, extra_data
                ) VALUES (
                    ?, ?, ?, 
                    ?, ?, ?, ?, ?, ?, 
                    ?, ?, ?, ?, ?, ?
                )
            ");
            
            $payslipStmt->execute([
                $user_id,
                $data['staff_id'],
                $batch_id,
                $data['gross_salary'] ?? 0,
                $data['basic_salary'] ?? 0,
                $data['housing_allowance'] ?? 0,     // ✅ Goes to 'housing' column
                $data['transport_allowance'] ?? 0,   // ✅ Goes to 'transport' column
                $data['medical'] ?? 0,
                $data['utility'] ?? 0,
                $data['monthly_paye'] ?? 0,          // ✅ Goes to 'paye' column
                $data['deductions'] ?? 0,
                $data['pension'] ?? 0,
                $data['net_salary'] ?? 0,
                $data['days_worked'] ?? 0,
    $data['extra_data'] ?? null          // ✅ NULL for MAIN HR
            ]);
            $inserted++;
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Save failed: ' . $e->getMessage()]);
        exit;
    }

    unset($_SESSION['preview_' . $batch_id]);
    echo json_encode([
        'success' => true,
        'message' => "$inserted payslips saved correctly ($hrType format)!",
        'inserted' => $inserted
    ]);
    break;

    case 'cancel':
        if (!$batch_id) {
            echo json_encode(['success' => false, 'error' => 'Batch ID required']);
            exit;
        }
        
        $stmt = $conn->prepare("SELECT file_path FROM payroll_batches WHERE id = ? AND status = 'preview'");
        $stmt->execute([$batch_id]);
        $batch = $stmt->fetch();
        
        if ($batch && file_exists($batch['file_path'])) {
            unlink($batch['file_path']);
        }
        
        $stmt = $conn->prepare("DELETE FROM payroll_batches WHERE id = ? AND status = 'preview'");
        $stmt->execute([$batch_id]);
        unset($_SESSION['preview_' . $batch_id]);
        
        echo json_encode(['success' => true, 'message' => 'Cancelled']);
        break;

    default:
        echo json_encode(['success' => false, 'error' => "Unknown mode: $mode"]);
}
?>