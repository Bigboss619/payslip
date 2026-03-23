<?php 

include_once("../includes/header.php"); ?>


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

          <!-- TITLE -->
          <div class="mb-6">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Manage your account information</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- PROFILE CARD -->
            <div class="bg-white p-6 rounded-xl shadow text-center">

              <div class="w-24 h-24 mx-auto bg-blue-500 text-white flex items-center justify-center rounded-full text-3xl font-bold mb-4">
                <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
              </div>

              <h2 class="text-lg font-semibold"><?php echo $_SESSION['name']; ?></h2>
              <p class="text-gray-500 text-sm">HR Department</p>

              <button class="mt-4 bg-gray-200 px-4 py-2 rounded-lg text-sm">
                Change Avatar
              </button>

            </div>

            <!-- DETAILS -->
            <div class="lg:col-span-2 space-y-6">

              <!-- PERSONAL INFO -->
              <form method="post" action="../includes/hrprofile.php" class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-lg font-semibold mb-4">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                </div>

                <input type="hidden" name="update_profile" value="1">
                <button type="submit" class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg">
                  Save Changes
                </button>

              </form>

              <!-- WORK INFO -->
              <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-lg font-semibold mb-4">Work Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Staff ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['staff_id']); ?>" disabled class="w-full mt-1 border px-4 py-2 rounded-lg bg-gray-100">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Department</label>
                    <input type="text" value="IT" disabled class="w-full mt-1 border px-4 py-2 rounded-lg bg-gray-100">
                  </div>

                </div>

              </div>

              <!-- CHANGE PASSWORD -->
              <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-lg font-semibold mb-4">Change Password</h2>

                <form method="post" action="../includes/hrprofile.php" class="space-y-4">
                  <input type="hidden" name="change_password" value="1">
                  <div class="mb-4">
                    <label class="text-sm font-medium">Current Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="current_password" id="currentPw" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('currentPw')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                    <p id="currentPwError" class="mt-1 text-sm text-red-600 hidden">Current password required.</p>
                  </div>

                  <div class="mb-4">
                    <label class="text-sm font-medium">New Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="new_password" id="newPw" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('newPw')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="text-sm font-medium">Confirm New Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="confirm_password" id="confirmPw" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('confirmPw')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                    <p id="confirmPwError" class="mt-1 text-sm text-red-600 hidden">Passwords do not match.</p>
                  </div>

                  <button type="submit" class="mt-4 bg-green-600 text-white px-6 py-2 rounded-lg">
                    Update Password
                  </button>
                </form>

              </div>

            </div>

          </div>

        </main>
      </div>

    </div>
    
<script src="../components/Toast/Toast.js"></script>
<script src="profile.js"></script>
</body>
</html>
