<?php
require_once('../config/config.php');
session_start();
header('Content-Type: application/json');

if (isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $staff_id = trim($_POST['staff_id'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = [];

    if (empty($email) && empty($staff_id)) {
        $errors[] = 'Email or Staff ID is required.';
    }
    if (empty($password)) {
        $errors[] = 'Password is required.';
    }
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
        exit;
    }

    try {
        // Query by email OR staff_id
        $query = "SELECT id, name, staff_id, email, password, role FROM users WHERE (email = ? OR staff_id = ?) LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([$email, $staff_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['staff_id'] = $user['staff_id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirect
$redirect_url = 'HR/dashboard.php'; // Unified shared dashboard for all roles

            echo json_encode([
                'success' => true,
                'message' => 'Login successful! Welcome back, ' . $user['name'] . '.',
                'redirect' => $redirect_url,
                'role' => $user['role']
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email/staff ID or password.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
}
?>

