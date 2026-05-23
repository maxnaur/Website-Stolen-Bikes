/*
    Tokens
    Does error and success display
    sends the message to attempt log in 
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
                } else {
                    csrfInput.value = generateCSRFToken();
                }
            })
            .catch(error => {
                console.error('CSRF token fetch failed:', error);
                csrfInput.value = generateCSRFToken();
            });
    }

    // Function to handle displaying errors from URL parameters (often from redirect failure)
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

    // Function to display registration success messages
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

    // Select checkboxes toggle-password
    const toggleSelector = 'input[type="checkbox"].toggle-password, input[type="checkbox"][data-target], #showPasswordCheck';
    document.querySelectorAll(toggleSelector).forEach(cb => {
        cb.addEventListener('change', () => {
        const targetSelector = cb.dataset.target || (cb.id === 'showPasswordCheck' ? '#password' : null);
        if (!targetSelector) return;
        const tgt = document.querySelector(targetSelector);
        if (tgt) tgt.type = cb.checked ? 'text' : 'password';
        });
    });

    // Email validation function - checks proper email format
    function isValidEmail(email) {
        // Pattern: local-part@domain.extension
        // Local part: alphanumeric, dots, hyphens, underscores (not starting/ending with dot)
        // Domain: alphanumeric, dots, hyphens (at least one dot)
        // Extension: at least 2 alphabetic characters
        const emailPattern = /^[a-zA-Z0-9]+@[a-zA-Z0-9][a-zA-Z0-9.-]*[a-zA-Z0-9]\.[a-zA-Z]{2,}$/;
        return emailPattern.test(email.trim());
    }

    // Attach email validation to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', event => {
            let isFormValid = true;
            
            form.querySelectorAll('input[type="email"]').forEach(emailInput => {
                const errorElement = emailInput.parentElement.querySelector('.email-error') || emailInput.parentElement.querySelector('.text-danger');
                const emailValue = emailInput.value.trim();
                
                if (errorElement) {
                    if (!isValidEmail(emailValue)) {
                        errorElement.style.display = 'block';
                        errorElement.textContent = 'Please enter a valid email address (e.g., user@example.com).';
                        isFormValid = false;
                    } else {
                        errorElement.style.display = 'none';
                    }
                }
            });
            
            if (!isFormValid) {
                event.preventDefault();
            }
        });
    });

    // Event Listener for Form Submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            if (!csrfInput.value) {
                csrfInput.value = generateCSRFToken();
            }
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

    function generateCSRFToken() {
        return Math.random().toString(36).substring(2) +
               Math.random().toString(36).substring(2);
    }
});