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
  header("Location: register.html?error=" . urlencode("Emails do not match"));
  exit();
  }

  // Validate password match
  if ($password !== $confirmPassword) {
  header("Location: register.html?error=" . urlencode("Passwords do not match"));
  exit();
  }

  // Check if email already exists
  $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
  header("Location: register.html?error=" . urlencode("Email is already registered"));
  exit();
  }

  // Hash password
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // Insert into database
  $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, phone, address_line1, address_line2, postcode, county, city) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  // ten strings: first name, last name, email, hash, phone, address1, address2, postcode, county, city
  $stmt->bind_param("ssssssssss", $firstName, $lastName, $email, $password_hash, $phone, $addressLine1, $addressLine2, $postcode, $county, $city);
  $stmt->execute();

  // Redirect to login page (index.html is the login form)
  header("Location: index.html");
  exit();
}
?>