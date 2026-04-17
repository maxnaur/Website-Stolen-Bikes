<?php
// auth-check.php - Verify user is logged in
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Global check: if not logged in, redirect to login page (relative path)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  // Determine the path to Login.html based on current file location
  header('Location: ../login/Login.html');
  exit;
}

// Optional: Check for specific role
function checkRole($required_role) {
  if ($_SESSION['role'] !== $required_role) {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Access denied: Required role ' . $required_role]);
  exit;
  }
}

// Get current user info from session
function getCurrentUser() {
  return [
  'id' => $_SESSION['user_id'] ?? null,
  'username' => $_SESSION['username'] ?? null,
  'role' => $_SESSION['role'] ?? null,
  // Also fetch names if stored in session
  'first_name' => explode(' ', $_SESSION['username'] ?? '')[0] ?? '',
  'last_name' => explode(' ', $_SESSION['username'] ?? '')[1] ?? ''
  ];
}

// Handle logout (Helper function)
function logout() {
  session_destroy();
  header('Location: ../login/Login.html');
  exit;
}
?>
