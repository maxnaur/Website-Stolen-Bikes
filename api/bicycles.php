<?php
// bicycle management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
require_once '../common/security.php';
require_once '../includes/BicycleDAO.php';

$method = $_SERVER['REQUEST_METHOD'];
$user = getCurrentUser();
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'export') {
                checkRole('admin');
                exportBikesToCSV($pdo);
            } else {
                handleGetBikes($user, $action);
            }
            break;
        case 'POST':
            handleAddBike($user);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Throwable $e) {
    // Catch all errors (including TypeError) and return JSON for AJAX callers
    http_response_code(500);
    error_log('Unhandled error in api/bicycles.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Internal server error: ' . $e->getMessage()]);
}

function handleGetBikes($user, $action) {
    global $pdo;
    if ($action === 'detail') {
        echo json_encode(['success' => true, 'data' => getBikeDetail($pdo, (int)$_GET['id'])]);
    } elseif ($user['role'] === 'admin') {
        echo json_encode(['success' => true, 'data' => getAllBikes($pdo)]);
    } else {
        echo json_encode(['success' => true, 'data' => getUserBikes($pdo, $user['id'])]);
    }
}

function handleAddBike($user) {
    global $pdo;
    // Ensure user is authenticated and we have a valid user id
    if (empty($user['id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Authentication required. Please log in and try again.']);
        return;
    }
    
    // Validate text inputs for common SQL injection patterns
    $textFields = ['brand', 'model', 'type', 'colour', 'gender', 'age_group', 'mpn'];
    foreach ($textFields as $field) {
        if (isset($_POST[$field]) && !validateTextInput($_POST[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid characters detected in ' . $field]);
            exit;
        }
    }

    if (isset($_POST['wheel_size']) && $_POST['wheel_size'] !== '') {
        if ((float)$_POST['wheel_size'] > 999 || (float)$_POST['wheel_size'] < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Wheel size must be between 0 and 999']);
            exit;
        }
    }

    if (isset($_POST['gears']) && $_POST['gears'] !== '') {
        if ((int)$_POST['gears'] > 999 || (int)$_POST['gears'] < 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Number of gears must be between 0 and 999']);
            exit;
        }
    }

    $imagePath = null;
    if (isset($_FILES['bike_image']) && $_FILES['bike_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['bike_image']['type'], $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and WebP are allowed.']);
            exit;
        }
        
        if ($_FILES['bike_image']['size'] > $maxSize) {
            echo json_encode(['success' => false, 'message' => 'File is too large. Max size is 5MB.']);
            exit;
        }

        $uploadDir = '../upload/bikes/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        $extension = pathinfo($_FILES['bike_image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['bike_image']['tmp_name'], $targetPath)) {
            $imagePath = 'upload/bikes/' . $fileName;
        }
    }
    try {
        $id = addBicycle($pdo, $user['id'], $_POST, $imagePath);
        logActivity($pdo, 'BIKE_REGISTERED', 'bicycles', $id, "User registered bike: " . ($_POST['brand'] ?? 'Unknown'));
        echo json_encode(['success' => true, 'id' => $id]);
    } catch (Throwable $e) {
        error_log('Error adding bicycle: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to register bike: ' . $e->getMessage()]);
    }
}

function exportBikesToCSV($pdo) {
    $bikes = getAllBikes($pdo);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bikes.csv"');
    $out = fopen('php://output', 'w');
    if (!empty($bikes)) {
        fputcsv($out, array_keys($bikes[0]));
        foreach($bikes as $b) fputcsv($out, $b);
    }
    fclose($out);
    exit;
}
