<?php 

?>
<!-- SIDEBAR -->
      <aside id="sidebar" class="w-64 bg-white shadow-lg transition-all duration-300 md:block lg:w-64">

        <div class="p-6 text-xl font-bold border-b flex items-center justify-between">
          <span>PayslipSys</span>
          <button class="md:hidden lg:hidden p-1 rounded hover:bg-gray-100 sidebar-toggle">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <nav class="p-4 space-y-2 overflow-hidden">

          <a href="<?php echo HR_URL; ?>dashboard" class="sidebar-link block px-4 py-3 rounded-lg bg-blue-100 text-blue-600 font-medium group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
          </a>

          <a href="<?php echo HR_URL; ?>payslip" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Payslips</span>
          </a>

          <a href="<?php echo HR_URL; ?>department" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Department</span>
          </a>

          <a href="upload.php" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span>Upload Payroll</span>
          </a>

          <a href="<?php echo HR_URL; ?>profile" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span>Profile</span>
          </a>

            <a href="logout.php" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 text-red-500 group relative">
              <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
              </svg>
              <span>Logout</span>
            </a>


        </nav>
      </aside>