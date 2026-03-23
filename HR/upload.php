<?php

include_once("../includes/header.php");
include_once("../includes/nav.php");
?>
    <!-- MAIN -->
    <main class="flex-1 p-6 overflow-y-auto">
      <!-- TITLE -->
      <div class="mb-6">
        <h1 class="text-2xl font-bold">Upload Payroll</h1>
        <p class="text-gray-500 text-sm">Upload staff salary Excel file</p>
      </div>

      <!-- UPLOAD CARD -->
      <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">Upload Excel File</h2>
        <form id="uploadForm" enctype="multipart/form-data">
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
<select id="monthSelect" class="border p-3 rounded-lg w-full md:w-64">
            <option value="">All Months...</option>
          </select>
          <button onclick="checkMonthStatus()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">Check</button>
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

      <!-- FILTERS -->
      <div id="filterSection" class="bg-white rounded-xl shadow p-6 mb-6 hidden">
        <h3 class="text-lg font-semibold mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <input id="nameFilter" placeholder="Name" class="border p-3 rounded-lg">
          <input id="staffIdFilter" placeholder="Staff ID" class="border p-3 rounded-lg">
          <select id="monthFilter" class="border p-3 rounded-lg"><option value="">All Months</option></select>
          <select id="deptFilter" class="border p-3 rounded-lg"><option value="">All Depts</option></select>
        </div>
        <button id="applyFilters" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg">Apply</button>
      </div>

      <!-- PAYROLL TABLE -->
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold">Uploaded Payroll</h2>
          <button onclick="toggleFilters()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">Filters</button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-100">
              <tr>
                <th class="p-2 text-left">Name</th>
                <th class="p-2 text-left">Department</th>
                <th class="p-2 text-left">Gross</th>
                <th class="p-2 text-left">Net</th>
              </tr>
            </thead>
            <tbody id="payrollTableBody">
              <tr><td colspan="4" class="p-4 text-center text-gray-500">Loading...</td></tr>
            </tbody>
          </table>
        </div>
        <div id="payrollPagination"></div>
      </div>

      <!-- ACTIONS -->
      <div class="flex gap-4 mt-6">
        <button id="viewPayslipsBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg">View Payslips</button>
        <button class="bg-gray-200 px-6 py-2 rounded-lg">Upload Another</button>
      </div>
    </main>
  </div>

  <script src="../js/upload.js"></script>
  <script>
    // JS handles init
  </script>


</body>
</html>

