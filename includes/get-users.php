<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require '../config/config.php';

$stmt = $conn->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

echo json_encode([
    'success' => true,
    'total_employees' => $totalUsers
]);
?>

