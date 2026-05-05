<?php
/**
 * common/bootstrap.php
 * Global bootstrap file for security and configuration.
 */

// Prevent multiple inclusions
if (defined('BOOTSTRAP_INCLUDED')) {
    return;
}
define('BOOTSTRAP_INCLUDED', true);

// Secure session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
// ini_set('session.cookie_secure', 1); // Enable if using HTTPS

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://unpkg.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://unpkg.com; img-src 'self' data: https://*.tile.openstreetmap.org https://tile.openstreetmap.org; connect-src 'self' https://nominatim.openstreetmap.org; frame-src 'none'; object-src 'none';");

/**
 * CSRF Protection Functions (only define if not already defined in config)
 */
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('validateCsrfToken')) {
    function validateCsrfToken($token) {
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'CSRF token validation failed.']);
            exit;
        }
        return true;
    }
}

/**
 * Helper to escape HTML output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Regenerate session ID to prevent session fixation (call after login)
 */
function secureLogin() {
    session_regenerate_id(true);
    generateCsrfToken(); // New token for new session
}
