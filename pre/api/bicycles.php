<?php
// api/bicycles.php - REST API for bicycle management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';

// auth-check.php already handles session_start and basic login check

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();

// Route based on method and action
$action = $_GET['action'] ?? '';

try {
  switch ($method) {
  case 'GET':
    if ($action === 'list') {
    listBikes($user);
    } elseif ($action === 'detail') {
    getBikeDetail($user);
    } elseif ($action === 'export') {
    exportBikes($user);
    } else {
    listBikes($user);
    }
    break;

  case 'POST':
    if ($action === 'add') {
    addBike($user);
    }
    break;

  case 'PUT':
    if ($action === 'update') {
    updateBike($user);
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  }
} catch (Exception $e) {
  error_log("Bicycle API Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
}

// Get list of bikes based on role
function listBikes($user) {
  global $pdo;
  
  try {
  if ($user['role'] === 'admin' || $user['role'] === 'police') {
    // Admin and Police see all bikes
    $stmt = $pdo->prepare("
    SELECT b.*, u.first_name, u.last_name, u.email 
    FROM bicycles b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
    ");
    $stmt->execute();
  } else {
    // Public only sees their own bikes
    $stmt = $pdo->prepare("
    SELECT b.* FROM bicycles b 
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user['id']]);
  }
  
  $bikes = $stmt->fetchAll();
  echo json_encode(['success' => true, 'data' => $bikes]);
  } catch (PDOException $e) {
  error_log("List Bikes Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

// Get single bike detail
function getBikeDetail($user) {
  global $pdo;
  $bike_id = $_GET['id'] ?? 0;
  
  try {
  $stmt = $pdo->prepare("
    SELECT b.*, u.first_name, u.last_name, u.email 
    FROM bicycles b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
  ");
  $stmt->execute([$bike_id]);
  $bike = $stmt->fetch();
  
  if (!$bike) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Bike not found']);
    return;
  }
  
  // Check authorization
  if ($user['role'] === 'public' && $bike['user_id'] != $user['id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    return;
  }
  
  echo json_encode(['success' => true, 'data' => $bike]);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

// Export all bikes to CSV (Admin/Police only)
function exportBikes($user) {
  global $pdo;
  
  if ($user['role'] !== 'admin' && $user['role'] !== 'police') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
  }
  
  try {
  $stmt = $pdo->prepare("
    SELECT b.*, u.first_name, u.last_name, u.email 
    FROM bicycles b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.created_at DESC
  ");
  $stmt->execute();
  $bikes = $stmt->fetchAll(PDO::FETCH_ASSOC);
  
  if (empty($bikes)) {
     echo "No data to export.";
     exit;
  }

  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="bicycle_registry_' . date('Y-m-d') . '.csv"');
  
  $output = fopen('php://output', 'w');
  
  // Header
  fputcsv($output, array_keys($bikes[0]));
  foreach ($bikes as $row) {
    fputcsv($output, $row);
  }
  
  fclose($output);
  exit;
  } catch (PDOException $e) {
  http_response_code(500);
  echo "Error exporting data.";
  }
}

// Add new bike
function addBike($user) {
  global $pdo;
  
  if ($user['role'] === 'police') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Police cannot add bikes']);
  exit;
  }
  
  $data = json_decode(file_get_contents("php://input"), true);
  
  // Validate required fields
  if (empty($data['brand']) || empty($data['model']) || empty($data['colour']) || empty($data['type'])) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Missing required fields']);
  exit;
  }
  
  try {
  $stmt = $pdo->prepare("
    INSERT INTO bicycles (user_id, brand, model, type, colour, wheel_size, gears, brake_type, suspension, gender, age_group, mpn, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");
  
  $stmt->execute([
    $user['id'],
    $data['brand'],
    $data['model'],
    $data['type'],
    $data['colour'],
    $data['wheel_size'] ?? null,
    $data['gears'] ?? null,
    $data['brake_type'] ?? null,
    $data['suspension'] ?? null,
    $data['gender'] ?? 'Unisex',
    $data['age_group'] ?? 'Adult',
    $data['mpn'] ?? null,
    'safe'
  ]);
  
  echo json_encode(['success' => true, 'message' => 'Bike added successfully']);
  } catch (PDOException $e) {
  error_log("Add Bike Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to add bike']);
  }
}

// Update bike
function updateBike($user) {
  global $pdo;
  $bike_id = $_GET['id'] ?? 0;
  $data = json_decode(file_get_contents("php://input"), true);
  
  try {
  // Check authorization
  $stmt = $pdo->prepare("SELECT user_id FROM bicycles WHERE id = ?");
  $stmt->execute([$bike_id]);
  $bike = $stmt->fetch();
  
  if (!$bike || ($user['role'] === 'public' && $bike['user_id'] != $user['id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
  }
  
  // Update bike
  $stmt = $pdo->prepare("UPDATE bicycles SET status = ? WHERE id = ?");
  $stmt->execute([$data['status'] ?? 'safe', $bike_id]);
  
  echo json_encode(['success' => true, 'message' => 'Bike updated successfully']);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to update bike']);
  }
}
?>
