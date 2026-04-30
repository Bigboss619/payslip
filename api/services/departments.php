<?php
require_once('../config/config.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT name FROM departments ORDER BY name");
    $stmt->execute();
    $departments = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode($departments);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Failed to fetch departments']);
}
?>
