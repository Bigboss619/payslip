// Delete Confirmation Modal JS
let currentDeleteId = null;

function openDeleteModal(id, name) {
  console.log("DELETE CLICKED:", id, name);
  currentDeleteId = id;
  document.getElementById('delete-confirm-name').textContent = escapeHtml(name);
  document.getElementById('delete-confirm-modal').classList.remove('hidden');
}

function closeDeleteModal() {
  document.getElementById('delete-confirm-modal').classList.add('hidden');
  currentDeleteId = null;
}

document.getElementById('delete-confirm-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeDeleteModal();
});

async function confirmDelete() {
  if (!currentDeleteId) return;
  // const idToDelete = currentDeleteId;
  
  
  const formData = `action=delete&id=${currentDeleteId}`;
  console.log('Delete request data:', formData); // Debug log
  
  try {
    const res = await fetch('../includes/departmentSub.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData
    });
    const data = await res.json();
    console.log('Delete response:', data); // Debug log
    closeDeleteModal();
    if (data.success) {
      showMessage(data.message, 'success');
      window.loadDepartments();
    } else {
      showMessage(data.message, 'error');
    }
  } catch (err) {
    console.error('Delete error:', err);
    showMessage('Delete failed', 'error');
  }
}

// Expose for onclick - added for cross-script access
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;
window.loadDepartments = loadDepartments;
window.showMessage = showMessage;

// Ensure loadDepartments is properly exposed from department.js
if (typeof window.loadDepartments !== 'function') {
  window.loadDepartments = () => {
    if (typeof loadDepartments === 'function') {
      loadDepartments();
    }
  };
}

