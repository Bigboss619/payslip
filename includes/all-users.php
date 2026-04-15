<?php
// Get database connection
require_once '../config/config.php';

// Handle search, filter, pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build WHERE conditions
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($role_filter) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
}

if ($status_filter && $status_filter !== 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

$where_sql = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// 1. COUNT - All positional params
$count_sql = "SELECT COUNT(*) as total FROM users $where_sql";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->execute($params);
$total_users = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_users / $limit);

// 2. DATA - SIMPLIFIED with direct LIMIT/OFFSET (NO binding issues)
$data_sql = "SELECT * FROM users $where_sql ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$data_stmt = $conn->prepare($data_sql);
$data_stmt->execute($params);
$users = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

// Export variables
$GLOBALS['users'] = $users;
$GLOBALS['total_users'] = $total_users;
$GLOBALS['total_pages'] = $total_pages;
$GLOBALS['page'] = $page;
$GLOBALS['search'] = $search;
$GLOBALS['role_filter'] = $role_filter;
$GLOBALS['status_filter'] = $status_filter;
?>