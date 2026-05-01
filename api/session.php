<?php
// api/session.php - Returns current user session info as JSON
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['loggedin' => false]);
    exit;
}

echo json_encode([
    'loggedin' => true,
    'user_id' => $_SESSION['user_id'] ?? null,
    'username' => $_SESSION['username'] ?? '',
    'role' => $_SESSION['role'] ?? 'public',
    'first_name' => explode(' ', $_SESSION['username'] ?? '')[0] ?? ''
]);
