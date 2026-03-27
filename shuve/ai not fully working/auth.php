<?php
session_start();
require_once 'db.php';

/**
 * Sanitizes input data to prevent XSS and other injection attacks.
 * @param string $data The raw input data.
 * @return string The sanitized data.
 */
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// =========================================================
// Handle User Login
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'login') {
    // Sanitize and retrieve form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Basic validation to ensure fields are not empty
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields");
        exit();
    }

    try {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verify password hash against the stored hash
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables for authentication and role management
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role (Admin/Police -> Police Dashboard, Public -> User Dashboard)
            if ($user['role'] == 'admin' || $user['role'] == 'police') {
                header("Location: police_dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit();
        } else {
            // Invalid credentials
            header("Location: login.php?error=Invalid email or password");
            exit();
        }
    } catch (PDOException $e) {
        // Handle database errors gracefully
        header("Location: login.php?error=Database error: " . $e->getMessage());
        exit();
    }
}

// =========================================================
// Handle User Registration (Public Users Only)
// =========================================================
if (isset($_POST['action']) && $_POST['action'] == 'register') {
    // Sanitize all input fields
    $first_name = sanitize($_POST['first_name']);
    $last_name = sanitize($_POST['last_name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize($_POST['phone']);
    $address1 = sanitize($_POST['address1']);
    $address2 = sanitize($_POST['address2']);
    $postcode = sanitize($_POST['postcode']);
    $county = sanitize($_POST['county']);
    $city = sanitize($_POST['city']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        header("Location: register.php?error=Passwords do not match");
        exit();
    }

    // Securely hash the password before storage
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Check if email is already registered to prevent duplicates
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: register.php?error=Email already registered");
            exit();
        }

        // Insert new user into database with default role 'public'
        $sql = "INSERT INTO users (first_name, last_name, email, password, role, phone, address_line1, address_line2, postcode, county, city) 
                VALUES (?, ?, ?, ?, 'public', ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$first_name, $last_name, $email, $hashed_password, $phone, $address1, $address2, $postcode, $county, $city]);

        // Redirect to login page upon success
        header("Location: login.php?success=Registration successful! Please login.");
        exit();
    } catch (PDOException $e) {
        header("Location: register.php?error=Registration failed: " . $e->getMessage());
        exit();
    }
}

// =========================================================
// Handle User Logout
// =========================================================
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    // Clear all session data and destroy the session
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}
?>
