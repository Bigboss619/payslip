<?php

require '../config/config.php';
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
if(!isset($_FILES['payroll_file'])) {
    die('No file uploaded');
}

$file = $_FILES['payroll_file'];

// validate file
$allowedExtensions = ['xlsx', 'xls'];
$fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);

if(!in_array($fileExt, $allowedExtensions)){
    die("Invalid file type");
}


$uploadDir = "uploads/excel/";
if(!is_dir($uploadDir)){

}

// Unique File name
$fileName = time() . "_" . basename($file['name']);
$filePath = $uploadDir . $fileName;

// Move file 
if(!move_uploaded_file($file['tmp_name'], $filePath)){
    die('Failed to upload file');
}

echo "File uploaded successfully <br>";

// Create Payroll Batch
$stmt = $conn->prepare("
 INSERT INTO payroll_batches (month, year, uploaded_by, file_path) VALUES (?, ?, ?, ?)
");
$stmt->execute([$month, $year, $uploaded_by, $filePath]);
$bacth_id = $conn->lastInsertId();

// READ EXCEL
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray();

// Remove header row
array_shift($rows);
array_shift($rows);

foreach ($rows as $row) {
$staff_id = $row[0];
$name = $row[1];
$department = $row[2];
$gross = $row[3];
$pro_rata = $row[4];
$days_worked = $row[5];
$basic = $row[6];
$housing = $row[7];
$transport = $row[8];
$medical = $row[9];
$utility = $row[10];
$gross2 = $row[11];
$paye = $row[12];
$deductions = $row[13];
$pension = $row[14];
$net = $row[15];

// FIND USER (by name or staff_id later)
$stmt = $conn->prepare("SELECT id from users WHERE name = ?");
$stmt->execute([$name]);
$user = $stmt->fetch();

if(!$user){
    continue; //skip if not found
}

$user_id = $user["id"];

// INSERT PAYSLIP
$stmt = $conn->prepare("
    INSERT INTO payslips (
         user_id, batch_id, gross_salary, basic_salary, housing,
         transport, medical, utility, paye, deductions,
         pension, net_salary, days_worked, pro_rata
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $user_id, $batch_id, $gross, $basic, $housing,
    $transport, $medical, $utility, $paye, $deductions,
    $pension, $net, $days_worked, $pro_rata
]);

}

echo "Payroll processed successfully!";
}

