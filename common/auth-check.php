<?php
// auth-check.php - Verify user is logged in
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: /login/Login.html');
    exit;
}

// Optional: Check for specific role
function checkRole($required_role) {
    if ($_SESSION['role'] !== $required_role) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Access denied']);
        exit;
    }
}

// Get current user info
function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

// Handle logout
function logout() {
    session_destroy();
    header('Location: /login/Login.html');
    exit;
}
?>
