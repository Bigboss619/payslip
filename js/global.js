// ✅ SIDEBAR ACTIVE LOGIC - Works on ALL pages
document.addEventListener('DOMContentLoaded', function() {
    // Ensure showToast is loaded
    if (typeof showToast === 'undefined') {
        console.warn('Toast.js not loaded. Check script include.');
    } else {
        console.log('✅ Toast system ready');
    }
    highlightActivePage();
    
    // Re-highlight on navigation (if using SPA)
    const links = document.querySelectorAll('.sidebar-link');
    links.forEach(link => {
        link.addEventListener('click', function() {
            setTimeout(highlightActivePage, 100);
        });
    });
});

function highlightActivePage() {
    const currentPath = window.location.pathname.split('/').pop(); // Gets "dashboard.php", "payslip.php", etc.
    
    document.querySelectorAll('.sidebar-link').forEach(link => {
        // Remove active class
        link.classList.remove('bg-blue-100', 'text-blue-600', 'font-medium');
        link.classList.add('hover:bg-gray-100');
        
        // Get href from link
        const linkPath = link.getAttribute('href')?.split('/').pop() || '';
        
        // Match current page
        if (linkPath === currentPath || linkPath.includes(currentPath)) {
            link.classList.add('bg-blue-100', 'text-blue-600', 'font-medium');
            link.classList.remove('hover:bg-gray-100');
            link.classList.add('hover:bg-blue-50');
        }
    });
}