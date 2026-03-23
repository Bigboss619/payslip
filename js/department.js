// Department Management JS
document.addEventListener('DOMContentLoaded', function() {
  loadDepartments();
  
  document.getElementById('department-form').addEventListener('submit', handleSave);
});

async function loadDepartments() {
  try {
    const res = await fetch('', {
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
  
  tbody.innerHTML = depts.map(dept => `
    <tr class="hover:bg-gray-50">
      <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${dept.id}</td>
      <td class="px-6 py-4 whitespace-nowrap">
        <div class="text-sm font-medium text-gray-900">${escapeHtml(dept.name)}</div>
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
        ${new Date().toLocaleDateString()}
      </td>
      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
        <button onclick="editDepartment(${dept.id}, '${escapeHtml(dept.name)}')" 
                class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-lg hover:bg-blue-50 transition">
          Edit
        </button>
        <button onclick="deleteDepartment(${dept.id})" 
                class="text-red-600 hover:text-red-900 px-3 py-1 rounded-lg hover:bg-red-50 transition">
          Delete
        </button>
      </td>
    </tr>
  `).join('');
}

async function handleSave(e) {
  e.preventDefault();
  const id = document.getElementById('edit-id').value;
  const name = document.getElementById('dept-name').value.trim();
  
  if (!name) return showMessage('Name required', 'error');
  
  const action = id ? 'edit' : 'add';
  const formData = `action=${action}${id ? `&id=${id}` : ''}&name=${encodeURIComponent(name)}`;
  
  try {
    const res = await fetch('', {
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

async function deleteDepartment(id) {
  if (!confirm('Delete this department? This cannot be undone.')) return;
  
  try {
    const formData = `action=delete&id=${id}`;
    const res = await fetch('', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData
    });
    const data = await res.json();
    
    if (data.success) {
      showMessage(data.message, 'success');
      loadDepartments();
    } else {
      showMessage(data.message, 'error');
    }
  } catch (err) {
    showMessage('Delete failed', 'error');
  }
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

function showMessage(msg, type = 'info') {
  // Simple toast using native alert for now; can enhance with Toast component later
  const bg = type === 'success' ? 'bg-green-500' : 'bg-red-500';
  console.log(`%c${msg}`, `background: ${bg}; color: white; padding: 8px 12px; border-radius: 4px;`);
  // TODO: Implement proper toast
}

function escapeHtml(text) {
  const map = { '&': '&amp;', '<': '<', '>': '>', '"': '"', \"'\": '&#039;' };
  return text.replace(/[&<>\"']/g, m => map[m]);
}
