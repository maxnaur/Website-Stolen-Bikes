<?php
require_once 'config.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Validate input
  if (empty($email) || empty($password)) {
  header("Location: index.html?error=" . urlencode("Please fill in all fields"));
  exit();
  }

  // Check if user exists
  $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
  header("Location: index.html?error=" . urlencode("Email not found"));
  exit();
  }

  $user = $result->fetch_assoc();
  $password_hash = $user['password'];

  // Verify password
  if (password_verify($password, $password_hash)) {
  // Set session
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['email'] = $email;
  header("Location: dashboard.php");
  exit();
  } else {
  header("Location: index.html?error=" . urlencode("Invalid password"));
  exit();
  }
}
?>
