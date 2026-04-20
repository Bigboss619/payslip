<?php
require_once('../config/config.php');
header('Content-Type: application/json');

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname'] ?? '');
    $staff_id = trim($_POST['staff_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $department_name = trim($_POST['department'] ?? '');
    $pension_id = trim($_POST['pension_id'] ?? '');
    $tax_id = trim($_POST['tax_id'] ?? '');
    $bank = trim($_POST['bank'] ?? '');
    $account_name = trim($_POST['account_id'] ?? '');
    $role = 'STAFF';

    $errors = [];

    // Required fields validation
    if (empty($fullname)) $errors[] = 'Full name is required.';
    if (empty($staff_id)) $errors[] = 'Staff ID is required.';
    if (empty($department_name)) $errors[] = 'Department is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (empty($confirm_password)) $errors[] = 'Confirm password is required.';

    // Format validation
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    if (!empty($fullname) && strlen($fullname) < 2) {
        $errors[] = 'Full name must be at least 2 characters.';
    }
    if (!empty($password) && strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }

    // 🔥 NEW: STAFF ID FORMAT VALIDATION (BACKEND)
    if (!empty($staff_id)) {
        // Valid formats: N/SA/001, N/TR/001, N/AV/001, N/RT/001, N/CS/001
        if (!preg_match('/^N\/(SA|TR|AV|RT|CS)\/\d{3}$/', $staff_id)) {
            $errors[] = "❌ Invalid Staff ID format. Must be: N/SA/001, N/TR/001, N/AV/001, N/RT/001, or N/CS/001";
        }
    }

    // 🔥 CRITICAL: CHECK EMAIL & STAFF ID DUPLICATES FIRST
    if (empty($errors)) {
        try {
            // Check EMAIL duplicate
            $stmt_email = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt_email->execute([$email]);
            if ($stmt_email->rowCount() > 0) {
                $existing_user = $stmt_email->fetch(PDO::FETCH_ASSOC);
                $errors[] = "❌ Email '{$email}' already registered by '{$existing_user['name']}'";
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error checking email.';
        }
    }

    if (empty($errors)) {
        try {
            // Check STAFF ID duplicate
            $stmt_staff = $conn->prepare("SELECT id, name, email FROM users WHERE staff_id = ?");
            $stmt_staff->execute([$staff_id]);
            if ($stmt_staff->rowCount() > 0) {
                $existing_staff = $stmt_staff->fetch(PDO::FETCH_ASSOC);
                $errors[] = "❌ Staff ID '{$staff_id}' already registered by '{$existing_staff['name']}' ({$existing_staff['email']})";
            }
        } catch (PDOException $e) {
            $errors[] = 'Database error checking staff ID.';
        }
    }

    // 🚫 IF ANY ERRORS - STOP & SHOW TOAST MESSAGE
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' | ', $errors),
            'toast_type' => 'error'
        ]);
        exit;
    }

    // ✅ ALL CHECKS PASSED - CREATE USER
    try {
        // Get department_id
        $stmt_dep = $conn->prepare("SELECT id FROM departments WHERE name = ?");
        $stmt_dep->execute([$department_name]);
        $department_id = $stmt_dep->fetchColumn();
        
        if (!$department_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Department not found. Please refresh and try again.',
                'toast_type' => 'error'
            ]);
            exit;
        }

        // Hash password & insert user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (
                name, staff_id, email, password, role, 
                department_id, pension_id, tax_id, 
                account_number, bank_name, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $fullname, $staff_id, $email, $hashed_password, 
            $role, $department_id, $pension_id, $tax_id, 
            $account_name, $bank
        ]);

        $user_id = $conn->lastInsertId();

        // 🔥 AUTO-LINK EXISTING PAYSLIPS by staff_id
        $stmt_link = $conn->prepare("
            UPDATE payslip 
            SET user_id = ? 
            WHERE staff_id = ? AND user_id IS NULL
        ");
        $stmt_link->execute([$user_id, $staff_id]);

        // Count linked payslips
        $stmt_count = $conn->prepare("SELECT COUNT(*) FROM payslip WHERE user_id = ?");
        $stmt_count->execute([$user_id]);
        $linked_count = $stmt_count->fetchColumn();

        echo json_encode([
            'success' => true,
            'message' => "🎉 Welcome {$fullname}! Account created successfully.",
            'linked_payslips' => $linked_count,
            'toast_type' => 'success',
            'user' => [
                'email' => $email,
                'role' => $role,
                'dashboard' => '../HR/dashboard.php'
            ]
        ]);

    } catch (PDOException $e) {
        // Handle unique constraint violations
        if (stripos($e->getMessage(), 'Duplicate entry') !== false) {
            echo json_encode([
                'success' => false,
                'message' => 'Duplicate entry detected. Please try a different email/staff ID.',
                'toast_type' => 'error'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
                'toast_type' => 'error'
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.',
        'toast_type' => 'error'
    ]);
}
?>