<?php

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
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);
        if (empty($name) || $id <= 0) throw new Exception('Invalid data');
        $stmt = $conn->prepare("UPDATE departments SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
        $response['success'] = true;
        $response['message'] = 'Department updated successfully';
        break;
      
      case 'delete':
        $id = (int)$_POST['id'];
        if ($id <= 0) throw new Exception('Invalid ID');
        $stmt = $conn->prepare("DELETE FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        $response['success'] = true;
        $response['message'] = 'Department deleted successfully';
        break;
      
      case 'list':
        $stmt = $conn->query("SELECT id, name FROM departments ORDER BY name");
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
