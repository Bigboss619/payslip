<?php include_once("config/config.php"); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip System</title>
    <link rel="stylesheet" href="src/output.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-xl rounded-2xl w-full max-w-md p-8">
        <!-- Title -->
         <h2 id="formTitle" class="text-2xl font-bold text-center mb-6">
            Login
         </h2>

         <!-- Login Form -->
            <form action="includes/logsub.php" id="loginForm">
                <div class="mb-4">
                    <label class="text-sm font-medium">Email</label>
                    <input id="loginEmail" type="email" value="user@example.com" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500">
                    <p id="loginEmailError" class="mt-1 text-sm text-red-600 hidden">Please enter a valid email.</p>

                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Staff ID</label>
                    <input name="staff_id" id="loginStaffId" type="text" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500">
                    <p id="loginStaffIdError" class="mt-1 text-sm text-red-600 hidden">Staff ID is required.</p>
                </div>

<div class="mb-6 relative">

                    <label class="text-sm font-medium">Password</label>
                    <div class="relative mt-1">
                        <input id="loginPassword" type="password" value="userpass" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                        <button type="button" onclick="togglePassword('loginPassword')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="loginPasswordError" class="mt-1 text-sm text-red-600 hidden">Password must be at least 6 characters.</p>
                </div>

                <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Login
                </button>
            </form>

             <!-- SIGNUP FORM -->
            <form id="signupForm" class="hidden">

                <div class="mb-4">
                    <label class="text-sm font-medium">Full Name</label>
                    <input name="fullname" id="signupName" type="text" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500">
                    <p id="signupNameError" class="mt-1 text-sm text-red-600 hidden">Name must be at least 2 characters.</p>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Staff ID</label>
                    <input name="staff_id" id="signupStaffId" type="text" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500">
                    <p id="signupStaffIdError" class="mt-1 text-sm text-red-600 hidden">Staff ID must be at least 2 characters.</p>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Department</label>
                    <select name="department" id="signupDepartment" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500" required>
                        <option value="">Select Department</option>
                    </select>
                    <p id="signupDepartmentError" class="mt-1 text-sm text-red-600 hidden">Please select a department.</p>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-medium">Email</label>
                    <input name="email" id="signupEmail" type="email" class="w-full mt-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none invalid:border-red-500 invalid:focus:ring-red-500">
                    <p id="signupEmailError" class="mt-1 text-sm text-red-600 hidden">Please enter a valid email.</p>
                </div>

<div class="mb-4 relative">
                    <label class="text-sm font-medium">Password</label>
                    <div class="relative mt-1">
                        <input name="password" id="signupPassword" type="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                        <button type="button" onclick="togglePassword('signupPassword')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="signupPasswordError" class="mt-1 text-sm text-red-600 hidden">Password must be at least 6 characters.</p>
                </div>

<div class="mb-6 relative">
                    <label class="text-sm font-medium">Confirm Password</label>
                    <div class="relative mt-1">
                        <input name="confirm_password" id="signupConfirmPassword" type="password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 outline-none pr-12 invalid:border-red-500 invalid:focus:ring-red-500">
                        <button type="button" onclick="togglePassword('signupConfirmPassword')" class="absolute right-3 top-2 h-6 w-6 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <p id="signupConfirmPasswordError" class="mt-1 text-sm text-red-600 hidden">Passwords do not match.</p>
                </div>

                <button name="register" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                    Sign Up
                </button>
            </form>

            <!-- TOGGLE -->
            <p class="text-center text-sm mt-6">
            <span id="toggleText">Don't have an account? </span>
            <button id="toggleButton" onclick="toggleForm()" class="text-blue-600 font-medium ml-1">
                Sign Up
            </button>
            </p>
        </div>
    
</body>
<script src="components/Toast/Toast.js"></script>
<!-- <script src="components/modal/Modal.js"></script> -->
<script src="js/login-new.js"></script>
</html>