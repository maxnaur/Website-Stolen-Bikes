<?php
// api/users.php - REST API for user management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
require_once '../includes/UserDAO.php';

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGet($user, $action);
            break;
        case 'POST':
            handlePost($user);
            break;
        case 'PUT':
            handlePut($user, $action);
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

function handleGet($currentUser, $action) {
    global $pdo;
    if ($action === 'list') {
        checkRole('admin');
        echo json_encode(['success' => true, 'data' => listUsers($pdo)]);
    } elseif ($action === 'detail') {
        checkRole('admin');
        echo json_encode(['success' => true, 'data' => getUserDetail($pdo, $_GET['id'])]);
    } elseif ($action === 'profile') {
        echo json_encode(['success' => true, 'data' => getUserDetail($pdo, $currentUser['id'])]);
    } elseif ($action === 'export') {
        checkRole('admin');
        exportUsersToCSV($pdo);
    }
}

function handlePost($currentUser) {
    global $pdo;
    checkRole('admin');
    
    // Support both JSON and standard POST (FormData)
    $data = json_decode(file_get_contents("php://input"), true);
    if (!$data) {
        $data = $_POST;
    }
    
    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email and password are required']);
        return;
    }

    $id = createUser($pdo, $data);
    logActivity($pdo, 'USER_CREATED', 'users', $id, "Admin created user: " . $data['email']);
    echo json_encode(['success' => true, 'id' => $id]);
}

function handlePut($currentUser, $action) {
    global $pdo;
    if ($action === 'update_profile') {
        $data = json_decode(file_get_contents("php://input"), true);
        updateUserProfile($pdo, $currentUser['id'], $data);
        logActivity($pdo, 'PROFILE_UPDATED', 'users', $currentUser['id'], "User updated their own profile");
        echo json_encode(['success' => true]);
    }
}

function exportUsersToCSV($pdo) {
    $users = listUsers($pdo);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="users.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, array_keys($users[0]));
    foreach($users as $u) fputcsv($out, $u);
    fclose($out);
    exit;
}
