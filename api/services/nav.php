<?php
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '', '/');

$isActive = static function (string $route) use ($currentPath): bool {
    return str_ends_with($currentPath, trim($route, '/'));
};

$linkClass = static function (bool $active): string {
    return 'sidebar-link group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all duration-150 ' .
        ($active
            ? 'bg-blue-100 text-blue-700 shadow-sm'
            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900');
};

$isHr = isset($_SESSION['role']) && $_SESSION['role'] === 'HR';
$isMainHr = $isHr && isset($_SESSION['hr_type']) && $_SESSION['hr_type'] === 'MAIN';
?>

<!-- SIDEBAR -->
<aside id="sidebar" class="w-80 bg-white shadow-lg transition-all duration-300 md:block">
    <div class="border-b border-gray-100 px-6 py-5">
        <div class="flex items-center justify-between">
            <div class="min-w-0">
                <p class="sidebar-brand-detail text-xs font-semibold uppercase tracking-widest text-blue-600">Version 2.0</p>
                <h2 class="sidebar-brand-detail truncate text-xl font-bold text-gray-800">PayslipSys</h2>
            </div>
            <button type="button" class="sidebar-collapse-toggle items-center justify-center rounded-lg p-1.5 text-gray-600 hover:bg-gray-100" style="display: none;" aria-label="Toggle sidebar" title="Toggle sidebar">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path class="sidebar-collapse-left" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    <path class="sidebar-collapse-right hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <button class="sidebar-toggle rounded-lg p-1.5 text-gray-600 hover:bg-gray-100 md:hidden lg:hidden">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <nav class="space-y-1 p-4">
        <a href="<?php echo HR_URL; ?>dashboard" class="<?php echo $linkClass($isActive('dashboard')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="sidebar-label">Dashboard</span>
        </a>

        <a href="<?php echo HR_URL; ?>payslip" class="<?php echo $linkClass($isActive('payslip')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span class="sidebar-label">Payslips</span>
        </a>

        <?php if ($isMainHr): ?>
        <a href="<?php echo HR_URL; ?>department" class="<?php echo $linkClass($isActive('department')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="sidebar-label">Department</span>
        </a>
        <?php endif; ?>

        <?php if ($isHr): ?>
        <a href="<?php echo HR_URL; ?>upload" class="<?php echo $linkClass($isActive('upload')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
            <span class="sidebar-label">Upload Payroll</span>
        </a>

        <a href="<?php echo HR_URL; ?>users" class="<?php echo $linkClass($isActive('users')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <span class="sidebar-label">Users</span>
        </a>
        <?php endif; ?>

        <a href="<?php echo HR_URL; ?>profile" class="<?php echo $linkClass($isActive('profile')); ?>">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="sidebar-label">Profile</span>
        </a>
    </nav>

    <div class="border-t border-gray-100 px-4 py-4">
        <a href="logout.php" class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-red-600 transition-all duration-150 hover:bg-red-50 hover:text-red-700">
            <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            <span class="sidebar-label">Logout</span>
        </a>
    </div>
</aside>