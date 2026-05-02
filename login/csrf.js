// CSRF Token Generator for Login Page
document.addEventListener('DOMContentLoaded', function() {
  // Generate a new CSRF token when page loads
  const csrfToken = generateCSRFToken();
  document.getElementById('csrfToken').value = csrfToken;

  // Regenerate token before form submission (Best practice)
  document.getElementById('loginForm')?.addEventListener('submit', function() {
    document.getElementById('csrfToken').value = generateCSRFToken();
  });

  function generateCSRFToken() {
    // Uses a combination of random values for better entropy
    return Math.random().toString(36).substring(2) +
           Math.random().toString(36).substring(2);
  }
});