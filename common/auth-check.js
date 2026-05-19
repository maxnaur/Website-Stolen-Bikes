/**
 * common/auth-check.js
 * Included in dashboard HTML files to verify login status via API
 */

async function verifySession(requiredRole = null) {
    try {
        const response = await fetch('../api/session.php');
        const session = await response.json();

        if (!session.loggedin) {
            window.location.href = '../login/login.html';
            return null;
        }

        if (requiredRole && session.role !== 'admin' && session.role !== requiredRole) {
            alert('Access Denied: You do not have permission to view this page.');
            window.location.href = '../login/login.html';
            return null;
        }

        // Apply username to UI elements if they exist
        const nameElements = document.querySelectorAll('.user-first-name');
        nameElements.forEach(el => {
            el.textContent = session.first_name || session.username.split(' ')[0];
        });

        // Show/hide role-based elements
        if (session.role === 'admin') {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = 'block');
        }

        return session;
    } catch (error) {
        console.error('Session verification failed:', error);
        window.location.href = '../login/login.html';
        return null;
    }
}
