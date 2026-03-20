<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Bike's Safty</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Catppuccin CSS -->
  <link rel="stylesheet" href="form-base.css">
</head>
<body>
  <script>
    // show error from query string if present
    const params = new URLSearchParams(window.location.search);
    if (params.has('error')) {
      alert(params.get('error'));
    } else if (params.has('success')) {
      alert(params.get('success'));
    }
  </script>
  <div class="container py-1">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="text-center mb-4">Log In</h1>
            <!-- login form posts to backend script -->
            <form id="loginForm" method="POST" action="auth.php">
              <input type="hidden" name="action" value="login">
              <div class="row g-3">
                <!-- Email -->
                <div class="col-md-12">
                  <label for="email" class="form-label">Email*</label>
                  <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                  <div id="error-message" class="text-danger small mt-1" style="display:none;"></div>
                </div>
                
                <!-- Password -->
                <div class="col-md-12">
                  <label for="password" class="form-label">Password*</label>
                  <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                </div>

                <!-- Show password checkbox -->
                <div class="col-md-10 form-check mt-2">
                  <input class="form-check-input toggle-password" type="checkbox" value="" id="showPasswordCheck" data-target="#password">
                  <label class="form-check-label" for="showPasswordCheck">
                    Show password
                  </label>
                </div>
              </div>

              <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">Log In</button>
              </div>
              
              <div class="mt-6 text-center py-1">
                <a href="register.php" class="text-decoration-none text-muted">Create Account</a>
              </div>
              <div class="mt-6 text-center">
                <a href="forgot_password.php" class="text-decoration-none text-muted">Forgot Password</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (optional but recommended for form validation) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- shared utilities: password toggle + email validation -->
  <script src="form-utils.js"></script>
</body>
</html>