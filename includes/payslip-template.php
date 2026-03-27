<?php
session_start();
use Dompdf\Dompdf;
use Dompdf\Options;
require '../config/config.php';

$id = $_GET['id'] ?? 0;
if (!isset($_SESSION['role'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}
// Fetch payslip data
// ✅ FIXED: Fetch department name via JOIN
$stmt = $conn->prepare("
    SELECT 
        p.*, 
        u.name, 
        u.staff_id, 
        u.department_id,
        d.name as department_name,  -- ✅ Department name
        pb.month, 
        pb.year, 
        pb.status
    FROM payslip p
    JOIN users u ON p.user_id = u.id
    JOIN departments d ON u.department_id = d.id  -- ✅ JOIN departments table
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

// ✅ BASE64 - Works offline/online, no external files needed
$logoPath = '../uploads/company-logo/logo.png';
$logoData = base64_encode(file_get_contents($logoPath));
$logoBase64 = 'data:image/png;base64,' . $logoData;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
@page { margin: 15mm; }

body {
    font-family: Arial, sans-serif;
    font-size: 12px;
    color: #000;
}

.container {
    width: 100%;
}

/* HEADER */
.header {
    text-align: center;
    margin-bottom: 20px;
}
.logo {
    height: 120px;
    margin-bottom: 5px;
}
.title {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 10px;
}

/* TOP INFO TABLE */
.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.info-table td {
    padding: 6px;
    border: 1px solid #000;
}

.label {
    font-weight: bold;
    background: #f3f4f6;
}

/* MAIN TABLE */
.salary-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.salary-table th,
.salary-table td {
    border: 1px solid #000;
    padding: 8px;
}

.salary-table th {
    background: #e5e7eb;
    text-align: center;
    font-weight: bold;
}

.text-right {
    text-align: right;
}

.bold {
    font-weight: bold;
}

/* TOTAL SECTION */
.summary {
    margin-top: 15px;
    width: 100%;
}

.summary td {
    padding: 8px;
    border: 1px solid #000;
}

.net-pay {
    background: #d1fae5;
    font-weight: bold;
    font-size: 14px;
}

/* FOOTER */
.footer {
    margin-top: 25px;
    font-size: 10px;
    text-align: center;
}
</style>
</head>

<body>
<div class="container">

    <!-- TITLE -->
    <div class="header">
        <img src="<?= $logoBase64 ?>" alt="company logo"  class="logo"/>
        <div class="title">PAYSLIP</div>
    </div>

    <!-- TOP DETAILS -->
    <table class="info-table">
        <tr>
            <td class="label">Pay Type</td>
            <td>Monthly</td>
            <td class="label">Period</td>
            <td><?= htmlspecialchars($data['month']) ?> <?= $data['year'] ?></td>
        </tr>
        <tr>
            <td class="label">Worked Days</td>
            <td><?= $data['days_worked'] ?? '0' ?></td>
            <td class="label">Payer ID</td>
            <td>N-21897635</td>
        </tr>
        <tr>
            <td class="label">Employee Name</td>
            <td><?= htmlspecialchars($data['name']) ?></td>
            <td class="label">Designation</td>
            <td>HR Manager</td>
        </tr>
        <tr>
            <td class="label">Department</td>
            <td><?= htmlspecialchars($data['department_name']) ?></td>
            <td class="label">SBU</td>
            <td>Corporate Services</td>
        </tr>
    </table>

    <!-- EARNINGS & DEDUCTIONS -->
    <table class="salary-table">
        <thead>
            <tr>
                <th>Earnings</th>
                <th>Amount (₦)</th>
                <th>Deductions</th>
                <th>Amount (₦)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right"><?= number_format($data['basic_salary'] ?? 0, 2) ?></td>
                <td>Pension</td>
                <td class="text-right"><?= number_format($data['pension'] ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Housing</td>
                <td class="text-right"><?= number_format($data['housing'] ?? 0, 2) ?></td>
                <td>PAYE</td>
                <td class="text-right"><?= number_format($data['paye'] ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Transport</td>
                <td class="text-right"><?= number_format($data['transport'] ?? 0, 2) ?></td>
                <td>Other Deductions</td>
                <td class="text-right"><?= number_format($data['deductions'] ?? 0, 2) ?></td>
            </tr>
            <tr>
                <td>Medical</td>
                <td class="text-right"><?= number_format($data['medical'] ?? 0, 2) ?></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>Utility</td>
                <td class="text-right"><?= number_format($data['utility'] ?? 0, 2) ?></td>
                <td></td>
                <td></td>
            </tr>

            <!-- TOTALS -->
            <tr class="bold">
                <td>Gross Pay</td>
                <td class="text-right"><?= number_format($data['gross_salary'], 2) ?></td>
                <td>Total Deduction</td>
                <td class="text-right">
                    <?= number_format(($data['paye'] + $data['pension'] + $data['deductions']), 2) ?>
                </td>
            </tr>

            <tr class="net-pay">
                <td colspan="2">Net Pay</td>
                <td colspan="2" class="text-right">
                    <?= number_format($data['net_salary'], 2) ?>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- FOOTER -->
    <div class="footer">
        This is a system-generated payslip | Generated on <?= date('d M Y H:i') ?>
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