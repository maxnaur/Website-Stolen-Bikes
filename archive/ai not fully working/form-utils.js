document.addEventListener('DOMContentLoaded', () => {
  // password visibility toggles
  document.querySelectorAll('.toggle-password').forEach(cb => {
  cb.addEventListener('change', () => {
  const tgt = document.querySelector(cb.dataset.target);
  if (tgt && tgt.type === 'password') {
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