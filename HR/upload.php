 <!-- Header -->
  <?php include_once("../includes/header.php"); ?>
  

  <!-- Nav Section -->
   <?php include_once("../includes/nav.php"); ?>

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

            <div class="flex flex-col md:flex-row gap-4 items-center">

              <input 
                type="file" 
                id="fileInput"
                accept=".xlsx, .xls"
                class="border p-2 rounded-lg w-full md:w-auto"
              >

              <button 
                onclick="handleUpload()" 
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition"
              >
                Upload
              </button>

            </div>

            <!-- FILE NAME -->
            <p id="fileName" class="text-sm text-gray-500 mt-3"></p>

          </div>

          <!-- PREVIEW TABLE -->
          <div id="previewSection" class="bg-white rounded-xl shadow p-6 hidden">

            <div class="flex justify-between items-center mb-4">
              <h2 class="text-lg font-semibold">Preview Data</h2>

              <div class="space-x-2">
                <button class="bg-gray-200 px-4 py-2 rounded-lg">
                  Cancel
                </button>

                <button class="bg-green-600 text-white px-4 py-2 rounded-lg">
                  Save Payroll
                </button>
              </div>
            </div>

            <div class="overflow-x-auto">

              <table class="w-full text-sm border">

                <thead class="bg-gray-100">
                  <tr>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Department</th>
                    <th class="p-2 border">Gross</th>
                    <th class="p-2 border">Days Worked</th>
                    <th class="p-2 border">Basic</th>
                    <th class="p-2 border">Housing</th>
                    <th class="p-2 border">Transport</th>
                    <th class="p-2 border">Net Salary</th>
                  </tr>
                </thead>

                <tbody id="previewTable">
                  <!-- Dynamic rows go here -->
                </tbody>

                </table>



              </div>

<div id="previewPagination">
                <!-- Dynamic pagination controls (preview - limited rows, no pagination needed) -->
              </div>

            </div> 

          <!-- SUCCESS MESSAGE -->
          <div id="monthSection" class="bg-white p-6 rounded-xl shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Select Month to Upload</h2>
            <div class="flex flex-col md:flex-row gap-4 items-center mb-4">
              <select id="monthSelect" class="border p-3 rounded-lg w-full md:w-64" onchange="handleMonthSelect()">
                <option value="">Choose Month...</option>
              </select>
              <button onclick="handleCheckUpload()" class="bg-yellow-600 text-white px-6 py-2 rounded-lg hover:bg-yellow-700">
                Check Status
              </button>
            </div>
            <div id="previousMonths" class="flex flex-wrap gap-2">
              <!-- Dynamic previous months chips -->
            </div>
          </div>

            <!-- SUMMARY CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

              <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-sm text-gray-500">Total Employees</h2>
                <p id="total-employees" class="text-2xl font-bold mt-2">25</p>
              </div>

              <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-sm text-gray-500">Total Gross</h2>
                <p id="total-gross" class="text-2xl font-bold mt-2">₦7,500,000</p>
              </div>

              <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-sm text-gray-500">Total Net Paid</h2>
                <p id="total-net" class="text-2xl font-bold mt-2">₦6,200,000</p>
              </div>

            </div>

            <!-- FILTERS -->
            <div id="filterSection" class="bg-white rounded-xl shadow p-6 mb-6 hidden">
              <h3 class="text-lg font-semibold mb-4">Filters</h3>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input id="nameFilter" placeholder="Name" class="border p-3 rounded-lg">
                <input id="staffIdFilter" placeholder="Staff ID" class="border p-3 rounded-lg">
            <select id="monthSelect2" class="border p-3 rounded-lg">
                  <option value="">All Months</option>
                </select>
                <select id="deptSelect" class="border p-3 rounded-lg">
                  <option value="">All Departments</option>
                </select>
              </div>
              <button id="filterBtn" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Apply Filters</button>
            </div>

            <!-- PAYROLL TABLE -->
              <div class="bg-white rounded-xl shadow p-6 mb-6">

              <h2 class="text-lg font-semibold mb-4">Uploaded Payroll</h2>

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
                    <!-- Dynamic payroll rows -->
                  </tbody>

                </table>

              </div>
                <div id="payrollPagination">
                <!-- Dynamic pagination controls -->
              </div>

            </div> 

          <!-- ACTIONS -->
          <div class="flex gap-4">

            <button onclick="document.getElementById('filterSection')?.classList.toggle('hidden')" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
              Toggle Filters
            </button>
            <button id="viewPayslipsBtn" class="bg-blue-600 text-white px-6 py-2 rounded-lg">
              View Payslips
            </button>

            <button class="bg-gray-200 px-6 py-2 rounded-lg">
              Upload Another
            </button>

          </div>
        </main>
    </div>
</body> 
<!-- <script src="../js/jspdf.umd.min.js"></script> -->
 <script src="../js/upload.js"></script>
</html>
