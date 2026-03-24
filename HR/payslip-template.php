<?php
session_start();
use Dompdf\Dompdf;
use Dompdf\Options;
require '../config/config.php';

$id = $_GET['id'] ?? 0;
if (!$id || !isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    http_response_code(403);
    header('Content-Type: text/plain');
    die('Access denied');
}

// Fetch payslip data
$stmt = $conn->prepare("
    SELECT p.*, u.name, u.staff_id, pb.month, pb.year, pb.status
    FROM payslip p
    JOIN users u ON p.user_id = u.id
    JOIN payroll_batches pb ON p.batch_id = pb.id
    WHERE p.id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    http_response_code(404);
    header('Content-Type: text/plain');
    die('Payslip not found');
}



require '../vendor/autoload.php';

// Start output buffering
ob_start();
// HTML for PDF (browser will convert to PDF when printing/downloading)
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payslip - <?= htmlspecialchars($data['name']) ?></title>
    <style>
        @page { 
            margin: 10mm; 
            size: A4 portrait; 
        }
        * { 
            box-sizing: border-box; 
        }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            font-size: 11pt; 
            line-height: 1.4; 
            color: #1f2937; 
            margin: 0; 
            padding: 0;
            background: white;
        }
        .container { 
            max-width: 190mm; 
            margin: 0 auto; 
            padding: 20px; 
        }
        .header { 
            text-align: center; 
            border-bottom: 4px solid #2563eb; 
            padding-bottom: 20px; 
            margin-bottom: 25px;
        }
        .logo { 
            font-size: 26pt; 
            font-weight: 800; 
            color: #1e40af; 
            margin-bottom: 5px; 
            letter-spacing: 1px;
        }
        .company { 
            font-size: 12pt; 
            color: #6b7280; 
            font-weight: 500;
        }
        .pay-period { 
            background: linear-gradient(135deg, #3b82f6, #1d4ed8); 
            color: white; 
            padding: 12px 25px; 
            border-radius: 12px; 
            display: inline-block; 
            margin: 15px 0; 
            font-weight: 700; 
            font-size: 13pt;
        }
        .employee-section { 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
            border-radius: 16px; 
            padding: 25px; 
            margin-bottom: 25px; 
            border: 2px solid #e2e8f0;
        }
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
        }
        .info-item { 
            display: flex; 
            justify-content: space-between; 
            padding: 10px 0; 
            border-bottom: 1px solid #f1f5f9;
        }
        .info-label { 
            font-weight: 600; 
            color: #374151; 
            font-size: 11pt;
        }
        .info-value { 
            font-weight: 700; 
            color: #111827; 
            font-size: 12pt;
        }
        .earnings-deductions { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 25px; 
            margin-bottom: 30px;
        }
        .pay-table { 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            overflow: hidden;
        }
        .table-header { 
            background: linear-gradient(135deg, #f8fafc, #f1f5f9); 
            padding: 18px; 
            font-weight: 700; 
            font-size: 12pt; 
            color: #1f2937; 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }
        .table-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 14px 18px; 
            border-bottom: 1px solid #f3f4f6;
        }
        .table-row:last-child { 
            border-bottom: none; 
            font-weight: 800; 
            background: linear-gradient(135deg, #10b981, #059669); 
            color: white;
        }
        .amount { 
            font-family: 'Courier New', monospace; 
            min-width: 90px; 
            text-align: right;
        }
        .net-summary { 
            background: linear-gradient(135deg, #dcfce7, #bbf7d0); 
            border: 3px solid #10b981; 
            border-radius: 20px; 
            padding: 30px; 
            text-align: center; 
            margin: 30px 0;
            box-shadow: 0 10px 30px rgba(16,185,129,0.3);
        }
        .net-amount { 
            font-size: 28pt; 
            font-weight: 900; 
            color: #059669; 
            margin: 10px 0;
        }
        .gross-amount { 
            font-size: 16pt; 
            color: #92400e; 
            margin-bottom: 5px;
        }
        .footer { 
            text-align: center; 
            padding-top: 30px; 
            border-top: 2px dashed #d1d5db; 
            font-size: 9pt; 
            color: #6b7280;
        }
        @media print {
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo">PayslipSys HR</div>
            <div class="company">Professional Payroll Management System</div>
            <div class="pay-period"><?= htmlspecialchars($data['month']) ?> <?= $data['year'] ?></div>
        </div>

        <!-- Employee Info -->
        <div class="employee-section">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Employee Name:</span>
                    <span class="info-value"><?= htmlspecialchars($data['name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Staff ID:</span>
                    <span class="info-value"><?= htmlspecialchars($data['staff_id']) ?></span>
                </div>
                <!-- <div class="info-item">
                    <span class="info-label">Department:</span>
                    <span class="info-value"><?= htmlspecialchars($data['department']) ?></span>
                </div> -->
                <div class="info-item">
                    <span class="info-label">Status:</span>
                    <span class="info-value" style="color: #10b981; font-weight: 700;"><?= htmlspecialchars($data['status']) ?></span>
                </div>
            </div>
        </div>

        <!-- Earnings & Deductions -->
        <div class="earnings-deductions">
            <div class="pay-table">
                <div class="table-header">💰 Earnings</div>
                <div class="table-row">
                    <span>Basic Salary</span>
                    <span class="amount">₦<?= number_format($data['basic_salary'] ?? 0) ?></span>
                </div>
                <div class="table-row">
                    <span>Housing Allowance</span>
                    <span class="amount">₦<?= number_format($data['housing'] ?? 0) ?></span>
                </div>
                <div class="table-row">
                    <span>Transport Allowance</span>
                    <span class="amount">₦<?= number_format($data['transport'] ?? 0) ?></span>
                </div>
                <div class="table-row">
                    <span>Total Gross</span>
                    <span class="amount">₦<?= number_format($data['gross_salary']) ?></span>
                </div>
            </div>

            <div class="pay-table">
                <div class="table-header">📉 Deductions</div>
                <div class="table-row">
                    <span>PAYE Tax</span>
                    <span class="amount">₦<?= number_format($data['paye'] ?? 0) ?></span>
                </div>
                <div class="table-row">
                    <span>Pension Contribution</span>
                    <span class="amount">₦<?= number_format($data['pension'] ?? 0) ?></span>
                </div>
                <div class="table-row">
                    <span>Other Deductions</span>
                    <span class="amount">₦<?= number_format($data['deductions']) ?></span>
                </div>
            </div>
        </div>

        <!-- Net Pay Summary -->
        <div class="net-summary">
            <div class="gross-amount">Gross Salary: ₦<?= number_format($data['gross_salary']) ?></div>
            <div class="net-amount">NET PAY</div>
            <div style="font-size: 14pt; font-weight: 700; color: #059669;">
                ₦<?= number_format($data['net_salary']) ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>✅ This is an official computer-generated payslip</p>
            <p>Generated: <?= date('d M Y H:i:s') ?> | PayslipSys HR v2.0</p>
        </div>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// DomPDF setup
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF
$dompdf->stream("payslip_{$data['staff_id']}.pdf", [
    "Attachment" => true // download
]);
exit;