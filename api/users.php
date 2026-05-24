<?php
// user management
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
        echo json_encode(['success' => true, 'data' => getUserDetail($pdo, (int)$_GET['id'])]);
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

    if (!validateEmail($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format or malicious characters detected']);
        return;
    }

    // Validate text inputs
    $textFields = ['first_name', 'last_name', 'address_line1', 'address_line2', 'city', 'county', 'postcode'];
    foreach ($textFields as $field) {
        if (isset($data[$field]) && !validateTextInput($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid characters detected in ' . $field]);
            return;
        }
    }

    $id = createUser($pdo, $data);
    logActivity($pdo, 'USER_CREATED', 'users', $id, "Admin created user: " . $data['email']);
    echo json_encode(['success' => true, 'id' => $id]);
}

function handlePut($currentUser, $action) {
    global $pdo;
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['email']) && !validateEmail($data['email'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid email format or malicious characters detected']);
        return;
    }

    $textFields = ['first_name', 'last_name', 'address_line1', 'address_line2', 'city', 'county', 'postcode', 'phone'];
    foreach ($textFields as $field) {
        if (isset($data[$field]) && !validateTextInput($data[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid characters detected in ' . $field]);
            return;
        }
    }

    if ($action === 'update_profile') {
        updateUserProfile($pdo, $currentUser['id'], $data);
        logActivity($pdo, 'PROFILE_UPDATED', 'users', $currentUser['id'], "User updated their own profile");
        echo json_encode(['success' => true]);
    } elseif ($action === 'update_user') {
        checkRole('admin');
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['user_id'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'User ID is required']);
            return;
        }
        updateUserAdmin($pdo, $data['user_id'], $data);
        logActivity($pdo, 'USER_UPDATED', 'users', $data['user_id'], "Admin updated user: " . $data['email']);
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
