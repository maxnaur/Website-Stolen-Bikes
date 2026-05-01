/**
 * login/login.js
 * Handles error display and login form enhancements
 */

document.addEventListener('DOMContentLoaded', () => {
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
