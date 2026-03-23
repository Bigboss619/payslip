// HR Profile AJAX handler (uses Toast.js)
document.addEventListener('DOMContentLoaded', () => {
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
          // Reload page to show updates
          setTimeout(() => location.reload(), 1500);
        }
      } catch (error) {
        showToast('Network error. Try again.', 'error');
        console.error('Profile error:', error);
      }
    });
  });
});

