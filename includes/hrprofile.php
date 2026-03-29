<?php
session_start();
require_once('../config/config.php');
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $errors = [];
    if (empty($name)) $errors[] = 'Name required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email already in use.';
        }
    }

        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $user_id]);
            
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            
            echo json_encode([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => implode(' ', $errors)
            ]);
        }
        exit;
    }

    if (isset($_POST['change_password'])) {
    $current_pw = $_POST['current_password'] ?? '';
    $new_pw = $_POST['new_password'] ?? '';
    $confirm_pw = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (empty($current_pw)) $errors[] = 'Current password required.';
    if (empty($new_pw) || strlen($new_pw) < 6) $errors[] = 'New password min 6 chars.';
    if ($new_pw !== $confirm_pw) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($current_pw, $user['password'])) {
            $hashed_new = password_hash($new_pw, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_new, $user_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Current password incorrect.'
            ]);
        }

        
    } else {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
    }
    exit;
}

if(isset($_POST['update_profile_picture'])) {
    // Update a Photo
        $path = $_FILES['photo']['name'];
        $path_tmp = $_FILES['photo']['tmp_name'];
        if(!empty($path)){
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($ext, $allowed_ext)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid file type.'
                ]);
                exit;
            }
            $new_name = 'profile_' . $user_id . '.' . $ext;
            $upload_dir = '../uploads/dp/';
            if (move_uploaded_file($path_tmp, $upload_dir . $new_name)) {
                $stmt = $conn->prepare("UPDATE users SET photo = ? WHERE id = ?");
                $stmt->execute([$new_name, $user_id]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Profile photo updated successfully!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to upload photo.'
                ]);
            }

        }
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>

