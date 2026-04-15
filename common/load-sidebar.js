// Load sidebar dynamically into the page
document.addEventListener('DOMContentLoaded', function() {
    fetch('../Home/sidebar.html')
        .then(response => response.text())
        .then(html => {
            const sidebarContainer = document.getElementById('sidebar-container');
            if (sidebarContainer) {
                sidebarContainer.innerHTML = html;
            }
        })
        .catch(error => console.error('Error loading sidebar:', error));
});
