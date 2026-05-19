<?php
/**
 * database/config.php
 * Database configuration using environment variables for security
 */
require_once __DIR__ . '/../common/bootstrap.php';
require_once __DIR__ . '/../common/env-loader.php';

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Load database configuration from environment variables
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '0kztCN@fZAb4@R^pRYz@');
define('DB_NAME', getenv('DB_NAME') ?: 's4512742_');

// Create PDO connection with proper error handling
try {
  $pdo = new PDO(
    "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    DB_USERNAME,
    DB_PASSWORD,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );
} catch (PDOException $e) {
  error_log("Database Connection Error: " . $e->getMessage());
  throw new RuntimeException('Database connection failed.', 0, $e);
}

// For backward compatibility
$conn = $pdo;

/**
 * Generate CSRF token
 */
if (!function_exists('generateCSRFToken')) {
    function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

/**
 * Verify CSRF token
 */
if (!function_exists('verifyCSRFToken')) {
    function verifyCSRFToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
            return false;
        }

        // Optionally, regenerate the token upon successful verification to prevent replay attacks.
        // For simplicity, we stick to just verification here based on the snippet provided.
        return true;
    }
}

