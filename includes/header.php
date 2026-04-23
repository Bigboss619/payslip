<?php
ob_start();
session_start();
// Shared access: HR full, Users limited dashboard access
if (!isset($_SESSION['role'])) {
  header('Location: ../index.php');
  exit;
}
// Role logged for debugging
error_log("Header access: {$_SESSION['role']}");

include_once("../config/config.php"); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  
  <!-- Company Logo Favicon -->
  <link rel="icon" type="image/png" href="../uploads/company-logo/nepal-logo.png">
  
  <link rel="stylesheet" href="../src/output.css">
  <style>
    .page-transition-prep .page-reveal-item {
      opacity: 0;
      transform: translateY(12px);
    }

    .page-transition-ready .page-reveal-item {
      opacity: 1;
      transform: translateY(0);
      transition: opacity 560ms ease, transform 560ms ease;
      transition-delay: var(--reveal-delay, 0ms);
    }

    @media (prefers-reduced-motion: reduce) {
      .page-transition-prep .page-reveal-item,
      .page-transition-ready .page-reveal-item {
        opacity: 1;
        transform: none;
        transition: none;
      }
    }
  </style>
</head>

<body class="bg-gray-100 page-transition-prep">
    <div class="flex h-screen overflow-hidden">

<!-- 🔥 TOAST SCRIPT - Global Availability -->
<script defer src="../components/Toast/Toast.js"></script>

<!-- ✅ GLOBAL SIDEBAR SCRIPT (Add before closing body) -->
<script>
// Enhanced Sidebar - Active for users/* pages
document.addEventListener('DOMContentLoaded', function() {
    const revealRoots = document.querySelectorAll('main, .flex-1');
    revealRoots.forEach(root => {
        const revealItems = Array.from(root.children).filter(el => {
            return !['SCRIPT', 'STYLE'].includes(el.tagName);
        });

        revealItems.forEach((el, index) => {
            el.classList.add('page-reveal-item');
            el.style.setProperty('--reveal-delay', `${Math.min(index * 95, 700)}ms`);
        });
    });

    requestAnimationFrame(() => {
        document.body.classList.remove('page-transition-prep');
        document.body.classList.add('page-transition-ready');
    });

    const sidebar = document.getElementById('sidebar');
    const collapseToggle = document.querySelector('.sidebar-collapse-toggle');
    const sidebarLabels = document.querySelectorAll('.sidebar-label, .sidebar-brand-detail');
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    const collapseKey = 'payslipsys-sidebar-collapsed-v2';
    const desktopQuery = window.matchMedia('(min-width: 768px)');

    function syncCollapseToggleVisibility() {
        if (!collapseToggle) return;
        collapseToggle.style.display = desktopQuery.matches ? 'inline-flex' : 'none';
    }

    function setSidebarCollapsed(collapsed) {
        if (!sidebar) return;

        sidebar.classList.toggle('md:w-20', collapsed);
        sidebar.classList.toggle('md:w-80', !collapsed);

        sidebarLabels.forEach(label => {
            label.classList.toggle('hidden', collapsed);
        });

        sidebarLinks.forEach(link => {
            link.classList.toggle('justify-center', collapsed);
            link.classList.toggle('px-3', collapsed);
            link.classList.toggle('px-4', !collapsed);
        });

        document.querySelectorAll('.sidebar-collapse-left').forEach(icon => {
            icon.classList.toggle('hidden', collapsed);
        });
        document.querySelectorAll('.sidebar-collapse-right').forEach(icon => {
            icon.classList.toggle('hidden', !collapsed);
        });
    }

    if (collapseToggle) {
        collapseToggle.addEventListener('click', function() {
            const nextCollapsed = !sidebar.classList.contains('md:w-20');
            setSidebarCollapsed(nextCollapsed);
            localStorage.setItem(collapseKey, nextCollapsed ? '1' : '0');
        });
    }

    const savedCollapsed = localStorage.getItem(collapseKey) === '1';
    if (savedCollapsed) {
        setSidebarCollapsed(true);
    }
    syncCollapseToggleVisibility();
    desktopQuery.addEventListener('change', syncCollapseToggleVisibility);

    const currentPath = window.location.pathname.split('/').pop();
    const currentDir = window.location.pathname.split('/').slice(-2).join('/');
    
    document.querySelectorAll('.sidebar-link').forEach(link => {
        // Reset all
        link.classList.remove('bg-blue-100', 'text-blue-600', 'font-medium');
        link.classList.add('hover:bg-gray-100');
        
        const linkHref = link.getAttribute('href') || '';
        const linkPath = linkHref.split('/').pop();
        const linkDir = linkHref.split('/').slice(-2).join('/');
        
        // Exact match OR parent section match
        let isActive = false;
        
        // 1. Exact filename match
        if (linkPath === currentPath) {
            isActive = true;
        }
        // 2. Users section: users.php OR edit-user.php
        else if (linkPath === 'users' && (currentPath === 'users.php' || currentPath === 'edit-user.php' || currentDir.includes('users'))) {
            isActive = true;
        }
        // 3. Add more sections as needed
        else if (linkPath === 'payslip' && currentDir.includes('payslip-view')) {
            isActive = true;
        }
        
        if (isActive) {
            link.classList.remove('hover:bg-gray-100');
            link.classList.add('bg-blue-100', 'text-blue-600', 'font-medium');
            link.style.backgroundColor = '#dbeafe';
            link.style.color = '#2563eb';
        }
    });
});
</script>

<!-- Your existing content continues here... -->