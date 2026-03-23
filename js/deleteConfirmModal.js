// Delete Confirmation Modal JS
let currentDeleteId = null;

function openDeleteModal(id, name) {
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
  closeDeleteModal();
  try {
    const formData = `action=delete&id=${currentDeleteId}`;
    const res = await fetch('../includes/departmentSub.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData
    });
    const data = await res.json();
    
    if (data.success) {
      showMessage(data.message, 'success');
      window.loadDepartments();
    } else {
      showMessage(data.message, 'error');
    }
  } catch (err) {
    showMessage('Delete failed', 'error');
  }
}

// Expose for onclick
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.confirmDelete = confirmDelete;

