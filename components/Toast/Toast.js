// components/Toast/Toast.js
function showToast(message, type = 'success') {
  // Remove existing toast
  const existing = document.getElementById('custom-toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.id = 'custom-toast';
  toast.className = 'fixed top-4 right-4 z-50 max-w-sm w-full transform translate-x-full transition-transform duration-300';
  
  const colors = {
    success: 'bg-green-500 border-green-400 text-green-100',
    error: 'bg-red-500 border-red-400 text-red-100'
  };
  
  toast.innerHTML = `
    <div class="bg-white shadow-2xl rounded-xl border-2 ${colors[type]} p-4 flex items-start space-x-3">
      <div class="flex-shrink-0">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          ${type === 'success' ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' : 
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>'
          }
        </svg>
      </div>
      <div>
        <p class="font-medium">${type === 'success' ? 'Success' : 'Error'}</p>
        <p>${message}</p>
      </div>
    </div>
  `;
  
  document.body.appendChild(toast);
  
  // Slide in
  requestAnimationFrame(() => {
    toast.classList.remove('translate-x-full');
  });
  
  // Auto hide after 4 seconds
  setTimeout(() => {
    toast.classList.add('translate-x-full');
    setTimeout(() => {
      if (toast.parentNode) toast.remove();
    }, 300);
  }, 4000);
}

window.showToast = showToast;
