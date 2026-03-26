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
        <main class="p-6 overflow-y-auto">
            <div class="max-w-4xl mx-auto">
                <div id="payslip-detail" class="bg-white shadow-2xl rounded-3xl p-8 border border-gray-100">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 mb-1">Payslip Detail</h1>
<p id="payslip-period" class="text-lg text-gray-600 font-medium">Loading...</p>
                        </div>
                        <div class="text-right">
                            <button onclick="window.history.back()" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all mr-3">
                                ← Back to Payslips
                            </button>
                            <!-- <button onclick="window.print()" class="inline-flex items-center px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5" />
                                </svg>
                                Print Payslip
                            </button> -->
                        </div>
                    </div>

                    <!-- LOADING -->
                    <div id="loading" class="flex flex-col items-center justify-center py-20 text-gray-500">
                        <div class="w-16 h-16 border-4 border-blue-200 border-t-blue-500 rounded-full animate-spin mb-4"></div>
                        <p>Loading payslip...</p>
                    </div>

                    <!-- PAYSLOP DETAIL -->
                    <div id="detail-content" class="hidden">

                        <!-- COMPANY & PERIOD -->
                        <div class="flex justify-between items-start border-b pb-6 mb-8">
                            <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-1" id="company-name">PayslipSys HR</h2>

                                <p class="text-lg text-gray-600 font-semibold" id="company-period"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 mb-1">Date Generated</p>
                                <p class="font-semibold text-gray-900" id="generated-date"></p>
                            </div>
                        </div>

                        <!-- EMPLOYEE INFO -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-2">Employee Information</p>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span>Name:</span>
                                        <span class="font-semibold" id="employee-name"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Employee ID:</span>
                                        <span class="font-semibold" id="employee-id"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Department:</span>
                                        <span class="font-semibold" id="department"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Position:</span>
                                        <span class="font-semibold" id="position"></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide mb-2">Pay Period</p>
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span>Period:</span>
                                        <span class="font-semibold" id="pay-period"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Status:</span>
                                        <span class="font-semibold px-2 py-1 rounded-full text-xs" id="status-badge">Paid</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Days Worked:</span>
                                        <span class="font-semibold">22/22</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Pro-rata:</span>
                                        <span class="font-semibold">Full Month</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- EARNINGS & DEDUCTIONS -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Earnings</h3>
                                <table class="w-full">
                                    <tbody class="divide-y divide-gray-200">
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Basic Salary (40%)</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="basic-salary"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Housing Allowance (25%)</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="housing"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Transport Allowance (20%)</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="transport"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Medical Allowance (10%)</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="medical"></td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="py-3 text-sm font-bold text-gray-900">Total Earnings</td>
                                            <td class="py-3 text-sm text-right font-bold text-gray-900" id="total-earnings"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div>
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Deductions</h3>
                                <table class="w-full">
                                    <tbody class="divide-y divide-gray-200">
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">PAYE Tax</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="tax"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Pension (8%)</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="pension"></td>
                                        </tr>
                                        <tr>
                                            <td class="py-3 text-sm font-medium text-gray-900">Payroll Deductions</td>
                                            <td class="py-3 text-sm text-right font-semibold text-gray-900" id="payroll-deductions"></td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="py-3 text-sm font-bold text-gray-900">Total Deductions</td>
                                            <td class="py-3 text-sm text-right font-bold text-gray-900" id="total-deductions"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- SUMMARY -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-100">
                            <div class="flex justify-between items-center text-lg mb-4">
                                <span class="font-bold text-gray-900">Gross Salary</span>
                                <span class="font-bold text-gray-900" id="gross-salary-display"></span>
                            </div>
                            <div class="flex justify-between items-center text-xl">
                                <span class="font-bold text-gray-900">Net Pay</span>
                                <span class="font-bold text-green-600 text-2xl" id="net-salary-display"></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2 opacity-75" id="status-display">Paid ✓</p>
                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="flex justify-between gap-4 mt-8 pt-6 border-t">
                            <button onclick="window.history.back()" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 transition-all font-medium">
                                ← Back to Payslips
                            </button>
                            <div class="flex gap-2">
                                <button onclick="printPayslip()" class="px-8 py-3 bg-blue-600 from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v.5"/>
                                    </svg>
                                    Print Payslip
                                </button>
                                <button onclick="downloadPDF()" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10l-5.5 5.5m0 0L7.5 18M7.5 18l1.5-1.5M12 10l5.5 5.5m0 0L16.5 18M16.5 18l-1.5-1.5"/>
                                    </svg>
                                    PDF
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
