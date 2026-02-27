<?php
require_once 'config.php';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $confirmEmail = trim($_POST['confirmEmail']);
    $phone = trim($_POST['phone']);
    $addressLine1 = trim($_POST['addressLine1']);
    $addressLine2 = trim($_POST['addressLine2']);
    $postcode = trim($_POST['postcode']);
    $county = trim($_POST['county']);
    $city = trim($_POST['city']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validate email and confirmation
    if ($email !== $confirmEmail) {
        die("Error: Email and Confirm Email do not match");
    }

    // Validate password match
    if ($password !== $confirmPassword) {
        die("Error: Passwords do not match");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        die("Error: Email is already registered");
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address_line1, address_line2, postcode, county, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstName, $lastName, $email, $password_hash, $phone, $addressLine1, $addressLine2, $postcode, $county, $city);
    $stmt->execute();

    // Redirect to login
    header("Location: login.php");
    exit();
}
?>
