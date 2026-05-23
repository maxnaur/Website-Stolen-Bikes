document.addEventListener('DOMContentLoaded', () => {
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

  // Email validation for every form
  const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', event => {
      let valid = true;
      form.querySelectorAll('input[type="email"]').forEach(input => {
        const err = input.parentElement.querySelector('.email-error') || input.parentElement.querySelector('.text-danger');
        if (err) {
          if (!emailPattern.test(input.value)) {
            err.style.display = 'block';
            err.textContent = 'Please enter a valid email address.';
            valid = false;
          } else {
            err.style.display = 'none';
          }
        }
      });
      if (!valid) event.preventDefault();
    });
  });
});
