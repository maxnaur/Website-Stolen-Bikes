<?php
// Database configuration
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'bikesafety');
define('DB_PASSWORD', '0kztCN@fZAb4@R^pRYz@');
define('DB_NAME', 's4512742_');

// Create PDO connection
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
