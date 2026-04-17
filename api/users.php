<?php
// api/users.php - REST API for user management (Admin only)
header('Content-Type: application/json');
require_once '../database/config.php';

session_start();

if (!isset($_SESSION['loggedin'])) {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$user = $_SESSION;
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
  switch ($method) {
  case 'GET':
    if ($action === 'profile') {
      getOwnProfile($user);
    } elseif ($user['role'] === 'admin') {
      if ($action === 'list') {
        listUsers();
      } elseif ($action === 'detail') {
        getUserDetail();
      } else {
        listUsers();
      }
    } else {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Forbidden']);
    }
    break;

  case 'POST':
    if ($user['role'] === 'admin') {
      createUser();
    } else {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Forbidden']);
    }
    break;

  case 'PUT':
    if ($action === 'update_profile') {
      updateOwnProfile($user);
    } elseif ($user['role'] === 'admin') {
      updateUser();
    } else {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Forbidden']);
    }
    break;

  case 'DELETE':
    if ($user['role'] === 'admin') {
      deleteUser();
    } else {
      http_response_code(403);
      echo json_encode(['success' => false, 'message' => 'Forbidden']);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  }
} catch (Exception $e) {
  error_log("User API Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
}

function getOwnProfile($user) {
  global $pdo;
  try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, role, phone, address_line1, address_line2, postcode, county, city, date_of_birth, created_at FROM users WHERE id = ?");
    $stmt->execute([$user['user_id']]);
    $data = $stmt->fetch();
    echo json_encode(['success' => true, 'data' => $data]);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function updateOwnProfile($user) {
  global $pdo;
  $data = json_decode(file_get_contents("php://input"), true);
  
  // Validation
  $phone = trim($data['phone'] ?? '');
  if (!empty($phone) && !preg_match('/^[\d\s\-+()]{7,20}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone number format']);
    return;
  }

  $allowed_fields = ['phone', 'address_line1', 'address_line2', 'postcode', 'county', 'city'];
  $update_parts = [];
  $params = [];

  foreach ($allowed_fields as $field) {
    if (isset($data[$field])) {
      $update_parts[] = "$field = ?";
      $params[] = trim($data[$field]);
    }
  }

  if (empty($update_parts)) {
    echo json_encode(['success' => true, 'message' => 'No changes made']);
    return;
  }

  $params[] = $user['user_id'];
  try {
    $stmt = $pdo->prepare("UPDATE users SET " . implode(", ", $update_parts) . " WHERE id = ?");
    $stmt->execute($params);
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
  } catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function listUsers() {
  global $pdo;
  try {
  $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, phone, city, created_at FROM users ORDER BY created_at DESC");
  $users = $stmt->fetchAll();
  echo json_encode(['success' => true, 'data' => $users]);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function getUserDetail() {
  global $pdo;
  $id = $_GET['id'] ?? 0;
  try {
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$id]);
  $user = $stmt->fetch();
  if ($user) {
    unset($user['password']); // Don't send password hash
    echo json_encode(['success' => true, 'data' => $user]);
  } else {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
  }
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function createUser() {
  global $pdo;
  $data = json_decode(file_get_contents("php://input"), true);
  
  if (empty($data['email']) || empty($data['password']) || empty($data['first_name'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Missing required fields']);
  return;
  }

  $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

  try {
  $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, role, phone, address_line1, postcode, county, city, date_of_birth) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $data['first_name'],
    $data['last_name'],
    $data['email'],
    $password_hash,
    $data['role'] ?? 'public',
    $data['phone'] ?? null,
    $data['address_line1'] ?? '',
    $data['postcode'] ?? '',
    $data['county'] ?? '',
    $data['city'] ?? '',
    $data['date_of_birth'] ?? '2000-01-01'
  ]);
  echo json_encode(['success' => true, 'message' => 'User created successfully']);
  } catch (PDOException $e) {
  if ($e->getCode() == 23000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
  } else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
  }
  }
}

function updateUser() {
  global $pdo;
  $id = $_GET['id'] ?? 0;
  $data = json_decode(file_get_contents("php://input"), true);

  try {
  $fields = [];
  $params = [];
  $allowed_fields = ['first_name', 'last_name', 'role', 'phone', 'address_line1', 'postcode', 'county', 'city'];
  
  foreach ($allowed_fields as $field) {
    if (isset($data[$field])) {
    $fields[] = "$field = ?";
    $params[] = $data[$field];
    }
  }

  if (isset($data['password']) && !empty($data['password'])) {
    $fields[] = "password = ?";
    $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
  }

  if (empty($fields)) {
    echo json_encode(['success' => true, 'message' => 'No changes made']);
    return;
  }

  $params[] = $id;
  $stmt = $pdo->prepare("UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?");
  $stmt->execute($params);
  echo json_encode(['success' => true, 'message' => 'User updated successfully']);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function deleteUser() {
  global $pdo;
  $id = $_GET['id'] ?? 0;
  try {
  $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
  $stmt->execute([$id]);
  echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}
?>
