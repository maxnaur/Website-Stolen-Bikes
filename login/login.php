<?php
// login.php - Secure login processing script
header('Content-Type: application/json'); 
session_start();

require_once '../database/config.php';

$response = ['success' => false, 'message' => '', 'redirect_url' => null];

// Check if form was submitted via POST method
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
  // 1. Input Sanitation and Retrieval
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($email) || empty($password)) {
  $response['message'] = "Please fill in both email and password.";
  echo json_encode($response);
  exit;
  }

  // 2. Verify email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $response['message'] = "Invalid email format.";
  echo json_encode($response);
  exit;
  }

  // 3. Database query using PDO
  try {
  $stmt = $pdo->prepare("SELECT id, first_name, last_name, role, password FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user) {
    // 4. Verify Password Hash using password_verify
    if (password_verify($password, $user['password'])) {
    // Successful Login!
    
    // 5. Start Session and Store User Data
    $_SESSION['loggedin'] = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['role'] = $user['role'];

    // 6. Determine Redirect URL based on role
    $role = strtolower($user['role']);
    switch ($role) {
      case 'police':
      $response['redirect_url'] = '/police/police-dashboard.php'; 
      break;
      case 'admin':
      $response['redirect_url'] = '/admin/admin-dashboard.php'; 
      break;
      case 'public':
      default:
      $response['redirect_url'] = '/public/public-dashboard.php'; 
      break;
    }

    $response['success'] = true;
    $response['message'] = "Login successful.";

    } else {
    // Password verification failed
    $response['message'] = "Invalid email or password.";
    }
  } else {
    // User not found
    $response['message'] = "No user found with this email address.";
  }

  } catch (PDOException $e) {
  // Database connection error
  error_log("Login PDO Error: " . $e->getMessage());
  $response['message'] = "System maintenance. Please try again later.";
  }
} else {
  $response['message'] = "Invalid request method.";
}

// Send the JSON response back to the JavaScript client
echo json_encode($response);
?>