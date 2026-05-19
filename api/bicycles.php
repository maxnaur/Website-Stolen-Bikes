<?php
// api/bicycles.php - REST API for bicycle management
header('Content-Type: application/json');
require_once '../database/config.php';
require_once '../common/auth-check.php';
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
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleGetBikes($user, $action) {
    global $pdo;
    if ($action === 'detail') {
        echo json_encode(['success' => true, 'data' => getBikeDetail($pdo, $_GET['id'])]);
    } elseif ($user['role'] === 'admin') {
        echo json_encode(['success' => true, 'data' => getAllBikes($pdo)]);
    } else {
        echo json_encode(['success' => true, 'data' => getUserBikes($pdo, $user['id'])]);
    }
}

function handleAddBike($user) {
    global $pdo;
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
    $id = addBicycle($pdo, $user['id'], $_POST, $imagePath);
    logActivity($pdo, 'BIKE_REGISTERED', 'bicycles', $id, "User registered bike: " . ($_POST['brand'] ?? 'Unknown'));
    echo json_encode(['success' => true, 'id' => $id]);
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
