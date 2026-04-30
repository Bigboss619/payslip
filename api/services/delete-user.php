<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'HR') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}
require_once '../config/config.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    try {
        // Check if user exists and not deleting self
        $check_stmt = $conn->prepare("SELECT name, id FROM users WHERE id = ?");
        $check_stmt->execute([$user_id]);
        $user = $check_stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'User not found!'];
        } elseif ($user['id'] == $_SESSION['user_id']) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Cannot delete your own account!'];
        } else {
            // Delete user
            $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $result = $delete_stmt->execute([$user_id]);
            
            if ($result) {
                $_SESSION['message'] = [
                    'type' => 'success', 
                    'text' => "User '{$user['name']}' deleted successfully!"
                ];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to delete user!'];
            }
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Database error: ' . $e->getMessage()];
    }
}

// Redirect back to users page
header('Location: ../../HR/users');
exit;
?>