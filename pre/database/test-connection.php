<?php
// test-connection.php - Test database connectivity

echo "<h2>Database Connection Test</h2>";

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bike_police_system";

// Test 1: Connect without database
echo "<h3>Test 1: Connect to MySQL Server</h3>";
try {
  $pdo = new PDO(
  "mysql:host=" . $servername,
  $username,
  $password
  );
  echo "<p style='color: green;'>✓ Connected to MySQL server successfully</p>";
} catch (PDOException $e) {
  echo "<p style='color: red;'>✗ Failed to connect to MySQL: " . htmlspecialchars($e->getMessage()) . "</p>";
  echo "<p><strong>Troubleshooting:</strong> Make sure MySQL is running on localhost:3306</p>";
  exit;
}

// Test 2: Check if database exists
echo "<h3>Test 2: Check Database</h3>";
try {
  $pdo->exec("USE " . $dbname);
  echo "<p style='color: green;'>✓ Database '" . $dbname . "' exists</p>";
  
  // Test 3: Check tables
  echo "<h3>Test 3: Check Tables</h3>";
  $result = $pdo->query("SHOW TABLES");
  $tables = $result->fetchAll(PDO::FETCH_COLUMN);
  
  if (count($tables) > 0) {
  echo "<p style='color: green;'>✓ Tables found: " . implode(", ", $tables) . "</p>";
  } else {
  echo "<p style='color: orange;'>⚠ Database exists but has no tables</p>";
  echo "<p>Run <a href='setup.php'>setup.php</a> to create tables</p>";
  }
} catch (PDOException $e) {
  echo "<p style='color: orange;'>⚠ Database '" . $dbname . "' does not exist</p>";
  echo "<p>Run <a href='setup.php'>setup.php</a> to create the database and tables</p>";
  exit;
}

// Test 4: Full connection test
echo "<h3>Test 4: Full PDO Configuration Test</h3>";
try {
  $pdo = new PDO(
  "mysql:host=" . $servername . ";dbname=" . $dbname . ";charset=utf8mb4",
  $username,
  $password,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ]
  );
  echo "<p style='color: green;'>✓ Full configuration test passed</p>";
  
  // Test 5: Query test
  echo "<h3>Test 5: Sample Query</h3>";
  $result = $pdo->query("SELECT COUNT(*) as user_count FROM users");
  $data = $result->fetch();
  echo "<p style='color: green;'>✓ Query successful - Users in database: " . $data['user_count'] . "</p>";
  
} catch (PDOException $e) {
  echo "<p style='color: red;'>✗ Configuration test failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<hr>";
echo "<p><strong>Connection configuration (from config.php):</strong></p>";
echo "<pre>";
echo "DB_SERVER: localhost\n";
echo "DB_USERNAME: root\n";
echo "DB_PASSWORD: (empty)\n";
echo "DB_NAME: bike_police_system\n";
echo "</pre>";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Connection Test</title>
  <style>
  body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; }
  h2, h3 { color: #333; }
  p { line-height: 1.8; }
  pre { background: #f0f0f0; padding: 10px; border-radius: 5px; overflow-x: auto; }
  a { color: #0066cc; }
  </style>
</head>
<body>
</body>
</html>
