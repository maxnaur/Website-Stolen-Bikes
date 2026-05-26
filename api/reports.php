<?php
// report management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
require_once '../common/security.php';
require_once '../includes/ReportDAO.php';
require_once '../includes/BicycleDAO.php';

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'export') {
                checkRole('admin');
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
            echo json_encode(array('success' => false, 'message' => 'Method not allowed'));
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('success' => false, 'message' => $e->getMessage()));
}

function handleGetReports($user) {
    global $pdo;
    if ($user['role'] === 'admin' || $user['role'] === 'police') {
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $reports = getAllReports($pdo, $status);
    } else {
        $reports = getUserReports($pdo, $user['id']);
    }
    echo json_encode(array('success' => true, 'data' => $reports));
}

function handleCreateReport($user) {
    global $pdo;
    if ($user['role'] === 'police') {
        echo json_encode(array('success' => false, 'message' => 'Police cannot create reports'));
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    if ($data === null) {
        $data = $_POST;
    }

    if (isset($data['incident_location']) && !validateTextInput($data['incident_location'], 200)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid characters detected in incident location.'));
        exit;
    }

    if (isset($data['recovery_explanation']) && !validateTextInput($data['recovery_explanation'], 200)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid characters or too long recovery explanation.'));
        exit;
    }

    if (isset($data['investigation_notes']) && !validateTextInput($data['investigation_notes'], 200)) {
        echo json_encode(array('success' => false, 'message' => 'Invalid characters or too long notes.'));
        exit;
    }

    if (empty($data['bicycle_id'])) {
        echo json_encode(array('success' => false, 'message' => 'Bicycle ID is required'));
        exit;
    }

    $bike = getBikeDetail($pdo, (int)$data['bicycle_id']);
    if (!$bike) {
        echo json_encode(array('success' => false, 'message' => 'Bicycle not found'));
        exit;
    }

    if ($user['role'] !== 'admin' && $bike['user_id'] != $user['id']) {
        echo json_encode(array('success' => false, 'message' => 'Access Denied: You do not own this bicycle'));
        exit;
    }

    $newStatus = isset($data['bike_status']) ? $data['bike_status'] : '';
    $currentStatus = $bike['status'];

    if ($newStatus === 'stolen' && $currentStatus === 'stolen') {
        echo json_encode(array('success' => false, 'message' => 'This bike is already reported as stolen'));
        exit;
    }

    if (($newStatus === 'returned' || $newStatus === 'recovered') && $currentStatus === 'safe') {
        echo json_encode(array('success' => false, 'message' => 'A safe bike cannot be reported as recovered'));
        exit;
    }

    $id = createReportEntry($pdo, $data);
    logActivity($pdo, 'REPORT_CREATED', 'report', $id, "Report for bike ID " . $data['bicycle_id']);
    echo json_encode(array('success' => true, 'message' => 'Report created successfully'));
}

function handleUpdateReport($user) {
    global $pdo;
    checkRole('police');
    $reportId = (int)(isset($_GET['id']) ? $_GET['id'] : 0);
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['investigation_notes']) && !validateTextInput($data['investigation_notes'], 2000)) {
        http_response_code(400);
        echo json_encode(array('success' => false, 'message' => 'Invalid characters detected or notes too long.'));
        return;
    }

    updateReportEntry($pdo, $reportId, $data);
    logActivity($pdo, 'REPORT_UPDATED', 'report', $reportId, "Status updated by " . $user['username']);
    echo json_encode(array('success' => true, 'message' => 'Report updated successfully'));
}

function exportReportsToCSV($pdo) {
    $reports = getAllReports($pdo);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reports.csv"');
    $out = fopen('php://output', 'w');
    if (!empty($reports)) {
        fputcsv($out, array_keys($reports[0]));
        foreach ($reports as $r) {
            fputcsv($out, $r);
        }
    }
    fclose($out);
    exit;
}