// Department Management JS
document.addEventListener('DOMContentLoaded', function() {
  loadDepartments();

  document.addEventListener('click', function(e) {
  if (e.target.classList.contains('delete-btn')) {
    const id = e.target.dataset.id;
    const name = e.target.dataset.name;

    openDeleteModal(id, name);
  }
});

document.addEventListener('click', function(e) {
  if (e.target.classList.contains('edit-btn')) {
    const id = parseInt(e.target.dataset.id);
    const name = e.target.dataset.name;

    editDepartment(id, name);
  }
});
  document.getElementById('department-form').addEventListener('submit', handleSave);
  
// Expose functions for onclick including reload
  window.openAddModal = openAddModal;
  window.closeModal = closeModal;
  window.editDepartment = editDepartment;
  window.loadDepartments = loadDepartments;
});

async function loadDepartments() {
  try {
    const res = await fetch('../api/services/departmentSub.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'action=list'
    });
    const data = await res.json();
    
    if (data.success) {
      renderTable(data.data);
    } else {
      showMessage(data.message, 'error');
    }
  } catch (err) {
    showMessage('Failed to load departments', 'error');
  }
}

function renderTable(depts) {
  const tbody = document.getElementById('departments-table');
  const tableCount = document.getElementById('table-count');
  const emptyState = document.getElementById('empty-state');
  
  if (depts.length === 0) {
    tbody.innerHTML = '';
    emptyState.classList.remove('hidden');
    tableCount.textContent = 'No departments found';
    return;
  }
  
  emptyState.classList.add('hidden');
  tableCount.textContent = `${depts.length} department${depts.length !== 1 ? 's' : ''}`;
  
  tbody.innerHTML = depts.map(dept => {
    // console.log('Rendering dept:', dept.id, dept.name); // Debug dept data
    return `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${dept.id}</td>
      <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">${escapeHtml(dept.name)}</div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        ${new Date().toLocaleDateString()}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
        <button data-id="${dept.id}" data-name="${dept.name}" 
                class="text-blue-600 edit-btn hover:text-blue-900 px-3 py-1 rounded-lg hover:bg-blue-50 transition">
          Edit
        </button>
        <button  data-id="${dept.id}" data-name="${dept.name}"
                class="text-red-600 hover:text-red-900 px-3 py-1 delete-btn rounded-lg hover:bg-red-50 transition">
          Delete
        </button>
      </td>
    </tr>
    `;
  }).join('');
}

async function handleSave(e) {
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  const name = document.getElementById('dept-name').value.trim();
  
  if (!name) return showMessage('Name required', 'error');
  
  const action = id ? 'edit' : 'add';
  const formData = `action=${action}${id ? `&id=${id}` : ''}&name=${encodeURIComponent(name)}`;
  
  try {
    const res = await fetch('../api/services/departmentSub.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData
    });
    const data = await res.json();
    
    if (data.success) {
      showMessage(data.message, 'success');
      closeModal();
      loadDepartments();
      document.getElementById('department-form').reset();
      document.getElementById('edit-id').value = '';
    } else {
      showMessage(data.message, 'error');
    }
  } catch (err) {
    showMessage('Operation failed', 'error');
  }
}

function editDepartment(id, name) {
  document.getElementById('edit-id').value = id;
  document.getElementById('dept-name').value = name;
  document.getElementById('modal-title').textContent = 'Edit Department';
  document.getElementById('department-modal').classList.remove('hidden');
  document.getElementById('dept-name').focus();
}

function openAddModal() {
  document.getElementById('department-modal').classList.remove('hidden');
  document.getElementById('modal-title').textContent = 'Add Department';
  document.getElementById('department-form').reset();
  document.getElementById('edit-id').value = '';
  document.getElementById('dept-name').focus();
}

function closeModal() {
  document.getElementById('department-modal').classList.add('hidden');
  document.getElementById('department-form').reset();
  document.getElementById('edit-id').value = '';
}

// Close modal on outside click
document.getElementById('department-modal').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

function showMessage(msg, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-2xl text-white transform translate-x-full transition-all duration-300 max-w-sm ${
    type === 'success' ? 'bg-green-500' : 'bg-red-500'
  }`;
  toast.innerHTML = `<div>${msg}</div>`;
  document.body.appendChild(toast);

  // Slide in
  requestAnimationFrame(() => toast.classList.remove('translate-x-full'));
  
  // Slide out after 3s
  setTimeout(() => {
    toast.classList.add('translate-x-full');
    setTimeout(() => toast.remove(), 300);
  }, 3000);
}

function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '<',
    '>': '>',
    '"': '"',
    "'": '&#039;'
  };
  return text.replace(/[&<>\"']/g, m => map[m]);
}

