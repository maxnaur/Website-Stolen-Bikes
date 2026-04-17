<?php
// api/reports.php - REST API for report management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';

// auth-check.php already handles session_start and basic login check

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();

$action = $_GET['action'] ?? '';

try {
  switch ($method) {
  case 'GET':
    listReports($user);
    break;

  case 'POST':
    createReport($user);
    break;

  case 'PUT':
    updateReport($user);
    break;

  default:
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  }
} catch (Exception $e) {
  error_log("Report API Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
}

// List reports based on role and filters
function listReports($user) {
  global $pdo;
  
  $status = $_GET['status'] ?? '';
  
  try {
  if ($user['role'] === 'admin') {
    // Admin sees all reports
    $query = "
    SELECT r.*, b.brand, b.model, b.colour, u.first_name, u.last_name, u.email
    FROM report r
    JOIN bicycles b ON r.bicycle_id = b.id
    JOIN users u ON b.user_id = u.id
    ";
    
    $params = [];
    if (!empty($status)) {
    $query .= " WHERE r.bike_status = ?";
    $params[] = $status;
    }
    
    $query .= " ORDER BY r.report_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
  } elseif ($user['role'] === 'police') {
    // Police sees all reports
    $query = "
    SELECT r.*, b.brand, b.model, b.colour, u.first_name, u.last_name, u.email
    FROM report r
    JOIN bicycles b ON r.bicycle_id = b.id
    JOIN users u ON b.user_id = u.id
    ";
    
    $params = [];
    if (!empty($status)) {
    $query .= " WHERE r.bike_status = ?";
    $params[] = $status;
    }
    
    $query .= " ORDER BY r.report_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    
  } else {
    // Public only sees their own reports
    $query = "
    SELECT r.*, b.brand, b.model, b.colour
    FROM report r
    JOIN bicycles b ON r.bicycle_id = b.id
    WHERE b.user_id = ?
    ORDER BY r.report_date DESC
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user['id']]);
  }
  
  $reports = $stmt->fetchAll();
  echo json_encode(['success' => true, 'data' => $reports]);
  
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

// Create new report
function createReport($user) {
  global $pdo;
  
  if ($user['role'] === 'police') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Police cannot create reports']);
  exit;
  }
  
  $data = json_decode(file_get_contents("php://input"), true) ?: $_POST;
  
  $bike_id = $data['bicycle_id'] ?? 0;
  $investigation_status = $data['investigation_status'] ?? 'open';
  $investigation_notes = $data['investigation_notes'] ?? '';
  $bike_status = $data['bike_status'] ?? 'stolen';
  $incident_location = $data['incident_location'] ?? '';
  
  if (empty($bike_id)) {
  http_response_code(400);
  echo json_encode(['success' => false, 'message' => 'Missing bicycle_id']);
  exit;
  }
  
  try {
  // Verify user owns the bike (if public user)
  if ($user['role'] === 'public') {
    $stmt = $pdo->prepare("SELECT user_id FROM bicycles WHERE id = ?");
    $stmt->execute([$bike_id]);
    $bike = $stmt->fetch();
    
    if (!$bike || $bike['user_id'] != $user['id']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
    }
  }
  
  // Create report
  $stmt = $pdo->prepare("
    INSERT INTO report (bicycle_id, investigation_status, investigation_notes, bike_status, incident_location)
    VALUES (?, ?, ?, ?, ?)
  ");
  
  $stmt->execute([$bike_id, $investigation_status, $investigation_notes, $bike_status, $incident_location]);
  
  // Update bike status
  $updateStmt = $pdo->prepare("UPDATE bicycles SET status = ? WHERE id = ?");
  $updateStmt->execute([$bike_status, $bike_id]);
  
  echo json_encode(['success' => true, 'message' => 'Report created successfully']);
  
  } catch (PDOException $e) {
  error_log("Create Report Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to create report']);
  }
}

// Update report
function updateReport($user) {
  global $pdo;
  
  if ($user['role'] === 'public') {
  http_response_code(403);
  echo json_encode(['success' => false, 'message' => 'Public users cannot update reports']);
  exit;
  }
  
  $report_id = $_GET['id'] ?? 0;
  $data = json_decode(file_get_contents("php://input"), true);
  
  try {
  $investigation_status = $data['investigation_status'] ?? null;
  $investigation_notes = $data['investigation_notes'] ?? null;
  $bike_status = $data['bike_status'] ?? null;
  
  $query = "UPDATE report SET ";
  $params = [];
  
  if (!is_null($investigation_status)) {
    $query .= "investigation_status = ?, ";
    $params[] = $investigation_status;
  }
  
  if (!is_null($investigation_notes)) {
    $query .= "investigation_notes = ?, ";
    $params[] = $investigation_notes;
  }
  
  if (!is_null($bike_status)) {
    $query .= "bike_status = ?, ";
    $params[] = $bike_status;
  }
  
  if ($investigation_status === 'closed') {
    $query .= "closed_at = NOW(), ";
  }
  
  // Remove trailing comma
  $query = rtrim($query, ", ");
  $query .= " WHERE id = ?";
  $params[] = $report_id;
  
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  
  echo json_encode(['success' => true, 'message' => 'Report updated successfully']);
  
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to update report']);
  }
}
?>
