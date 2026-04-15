<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
require_once '../config/config.php';

$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

// Fetch user
if ($user_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'User not found!'];
        header('Location: ../HR/users.php');
        exit;
    }
} else {
    $_SESSION['message'] = ['type' => 'error', 'text' => 'Invalid user ID!'];
    header('Location: ../HR/users.php');
    exit;
}

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    // $role = $_POST['role'];
    $status = $_POST['status'];
    
    // Validation
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';

    if (!in_array($status, ['active','inactive'])) $errors[] = 'Invalid status';
    // Password validation (optional)
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters';
        }
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match';
        }
    }
    
    // Email unique check
    $email_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_stmt->execute([$email, $user_id]);
    if ($email_stmt->fetch()) {
        $errors[] = 'Email already exists';
    }

    if (empty($errors)) {
        // Base update
        $sql = "UPDATE users SET name = ?, email = ?, role = ?, status = ? WHERE id = ?";
        $params = [$name, $email, $role, $status, $user_id];
        
        // Add password if provided
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name = ?, email = ?, role = ?, status = ?, password = ? WHERE id = ?";
            $params = [$name, $email, $role, $status, $hashed_password, $user_id];
        }
        
        $update_stmt = $conn->prepare($sql);
        
        if ($update_stmt->execute($params)) {
            $msg = "User '$name' updated successfully!";
            if (!empty($password)) $msg .= " Password changed!";
            $_SESSION['message'] = ['type' => 'success', 'text' => $msg];
            header('Location: ../HR/users.php');
            exit;
        } else {
            $errors[] = 'Update failed';
        }
    }
    
    // Repopulate form on error
    $user['name'] = $name;
    $user['email'] = $email;
    $user['role'] = $role;
    $user['status'] = $status;
}
?>