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

          <!-- TITLE -->
          <div class="mb-6">
            <h1 class="text-2xl font-bold">Profile</h1>
            <p class="text-gray-500 text-sm">Manage your account information</p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- PROFILE CARD -->
            <div class="bg-white p-6 rounded-xl shadow text-center">

              <div class="w-24 h-24 mx-auto bg-blue-500 text-white flex items-center justify-center rounded-full text-3xl font-bold mb-4">
                E
              </div>

              <h2 class="text-lg font-semibold">Emmanuel Ugochukwu</h2>
              <p class="text-gray-500 text-sm">IT Department</p>

              <button class="mt-4 bg-gray-200 px-4 py-2 rounded-lg text-sm">
                Change Avatar
              </button>

            </div>

            <!-- DETAILS -->
            <div class="lg:col-span-2 space-y-6">

              <!-- PERSONAL INFO -->
              <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-lg font-semibold mb-4">Personal Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Full Name</label>
                    <input type="text" value="Emmanuel Ugochukwu" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <input type="email" value="emmanuel@email.com" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Phone</label>
                    <input type="text" value="08012345678" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                </div>

                <button class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg">
                  Save Changes
                </button>

              </div>

              <!-- WORK INFO -->
              <div class="bg-white p-6 rounded-xl shadow">

                <h2 class="text-lg font-semibold mb-4">Work Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                  <div>
                    <label class="text-sm text-gray-500">Staff ID</label>
                    <input type="text" value="EMP001" disabled class="w-full mt-1 border px-4 py-2 rounded-lg bg-gray-100">
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

                <div class="space-y-4">

                  <div>
                    <label class="text-sm text-gray-500">Current Password</label>
                    <input type="password" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">New Password</label>
                    <input type="password" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                  <div>
                    <label class="text-sm text-gray-500">Confirm New Password</label>
                    <input type="password" class="w-full mt-1 border px-4 py-2 rounded-lg">
                  </div>

                </div>

                <button class="mt-4 bg-green-600 text-white px-6 py-2 rounded-lg">
                  Update Password
                </button>

              </div>

            </div>

          </div>

        </main>
      </div>

    </div>
    
</body>
</html>