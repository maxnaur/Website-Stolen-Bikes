<?php
// config.php

define('DB_HOST', 'localhost');
define('DB_NAME', 'bikesafety');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// (re)use the same database name constant everywhere
$db = DB_NAME;

// connect to the server (no database yet)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// create the database if it doesn't already exist
$sql = "CREATE DATABASE IF NOT EXISTS `{$db}` \
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (! $conn->query($sql)) {
    die('Database creation failed: ' . $conn->error);
}

// select the database for future queries
$conn->select_db($db);

// create tables as needed (example users table)
$conn->query("CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB");

// At this point $conn is connected to the desired database and ready for use.
?>
