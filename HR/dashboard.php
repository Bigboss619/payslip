  <!-- Header -->
  <?php include_once("../includes/header.php"); ?>
  

  <!-- Nav Section -->
   <?php include_once("../includes/nav.php"); ?>

      <!-- MAIN -->
      <div class="flex-1 flex flex-col">

        <!-- TOPBAR -->
        <header class="bg-white shadow px-6 py-4 flex items-center lg:justify-between">

          <!-- Mobile toggle + title -->
          <div class="flex items-center lg:hidden">
            <button class="mr-4 p-2 rounded-lg hover:bg-gray-100 sidebar-toggle">
              <svg class="w-5 h-5 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <h1 class="text-lg font-semibold">
              Dashboard
            </h1>
          </div>
          
          <h1 class="text-lg font-semibold hidden lg:block">
            Dashboard
          </h1>

          <div class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">Welcome, Emmanuel</span>
            <div class="w-8 h-8 bg-blue-500 text-white flex items-center justify-center rounded-full">
              E
            </div>
          </div>

        </header>

        <!-- CONTENT -->
        <main class="p-6 overflow-y-auto">

  <!-- WELCOME -->
  <div class="mb-6">
    <h1 class="text-2xl font-bold">Welcome, Emmanuel 👋</h1>
    <p class="text-gray-500 text-sm">Here’s your salary overview</p>
  </div>

  <!-- STATS -->
  <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-sm text-gray-500">Total Payslips</h2>
      <p class="text-2xl font-bold mt-2">12</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-sm text-gray-500">Last Salary</h2>
      <p class="text-2xl font-bold mt-2">₦250,000</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-sm text-gray-500">Current Month</h2>
      <p class="text-2xl font-bold mt-2">March 2026</p>
    </div>

  </div> -->
<!-- STATS - Add data-stat attributes -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-sm text-gray-500">Total Payslips</h2>
        <p class="text-2xl font-bold mt-2" data-stat="total_payslips">Loading...</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-sm text-gray-500">Last Salary</h2>
        <p class="text-2xl font-bold mt-2" data-stat="last_salary">Loading...</p>
    </div>
    <div class="bg-white p-6 rounded-xl shadow">
        <h2 class="text-sm text-gray-500">Current Month</h2>
        <p class="text-2xl font-bold mt-2" data-stat="current_month">Loading...</p>
    </div>
</div>
  <!-- QUICK ACTIONS -->
  <div class="bg-white p-6 rounded-xl shadow mb-6 flex flex-wrap gap-4">

    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
      View Payslips
    </button>

    <button class="bg-green-600 text-white px-6 py-2 rounded-lg hidden" disabled>
      Download Last Payslip
    </button>

  </div>

  <!-- RECENT PAYSLIPS -->
 <!-- RECENT PAYSLIPS - FIXED -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-lg font-semibold mb-4">Recent Payslips</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left">
                    <th class="py-2">Month</th>
                    <th class="py-2">Gross</th>
                    <th class="py-2">Net</th>
                    <th class="py-2">Action</th>
                </tr>
            </thead>
            <tbody id="recent-tbody">  <!-- ✅ REQUIRED ID -->
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</main>

      </div>

    </div>
</body>
<script src="../js/dashboard.js"></script>
</html>
