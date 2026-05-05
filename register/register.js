// Register.js
document.addEventListener('DOMContentLoaded', function() {
  // Email validation regex
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  
  // Password validation regex
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;
  
  // Initialize error messages
  const emailError = document.getElementById('emailError');
  const confirmEmailError = document.getElementById('confirmEmailError');
  const passwordError = document.getElementById('passwordError');
  const confirmPasswordError = document.getElementById('confirmPasswordError');
  
  // Toggle password visibility
  const togglePasswordVisibility = (inputId) => {
  const input = document.getElementById(inputId);
  const type = input.type === 'password' ? 'text' : 'password';
  input.type = type;
  };

  // Password validation function
  const validatePassword = () => {
  const password = document.getElementById('password').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();
  
  // Check if passwords match
  if (password !== confirmPassword) {
  confirmPasswordError.textContent = "Passwords don't match";
  confirmPasswordError.style.display = 'block';
  return false;
  }
  
  // Check password requirements
  if (!passwordRegex.test(password)) {
  passwordError.textContent = "Password must be 8+ characters with uppercase, lowercase, number and special character";
  passwordError.style.display = 'block';
  return false;
  }
  
  // Clear error messages
  passwordError.style.display = 'none';
  confirmPasswordError.style.display = 'none';
  return true;
  };

  // Initialize password validation
  document.getElementById('password').onchange = validatePassword;
  document.getElementById('confirmPassword').onkeyup = validatePassword;

  // Email validation function
  const validateEmail = () => {
  const email = document.getElementById('email').value.trim();
  const confirmEmail = document.getElementById('confirmEmail').value.trim();
  
  // Check email format
  if (!emailRegex.test(email)) {
  emailError.textContent = "Please enter a valid email address";
  emailError.style.display = 'block';
  return false;
  }
  
  // Check email match
  if (email !== confirmEmail) {
  confirmEmailError.textContent = "Email addresses do not match";
  confirmEmailError.style.display = 'block';
  return false;
  }
  
  // Clear error messages
  emailError.style.display = 'none';
  confirmEmailError.style.display = 'none';
  return true;
  };

  // Initialize email validation
  document.getElementById('email').onchange = validateEmail;
  document.getElementById('confirmEmail').onchange = validateEmail;

  const loadCsrfToken = () => {
    const csrfInput = document.getElementById('csrfToken');
    if (!csrfInput) return;

    fetch('../api/auth.php?action=csrf')
      .then(response => response.json())
      .then(data => {
        if (data.success && data.csrf_token) {
          csrfInput.value = data.csrf_token;
        }
      })
      .catch(error => console.error('Failed to load CSRF token:', error));
  };

  loadCsrfToken();

  // Form submission handler
  document.getElementById('registerForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  // Validate required fields
  const firstName = document.getElementById('firstName').value.trim();
  const lastName = document.getElementById('lastName').value.trim();
  const email = document.getElementById('email').value.trim();
  const confirmEmail = document.getElementById('confirmEmail').value.trim();
  const phone = document.getElementById('phone').value.trim();
  const DOB = document.getElementById('birth').value.trim();
  const addressLine1 = document.getElementById('addressLine1').value.trim();
  const addressLine2 = document.getElementById('addressLine2').value.trim();
  const postcode = document.getElementById('postcode').value.trim();
  const county = document.getElementById('county').value.trim();
  const city = document.getElementById('city').value.trim();
  const password = document.getElementById('password').value.trim();
  const confirmPassword = document.getElementById('confirmPassword').value.trim();
  
  // Check if all required fields are filled
  if (!firstName || !lastName || !email || !confirmEmail || !addressLine1 || !postcode || !county || !city || !DOB) {
  alert("Please fill in all required fields including Date of Birth");
  return;
  }
  
  // Check email validation
  if (!validateEmail()) return;
  
  // Check password validation
  if (!validatePassword()) return;
  
  // Validate phone number format
  const phoneRegex = /^[\d\s\-]{7,15}$/;
  if (phone && !phoneRegex.test(phone)) {
  alert("Please enter a valid phone number");
  return;
  }
  
  // Validate DOB format
  const dobRegex = /^\d{4}-\d{2}-\d{2}$/;
  if (!dobRegex.test(DOB)) {
  alert("Please enter a valid date of birth (YYYY-MM-DD)");
  return;
  }
  
  // Prepare data for submission
  const formData = new FormData(this);
  
  // Submit form to backend
  fetch('../api/auth.php?action=register', {
  method: 'POST',
  body: formData
  })
  .then(response => response.json())
  .then(data => {
  if (data.success) {
  alert('Registration successful! Welcome to Bike\'s Safety!');
  window.location.href = '../login/login.html?success=' + encodeURIComponent(data.message);
  } else {
  alert('Registration failed: ' + data.message);
  }
  })
  .catch(error => {
  console.error('Registration error:', error);
  alert('An error occurred during registration. Please try again.');
  });
  });

  // Leaflet address preview
  const debounce = (fn, delay = 400) => {
    let timer = null;
    return (...args) => {
      if (timer) clearTimeout(timer);
      timer = setTimeout(() => fn(...args), delay);
    };
  };

  const getFullAddress = () => {
    const addressLine1Value = document.getElementById('addressLine1').value.trim();
    const postcodeValue = document.getElementById('postcode').value.trim();
    const cityValue = document.getElementById('city').value.trim();
    const countyValue = document.getElementById('county').value.trim();
    return [addressLine1Value, cityValue, countyValue, postcodeValue].filter(Boolean).join(', ');
  };

  const updateAddressPreview = async () => {
    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
      return;
    }
    const query = getFullAddress();
    if (!query || !window.leafletUtils) {
      mapContainer.style.display = 'none';
      return;
    }

    const location = await window.leafletUtils.geocodeAddress(query);
    if (!location) {
      mapContainer.style.display = 'none';
      return;
    }

    mapContainer.style.display = 'block';
    window.leafletUtils.createOrUpdateMap('map', location.lat, location.lng, 14);
  };

  const addressInputIds = ['addressLine1', 'postcode', 'city', 'county'];
  addressInputIds.forEach((fieldId) => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', debounce(updateAddressPreview));
    }
  });

  updateAddressPreview();

  // Toggle password visibility handlers
  const passwordToggleElements = document.querySelectorAll('.toggle-password');
  passwordToggleElements.forEach(toggle => {
  toggle.addEventListener('change', () => {
  const targetId = toggle.dataset.target.replace('#', '');
  togglePasswordVisibility(targetId);
  });
  });
});
