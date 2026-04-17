<?php
// api/logs.php - REST API for system activity logs (Admin only)
header('Content-Type: application/json');
require_once '../database/config.php';

session_start();

// Admin only check
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'admin') {
  http_response_code(401);
  echo json_encode(['success' => false, 'message' => 'Unauthorized']);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
  switch ($method) {
  case 'GET':
    if ($action === 'list') {
      listLogs();
    }
    break;

  case 'DELETE':
    if ($action === 'clear') {
      clearLogs();
    }
    break;

  default:
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
  }
} catch (Exception $e) {
  error_log("Logs API Error: " . $e->getMessage());
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Server error']);
}

function listLogs() {
  global $pdo;
  try {
  // Join with users to show emails instead of just IDs
  $stmt = $pdo->query("
    SELECT l.*, u.email as user_email 
    FROM activity_log l
    LEFT JOIN users u ON l.user_id = u.id
    ORDER BY l.created_at DESC
    LIMIT 500
  ");
  $logs = $stmt->fetchAll();
  echo json_encode(['success' => true, 'data' => $logs]);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Database error']);
  }
}

function clearLogs() {
  global $pdo;
  try {
  $pdo->exec("TRUNCATE TABLE activity_log");
  echo json_encode(['success' => true, 'message' => 'Logs cleared successfully']);
  } catch (PDOException $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'Failed to clear logs']);
  }
}
?>