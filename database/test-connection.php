<?php
// test-connection.php - Test database connectivity (Diagnostics Mode)

$servername = "127.0.0.1";
$password = "0kztCN@fZAb4@R^pRYz@";
$dbname = "bike_police_system";

$users_to_try = ["root", "bikesafety"];

echo "<h2>Database Connection Diagnostic</h2>";

foreach ($users_to_try as $username) {
    echo "<h3>Testing User: $username</h3>";
    
    // Test 1: Connect to MySQL Server
    try {
        $pdo = new PDO("mysql:host=$servername", $username, $password, [PDO::ATTR_TIMEOUT => 5]);
        echo "<p style='color: green;'>✓ Connected as <strong>$username</strong> successfully!</p>";
        
        // Test 2: Check if database exists
        try {
            $pdo->exec("USE `$dbname` ");
            echo "<p style='color: green;'>✓ Database '$dbname' exists</p>";
            
            // Test 3: Check Tables
            $result = $pdo->query("SHOW TABLES");
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);
            if (count($tables) > 0) {
                echo "<p style='color: green;'>✓ Tables found: " . implode(", ", $tables) . "</p>";
            } else {
                echo "<p style='color: orange;'>⚠ Database exists but has no tables. Run setup.php.</p>";
            }
            
            // Success! Stop here
            echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
            echo "<strong>SOLUTION FOUND:</strong> Use username <code>$username</code> with your provided password.";
            echo "</div>";
            break; 

        } catch (PDOException $e) {
            echo "<p style='color: orange;'>⚠ Connected but Database '$dbname' does not exist or access denied.</p>";
            echo "<p>Details: " . htmlspecialchars($e->getMessage()) . "</p>";
        }

    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Failed to connect as $username: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    echo "<hr>";
}

echo "<h4>Next Steps</h4>";
echo "<ul>";
echo "<li>If both fail, verify the password <code>" . htmlspecialchars($password) . "</code> is exactly what you set.</li>";
echo "<li>If using a control panel (WAMP/XAMPP), check which user has privileges.</li>";
echo "</ul>";
?>
