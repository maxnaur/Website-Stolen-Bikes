<?php
/**
 * common/common.php
 * Library of shared utility functions.
 */

require_once __DIR__ . '/bootstrap.php';

/**
 * Logs an activity to the database
 */
function logActivity($pdo, $action, $table = null, $id = null, $details = null) {
  try {
  $user_id = $_SESSION['user_id'] ?? null;
  $ip = $_SERVER['REMOTE_ADDR'] ?? null;
  
  $stmt = $pdo->prepare("
    INSERT INTO activity_log (user_id, action, table_name, record_id, details, ip_address)
    VALUES (?, ?, ?, ?, ?, ?)
  ");
  $stmt->execute([$user_id, $action, $table, $id, $details, $ip]);
  } catch (Exception $e) {
  error_log("Failed to log activity: " . $e->getMessage());
  }
}
