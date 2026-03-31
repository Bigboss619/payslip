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

    // Required fields
    if (empty($fullname)) $errors[] = 'Full name is required.';
    if (empty($staff_id)) $errors[] = 'Staff ID is required.';
    if (empty($department_name)) $errors[] = 'Department is required.';
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

    // Real DB duplicate check with error handling
    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = 'Email already exists.';
        }
    } catch (PDOException $e) {
        $errors[] = 'Database error: Table may not exist.';
    }

    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(' ', $errors)
        ]);
    } else {
        try {
            // Validate and get department_id
            $stmt_dep = $conn->prepare("SELECT id FROM departments WHERE name = ?");
            $stmt_dep->execute([$department_name]);
            $department_id = $stmt_dep->fetchColumn();
            if (!$department_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid department selected.']);
                exit;
            }

            // Real insert with hashed password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, staff_id, email, password, role, department_id, pension_id, tax_id, account_number, bank_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fullname, $staff_id, $email, $hashed_password, $role, $department_id, $pension_id, $tax_id, $account_name, $bank]);
            // 🔥 NEW: GET THE CREATED USER ID
            $user_id = $conn->lastInsertId();
            // 🔥 MAGIC FIX: LINK ALL EXISTING PAYSLIPS BY staff_id
            $stmt_link = $conn->prepare("
                UPDATE payslip 
                SET user_id = ? 
                WHERE staff_id = ? AND user_id IS NULL
            ");
            $stmt_link->execute([$user_id, $staff_id]);

            // 🔥 LOG HOW MANY PAYSLIPS WERE LINKED (for debugging)
            $stmt_count = $conn->prepare("SELECT COUNT(*) FROM payslip WHERE staff_id = ? AND user_id = ?");
            $stmt_count->execute([$staff_id, $user_id]);
            $linked_count = $stmt_count->fetchColumn();

            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully! and  ' . $linked_count . ' existing payslips linked.',
                'user' => [
                    'email' => $email,
                    'role' => 'user',
                    'dashboard' => '../HR/dashboard.php'
                ]
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Insert failed: ' . $e->getMessage()
            ]);
        }
    }
    exit;


}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>

