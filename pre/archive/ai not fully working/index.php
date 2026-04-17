<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
  // Redirect based on role
  if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'police') {
  header("Location: police_dashboard.php");
  } else {
  header("Location: dashboard.php");
  }
  exit();
} else {
  // If not logged in, redirect to login page
  header("Location: login.php");
  exit();
}
?>
