<?php
// Backup of upgraded template - copy to payslip-template.php after review
session_start();
require '../config/config.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['role'])) {
    die('Unauthorized');
}

// Get params
$staff_id = $_GET['staff_id'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

if (!$staff_id || !$month || !$year) {
    die('Missing parameters');
}

// Fetch data
$stmt = $conn->prepare("
SELECT u.name, u.staff_id, u.department, u.position,
       p.*, pb.month, pb.year, pb.created_at as batch_date
FROM payslip p
JOIN users u ON p.user_id = u.id
JOIN payroll_batches pb ON p.batch_id = pb.id
WHERE u.staff_id = ? AND pb.month = ? AND pb.year = ?
LIMIT 1
");
$stmt->execute([$staff_id, $month, $year]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Payslip not found');
}

// Calculate if missing (consistent with get-payslip-detail.php)
$gross = $data['gross_salary'] ?? 0;
$data['basic_salary'] = $data['basic_salary'] ?? round($gross * 0.4);
$data['housing'] = $data['housing'] ?? round($gross * 0.25);
$data['transport'] = $data['transport'] ?? round($gross * 0.2);
$data['medical'] = $data['medical'] ?? round($gross * 0.15);
$data['gross_salary'] = $gross;
$deductions = $data['deductions'] ?? 0;
$data['paye'] = $data['paye'] ?? round($deductions * 0.5);
$data['pension'] = $data['pension'] ?? round($gross * 0.08);
$data['payroll_deductions'] = $deductions - $data['paye'] - $data['pension'];
$data['net_salary'] = $data['net_salary'] ?? ($data['gross_salary'] - $deductions);
$data['date'] = date('d M Y', strtotime($data['batch_date'] ?? 'now'));

// Modern HTML Template
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payslip - ' . htmlspecialchars($data['name']) . '</title>
<style>
@page { margin: 20mm; }
body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; line-height: 1.4; color: #1f2937; background: white; }
.container { max-width: 800px; margin: 0 auto; box-shadow: 0 10px 25px rgba(0,0,0,0.1); padding: 30px; border-radius: 12px; }
.header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #3b82f6; }
.logo { font-size: 28px; font-weight: bold; color: #1e40af; margin-bottom: 5px; }
.company-name { font-size: 16px; color: #374151; font-weight: 600; }
.pay-period { background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; margin: 10px auto; max-width: 250px; }
.employee-info { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
.info-label { font-weight: 600; color: #4b5563; }
.info-value { font-weight: 500; color: #111827; }
.tables-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
.table { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #e5e7eb; }
.table-header { background: linear-gradient(135deg, #f8fafc, #f1f5f9); padding: 15px 20px; font-weight: 700; font-size: 13px; color: #1f2937; border-bottom: 2px solid #e5e7eb; }
.table-row { display: flex; justify-content: space-between; padding: 12px 20px; border-bottom: 1px solid #f3f4f6; }
.table-row:last-child { border-bottom: none; }
.table-footer { background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 700; }
.summary { background: linear-gradient(135deg, #fef3c7, #fde68a); border: 2px solid #f59e0b; border-radius: 16px; padding: 25px; text-align: center; margin-bottom: 20px; box-shadow: 0 8px 20px rgba(245,158,11,0.2); }
.summary-gross { font-size: 16px; font-weight: 600; color: #92400e; margin-bottom: 10px; }
.summary-net { font-size: 24px; font-weight: 800; color: #059669; margin-bottom: 5px; }
.footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px dashed #d1d5db; font-size: 10px; color: #6b7280; }
.amount { text-align: right; font-weight: 600; min-width: 80px; }
.positive { color: #059669; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo">PayslipSys HR</div>
    <div class="company-name">Professional Payroll Solutions</div>
    <div class="pay-period">' . htmlspecialchars($data['month']) . ' ' . $data['year'] . '</div>
  </div>

  <div class="employee-info">
    <div class="info-grid">
      <div>
        <div class="info-row"><span class="info-label">Name:</span> <span class="info-value">' . htmlspecialchars($data['name']) . '</span></div>
        <div class="info-row"><span class="info-label">Staff ID:</span> <span class="info-value">' . htmlspecialchars($data['staff_id']) . '</span></div>
        <div class="info-row"><span class="info-label">Department:</span> <span class="info-value">' . htmlspecialchars($data['department'] ?? 'N/A') . '</span></div>
        <div class="info-row"><span class="info-label">Position:</span> <span class="info-value">' . htmlspecialchars($data['position'] ?? 'N/A') . '</span></div>
      </div>
      <div>
        <div class="info-row"><span class="info-label">Pay Period:</span> <span class="info-value">' . htmlspecialchars($data['month']) . ' ' . $data['year'] . '</span></div>
        <div class="info-row"><span class="info-label">Date Generated:</span> <span class="info-value">' . $data['date'] . '</span></div>
        <div class="info-row"><span class="info-label">Status:</span> <span class="info-value" style="color: #10b981; font-weight: 700;">Paid ✓</span></div>
      </div>
    </div>
  </div>

  <div class="tables-container">
    <div class="table">
      <div class="table-header">Earnings</div>
      <div class="table-row"><span>Basic Salary</span> <span class="amount">₦' . number_format($data['basic_salary']) . '</span></div>
      <div class="table-row"><span>Housing Allowance</span> <span class="amount">₦' . number_format($data['housing']) . '</span></div>
      <div class="table-row"><span>Transport Allowance</span> <span class="amount">₦' . number_format($data['transport']) . '</span></div>
      <div class="table-row"><span>Medical Allowance</span> <span class="amount">₦' . number_format($data['medical']) . '</span></div>
      <div class="table-footer table-row"><span>Total Earnings</span> <span class="amount positive">₦' . number_format($data['gross_salary']) . '</span></div>
    </div>

    <div class="table">
      <div class="table-header">Deductions</div>
      <div class="table-row"><span>PAYE Tax</span> <span class="amount">₦' . number_format($data['paye']) . '</span></div>
      <div class="table-row"><span>Pension (8%)</span> <span class="amount">₦' . number_format($data['pension']) . '</span></div>
      <div class="table-row"><span>Payroll Deductions</span> <span class="amount">₦' . number_format($data['payroll_deductions']) . '</span></div>
      <div class="table-footer table-row"><span>Total Deductions</span> <span class="amount">₦' . number_format($data['paye'] + $data['pension'] + $data['payroll_deductions']) . '</span></div>
    </div>
  </div>

  <div class="summary">
    <div class="summary-gross">Gross Salary: ₦' . number_format($data['gross_salary']) . '</div>
    <div class="summary-net">Net Pay: ₦' . number_format($data['net_salary']) . '</div>
    <div style="font-size: 11px; color: #92400e; font-weight: 500;">Amount in words: ' . strtoupper(number_format($data['net_salary'])) . ' NAIRA ONLY</div>
  </div>

  <div class="footer">
    <p>This is a computer-generated payslip. No signature required.</p>
    <p>&copy; ' . date('Y') . ' PayslipSys HR. Confidential.</p>
  </div>
</div>
</body>
</html>
';
?>

