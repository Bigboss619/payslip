<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}
include_once("../config/config.php");

$response = ['success' => false, 'message' => '', 'data' => []];

if ($_POST['action'] ?? '') {
  try {
    switch ($_POST['action']) {
      case 'add':
        $name = trim($_POST['name']);
        if (empty($name)) throw new Exception('Department name required');
        $stmt = $conn->prepare("INSERT INTO departments (name) VALUES (?)");
        $stmt->execute([$name]);
        $response['success'] = true;
        $response['message'] = 'Department added successfully';
        break;
      
      case 'edit':
        if (!isset($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] <= 0) {
          throw new Exception('Invalid data');
        }
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        if (empty($name)) throw new Exception('Invalid data');
        $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        $response['success'] = true;
        $response['message'] = 'Department updated successfully';
        break;
      
      case 'delete':
        if (!isset($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] <= 0) {
          throw new Exception('Failed to delete department');
        }
        $id = (int)$_POST['id'];
        
        // Check if department exists
        $check = $conn->prepare("SELECT COUNT(*) FROM departments WHERE id = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() == 0) {
          throw new Exception('Failed to delete department');
        }
        
        $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $response['success'] = true;
        $response['message'] = 'Department deleted successfully';
        break;
      
      case 'list':
        $stmt = $conn->query("SELECT id, name FROM departments ORDER BY id ASC");
        $response['data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response['success'] = true;
        break;
    }
  } catch (Exception $e) {
    $response['message'] = $e->getMessage();
  }
  header('Content-Type: application/json');
  echo json_encode($response);
  exit;
}
