<?php
// report management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
require_once '../includes/ReportDAO.php';
require_once '../includes/BicycleDAO.php';

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'export') {
                checkRole('police'); // Admin or Police
                exportReportsToCSV($pdo);
            } else {
                handleGetReports($user);
            }
            break;
        case 'POST':
            handleCreateReport($user);
            break;
        case 'PUT':
            handleUpdateReport($user);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleGetReports($user) {
    global $pdo;
    if ($user['role'] === 'admin' || $user['role'] === 'police') {
        $reports = getAllReports($pdo, $_GET['status'] ?? null);
    } else {
        $reports = getUserReports($pdo, $user['id']);
    }
    echo json_encode(['success' => true, 'data' => $reports]);
}

function handleCreateReport($user) {
    global $pdo;
    if ($user['role'] === 'police') {
        echo json_encode(['success' => false, 'message' => 'Police cannot create reports']); exit;
    }
    $data = json_decode(file_get_contents("php://input"), true) ?: $_POST;
    
    if (isset($data['incident_location']) && !validateTextInput($data['incident_location'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid characters detected in incident location.']); exit;
    }

    // Validation
    if (empty($data['bicycle_id'])) {
        echo json_encode(['success' => false, 'message' => 'Bicycle ID is required']); exit;
    }

    $bike = getBikeDetail($pdo, (int)$data['bicycle_id']);
    if (!$bike) {
        echo json_encode(['success' => false, 'message' => 'Bicycle not found']); exit;
    }

    // Ownership check (Admins can skip if they ever use this API, but typically it's for public)
    if ($user['role'] !== 'admin' && $bike['user_id'] != $user['id']) {
        echo json_encode(['success' => false, 'message' => 'Access Denied: You do not own this bicycle']); exit;
    }

    $newStatus = $data['bike_status'] ?? '';
    $currentStatus = $bike['status'];

    if ($newStatus === 'stolen' && $currentStatus === 'stolen') {
        echo json_encode(['success' => false, 'message' => 'This bike is already reported as stolen']); exit;
    }

    if (($newStatus === 'returned' || $newStatus === 'recovered') && $currentStatus === 'safe') {
        echo json_encode(['success' => false, 'message' => 'A safe bike cannot be reported as recovered']); exit;
    }

    $id = createReportEntry($pdo, $data);
    logActivity($pdo, 'REPORT_CREATED', 'report', $id, "Report for bike ID " . $data['bicycle_id']);
    echo json_encode(['success' => true, 'message' => 'Report created successfully']);
}

function handleUpdateReport($user) {
    global $pdo;
    checkRole('police');
    $reportId = (int)($_GET['id'] ?? 0);
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['investigation_notes']) && !validateTextInput($data['investigation_notes'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid characters detected in investigation notes.']);
        return;
    }

    updateReportEntry($pdo, $reportId, $data);
    logActivity($pdo, 'REPORT_UPDATED', 'report', $reportId, "Status updated by " . $user['username']);
    echo json_encode(['success' => true, 'message' => 'Report updated successfully']);
}

function exportReportsToCSV($pdo) {
    $reports = getAllReports($pdo);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reports.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, array_keys($reports[0]));
    foreach($reports as $r) fputcsv($out, $r);
    fclose($out);
    exit;
}
