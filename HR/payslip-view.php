<!-- Header -->
<?php include_once("../includes/header.php"); ?>

<!-- Nav Section -->
<?php include_once("../includes/nav.php"); ?>
<style>
/* PRINT STYLES - Hides everything except payslip */
@media print {
/* Hide everything */
body * { 
visibility: hidden; 
-webkit-print-color-adjust: exact !important;
color-adjust: exact !important;
}

/* Show only payslip content */
#payslip-detail, #payslip-detail * {
visibility: visible !important;
}

/* Hide buttons during print */
button, .flex.justify-between, #loading {
display: none !important;
}

/* Full page payslip */
#payslip-detail {
position: absolute !important;
left: 0 !important;
top: 0 !important;
width: 100% !important;
max-width: 8.5in !important;
margin: 0 !important;
padding: 1in !important;
box-shadow: none !important;
border: none !important;
}

/* Perfect A4 layout */
@page {
size: A4;
margin: 0.5in;
}

/* Fix colors */
.bg-gradient-to-r {
background: linear-gradient(90deg, #eff6ff 0%, #e0e7ff 100%) !important;
-webkit-print-color-adjust: exact;
}
}
</style>

<!-- MAIN -->
<main class="flex-1 p-4 overflow-y-auto">
<div class="max-w-4xl mx-auto h-full flex flex-col">
<div id="payslip-detail" class="flex-1 bg-white shadow-2xl rounded-3xl p-8 border border-gray-100 overflow-y-auto">

<!-- LOADING -->
<div id="loading" class="flex flex-col items-center justify-center py-20 text-gray-500">
<div class="w-16 h-16 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mb-4"></div>
<p>Loading payslip...</p>
</div>

<!-- PAYSLOP DETAIL -->
<div id="detail-content" class="hidden">

<!-- ✅ SAME HEADER AS PDF -->
<div style="text-align: center; margin-bottom: 6px;">
<img src="../uploads/company-logo/logo.png" class="flex justify-center" alt="Company Logo" style="height: 120px;" id="pdf-logo">
<h1 style="font-size: 28px; font-weight: bold; margin: 0; color: #1f2937;">PAYSLIP</h1>
</div>

<!-- ✅ SAME TOP INFO TABLE -->
<table style="width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 14px;" id="info-table">
<tr>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold; width: 25%;">Pay Type</td>
<td style="padding: 12px; border: 2px solid #000;">Monthly</td>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold; width: 25%;">Period</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-period"></td>
</tr>
<tr>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Worked Days</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-days-worked">22</td>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Payer ID</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-payer-id">N/A</td>

</tr>
<tr>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Employee Name</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-employee-name"></td>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Account name</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-position"></td>
</tr>
<tr>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Department</td>
<td style="padding: 12px; border: 2px solid #000;" id="pdf-department"></td>
<td style="padding: 12px; border: 2px solid #000; background: #f3f4f6; font-weight: bold;">Bank</td>
<td id="pdf-bank" style="padding: 12px; border: 2px solid #000;"></td>
</tr>
</table>

<!-- ✅ SAME EARNINGS/DEDUCTIONS TABLE -->
<table style="width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px;" id="salary-table">
<thead>
<tr>
<th style="border: 2px solid #000; padding: 12px; background: #e5e7eb; text-align: center; font-weight: bold;">Earnings</th>
<th style="border: 2px solid #000; padding: 12px; background: #e5e7eb; text-align: center; font-weight: bold;">Amount (₦)</th>
<th style="border: 2px solid #000; padding: 12px; background: #e5e7eb; text-align: center; font-weight: bold;">Deductions</th>
<th style="border: 2px solid #000; padding: 12px; background: #e5e7eb; text-align: center; font-weight: bold;">Amount (₦)</th>
</tr>
</thead>
<tbody>
<tr>
<td style="padding: 10px; border: 2px solid #000;">Basic Salary</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-basic">0.00</td>
<td style="padding: 10px; border: 2px solid #000;">Pension</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-pension">0.00</td>
</tr>
<tr>
<td style="padding: 10px; border: 2px solid #000;">Housing</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-housing">0.00</td>
<td style="padding: 10px; border: 2px solid #000;">PAYE</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-paye">0.00</td>
</tr>
<tr>
<td style="padding: 10px; border: 2px solid #000;">Transport</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-transport">0.00</td>
<td style="padding: 10px; border: 2px solid #000;">Other Deductions</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-deductions">0.00</td>
</tr>
<tr>
<td style="padding: 10px; border: 2px solid #000;">Medical</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-medical">0.00</td>
<td style="padding: 10px; border: 2px solid #000;"></td>
<td style="padding: 10px; border: 2px solid #000;"></td>
</tr>
<tr>
<td style="padding: 10px; border: 2px solid #000;">Utility</td>
<td style="padding: 10px; border: 2px solid #000; text-align: right; font-weight: bold;" id="pdf-utility">0.00</td>
<td style="padding: 10px; border: 2px solid #000;"></td>
<td style="padding: 10px; border: 2px solid #000;"></td>
</tr>
<!-- TOTALS -->
<tr style="font-weight: bold; font-size: 14px;">
<td style="padding: 12px; border: 2px solid #000;">Gross Pay</td>
<td style="padding: 12px; border: 2px solid #000; text-align: right;" id="pdf-gross-total">0.00</td>
<td style="padding: 12px; border: 2px solid #000;">Total Deduction</td>
<td style="padding: 12px; border: 2px solid #000; text-align: right;" id="pdf-total-deduction">0.00</td>
</tr>
<tr style="background: #d1fae5; font-weight: bold; font-size: 16px;">
<td colspan="2" style="padding: 15px; border: 2px solid #000;">Net Pay</td>
<td colspan="2" style="padding: 15px; border: 2px solid #000; text-align: right;" id="pdf-net-pay">0.00</td>
</tr>
</tbody>
</table>

<!-- ✅ SAME FOOTER -->
<div style="margin-top: 35px; margin-bottom: 20px; font-size: 11px; text-align: center; color: #666;">
This is a system-generated payslip | Generated on <span id="pdf-generated-date"><?= date('d M Y H:i') ?></span>
</div>

<!-- ACTION BUTTONS (Outside template for print hiding) -->
<div class="flex justify-between gap-4 mt-12 pt-8 border-t border-gray-200">
<button onclick="window.history.back()" class="px-8 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all font-semibold">
← Back to Payslips
</button>
<div class="flex gap-3">
<button onclick="printPayslip()" class=" bg-blue-600 from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl px-4 py-4 transition-all flex items-center">
<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"/>
</svg>
Print
</button>
<!-- <a href="../includes/payslip-template.php?id=<?= urlencode($id) ?>" target="_blank"         class="px-4 py-4 bg-green-600 from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all flex items-center">
    <svg class="w-9 h-9 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10l-5.5 5.5m0 0L7.5 18M7.5 18l1.5-1.5M12 10l5.5 5.5m0 0L16.5 18M16.5 18l-1.5-1.5"/>
    </svg>
PDF
</a> -->
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
