<?php
header('Content-Type: application/json');
require_once '../database/config.php';

session_start();

$response = ['success' => false, 'message' => ''];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $firstName = trim($_POST['firstName'] ?? '');
  $lastName = trim($_POST['lastName'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $dob = trim($_POST['DOB'] ?? '');
  $addressLine1 = trim($_POST['addressLine1'] ?? '');
  $addressLine2 = trim($_POST['addressLine2'] ?? '');
  $postcode = trim($_POST['postcode'] ?? '');
  $county = trim($_POST['county'] ?? '');
  $city = trim($_POST['city'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirmEmail = trim($_POST['confirmEmail'] ?? '');
  
  // Validate required fields
  if (empty($firstName) || empty($lastName) || empty($email) || empty($addressLine1) || 
  empty($postcode) || empty($county) || empty($city) || empty($password) || empty($dob)) {
  http_response_code(400);
  $response['message'] = "Please fill in all required fields";
  echo json_encode($response);
  exit;
  }
  
  // Validate email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  $response['message'] = "Please enter a valid email address";
  echo json_encode($response);
  exit;
  }
  
  // Validate email match
  if ($email !== $confirmEmail) {
  http_response_code(400);
  $response['message'] = "Email addresses do not match";
  echo json_encode($response);
  exit;
  }
  
  // Validate password requirements
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/', $password)) {
  http_response_code(400);
  $response['message'] = "Password must be 8+ characters with uppercase, lowercase, number and special character";
  echo json_encode($response);
  exit;
  }
  
  // Validate phone number (optional but if provided, must be valid)
  if (!empty($phone) && !preg_match('/^[\d\s\-+()]{7,20}$/', $phone)) {
  http_response_code(400);
  $response['message'] = "Please enter a valid phone number";
  echo json_encode($response);
  exit;
  }
  
  // Validate DOB format
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
  http_response_code(400);
  $response['message'] = "Please enter a valid date of birth (YYYY-MM-DD)";
  echo json_encode($response);
  exit;
  }
  
  // Hash password using password_hash
  $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
  
  // Check if email already exists
  try {
  $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
  $checkStmt->execute([$email]);
  
  if ($checkStmt->rowCount() > 0) {
    http_response_code(400);
    $response['message'] = "Email address already registered";
    echo json_encode($response);
    exit;
  }
  
  // Insert into database
  $stmt = $pdo->prepare("
    INSERT INTO users (
    first_name, 
    last_name, 
    email, 
    phone, 
    date_of_birth, 
    address_line1, 
    address_line2, 
    postcode, 
    county, 
    city,
    password,
    role
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  
  $stmt->execute([
    $firstName,
    $lastName,
    $email,
    $phone ?: null,
    $dob,
    $addressLine1,
    $addressLine2 ?: null,
    $postcode,
    $county,
    $city,
    $hashedPassword,
    'public'
  ]);
  
  $user_id = $pdo->lastInsertId();
  logActivity($pdo, 'REGISTER', 'users', $user_id, "New account registered: $email");
  
  $response['success'] = true;
  $response['message'] = "Account created successfully! Please log in.";
  echo json_encode($response);
  exit;
  
  } catch (PDOException $e) {
  error_log("Registration Database Error: " . $e->getMessage());
  $response['message'] = "Registration failed. Please try again later.";
  echo json_encode($response);
  exit;
  }
} else {
  $response['message'] = "Invalid request method";
  echo json_encode($response);
}
?>

