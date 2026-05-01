<?php
/**
 * common/security.php
 * Security utilities: CSRF tokens, security headers, input validation
 */

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Set security headers for all responses
 */
function setSecurityHeaders() {
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');

    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');

    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://maps.googleapis.com; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data: https:;");

    // Referrer Policy
    header('Referrer-Policy: no-referrer-when-downgrade');

    // Permissions Policy
    header('Permissions-Policy: geolocation=(self), microphone=(), camera=()');
}

/**
 * Sanitize HTML output to prevent XSS
 */
function sanitizeHTML($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email address
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (basic validation)
 */
function validatePhone($phone) {
    return preg_match('/^[\d\s\-\+\(\)]{10,}$/', $phone);
}

/**
 * Validate URL
 */
function validateURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Sanitize file name to prevent directory traversal
 */
function sanitizeFileName($filename) {
    // Remove any path components
    $filename = basename($filename);
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    return $filename;
}

/**
 * Rate limiting helper
 */
function checkRateLimit($identifier, $maxAttempts = 5, $timeWindow = 300) {
    $cacheKey = 'rate_limit_' . md5($identifier);

    if (!isset($_SESSION[$cacheKey])) {
        $_SESSION[$cacheKey] = [];
    }

    $now = time();
    $_SESSION[$cacheKey] = array_filter($_SESSION[$cacheKey], function($timestamp) use ($now, $timeWindow) {
        return ($now - $timestamp) < $timeWindow;
    });

    if (count($_SESSION[$cacheKey]) >= $maxAttempts) {
        return false;
    }

    $_SESSION[$cacheKey][] = $now;
    return true;
}
