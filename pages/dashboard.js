// pages/dashboard.js - Collapsible Sidebar
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggleBtns = document.querySelectorAll('.sidebar-toggle');
  
  let isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
  
  // Set initial width
  if (isCollapsed) {
    sidebar.classList.add('w-16');
    sidebar.classList.remove('w-64');
  } else {
    sidebar.classList.add('w-64');
    sidebar.classList.remove('w-16');
  }
  
  // Toggle function
  function toggleSidebar() {
    isCollapsed = !isCollapsed;
    localStorage.setItem('sidebarCollapsed', isCollapsed);
    
    if (isCollapsed) {
      sidebar.classList.remove('w-64');
      sidebar.classList.add('w-16');
      
      // Hide text, center icons
      const spans = sidebar.querySelectorAll('nav span');
      spans.forEach(span => span.style.display = 'none');
      const links = sidebar.querySelectorAll('.sidebar-link');
      links.forEach(link => {
        link.style.justifyContent = 'center';
        link.style.padding = '0.75rem 1rem';
      });
    } else {
      sidebar.classList.remove('w-16');
      sidebar.classList.add('w-64');
      
      // Show text
      const spans = sidebar.querySelectorAll('nav span');
      spans.forEach(span => span.style.display = 'inline');
      const links = sidebar.querySelectorAll('.sidebar-link');
      links.forEach(link => {
        link.style.justifyContent = 'flex-start';
        link.style.padding = '0.75rem 1rem';
      });
    }
  }
  
  // Add event listeners to all toggle buttons
  toggleBtns.forEach(btn => {
    btn.addEventListener('click', toggleSidebar);
  });
});
