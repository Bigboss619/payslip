<?php 

include_once("../includes/header.php"); ?>


  <!-- Nav Section -->
   <?php include_once("../includes/nav.php"); ?>

<style>
.profile-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.profile-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 16px 30px -18px rgba(15, 23, 42, 0.35);
  border-color: #dbeafe;
}

.profile-btn {
  transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.profile-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 18px -12px rgba(30, 64, 175, 0.45);
}

.profile-input {
  transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
}

.profile-avatar {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.profile-avatar:hover {
  transform: scale(1.03);
  box-shadow: 0 18px 30px -20px rgba(30, 64, 175, 0.5);
}
</style>

      <!-- MAIN -->
      <div class="profile-page flex-1 flex flex-col">
                <!-- TOPBAR -->
        <header class="border-b border-gray-100 bg-white px-6 py-4 shadow-sm flex items-center lg:justify-between">

          <!-- Mobile toggle + title -->
          <div class="flex items-center lg:hidden">
            <button class="mr-4 p-2 rounded-lg hover:bg-gray-100 sidebar-toggle">
              <svg class="w-5 h-5 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>
            <h1 class="text-lg font-semibold">
              Profile
            </h1>
          </div>
          
          <h1 class="text-lg font-semibold hidden lg:block">
            Profile
          </h1>

          <div class="flex items-center space-x-3">
            <!-- <span class="text-sm text-gray-600">Welcome, Emmanuel</span>
            <div class="w-8 h-8 bg-blue-500 text-white flex items-center justify-center rounded-full">
              E
            </div> -->
          </div>

        </header>

        <!-- CONTENT -->
        <main class="overflow-y-auto bg-slate-50 p-4 md:p-6">

          <!-- TITLE -->
          <div class="mb-6 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 profile-card">
            <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
            <p class="text-sm text-gray-600">Manage your account, identity and security details.</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- PROFILE CARD -->
            <div class="profile-card rounded-2xl border border-gray-100 bg-white p-6 text-center shadow-sm">

              <?php $photo = $_SESSION['photo'] ?? ''; ?>
              <?php if ($photo && file_exists('../uploads/dp/' . $photo)): ?>
                <div class="profile-avatar mx-auto h-28 w-28 overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 p-1">
                  <img src="../uploads/dp/<?= htmlspecialchars($photo) ?>" alt="Profile Picture" class="h-full w-full rounded-xl object-cover">
                  <!-- Hover camera icon overlay -->
                  <!-- <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-transparent rounded-3xl opacity-0 group-hover:opacity-100 transition-all duration-400 flex items-center justify-center backdrop-blur-md">
                    <svg class="w-12 h-12 text-white/95 drop-shadow-2xl animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                      <circle cx="12" cy="13" r="3"></circle>
                    </svg>
                  </div> -->
                </div>
              <?php else: ?>
                <div class="profile-avatar mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-3xl font-bold text-white shadow-sm">
                  <?= strtoupper(substr($_SESSION['name'], 0, 1)) ?>
                </div>
              <?php endif; ?>

              <h2 class="text-lg font-semibold text-gray-900"><?php echo $_SESSION['name']; ?></h2>
              <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($_SESSION['department_name'] ?? ''); ?></p>


              <!-- <button class="mt-4 bg-gray-200 px-4 py-2 rounded-lg text-sm">
                Change Avatar
              </button> -->

              <form id="profile-pic-form" method="post" action="../includes/hrprofile.php" enctype="multipart/form-data" class="mt-6 space-y-2">
                <input type="hidden" name="update_profile_picture" value="1">
                <label for="avatar" class="profile-btn inline-flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-3 font-medium text-white hover:bg-blue-700">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <circle cx="12" cy="13" r="3"></circle>
                  </svg>
                  Change Profile Picture
                  <input type="file" name="photo" id="avatar" accept="image/*" class="hidden">
                </label>
                <p id="file-preview" class="text-sm text-gray-600 hidden ml-1 mt-1">No file selected</p>
                <button type="submit" id="upload-btn" class="profile-btn ml-1 hidden rounded-xl bg-emerald-600 px-6 py-2 font-medium text-white hover:bg-emerald-700">Save Photo</button>
              </form>

            </div>

            <!-- DETAILS -->
            <div class="lg:col-span-2 space-y-6">

              <!-- PERSONAL INFO -->
              <form method="post" action="../includes/hrprofile.php" class="profile-card rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-lg font-semibold text-gray-900">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($_SESSION['name']); ?>" required class="profile-input mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" required class="profile-input mt-1 w-full rounded-xl border border-gray-300 px-4 py-2.5">
                  </div>

                </div>

                <input type="hidden" name="update_profile" value="1">
                <button type="submit" class="profile-btn mt-4 rounded-xl bg-blue-600 px-6 py-2.5 text-white hover:bg-blue-700">
                  Save Changes
                </button>

              </form>

              <!-- WORK INFO -->
              <div class="profile-card rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-lg font-semibold text-gray-900">Work Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Staff ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['staff_id']); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Pension ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['pension_id']); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Tax ID</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['tax_id']); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Account Number</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['account_number'] ?? ''); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">

                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Bank</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['bank_name']); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Department</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['department_name'] ?? ''); ?>" disabled class="profile-input mt-1 w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5">
                  </div>


                </div>

              </div>

              <!-- CHANGE PASSWORD -->
              <div class="profile-card rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">

                <h2 class="mb-4 text-lg font-semibold text-gray-900">Change Password</h2>

                <form method="post" action="../includes/hrprofile.php" class="space-y-4">
                  <input type="hidden" name="change_password" value="1">
                  <div class="mb-4">
                    <label class="text-sm font-medium">Current Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="current_password" id="currentPw" required class="profile-input w-full rounded-xl border border-gray-300 px-4 py-2.5 pr-12 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('currentPw')" class="profile-btn absolute right-3 top-2.5 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                    <p id="currentPwError" class="mt-1 text-sm text-red-600 hidden">Current password required.</p>
                  </div>

                  <div class="mb-4">
                    <label class="text-sm font-medium">New Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="new_password" id="newPw" required minlength="6" class="profile-input w-full rounded-xl border border-gray-300 px-4 py-2.5 pr-12 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('newPw')" class="profile-btn absolute right-3 top-2.5 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                  </div>

                  <div class="mb-4">
                    <label class="text-sm font-medium">Confirm New Password</label>
                    <div class="relative mt-1">
                      <input type="password" name="confirm_password" id="confirmPw" required minlength="6" class="profile-input w-full rounded-xl border border-gray-300 px-4 py-2.5 pr-12 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500 invalid:border-red-500 invalid:focus:ring-red-500">
                      <button type="button" onclick="togglePassword('confirmPw')" class="profile-btn absolute right-3 top-2.5 h-6 w-6 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                      </button>
                    </div>
                    <p id="confirmPwError" class="mt-1 text-sm text-red-600 hidden">Passwords do not match.</p>
                  </div>

                  <button type="submit" class="profile-btn mt-4 inline-flex items-center justify-center rounded-xl bg-green-600 px-6 py-2.5 font-semibold text-white shadow-sm hover:bg-green-700">
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
