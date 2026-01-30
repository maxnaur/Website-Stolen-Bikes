// Prevent form submission for demo purposes
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Login attempted');
    });
});