<?php
// login.php - Secure login processing script
session_start();
require_once '../database/config.php';

// Check if form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
  // 1. Input Retrieval and Sanitation
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  // 2. Validate input
  if (empty($email) || empty($password)) {
    header('Location: Login.html?error=Please fill in both email and password.');
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: Login.html?error=Invalid email format.');
    exit;
  }

  // 3. Database query using PDO
  try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, role, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
      // 4. Successful Login!
      $_SESSION['loggedin'] = true;
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
      $_SESSION['role'] = $user['role'];

      // 5. Determine Redirect URL based on role
      $role = strtolower($user['role'] ?? 'public');
      switch ($role) {
        case 'police':
          header('Location: ../police/police-dashboard.php');
          break;
        case 'admin':
          header('Location: ../admin/admin-dashboard.php');
          break;
        case 'public':
        default:
          header('Location: ../public/public-dashboard.php');
          break;
      }
      exit();
    } else {
      // User not found or password incorrect (Generic message for security)
      header('Location: Login.html?error=Incorrect login or password');
      exit();
    }

  } catch (PDOException $e) {
    // Database connection error
    error_log("Login PDO Error: " . $e->getMessage());
    header('Location: Login.html?error=System maintenance. Please try again later.');
    exit();
  }
} else {
  // Direct access to script - redirect to login page
  header('Location: Login.html');
  exit();
}
?>