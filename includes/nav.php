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
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'HR'): ?>
        <!-- <div class="p-2 bg-yellow-100 text-xs text-yellow-800 rounded mb-2">
            🔍 DEBUG: role=<?= $_SESSION['role'] ?? 'NOT SET' ?> | 
            hr_type=<?= $_SESSION['hr_type'] ?? 'NOT SET' ?> | 
            user_id=<?= $_SESSION['user_id'] ?? 'NOT SET' ?> -->
        <!-- </div> -->
        <?php endif; ?>

        <!-- Dashboard 🏠 -->
        <a href="<?php echo HR_URL; ?>dashboard" class="sidebar-link block px-4 py-3 rounded-lg bg-blue-100 text-blue-600 font-medium group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- Payslips 💰 (Keep original) -->
        <a href="<?php echo HR_URL; ?>payslip" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Payslips</span>
        </a>

        <?php if($_SESSION['role'] === 'HR' && $_SESSION['hr_type'] === 'MAIN'): ?>
        <!-- Department 👥 (NEW ICON - Users/Building) -->
        <a href="<?php echo HR_URL; ?>department" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Department</span>
        </a>
        <?php endif; ?>

        <?php if($_SESSION['role'] === 'HR'): ?>
        <!-- Upload Payroll 📤 (Keep original) -->
        <a href="<?php echo HR_URL; ?>upload" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span>Upload Payroll</span>
        </a>

        <!-- Users 👨‍💼 (NEW ICON - Users) -->
        <a href="<?php echo HR_URL; ?>users" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <span>Users</span>
        </a>
        <?php endif; ?>

        <!-- Profile 😊 (NEW ICON - User Circle) -->
        <a href="<?php echo HR_URL; ?>profile" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profile</span>
        </a>

        <!-- Logout 🚪 -->
        <a href="logout.php" class="sidebar-link block px-4 py-3 rounded-lg hover:bg-gray-100 text-red-600 hover:text-red-800 font-semibold group relative">
            <svg class="w-5 h-5 flex-shrink-0 inline-block mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span>Logout</span>
        </a>
    </nav>
</aside>