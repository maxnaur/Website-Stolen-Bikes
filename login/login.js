/**
 * login/login.js
 * Handles error display and login form enhancements
 */

document.addEventListener('DOMContentLoaded', () => {
    // Load CSRF token for the form
    const csrfInput = document.getElementById('csrfToken');
    if (csrfInput) {
        fetch('../api/auth.php?action=csrf')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.csrf_token) {
                    csrfInput.value = data.csrf_token;
                }
            })
            .catch(error => console.error('CSRF token fetch failed:', error));
    }

    // Check for error parameters in the URL
    const params = new URLSearchParams(window.location.search);
    if (params.has('error')) {
        const errorDiv = document.getElementById('url-error');
        if (errorDiv) {
            errorDiv.textContent = decodeURIComponent(params.get('error'));
            errorDiv.style.display = 'block';
        }
    }

    // Check for success messages (e.g., after registration)
    if (params.has('success')) {
        const errorDiv = document.getElementById('url-error');
        if (errorDiv) {
            errorDiv.classList.remove('alert-danger');
            errorDiv.classList.add('alert-success');
            errorDiv.textContent = decodeURIComponent(params.get('success'));
            errorDiv.style.display = 'block';
        }
    }
});
