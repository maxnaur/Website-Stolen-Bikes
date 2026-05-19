<?php
// database/setup.php - Database initialization script
header('Content-Type: text/html; charset=utf-8');

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '0kztCN@fZAb4@R^pRYz@'; // Default XAMPP/WAMP/MAMP password
$db   = 'bike_police_system';

echo "<!DOCTYPE html><html><head><title>Database Setup</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'></head><body class='p-5'>";
echo "<div class='container card shadow p-4' style='max-width: 600px;'>";
echo "<h2 class='mb-4'>🚲 Bike Police System - Database Setup</h2>";

try {
  // 1. Connect without database to create it
  $pdo = new PDO("mysql:host=$host", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // 2. Create Database
  echo "<div class='alert alert-info'>🔨 Creating database <strong>$db</strong>...</div>";
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
  echo "<div class='text-success mb-2'>✅ Database created or already exists.</div>";

  // 3. Connect to the new database
  $pdo->exec("USE `$db` ");  // Reconnect with DB name to be safe
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // 4. Read and Execute SQL
  $sql_file = 'database_setup.sql';
  if (!file_exists($sql_file)) {
  throw new Exception("SQL setup file not found: $sql_file");
  }

  echo "<div class='alert alert-info'>📑 Executing <strong>$sql_file</strong>...</div>";
  $sql = file_get_contents($sql_file);
  
  // Split SQL into individual queries
  // Simple split by semicolon (caution: this is basic and might fail on complex SQL)
  $queries = array_filter(array_map('trim', explode(';', $sql)));
  
  foreach ($queries as $query) {
  if (!empty($query)) {
    $pdo->exec($query);
  }
  }
  
  echo "<div class='text-success mb-4'>✅ All tables and default data created successfully!</div>";
  echo "<div class='card bg-light p-3 mb-4'>";
  echo "<h5>Default Admin Account:</h5>";
  echo "<ul><li><strong>Email:</strong> admin@police.uk</li><li><strong>Password:</strong> admin123</li></ul>";
  echo "</ul>";
  echo "</div>";

  echo "<a href='../login/login.html' class='btn btn-success btn-lg w-100'>🔐 Go to Login Page</a>";
  echo "<a href='../index.html' class='btn btn-outline-secondary w-100 mt-2'>🏠 Back to Welcome</a>";
} catch (Exception $e) {
  echo "<div class='alert alert-danger'>";
  echo "<h4>❌ Setup Failed</h4>";
  echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
  echo "<hr><p class='mb-0 small'>Troubleshooting: Ensure MySQL is running and your credentials in setup.php match.</p>";
  echo "</div>";
  echo "<button onclick='window.location.reload()' class='btn btn-warning'>🔄 Try Again</button>";
}

echo "</div></body></html>";
?>
