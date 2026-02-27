<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'bikesafety');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
