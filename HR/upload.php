<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
  header('Location: dashboard.php?error=hr_only');
  exit;
}

include_once("../includes/header.php");
include_once("../includes/nav.php");
?>
    <!-- MAIN -->
    <main class="flex-1 p-6 overflow-y-auto">
      <!-- TITLE -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold">Upload Payroll</h1>
        <?php if(isset($_GET['error'])): ?>
        <p class="text-red-600 text-sm mt-2">HR access only. <a href="dashboard.php" class="underline">Back to Dashboard</a></p>
        <?php endif; ?>
        <p class="text-gray-500 text-sm">Upload staff salary Excel file</p>
      </div>

      <!-- UPLOAD CARD -->
      <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Upload Excel File</h2>
        <form id="uploadForm" enctype="multipart/form-data" method="POST">
          <div class="flex flex-col md:flex-row gap-4 items-center mb-4">
            <select id="monthSelectUpload" name="month" class="border p-3 rounded-lg w-full md:w-64" required>
              <option value="">Choose Month...</option>
              <option value="January">January</option><option value="February">February</option><option value="March">March</option>
              <option value="April">April</option><option value="May">May</option><option value="June">June</option>
              <option value="July">July</option><option value="August">August</option><option value="September">September</option>
              <option value="October">October</option><option value="November">November</option><option value="December">December</option>
            </select>
            <input type="number" id="yearSelect" name="year" value="<?php echo date('Y'); ?>" class="border p-3 rounded-lg w-32" min="2020" max="2030" required>
            <input name="payroll_file" type="file" id="fileInput" accept=".xlsx,.xls" class="border p-2 rounded-lg w-full md:w-auto" required>
            <button type="submit" id="uploadBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400">Upload & Preview</button>
          </div>
          <p id="fileName" class="text-sm text-gray-500"></p>
          <div id="uploadStatus" class="mt-2 hidden"></div>
        </form>
      </div>

      <!-- PREVIEW SECTION (post-upload) -->
      <div id="previewSection" class="bg-white rounded-xl shadow p-6 mb-6 hidden">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">Preview Data (Save to confirm)</h2>
          <div class="space-x-2">
            <button onclick="cancelPreview()" class="bg-gray-200 px-4 py-2 rounded-lg hover:bg-gray-300">Cancel</button>
            <button onclick="savePayroll()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700" id="saveBtn" disabled>Save Payroll</button>
          </div>
        </div>
<div class="overflow-x-auto max-h-96 overflow-y-auto">
  <table class="w-full text-sm border">
    <thead class="bg-gray-100">
      <tr>
        <th class="p-2 border">Staff ID</th>
        <th class="p-2 border">Name</th>
        <th class="p-2 border">Department</th>
        <th class="p-2 border">Monthly Gross</th>
        <th class="p-2 border">Pro-Rata</th>
        <th class="p-2 border">Days Worked</th>
        <th class="p-2 border">Basic Salary</th>
        <th class="p-2 border">Housing</th>
        <th class="p-2 border">Transport</th>
        <th class="p-2 border">Medical</th>
        <th class="p-2 border">Utility</th>
        <th class="p-2 border">Monthly PAYE</th>
        <th class="p-2 border">Payroll Deductions</th>
        <th class="p-2 border">Pension</th>
        <th class="p-2 border">Monthly Take Home</th>
      </tr>
    </thead>
    <tbody id="previewTable"></tbody>
  </table>
</div>
      </div>

      <!-- MONTH STATUS CHECK -->
      <div id="monthSection" class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Uploaded Payroll Status</h2>
<div class="flex flex-col md:flex-row gap-4 items-center mb-4">
<select id="statusMonthSelect" class="border p-3 rounded-lg w-full md:w-48">
<?php
              // Get LATEST payroll batch for auto-select  
              $stmt = $conn->query("SELECT month, year FROM payroll_batches ORDER BY id DESC LIMIT 1");

              $latest = $stmt->fetch(PDO::FETCH_ASSOC);
              $months = [
                '01'=>'January', '02'=>'February', '03'=>'March', '04'=>'April',
                '05'=>'May', '06'=>'June', '07'=>'July', '08'=>'August',
                '09'=>'September', '10'=>'October', '11'=>'November', '12'=>'December'
              ]; 
              $monthMap = array_flip($months);
              $latestMonthNum = $latest ? $monthMap[$latest['month']] : date('m');

              $latestYear = $latest ? $latest['year'] : date('Y');
              $latestMonthName = isset($months[$latestMonthNum]) ? $months[$latestMonthNum] : date('F');
              foreach($months as $val => $name): ?>
                <option value="<?php echo $val; ?>" <?php echo $val == $latestMonthNum ? 'selected' : ''; ?>><?php echo $name; ?></option>
              <?php endforeach; ?>
            </select>
            <input type="number" id="statusYearSelect" class="border p-3 rounded-lg w-32" min="2020" max="2030" value="<?php echo $latestYear; ?>" placeholder="Year">

            <button onclick="checkPayrollStatus()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">Check Status</button>
            <button onclick="loadExcelPreview()" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 ml-2">
                📊  Load Excel Preview
            </button>
          </div>
        <div id="previousMonths" class="flex flex-wrap gap-2 mb-4"></div>
        <div id="statusMsg" class="p-3 rounded-lg hidden"></div>
      </div>


      <!-- SUMMARY -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-sm text-gray-500">Total Employees</h2>
          <p id="total-employees" class="text-2xl font-bold mt-2">0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-sm text-gray-500">Total Gross</h2>
          <p id="total-gross" class="text-2xl font-bold mt-2">₦0</p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-sm text-gray-500">Total Net</h2>
          <p id="total-net" class="text-2xl font-bold mt-2">₦0</p>
        </div>
      </div>

  <!-- ✅ PAYSIP FILTER -->
