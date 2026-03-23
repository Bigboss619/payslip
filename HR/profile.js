// HR Profile AJAX handler (uses Toast.js)
document.addEventListener('DOMContentLoaded', () => {
  // Password toggle (same as login)
  window.togglePassword = function(inputId) {
    const input = document.getElementById(inputId);
    const button = input.nextElementSibling;
    const svg = button.querySelector('svg path');
    
    if (input.type === 'password') {
      input.type = 'text';
      svg.setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L19 19');
    } else {
      input.type = 'password';
      svg.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
    }
  };

  // Forms AJAX
  const forms = document.querySelectorAll('form[action*="/hrprofile.php"]');
  forms.forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const formData = new FormData(form);
      
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: formData
        });
        
        const data = await response.json();
        
        showToast(data.message, data.success ? 'success' : 'error');
        
        if (data.success) {
          setTimeout(() => location.reload(), 1500);
        }
      } catch (error) {
        showToast('Network error. Try again.', 'error');
        console.error('Profile error:', error);
      }
    });
  });
});


