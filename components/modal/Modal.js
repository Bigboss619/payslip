// components/modal/Modal.js
function showModal(title, message, type = 'info') {
  // Remove existing modal if any
  const existing = document.getElementById('custom-modal');
  if (existing) existing.remove();

  const modal = document.createElement('div');
  modal.id = 'custom-modal';
  modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
  
  const colors = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    info: 'bg-blue-500'
  };
  
  modal.innerHTML = `
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="w-10 h-10 rounded-full ${colors[type]} flex items-center justify-center mr-4 flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' : 
                type === 'error' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' : 
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'
              }
            </svg>
          </div>
          <h3 class="text-xl font-bold text-gray-900">${title}</h3>
        </div>
        <p class="text-gray-700 mb-6">${message}</p>
        <div class="flex justify-end space-x-3">
          <button onclick="this.closest('#custom-modal').remove()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Close</button>
        </div>
      </div>
    </div>
  `;
  
  document.body.appendChild(modal);
  
  // Focus close button
  const closeBtn = modal.querySelector('button');
  closeBtn.focus();
  
  // ESC key close
  const escHandler = (e) => {
    if (e.key === 'Escape') {
      modal.remove();
      document.removeEventListener('keydown', escHandler);
    }
  };
  document.addEventListener('keydown', escHandler);
}

function closeModal() {
  const modal = document.getElementById('custom-modal');
  if (modal) modal.remove();
}

// Global exports
window.showModal = showModal;
window.closeModal = closeModal;
