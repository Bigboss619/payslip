<?php
require_once('../config/config.php');
header('Content-Type: application/json');

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $staff_id = trim($_POST['staff_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Required fields
    if (empty($fullname)) $errors[] = 'Full name is required.';
    if (empty($staff_id)) $errors[] = 'Staff ID is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (empty($confirm_password)) $errors[] = 'Confirm password is required.';

    // Specific validations
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (!empty($fullname) && strlen($fullname) < 2) {
        $errors[] = 'Full name must be at least 2 characters.';
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if (!empty($password) && $password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // Real DB duplicate check
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $errors[] = 'Email already exists.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
    } else {
        // Real insert with hashed password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (fullname, staff_id, email, password, role) VALUES (?, ?, ?, ?, 'user')");
        $stmt->execute([$fullname, $staff_id, $email, $hashed_password]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully! You can now log in.',
            'user' => [
                'email' => $email,
                'role' => 'user',
                'dashboard' => '../HR/dashboard.php'
            ]
        ]);
    }
    exit;

}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>

