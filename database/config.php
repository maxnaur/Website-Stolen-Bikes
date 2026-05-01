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
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'bikesafety');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
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
  http_response_code(500);
  error_log("Database Connection Error: " . $e->getMessage());
  die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// For backward compatibility
$conn = $pdo;
