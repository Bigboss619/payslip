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

<!-- ✅ GLOBAL SIDEBAR SCRIPT (Add before closing body) -->
<script>
// Sidebar active state - Works on ALL pages
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname.split('/').pop();
    
    document.querySelectorAll('.sidebar-link').forEach(link => {
        // Reset all
        link.classList.remove('bg-blue-100', 'text-blue-600', 'font-medium');
        link.classList.add('hover:bg-gray-100');
        
        // Get link path
        const linkPath = link.getAttribute('href')?.split('/').pop() || '';
        
        // Active match
        if (linkPath === currentPath) {
            link.classList.remove('hover:bg-gray-100');
            link.classList.add('bg-blue-100', 'text-blue-600', 'font-medium');
            link.style.backgroundColor = '#dbeafe'; // Tailwind blue-100
            link.style.color = '#2563eb'; // Tailwind blue-600
        }
    });
});
</script>

<!-- Your existing content continues here... -->