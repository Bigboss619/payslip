<?php
include_once("../includes/header.php");
include_once("../includes/nav.php");
include_once("../includes/edit-user-sub.php");

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
?>

        <!-- Main Content -->
        <div class="flex-1 p-8 overflow-y-auto">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                    <li><a href="users.php" class="text-blue-600 hover:text-blue-800">Users</a></li>
                    <li><span class="text-gray-500 mx-2">/</span></li>
                    <li class="text-gray-900 font-medium">Edit <?php echo htmlspecialchars($user['name']); ?></li>
                </ol>
            </nav>

            <!-- Flash Messages -->
            <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-8 p-4 rounded-xl border-2 shadow-lg max-w-2xl mx-auto
                <?php echo $_SESSION['message']['type'] === 'success' ? 
                      'bg-green-50 border-green-200 text-green-900' : 
                      'bg-red-50 border-red-200 text-red-900'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']['text']); ?>
            </div>
            <?php unset($_SESSION['message']); endif; ?>

            <!-- Edit Form -->
            <div class="max-w-2xl mx-auto">
                <?php if (!empty($errors)): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <ul class="list-disc list-inside space-y-1 text-sm text-red-800">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit User</h1>
                    <p class="text-gray-600 mb-8">Update user information</p>
                    
                    <form method="POST" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" 
                                   required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" 
                                   required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>
                        <!-- Password (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">New Password (leave blank to keep current)</label>
                            <input type="password" name="password" placeholder="Enter new password (optional)" 
                                class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-semibold mb-2">Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password" 
                                class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" <?php echo ($user['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($user['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-200 mt-8">
                            <button type="submit" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl focus:ring-4 focus:ring-blue-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update User
                            </button>
                            <a href="users.php" 
                               class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-900 font-semibold py-3 px-6 rounded-xl transition-all shadow-sm">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>