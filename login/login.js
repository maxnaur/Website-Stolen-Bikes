// login-handler.js

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorDiv = document.getElementById('error-message');

    if (!loginForm) return; // Exit if the form doesn't exist

    loginForm.addEventListener('submit', async (event) => {
        // 1. Prevent default submission refresh
        event.preventDefault();

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        // Reset previous errors and show loading state
        errorDiv.textContent = '';
        errorDiv.style.display = 'block'; 

        try {
            // 2. Send credentials to the PHP backend via fetch API
            const response = await fetch('create_Login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    email: email,
                    password: password
                })
            });

            const result = await response.json(); // Assuming PHP returns JSON success/error status

            // 3. Handle the server response
            if (result.success) {
                // If successful, redirect the user based on their role
                window.location.href = `dashboard/${result.redirect_url}.html`; 
                // NOTE: I'm assuming your dashboard pages are in a subdirectory named 'dashboard'. Adjust this path as necessary.

            } else {
                // Display specific error messages from the server
                errorDiv.textContent = result.message || "An unknown login error occurred.";
            }

        } catch (e) {
            console.error('Login failed:', e);
            errorDiv.textContent = 'Could not connect to the server. Please try again later.';
        }
    });
});
