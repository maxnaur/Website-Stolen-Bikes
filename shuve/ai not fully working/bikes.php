<?php
session_start();
require_once 'db.php';

// =========================================================
// Authentication Check
// =========================================================
// Ensure user is logged in before processing any requests
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * Sanitizes input data.
 */
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// =========================================================
// Handle Add Bike Action
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'add_bike') {
    $user_id = $_SESSION['user_id'];
    
    // Sanitize all bike attributes
    $brand = sanitize($_POST['brand']);
    $model = sanitize($_POST['model']);
    $type = sanitize($_POST['type']);
    $colour = sanitize($_POST['colour']);
    $mpn = sanitize($_POST['mpn']);
    $wheel_size = sanitize($_POST['wheel_size']);
    $gears = (int)$_POST['gears']; // Cast to integer for safety
    $brake_type = sanitize($_POST['brake_type']);
    $suspension = sanitize($_POST['suspension']);
    $gender = sanitize($_POST['gender']);
    $age_group = sanitize($_POST['age_group']);

    // Handle Image Upload
    // Checks for errors and moves file to 'uploads/' directory
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        
        // Note: In a production environment, rigorous validation of file type (MIME) and size is essential.
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    try {
        // Insert bike data into database
        $sql = "INSERT INTO bicycles (user_id, brand, model, type, colour, mpn, wheel_size, gears, brake_type, suspension, gender, age_group, image_path) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id, $brand, $model, $type, $colour, $mpn, $wheel_size, $gears, $brake_type, $suspension, $gender, $age_group, $image_path]);

        header("Location: dashboard.php?success=Bike registered successfully");
        exit();
    } catch (PDOException $e) {
        header("Location: add_bike.php?error=Error registering bike: " . $e->getMessage());
        exit();
    }
}

// =========================================================
// Handle Report Stolen Action
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'report_stolen') {
    $bike_id = $_POST['bike_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Security Check: Verify that the bike belongs to the logged-in user
        $stmt = $pdo->prepare("SELECT id FROM bicycles WHERE id = ? AND user_id = ?");
        $stmt->execute([$bike_id, $user_id]);
        if (!$stmt->fetch()) {
            header("Location: dashboard.php?error=Unauthorized action");
            exit();
        }

        // Update status to 'stolen' and set report date
        $stmt = $pdo->prepare("UPDATE bicycles SET status = 'stolen', report_date = NOW(), investigation_status = 'open' WHERE id = ?");
        $stmt->execute([$bike_id]);

        header("Location: dashboard.php?success=Bike reported stolen. Police have been notified.");
        exit();
    } catch (PDOException $e) {
        header("Location: dashboard.php?error=Error reporting bike: " . $e->getMessage());
        exit();
    }
}

// =========================================================
// Handle Update Investigation Status (Police Only)
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'update_status') {
    // Access Control: Only 'police' or 'admin' roles can perform this action
    if ($_SESSION['role'] !== 'police' && $_SESSION['role'] !== 'admin') {
        header("Location: dashboard.php?error=Unauthorized");
        exit();
    }

    $bike_id = $_POST['bike_id'];
    $status = $_POST['investigation_status'];
    $notes = sanitize($_POST['investigation_notes']);

    try {
        // Update investigation status and notes
        $stmt = $pdo->prepare("UPDATE bicycles SET investigation_status = ?, investigation_notes = ? WHERE id = ?");
        $stmt->execute([$status, $notes, $bike_id]);

        header("Location: police_dashboard.php?success=Investigation status updated");
        exit();
    } catch (PDOException $e) {
        header("Location: police_dashboard.php?error=Error updating status: " . $e->getMessage());
        exit();
    }
}
?>
