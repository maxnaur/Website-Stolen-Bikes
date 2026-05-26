<?php
// auth-check.php - Verify user is logged in
require_once __DIR__ . '/common.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

function isApiRequest() {
  $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
  $xhr = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
  $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
  return str_contains($accept, 'application/json')
      || $xhr === 'xmlhttprequest'
      || str_contains($contentType, 'application/json');
}

function sendJsonAuthError($code = 401) {
  http_response_code($code);
  header('Content-Type: application/json');
  echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in.']);
  exit;
}

// Global check: if not logged in, return JSON for API requests
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  if (isApiRequest()) {
    sendJsonAuthError(401);
  }
  header('Location: ../login/login.html');
  exit;
}

// Global CSRF Check for all state-changing methods
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $headers['X-CSRF-TOKEN'] ?? $headers['X-Csrf-Token'] ?? $_POST['csrf_token'] ?? '';
    if (empty($token) && isset($headers['x-csrf-token'])) {
        $token = $headers['x-csrf-token'];
    }
    
    // Fallback for json body if needed
    if (empty($token) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input) && isset($input['csrf_token'])) {
            $token = $input['csrf_token'];
        }
    }

    validateCsrfToken($token);
}

// Optional: Check for specific role
function checkRole($required_role) {
  // Admin can access anything
  if ($_SESSION['role'] === 'admin') {
    return;
  }
  
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

