<!-- Header -->
<?php include_once("../includes/header.php"); ?>

<!-- Nav Section -->
<?php include_once("../includes/nav.php"); ?>
<style>
.payslip-info td {
    border: 1px solid #d1d5db;
    padding: 12px;
}

.payslip-info .label {
    width: 25%;
    background: #f9fafb;
    font-weight: 600;
    color: #374151;
}

.payslip-salary th,
.payslip-salary td {
    border: 1px solid #d1d5db;
    padding: 10px 12px;
}

.payslip-salary th {
    background: #f3f4f6;
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #374151;
}

.payslip-salary td:nth-child(2),
.payslip-salary td:nth-child(4) {
    text-align: right;
    font-weight: 600;
}

@media print {
    body * {
        visibility: hidden;
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }

    #payslip-detail, #payslip-detail * {
        visibility: visible !important;
    }

    #actions, #loading {
        display: none !important;
    }

    #payslip-detail {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        max-width: 8.5in !important;
        margin: 0 !important;
        padding: 0.6in !important;
        box-shadow: none !important;
        border: none !important;
        border-radius: 0 !important;
    }

    @page {
        size: A4;
        margin: 0.5in;
    }
}
</style>

<!-- MAIN -->
<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
<div class="mx-auto max-w-5xl">

<div class="mb-5 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5">
    <h1 class="text-2xl font-bold text-gray-800">Payslip Details</h1>
    <p class="mt-1 text-sm text-gray-600">Review salary breakdown, deductions and net pay in a clean printable format.</p>
</div>

<div id="payslip-detail" class="rounded-3xl border border-gray-100 bg-white p-6 shadow-xl md:p-8">

<!-- LOADING -->
<div id="loading" class="flex flex-col items-center justify-center py-20 text-gray-500">
<div class="mb-4 h-16 w-16 animate-spin rounded-full border-4 border-blue-200 border-t-blue-500"></div>
<p class="text-sm font-medium">Loading payslip...</p>
</div>

<!-- PAYSLIP DETAIL -->
<div id="detail-content" class="hidden">
<div class="mb-7 rounded-2xl border border-gray-100 bg-gradient-to-r from-slate-50 to-white px-6 py-5">
    <div class="flex flex-col items-center text-center">
        <img src="../uploads/company-logo/logo.png" alt="Company Logo" class="mb-0 h-10 w-auto md:h-16 object-contain" id="pdf-logo">
        <h2 class="text-2xl font-bold tracking-wide text-gray-800">PAYSLIP</h2>
        <p class="mt-1 text-sm text-gray-500">Generated payroll statement</p>
    </div>
</div>

<table id="info-table" class="payslip-info mb-6 w-full border-collapse text-sm text-gray-700">
<tr>
<td class="label">Pay Type</td>
<td>Monthly</td>
<td class="label">Period</td>
<td id="pdf-period"></td>
</tr>
<tr>
<td class="label">Worked Days</td>
<td id="pdf-days-worked">22</td>
<td class="label">Payer ID</td>
<td id="pdf-payer-id">N/A</td>
</tr>
<tr>
<td class="label">Employee Name</td>
<td id="pdf-employee-name"></td>
<td class="label">Account Number</td>
<td id="pdf-position"></td>
</tr>
<tr>
<td class="label">Department</td>
<td id="pdf-department"></td>
<td class="label">Bank</td>
<td id="pdf-bank"></td>
</tr>
</table>

<div class="mb-3 flex items-center justify-between">
    <h3 class="text-lg font-semibold text-gray-800">Earnings and Deductions</h3>
    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Current Payslip</span>
</div>

<table id="salary-table" class="payslip-salary w-full border-collapse text-sm text-gray-700">
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
<td id="pdf-basic">0.00</td>
<td>Pension</td>
<td id="pdf-pension">0.00</td>
</tr>
<tr>
<td>Housing</td>
<td id="pdf-housing">0.00</td>
<td>PAYE</td>
<td id="pdf-paye">0.00</td>
</tr>
<tr>
<td>Transport</td>
<td id="pdf-transport">0.00</td>
<td>Other Deductions</td>
<td id="pdf-deductions">0.00</td>
</tr>
<tr>
<td>Medical</td>
<td id="pdf-medical">0.00</td>
<td></td>
<td></td>
</tr>
<tr>
<td>Utility</td>
<td id="pdf-utility">0.00</td>
<td></td>
<td></td>
</tr>
<tr class="bg-gray-50 font-semibold">
<td>Gross Pay</td>
<td id="pdf-gross-total">0.00</td>
<td>Total Deduction</td>
<td id="pdf-total-deduction">0.00</td>
</tr>
<tr class="bg-emerald-50 text-emerald-900">
<td colspan="2" class="font-bold">Net Pay</td>
<td colspan="2" class="font-bold text-right" id="pdf-net-pay">0.00</td>
</tr>
</tbody>
</table>

<div class="mt-8 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-3 text-center text-xs text-gray-500">
This is a system-generated payslip | Generated on <span id="pdf-generated-date"><?= date('d M Y H:i') ?></span>
</div>

<div id="actions" class="mt-10 flex flex-col justify-between gap-4 border-t border-gray-200 pt-6 md:flex-row md:items-center">
<button onclick="window.history.back()" class="rounded-xl border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
&larr; Back to Payslips
</button>
<div class="flex gap-3">
<button onclick="printPayslip()" class="flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:bg-blue-700 hover:shadow-lg">
<svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"/>
</svg>
Print Payslip
</button>
</div>
</div>
</div>
</div>
</div>
</main>

</div>

</body>
<script src="../js/payslip-view.js"></script>
</html>
