// Load sidebar dynamically into the page
document.addEventListener('DOMContentLoaded', function() {
  // Try to determine the correct path - use lowercase to match server URL
  const sidebarPath = '../common/sidebar/sidebar.html';
  
  fetch(sidebarPath)
  .then(response => {
    if (!response.ok) {
    console.error('Sidebar fetch failed with status:', response.status, 'at path:', sidebarPath);
    throw new Error('Failed to load sidebar');
    }
    return response.text();
  })
  .then(html => {
    const sidebarContainer = document.getElementById('sidebar-container');
    if (!sidebarContainer) {
    console.error('sidebar-container not found in DOM');
    return;
    }
    // Extract only the body content
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const body = doc.querySelector('body');
    sidebarContainer.innerHTML = body ? body.innerHTML : html;
    console.log('Sidebar loaded successfully');
  })
  .catch(error => console.error('Error loading sidebar:', error));
});
