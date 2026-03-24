 <!-- Header -->
  <?php include_once("../includes/header.php"); ?>
  

  <!-- Nav Section -->
   <?php include_once("../includes/nav.php"); ?>

        <!-- MAIN -->
        <main class="p-6 overflow-y-auto">

    <!-- PAGE TITLE -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">All Payslips</h1>
        <p class="text-gray-500 text-sm">View and download all employee salary history</p>
    </div>

    <!-- FILTERS -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-2xl shadow-xl mb-8 border border-blue-100 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between">

        <div class="flex flex-wrap gap-4 flex-1">
            <select id="month-filter" class="bg-white shadow-sm border border-gray-200 px-4 py-2 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">All Months</option>
            </select>

            <select id="year-filter" class="bg-white shadow-sm border border-gray-200 px-4 py-2 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">All Years</option>
            </select>

            <input id="search"
                type="text" 
                placeholder="Search by month, amount..." 
                class="flex-1 bg-white shadow-sm border border-gray-200 px-4 py-2 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
            >
        </div>

        <button onclick="downloadAll()" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold px-8 py-2.5 rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all whitespace-nowrap">
            📥 Download All Payslips
        </button>

    </div>


    <!-- TABLE -->
    <div class="space-y-6">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl shadow-2xl p-1 border border-white/50 ring-1 ring-black/5">
            <div class="overflow-x-auto rounded-2xl">
        <table id="payslip-table" class="w-full text-sm divide-y divide-gray-100">


        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <tr class="text-left font-semibold text-gray-700 text-xs uppercase tracking-wide">
                <th class="px-6 py-4 cursor-pointer sort-header hover:text-blue-600 transition-colors select-none" onclick="handleSort('month')" data-column="month">Month</th>

                <th class="px-6 py-4 cursor-pointer sort-header hover:text-blue-600 transition-colors select-none" onclick="handleSort('grossSalary')" data-column="grossSalary">Gross Salary</th>

                <th class="px-6 py-4 cursor-pointer sort-header hover:text-blue-600 transition-colors select-none" onclick="handleSort('deductions')" data-column="deductions">Deductions</th>

                <th class="px-6 py-4 cursor-pointer sort-header hover:text-blue-600 transition-colors select-none" onclick="handleSort('netSalary')" data-column="netSalary">Net Salary</th>

                <th class="px-6 py-4 text-center">Status</th>
                <th class="px-6 py-4 text-left">Staff Name</th>
                <th class="px-6 py-4 text-center">Action</th>
            </tr>
        </thead>

        <tbody>


        </tbody>

        </table>


    </div>

        <!-- Empty State -->
        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 px-6 bg-gradient-to-br from-gray-50 to-gray-100 rounded-3xl shadow-xl border border-dashed border-gray-200 min-h-[400px]">
            <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-3xl flex items-center justify-center mb-6 shadow-lg">
                <svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No payslips match</h3>
            <p class="text-gray-500 mb-8 max-w-md">Try adjusting your search or filter criteria to see more results.</p>
<button onclick="loadData(1)" class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold px-8 py-3 rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all">Clear Filters</button>
        </div>

        <!-- Pagination -->
        <div id="pagination" class="hidden justify-center bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
            <div class="flex items-center gap-3">
                <button onclick="handlePagination('prev')" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 rounded-2xl flex items-center justify-center transition-all shadow-sm hover:shadow-md disabled:opacity-50" disabled>‹</button>
                <span class="font-semibold text-lg text-gray-900" id="page-info">Page 1 of 2</span>
                <button onclick="handlePagination('next')" class="w-12 h-12 bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 rounded-2xl flex items-center justify-center transition-all shadow-sm hover:shadow-md disabled:opacity-50" disabled>›</button>
            </div>
        </div>
        </div>
        </main>

    </div>
    <script src="../js/payslip.js"></script>
</body>
</html>