<!-- <div id="filterSection" class="bg-white p-6 rounded-xl shadow mb-6 hidden">
  <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    Filter Payslips
  </h3>
  <div class="flex flex-wrap gap-4 items-end">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
      <select id="payslipMonth" class="border p-3 rounded-lg w-32">
        <option value="">All Months</option>
        <option value="01">Jan</option><option value="02">Feb</option><option value="03">Mar</option>
        <option value="04">Apr</option><option value="05">May</option><option value="06">Jun</option>
        <option value="07">Jul</option><option value="08">Aug</option><option value="09">Sep</option>
        <option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
      <input type="number" id="payslipYear" value="<?php echo date('Y'); ?>" 
             class="border p-3 rounded-lg w-28" min="2020" max="2030">
    </div>
    <button onclick="refreshPayslips()" 
            class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-medium flex items-center gap-2">
      🔄 Load Payslips
    </button>
    <button onclick="window.payrollTable.renderPayslipTable()" 
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
      📊 Refresh Table
    </button>
  </div>
</div> -->

<!-- ✅ ENHANCED PAYSIP FILTER -->
<div id="filterSection" class="bg-white p-6 rounded-xl shadow mb-6 hidden">
  <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
    </svg>
    Filter Payslips <span id="filterStatus" class="ml-2 text-sm text-gray-500">(All Records)</span>
  </h3>
  <div class="flex flex-wrap gap-4 items-end">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
      <select id="payslipMonth" class="border p-3 rounded-lg w-32">
        <option value="">All Months</option>
        <option value="01">Jan</option><option value="02">Feb</option><option value="03">Mar</option>
        <option value="04">Apr</option><option value="05">May</option><option value="06">Jun</option>
        <option value="07">Jul</option><option value="08">Aug</option><option value="09">Sep</option>
        <option value="10">Oct</option><option value="11">Nov</option><option value="12">Dec</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
      <input type="number" id="payslipYear" value="<?php echo date('Y'); ?>" 
             class="border p-3 rounded-lg w-28" min="2020" max="2030">
    </div>
    <button onclick="refreshPayslips()" 
            class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 font-medium flex items-center gap-2">
      🔄 Filter Payslips
    </button>
    <button onclick="clearPayslipFilters()" 
            class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 font-medium flex items-center gap-1">
      🗑️ Clear Filters
    </button>
    <button onclick="window.payrollTable.renderPayslipTable()" 
            class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-medium">
      📊 Refresh Table
    </button>
  </div>
  <div id="filterCount" class="mt-3 text-sm font-medium text-blue-600 hidden"></div>
</div>
   <!-- PAYROLL TABLE WITH TOGGLE -->
<div class="bg-white rounded-xl shadow p-6">
  <div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Payroll Data 
      <span id="latestBadge" class="ml-2 bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
        Latest: <?php echo $latestMonthName; ?> <?php echo $latestYear; ?>
      </span>
      <span id="filterCount" class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm hidden"></span>
    </h2>

    
    <!-- ✅ TOGGLE BUTTONS -->
    <div class="flex gap-2 bg-gray-100 p-2 rounded-lg">
      <button id="excelToggle" class="toggle-btn active bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-all">
        📊 Excel Preview
      </button>
      <button id="payslipToggle" class="toggle-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-all">
        💰 Payslip Records
      </button>
    </div>
    
    <!-- Filter Toggle -->
    <button onclick="toggleFilters()" id="filterToggleBtn" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm flex items-center gap-1 ml-2">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
      Filters
    </button>
  </div>

  <!-- ✅ TWO TABLES -->
  <!-- Excel Record Table -->
  <div id="excelTableContainer" class="table-container active block">
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead class="bg-gray-100 sticky top-0 z-10">
          <tr>
            <th class="p-3 text-left font-semibold border">Row</th>
            <th class="p-3 text-left font-semibold border">Staff ID</th>
            <th class="p-3 text-left font-semibold border">Name</th>
            <th class="p-3 text-left font-semibold border">Department</th>
            <th class="p-3 text-right font-semibold border">Gross</th>
            <th class="p-3 text-right font-semibold border">Net</th>
          </tr>
        </thead>
        <tbody id="excelTableBody">
          <tr><td colspan="6" class="p-12 text-center text-gray-500">Loading Excel data...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Payslip Records Table -->
  <div id="payslipTableContainer" class="table-container hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-collapse">
        <thead class="bg-green-100 sticky top-0 z-10">
          <tr>
            <th class="p-3 text-left font-semibold border">Staff ID</th>
            <th class="p-3 text-left font-semibold border">Name</th>
            <th class="p-3 text-right font-semibold border">Month</th>
            <th class="p-3 text-right font-semibold border">Gross</th>
            <th class="p-3 text-right font-semibold border">Net Pay</th>
            <th class="p-3 text-center font-semibold border">Status</th>
          </tr>
        </thead>
        <tbody id="payslipTableBody">
          <tr><td colspan="6" class="p-12 text-center text-gray-500">Loading payslip records...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div id="payrollPagination" class="mt-6"></div>
</div>
<style>
.table-container {
  display: none;
}
.table-container.active {
  display: block !important;
}
</style>
<script src="../js/payroll-table.js"></script>
<script src="../js/upload.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  // ✅ Connect JS to form
  document.getElementById('uploadForm').addEventListener('submit', window.uploadManager.handleFileUpload);
  
  // Load initial data with LATEST payroll
  window.uploadManager.loadTotalEmployees();
  window.payrollTable.init();
  window.uploadManager.loadPayrollData('<?php echo $latestMonthNum; ?>', '<?php echo $latestYear; ?>');
});

</script>
</body>
</html>

