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

function exportLogs() {
  global $pdo;
  try {
    $stmt = $pdo->query("
      SELECT l.*, u.email as user_email 
      FROM activity_log l
      LEFT JOIN users u ON l.user_id = u.id
      ORDER BY l.created_at DESC
    ");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($logs)) {
      echo "No log data to export.";
      return;
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="activity_logs_export_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($logs[0]));
    foreach ($logs as $row) {
      fputcsv($output, $row);
    }
    fclose($output);
    exit;
  } catch (PDOException $e) {
    http_response_code(500);
    echo "Error exporting data: " . $e->getMessage();
  }
}
?>