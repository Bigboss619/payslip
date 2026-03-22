const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const formTitle = document.getElementById("formTitle");
const toggleText = document.getElementById("toggleText");
const toggleButton = document.getElementById("toggleButton");

let isLogin = true;

// Utility functions for validation
function showError(inputId, errorId, message) {
  const input = document.getElementById(inputId);
  const errorEl = document.getElementById(errorId);
  input.classList.add('invalid');
  errorEl.textContent = message;
  errorEl.classList.remove('hidden');
}

function clearError(inputId, errorId) {
  const input = document.getElementById(inputId);
  const errorEl = document.getElementById(errorId);
  input.classList.remove('invalid');
  errorEl.classList.add('hidden');
}

function isValidEmail(email) {
  const re = /^[^@]+@[^@]+\.[^@]+$/;
  return re.test(email);
}

function validateLogin() {
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value;

  let isValid = true;

  // Email validation
  if (!email) {
    showError('loginEmail', 'loginEmailError', 'Email is required.');
    isValid = false;
  } else if (!isValidEmail(email)) {
    showError('loginEmail', 'loginEmailError', 'Please enter a valid email.');
    isValid = false;
  } else {
    clearError('loginEmail', 'loginEmailError');
  }

  // Password validation
  if (!password) {
    showError('loginPassword', 'loginPasswordError', 'Password is required.');
    isValid = false;
  } else if (password.length < 6) {
    showError('loginPassword', 'loginPasswordError', 'Password must be at least 6 characters.');
    isValid = false;
  } else {
    clearError('loginPassword', 'loginPasswordError');
  }

  return isValid;
}

function validateSignup() {
  const name = document.getElementById('signupName').value.trim();
  const email = document.getElementById('signupEmail').value.trim();
  const password = document.getElementById('signupPassword').value;
  const confirmPassword = document.getElementById('signupConfirmPassword').value;

  let isValid = true;

  // Name validation
  if (!name) {
    showError('signupName', 'signupNameError', 'Name is required.');
    isValid = false;
  } else if (name.length < 2) {
    showError('signupName', 'signupNameError', 'Name must be at least 2 characters.');
    isValid = false;
  } else {
    clearError('signupName', 'signupNameError');
  }

  // Email validation
  if (!email) {
    showError('signupEmail', 'signupEmailError', 'Email is required.');
    isValid = false;
  } else if (!isValidEmail(email)) {
    showError('signupEmail', 'signupEmailError', 'Please enter a valid email.');
    isValid = false;
  } else {
    clearError('signupEmail', 'signupEmailError');
  }

  // Password validation
  if (!password) {
    showError('signupPassword', 'signupPasswordError', 'Password is required.');
    isValid = false;
  } else if (password.length < 6) {
    showError('signupPassword', 'signupPasswordError', 'Password must be at least 6 characters.');
    isValid = false;
  } else {
    clearError('signupPassword', 'signupPasswordError');
  }

  // Confirm password validation
  if (!confirmPassword) {
    showError('signupConfirmPassword', 'signupConfirmPasswordError', 'Please confirm your password.');
    isValid = false;
  } else if (confirmPassword !== password) {
    showError('signupConfirmPassword', 'signupConfirmPasswordError', 'Passwords do not match.');
    isValid = false;
  } else {
    clearError('signupConfirmPassword', 'signupConfirmPasswordError');
  }

  return isValid;
}

// Real-time validation
function addRealTimeValidation() {
  ['loginEmail', 'loginPassword', 'signupName', 'signupEmail', 'signupPassword', 'signupConfirmPassword'].forEach(id => {
    const input = document.getElementById(id);
    input.addEventListener('input', () => {
      clearError(id, `${id}Error`);
      
      // Quick validation on input
      if (id.includes('Email')) {
        if (input.value.trim() && !isValidEmail(input.value.trim())) {
          showError(id, `${id}Error`, 'Please enter a valid email.');
        }
      } else if (id.includes('Password') && input.value.length > 0) {
        if (input.value.length < 6) {
          showError(id, `${id}Error`, 'Password must be at least 6 characters.');
        }
      } else if (id === 'signupName' && input.value.length > 0) {
        if (input.value.trim().length < 2) {
          showError(id, `${id}Error`, 'Name must be at least 2 characters.');
        }
      }
      
      // Special check for confirm password
      if (id === 'signupConfirmPassword' && input.value.length > 0) {
        const pw = document.getElementById('signupPassword').value;
        if (input.value !== pw) {
          showError(id, `${id}Error`, 'Passwords do not match.');
        }
      }
    });
  });
}

// Password visibility toggle
function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const button = input.nextElementSibling; // button after input
  const svg = button.querySelector('svg path');
  
  if (input.type === 'password') {
    input.type = 'text';
    svg.setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L19 19');
  } else {
    input.type = 'password';
    svg.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
  }
}

// Form submit handlers
loginForm.addEventListener('submit', (e) => {
  e.preventDefault();
  if (validateLogin()) {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

    // Hardcoded demo credentials
    const users = {
      'user@example.com': { password: 'userpass', role: 'user', dashboard: 'pages/dashboard.php' },
      'hr@example.com': { password: 'hrpass', role: 'hr', dashboard: 'HR/dashboard.php' }
    };

    if (users[email] && users[email].password === password) {
      localStorage.setItem('userRole', users[email].role);
      showToast(`Welcome, ${users[email].role}!`, 'success');
      showModal('Login Successful!', `Redirecting to ${users[email].role} dashboard...`, 'success');
      setTimeout(() => {
        window.location.href = users[email].dashboard;
      }, 1500);
    } else {
      showToast('Invalid email or password!', 'error');
      showModal('Login Failed', 'Please check your credentials or use defaults:<br>User: user@example.com / userpass<br>HR: hr@example.com / hrpass', 'error');
    }
  }
});

signupForm.addEventListener('submit', (e) => {
  e.preventDefault();
  if (validateSignup()) {
    showToast('Account created!', 'success');
    showModal('Account Created!', 'Your account has been successfully created. You can now log in.', 'success');
    // Future: API call, auto login or redirect
    // setTimeout(() => toggleForm(), 2000);
  }
});

// Toggle form
function toggleForm() {
  isLogin = !isLogin;

  if (isLogin) {
    loginForm.classList.remove("hidden");
    signupForm.classList.add("hidden");
    formTitle.innerText = "Login";
    toggleText.innerText = "Don't have an account?";
    toggleButton.innerText = "Sign Up";
  } else {
    loginForm.classList.add("hidden");
    signupForm.classList.remove("hidden");
    formTitle.innerText = "Sign Up";
    toggleText.innerText = "Already have an account?";
    toggleButton.innerText = "Login";
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  addRealTimeValidation();
});
