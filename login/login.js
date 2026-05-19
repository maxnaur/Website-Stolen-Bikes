/**
 * login/login.js
 * Handles error display and login form enhancements - MODIFIED TO HANDLE JSON RESPONSE
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

    // Function to handle displaying errors from URL parameters (e.g., after a manual redirect failure)
    const displayUrlError = () => {
        const params = new URLSearchParams(window.location.search);
        if (params.has('error')) {
            const errorDiv = document.getElementById('url-error');
            if (errorDiv) {
                // Assuming 'error' is encoded using urlencode() on the server side
                errorDiv.textContent = decodeURIComponent(params.get('error')); 
                errorDiv.style.display = 'block';
            }
        }
    };

    // Function to display success messages (e.g., after registration)
    const displaySuccessMessage = () => {
        const params = new URLSearchParams(window.location.search);
        if (params.has('success')) {
            const errorDiv = document.getElementById('url-error');
            if (errorDiv) {
                // Change alert class to success
                errorDiv.classList.remove('alert-danger');
                errorDiv.classList.add('alert-success');
                errorDiv.textContent = decodeURIComponent(params.get('success'));
                errorDiv.style.display = 'block';
            }
        }
    };

    // Initial checks for URL errors/successes (for direct link access)
    displayUrlError();
    displaySuccessMessage();


    // Event Listener for Form Submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const csrfToken = csrfInput.value;

            // Clear previous messages
            document.getElementById('url-error').style.display = 'none';

            try {
                const response = await fetch('../api/auth.php?action=login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded', // Use standard form encoding for robustness
                    },
                    body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}&csrf_token=${csrfToken}`
                });

                const data = await response.json();

                if (data.success && data.redirect) {
                    // SUCCESS: Redirect the user using the path provided by the API
                    console.log('Login successful. Redirecting to:', data.redirect);
                    window.location.href = data.redirect;
                } else {
                    // FAILURE: Display the message returned in the JSON payload
                    alert(`Login Failed: ${data.message || 'Unknown error occurred.'}`);
                }

            } catch (error) {
                console.error('Network or processing error:', error);
                alert('Could not connect to the server. Please check your internet connection.');
            }
        });
    }
});