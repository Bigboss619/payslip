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
</head>

<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">

<!-- 🔥 TOAST SCRIPT - Global Availability -->
<script defer src="../components/Toast/Toast.js"></script>

<!-- ✅ GLOBAL SIDEBAR SCRIPT (Add before closing body) -->
<script>
// Enhanced Sidebar - Active for users/* pages
document.addEventListener('DOMContentLoaded', function() {
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