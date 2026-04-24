document.addEventListener('DOMContentLoaded', () => {
  // password visibility toggles
  document.querySelectorAll('.toggle-password').forEach(cb => {
    cb.addEventListener('click', (e) => {
      // For checkboxes, click event happens after value is toggled
      const tgt = document.querySelector(cb.dataset.target);
      if (tgt) {
        tgt.type = cb.checked ? 'text' : 'password';
      }
    });
  });
});

// email validation for every form
const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', event => {
  let valid = true;
  form.querySelectorAll('input[type="email"]').forEach(input => {
  const err = input.parentElement.querySelector('.email-error');
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
  if (!valid) {
  event.preventDefault();
  }
  });
});

document.addEventListener('DOMContentLoaded', () => {
  const showPass = document.getElementById('showPasswordCheck');
  if (showPass) {
  showPass.addEventListener('change', () => {
  const pass = document.getElementById('password');
  if (pass) {
    pass.type = showPass.checked ? 'text' : 'password';
  }
  });
  }
});