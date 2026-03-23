const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const formTitle = document.getElementById("formTitle");
const toggleText = document.getElementById("toggleText");
const toggleButton = document.getElementById("toggleButton");

let isLogin = true;

// Keep login validation (no backend yet)
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

  if (!email) {
    showError('loginEmail', 'loginEmailError', 'Email is required.');
    isValid = false;
  } else if (!isValidEmail(email)) {
    showError('loginEmail', 'loginEmailError', 'Please enter a valid email.');
    isValid = false;
  } else {
    clearError('loginEmail', 'loginEmailError');
  }

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

// NEW: Backend signup handler - NO client validation
signupForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const formData = new FormData(signupForm);
  formData.append('register', '1');  // Ensure backend condition passes
  
  try {
    const response = await fetch('includes/resub.php', {
      method: 'POST',
      body: formData
    });
    
    const data = await response.json();
    
    showToast(data.message, data.success ? 'success' : 'error');
    
    if (data.success) {
      // Clear form & switch to login
      signupForm.reset();
      toggleForm();  // Redirect to login section
    }
  } catch (error) {
    showToast('Network error. Please try again.', 'error');
    console.error('Signup error:', error);
  }


});

// Real-time for LOGIN only (no validation for signup)
function addRealTimeValidation() {
  // Only login fields
  ['loginEmail', 'loginPassword'].forEach(id => {
    const input = document.getElementById(id);
    input.addEventListener('input', () => {
      clearError(id, `${id}Error`);
      if (id === 'loginEmail' && input.value.trim() && !isValidEmail(input.value.trim())) {
        showError(id, `${id}Error`, 'Please enter a valid email.');
      } else if (id === 'loginPassword' && input.value.length > 0 && input.value.length < 6) {
        showError(id, `${id}Error`, 'Password must be at least 6 characters.');
      }
    });
  });
  // NO real-time for signup - backend only
}

// Password toggle
function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  const button = input.nextElementSibling;
  const svg = button.querySelector('svg path');
  
  if (input.type === 'password') {
    input.type = 'text';
    svg.setAttribute('d', 'M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L19 19');
  } else {
    input.type = 'password';
    svg.setAttribute('d', 'M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z');
  }
}

// Login handler (unchanged demo)
loginForm.addEventListener('submit', (e) => {
  e.preventDefault();
  if (validateLogin()) {
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;

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
      showToast('Invalid credentials!', 'error');
      showModal('Login Failed', 'Use: user@example.com/userpass or hr@example.com/hrpass', 'error');
    }
  }
});

// Toggle form
function toggleForm() {
  isLogin = !isLogin;

  if (isLogin) {
    loginForm.classList.remove("hidden");
    signupForm.classList.add("hidden");
    formTitle.innerText = "Login";
    toggleText.innerText = "Don't have an account? ";
    toggleButton.innerText = "Sign Up";
  } else {
    loginForm.classList.add("hidden");
    signupForm.classList.remove("hidden");
    formTitle.innerText = "Sign Up";
    toggleText.innerText = "Already have an account? ";
    toggleButton.innerText = "Login";
  }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
  addRealTimeValidation();
});

