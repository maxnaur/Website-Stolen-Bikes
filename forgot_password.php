<?php
require_once 'config.php';
session_start();

// initialize variables
$step = 1;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['sendCode'])) {
        $email = trim($_POST['email'] ?? '');
        if ($email === '') {
            $message = 'Please enter an email address.';
        } else {
            // check user exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $message = 'Email address not found.';
            } else {
                // generate code and store in session
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_code'] = rand(100000, 999999);
                // in real app you'd email this code; we'll display it for demo
                $message = 'Reset code generated: ' . $_SESSION['reset_code'];
                $step = 2;
            }
        }
    } elseif (isset($_POST['verifyCode'])) {
        $code = trim($_POST['code'] ?? '');
        if ($code === '' || !isset($_SESSION['reset_code'])) {
            $message = 'Please enter the code sent to your email.';
        } elseif ($code != $_SESSION['reset_code']) {
            $message = 'Invalid code.';
        } else {
            $step = 3;
        }
    } elseif (isset($_POST['updatePassword'])) {
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirmPassword'] ?? '');
        if ($password === '' || $confirm === '') {
            $message = 'Please fill in both password fields.';
            $step = 3;
        } elseif ($password !== $confirm) {
            $message = 'Passwords do not match.';
            $step = 3;
        } else {
            // update user password
            if (isset($_SESSION['reset_email'])) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
                $stmt->bind_param('ss', $hashed, $_SESSION['reset_email']);
                $stmt->execute();
                unset($_SESSION['reset_code'], $_SESSION['reset_email']);
                $message = 'Password updated successfully. You may now <a href="index.html">log in</a>.';
                $step = 4;
            } else {
                $message = 'Session expired. Please start over.';
                $step = 1;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password - Bike's Safty</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom Catppuccin CSS -->
  <link rel="stylesheet" href="form-base.css">
  <link rel="stylesheet" href="forgot_Password.css">
</head>
<body>
  <div class="container py-1">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="card shadow-sm">
          <div class="card-body">
            <h1 class="text-center mb-4">Forgot Password</h1>
            <h2 class="text-center mb-4">Enter Email then enter the code that was reserved to reset your password</h2>

            <?php if ($message): ?>
              <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="POST" id="forgotForm">
              <?php if ($step === 1): ?>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label for="email" class="form-label">Email*</label>
                    <input type="email" name="email" class="form-control" id="email" placeholder="Email" required>
                  </div>
                </div>
                <div class="mt-4 text-center">
                  <button type="submit" name="sendCode" class="btn btn-primary btn-lg px-5">Send Code</button>
                </div>
              <?php elseif ($step === 2): ?>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label for="code" class="form-label">Received Code*</label>
                    <input type="text" name="code" class="form-control" id="code" placeholder="Code" required>
                  </div>
                </div>
                <div class="mt-4 text-center">
                  <button type="submit" name="verifyCode" class="btn btn-primary btn-lg px-5">Verify Code</button>
                </div>
              <?php elseif ($step === 3): ?>
                <div class="row g-3">
                  <div class="col-md-12">
                    <label for="password" class="form-label">New Password*</label>
                    <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                  </div>
                  <div class="col-md-12">
                    <label for="confirmPassword" class="form-label">Confirm Password*</label>
                    <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" placeholder="Confirm Password" required>
                  </div>
                </div>
                <div class="mt-4 text-center">
                  <button type="submit" name="updatePassword" class="btn btn-primary btn-lg px-5">Update Password</button>
                </div>
              <?php else: ?>
                <!-- step 4: done message above, no form -->
              <?php endif; ?>
            </form>

            <div class="mt-3 text-center">
              <a href="index.html" class="text-decoration-none text-muted">Sign In</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (optional but recommended for form validation) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>