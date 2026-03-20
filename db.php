<?php
require_once 'config.php';

try {
    // Connect to MySQL server first to ensure database exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
    $pdo->exec("USE " . DB_NAME);

    // Now execute any table creation logic if needed, but we rely on database_setup.sql mainly.
    // However, for convenience, we can run a minimal check or just assume it's run via other means.
    // For now, let's just make sure we are connected to the DB.

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
