// Load sidebar dynamically into the page
document.addEventListener('DOMContentLoaded', function() {
    // Get the base path dynamically
    const basePath = window.location.pathname.split('/').slice(0, -2).join('/');
    const sidebarPath = basePath + '/Home/sidebar.html';
    
    fetch(sidebarPath)
        .then(response => {
            if (!response.ok) throw new Error('Failed to load sidebar: ' + response.status);
            return response.text();
        })
        .then(html => {
            const sidebarContainer = document.getElementById('sidebar-container');
            if (sidebarContainer) {
                // Extract only the content, removing the head and unnecessary tags
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const body = doc.querySelector('body');
                sidebarContainer.innerHTML = body ? body.innerHTML : html;
            }
        })
        .catch(error => console.error('Error loading sidebar:', error));
});
