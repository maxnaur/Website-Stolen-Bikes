// Registration.js

// Check if email is valid format
function isValidEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Check if password meets requirements
// at least 8 charictors long
// contains a uper, lower case letter
// contains a number and a syble
function isStrongPassword(password) {
  //const re = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
  return true//re.test(password);
}

// Toggle password visibility
function togglePasswordVisibility(inputId) {
  const input = document.getElementById(inputId);
  const type = input.type === 'password' ? 'text' : 'password';
  input.type = type;
}

// Password validation
function validatePassword() {
  const password = document.getElementById('password').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();
  
  // Check if passwords match
  if (password !== confirmPassword) {
    document.getElementById('confirmPassword').setCustomValidity("Passwords don't match");
    return false;
  }
  
  // Check if password meets requirements
  if (!isValidPassword(password)) {
    document.getElementById('password').setCustomValidity("Password must be 8+ characters with uppercase, lowercase, number and special character");
    return false;
  }
  
  // Clear validation messages
  document.getElementById('password').setCustomValidity('');
  document.getElementById('confirmPassword').setCustomValidity('');
  return true;
}

// Initialize password validation
document.getElementById('password').onchange = validatePassword;
document.getElementById('confirmPassword').onkeyup = validatePassword;

// Form submission handler
document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // trim all inputs
  const firstName = document.getElementById('firstName').value.trim();
  const lastName = document.getElementById('lastName').value.trim();
  const email = document.getElementById('email').value.trim();
  const confirmEmail = document.getElementById('confirmEmail').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const DOB = document.getElementById('DOB').value.trim();
  const addressLine1 = document.getElementById('addressLine1').value.trim();
  const addressLine2 = document.getElementById('addressLine2').value.trim();
  const postcode = document.getElementById('postcode').value.trim();
  const county = document.getElementById('county').value.trim();
  const city = document.getElementById('city').value.trim();
  const password = document.getElementById('password').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();
  
  // Validate required fields
  if (!firstName || !lastName || !email || !confirmEmail || !addressLine1 || !postcode || !county || !city) {
    alert("Please fill in all required fields");
    return;
  }
  
  // Validate email format
  if (!isValidEmail(email)) {
    alert("Please enter a valid email address");
    return;
  }
  
  // Validate email match
  if (email !== confirmEmail) {
    alert("Email addresses do not match");
    return;
  }
  
  // Validate password requirements
  if (!isValidPassword(password)) {
    alert("Password must be 8+ characters with uppercase, lowercase, number and special character");
    return;
  }
  
  // Get phone number and validate format
  const phoneRegex = /^[\d\s\-]{7,15}$/;
  if (phone && !phoneRegex.test(phone)) {
    alert("Please enter a valid phone number");
    return;
  }
  
  // Get DOB and validate format
  const dobRegex = /^\d{4}-\d{2}-\d{2}$/;
  if (DOB && !dobRegex.test(DOB)) {
    alert("Please enter a valid date of birth");
    return;
  }
  
  // Hash password (using a simple example - in production you'd use a proper library)
  const salt = Math.random().toString(36).substring(2, 10);
  const hashedPassword = sha256(password + salt); // Using a real SHA256 library in production
  
  // Prepare data for submission
  const formData = new FormData();
  formData.append('firstName', firstName);
  formData.append('lastName', lastName);
  formData.append('email', email);
  formData.append('phone', phone);
  formData.append('DOB', DOB);
  formData.append('addressLine1', addressLine1);
  formData.append('addressLine2', addressLine2);
  formData.append('postcode', postcode);
  formData.append('county', county);
  formData.append('city', city);
  formData.append('password', hashedPassword);
  formData.append('password_salt', salt);
  
  // Submit form to backend
  fetch('create_register.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (response.ok) {
      window.location.href = 'login.html';
    } else {
      return response.text();
    }
  })
  .then(data => {
    if (data.includes('error')) {
      alert(`Registration failed: ${data}`);
    } else {
      alert('Registration successful! Welcome to Bike\'s Safty!');
      window.location.href = 'login.html';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred during registration. Please try again.');
  });
});

// Simple SHA256 implementation (for demonstration purposes only)
// In production, use a proper library like crypto-js or bcrypt.js
function sha256(input) {
  const crypto = require('crypto'); // This would be handled by a real library
  return crypto.createHash('sha256').update(input).digest('hex');
}

// Add this to your HTML to show password toggle
document.querySelectorAll('.toggle-password').forEach(button => {
  button.addEventListener('click', function() {
    const input = document.querySelector(`[data-target="${this.dataset.target}"]`);
    togglePasswordVisibility(input.id);
  });
});
