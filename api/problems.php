<?php
// api/problems.php - REST API for reporting system problems
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
require_once '../includes/ProblemDAO.php';

// Ensure table exists
initProblemTable($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            checkRole('admin');
            echo json_encode(['success' => true, 'data' => getAllProblems($pdo)]);
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true) ?: $_POST;
            $type = $data['problem_type'] ?? 'General';
            $desc = $data['description'] ?? '';
            $url = $data['page_url'] ?? '';
            
            // Get user email from session if not provided
            $email = $user['username'] ? (getUserDetail($pdo, $user['id'])['email'] ?? 'Anonymous') : 'Guest';
            
            createProblemReport($pdo, $user['id'], $email, $type, $desc, $url);
            logActivity($pdo, 'PROBLEM_REPORTED', 'system_problems', null, "Type: $type");
            
            echo json_encode(['success' => true, 'message' => 'Problem reported to administrator. Thank you.']);
            break;

        case 'PUT':
            checkRole('admin');
            $data = json_decode(file_get_contents("php://input"), true);
            updateProblemStatus($pdo, $_GET['id'], $data['status']);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
