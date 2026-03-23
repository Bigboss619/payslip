<?php include_once("../includes/header.php"); ?>

<?php include_once("../includes/nav.php"); ?>

    <!-- MAIN -->
    <main class="flex-1 p-6 overflow-y-auto">
      <div class="max-w-4xl mx-auto">
        
        <!-- TITLE -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Departments</h1>
          <p class="text-gray-500 mt-2">Manage your organization departments</p>
        </div>

        <!-- ADD BUTTON -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
          <button onclick="openAddModal()" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Department
          </button>
        </div>

        <!-- DEPARTMENTS TABLE -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Department List</h2>
            <p class="text-sm text-gray-500 mt-1" id="table-count">Loading...</p>
          </div>
          
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department Name</th>
                  <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                  <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody id="departments-table" class="divide-y divide-gray-100">
                <!-- Dynamic rows -->
              </tbody>
            </table>
          </div>
          
          <!-- EMPTY STATE -->
          <div id="empty-state" class="text-center py-12 hidden">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No departments</h3>
            <p class="text-gray-500 mb-4">Get started by creating your first department.</p>
            <button onclick="openAddModal()" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-medium hover:bg-blue-700 transition">
              Create Department
            </button>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- ADD/EDIT MODAL -->
  <div id="department-modal" class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6 border-b border-gray-100">
        <h2 id="modal-title" class="text-xl font-bold text-gray-900">Add Department</h2>
      </div>
      <form id="department-form" class="p-6">
        <input type="hidden" id="edit-id">
        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Department Name</label>
          <input type="text" id="dept-name" required maxlength="100" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
          <button type="button" onclick="closeModal()" class="px-6 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition">Cancel</button>
          <button type="submit" class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition">Save Department</button>
        </div>
      </form>
    </div>
  </div>

  <!-- DELETE CONFIRM MODAL -->
  <div id="delete-confirm-modal" class="fixed inset-0 bg-gray-900/30 backdrop-blur-sm hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-full">
      <div class="p-8 text-center">
        <div class="w-16 h-16 mx-auto bg-red-100 rounded-2xl flex items-center justify-center mb-6">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
          </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Delete Department?</h3>
        <p class="text-gray-500 mb-6 max-w-sm mx-auto">This action cannot be undone. This will permanently delete the <strong id="delete-confirm-name" class="font-semibold text-gray-900"></strong> department and remove it from the system.</p>
        <div class="flex gap-3 justify-center">
          <button onclick="closeDeleteModal()" class="flex-1 px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition max-w-sm">Cancel</button>

          <button onclick="confirmDelete()" name="delete" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition max-w-sm">Delete</button>
        </div>
      </div>
    </div>
  </div>

  <script src="../js/department.js"></script>
  <script src="../js/deleteConfirmModal.js"></script>
</body>
</html>

