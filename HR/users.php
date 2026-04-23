<?php
// Your existing includes (they output header/nav/sidebar)
include_once("../includes/header.php"); 
include_once("../includes/nav.php"); 

// Include users logic
include_once("../includes/all-users.php");

// Extract variables
$users = $GLOBALS['users'] ?? [];
$total_users = $GLOBALS['total_users'] ?? 0;
$total_pages = $GLOBALS['total_pages'] ?? 1;
$page = $GLOBALS['page'] ?? 1;
$search = $GLOBALS['search'] ?? '';
$role_filter = $GLOBALS['role_filter'] ?? '';
$status_filter = $GLOBALS['status_filter'] ?? '';
?>
<style>
.users-table-wrap {
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 20px 40px -24px rgba(15, 23, 42, 0.35);
    overflow: hidden;
}

.users-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.users-table thead th {
    background: linear-gradient(90deg, #eff6ff 0%, #e0e7ff 100%);
    color: #334155;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    border-bottom: 1px solid #e2e8f0;
}

.users-table tbody td {
    border-bottom: 1px solid #eef2f7;
}

.users-table tbody tr:hover {
    background: #f8fbff;
}

.users-table tbody tr:last-child td {
    border-bottom: none;
}

.user-chip-active,
.user-chip-inactive {
    display: inline-flex;
    align-items: center;
    border-radius: 9999px;
    padding: 4px 12px;
    font-size: 11px;
    font-weight: 700;
}

.user-chip-active {
    background: #dcfce7;
    color: #166534;
}

.user-chip-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.user-action-link {
    display: inline-flex;
    align-items: center;
    border-radius: 9999px;
    padding: 4px 10px;
    font-size: 12px;
    font-weight: 600;
    transition: all .18s ease;
}

.user-action-edit {
    color: #1d4ed8;
    background: #eff6ff;
}

.user-action-edit:hover {
    background: #dbeafe;
}

.user-action-delete {
    color: #b91c1c;
    background: #fef2f2;
}

.user-action-delete:hover {
    background: #fee2e2;
}
</style>

<!-- MAIN CONTENT AREA - This goes INSIDE your existing body -->
<div class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <!-- Breadcrumb -->
    <nav class="mb-6 flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?php echo HR_URL; ?>dashboard" class="inline-flex items-center text-sm font-medium text-gray-600 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Users</span>
                </div>
            </li>
        </ol>
    </nav>

<!-- Flash Messages -->
<?php 
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    $is_success = $message['type'] === 'success';
?>
<div class="mx-auto mb-8 max-w-4xl rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="flex-shrink-0 p-2 bg-opacity-20 rounded-lg 
                <?php echo $is_success ? 'bg-green-500' : 'bg-red-500'; ?>">
                <svg class="w-6 h-6 text-white" 
                     fill="<?php echo $is_success ? 'currentColor' : 'none'; ?>" 
                     stroke="currentColor" viewBox="0 0 24 24">
                    <?php if ($is_success): ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    <?php else: ?>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    <?php endif; ?>
                </svg>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($message['text']); ?></p>
            </div>
        </div>
        <button onclick="this.parentElement.parentElement.remove()" 
                class="p-2 hover:bg-gray-200 rounded-full transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>
<?php } ?>
    <!-- Page Header -->
    <div class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 p-6 shadow-sm">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Users Management</h1>
                <p class="mt-1 text-sm text-gray-600">Total Users: <span class="font-semibold text-gray-900"><?php echo $total_users; ?></span></p>
            </div>
            <!-- <a href="add-user.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New User
            </a> -->
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
        <form method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
            <div class="flex-1 min-w-0">
                <label class="mb-2 block text-sm font-medium text-gray-700">Search users</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Search by name or email..." 
                       class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
            </div>
            
            <!-- <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Roles</option>
                    <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="employee" <?php echo $role_filter === 'employee' ? 'selected' : ''; ?>>Employee</option>
                    <option value="hr" <?php echo $role_filter === 'hr' ? 'selected' : ''; ?>>HR</option>
                </select>
            </div>
             -->
            <div class="w-48">
                <label class="mb-2 block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full rounded-xl border border-gray-300 px-4 py-2.5 focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div class="flex gap-3">
                <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 font-medium text-white shadow-sm transition-all hover:bg-blue-700">
                    Filter
                </button>
                <a href="<?php echo HR_URL; ?>users" class="rounded-xl bg-gray-100 px-6 py-2.5 font-medium text-gray-900 transition-all hover:bg-gray-200">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="users-table-wrap">
        <div class="overflow-x-auto">
            <table class="users-table min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">STAFF ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-600">Created</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="6" class="px-16 py-14 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="mx-auto mb-4 h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No users found</h3>
                                    <p class="text-sm">Try adjusting your search or filter criteria.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 shadow-sm">
                                        <span class="text-white font-semibold text-sm"><?php echo strtoupper(substr($user['name'], 0, 2)); ?></span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['name']); ?></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['staff_id']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="<?php echo $user['status'] === 'active' ? 'user-chip-active' : 'user-chip-inactive'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                            </td>
                            <td class="space-x-3 whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                   class="user-action-link user-action-edit">
                                    Edit
                                </a>
                                <form method="POST" action="../includes/delete-user.php" class="inline" 
                                      onsubmit="return confirm('Are you sure you want to delete <?php echo htmlspecialchars($user['name']); ?>?')">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="user-action-link user-action-delete">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium"><?php echo (($page - 1) * 10 + 1); ?></span> to 
                    <span class="font-medium"><?php echo min($page * 10, $total_users); ?></span> of 
                    <span class="font-medium"><?php echo $total_users; ?></span> results
                </div>
                <nav class="relative z-0 inline-flex -space-x-px rounded-xl shadow-sm">
                    <a href="?page=<?php echo max(1, $page-1); ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="<?php echo $page <= 1 ? 'pointer-events-none cursor-not-allowed opacity-50' : 'hover:bg-gray-100'; ?> relative inline-flex items-center rounded-l-xl border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="relative inline-flex items-center border border-gray-300 bg-white px-4 py-2 text-sm font-medium <?php echo $i === $page ? 'z-10 border-blue-500 bg-blue-50 text-blue-600' : 'text-gray-500 hover:bg-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <a href="?page=<?php echo min($total_pages, $page+1); ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="<?php echo $page >= $total_pages ? 'pointer-events-none cursor-not-allowed opacity-50' : 'hover:bg-gray-100'; ?> relative inline-flex items-center rounded-r-xl border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </a>
                </nav>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- <?php include_once("../includes/footer.php"); ?> -->