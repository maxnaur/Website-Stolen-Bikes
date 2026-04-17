<?php
require_once 'db.php';

try {
  $sql = file_get_contents('database_setup.sql');
  $pdo->exec($sql);
  echo "Database and tables created successfully.";
} catch (PDOException $e) {
  die("Error creating database: " . $e->getMessage());
}
?>
