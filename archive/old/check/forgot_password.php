<?php
session_start();
require_once 'db.php';

$step = 1;
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['sendCode'])) {
  $email = trim($_POST['email'] ?? '');
  if (empty($email)) {
    $error = 'Please enter an email address.';
  } else {
    try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
      $error = 'Email address not found.';
    } else {
      $_SESSION['reset_email'] = $email;
      $_SESSION['reset_code'] = rand(100000, 999999);
      // In a real application, you would send this code via email.
      // For this assignment/demo, we display it.
      $message = 'Reset code generated: ' . $_SESSION['reset_code'];
      $step = 2;
    }
    } catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    }
  }
  } elseif (isset($_POST['verifyCode'])) {
  $code = trim($_POST['code'] ?? '');
  if (empty($code) || !isset($_SESSION['reset_code'])) {
    $error = 'Please enter the code.';
  } elseif ($code != $_SESSION['reset_code']) {
    $error = 'Invalid code.';
    $step = 2; // Stay on step 2
  } else {
    $step = 3;
  }
  } elseif (isset($_POST['updatePassword'])) {
  $password = $_POST['password'] ?? '';
  $confirm = $_POST['confirmPassword'] ?? '';
  
  if (empty($password) || empty($confirm)) {
    $error = 'Please fill in both password fields.';
    $step = 3;
  } elseif ($password !== $confirm) {
    $error = 'Passwords do not match.';
    $step = 3;
  } else {
    if (isset($_SESSION['reset_email'])) {
    try {
      $hashed = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
      $stmt->execute([$hashed, $_SESSION['reset_email']]);
      
      unset($_SESSION['reset_code'], $_SESSION['reset_email']);
      $message = 'Password updated successfully. You can now <a href="login.php">log in</a>.';
      $step = 4;
    } catch (PDOException $e) {
      $error = "Database error: " . $e->getMessage();
      $step = 3;
    }
    } else {
    $error = 'Session expired. Please start over.';
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
  <title>Forgot Password - Bike Safety</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="form-base.css">
  <link rel="stylesheet" href="forgot_Password.css">
</head>
<body>
  <div class="container py-5">
  <div class="row justify-content-center">
  <div class="col-md-8">
  <div class="card shadow-sm">
    <div class="card-body">
    <h1 class="text-center mb-4">Forgot Password</h1>
    
    <?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($step < 4): ?>
    <form method="POST">
    <?php if ($step === 1): ?>
    <div class="mb-3">
      <label for="email" class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" id="email" required>
    </div>
    <div class="text-center">
      <button type="submit" name="sendCode" class="btn btn-primary px-5">Send Code</button>
    </div>

    <?php elseif ($step === 2): ?>
    <div class="alert alert-info">Code sent to <?php echo htmlspecialchars($_SESSION['reset_email']); ?></div>
    <div class="mb-3">
      <label for="code" class="form-label">Enter Code</label>
      <input type="text" name="code" class="form-control" id="code" required>
    </div>
    <div class="text-center">
      <button type="submit" name="verifyCode" class="btn btn-primary px-5">Verify Code</button>
    </div>

    <?php elseif ($step === 3): ?>
    <div class="mb-3">
      <label for="password" class="form-label">New Password</label>
      <input type="password" name="password" class="form-control" id="password" required>
    </div>
    <div class="mb-3">
      <label for="confirmPassword" class="form-label">Confirm Password</label>
      <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" required>
    </div>
    <div class="text-center">
      <button type="submit" name="updatePassword" class="btn btn-primary px-5">Update Password</button>
    </div>
    <?php endif; ?>
    </form>
    <?php endif; ?>

    <div class="mt-3 text-center">
    <a href="login.php" class="text-decoration-none">Back to Login</a>
    </div>
    </div>
  </div>
  </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
