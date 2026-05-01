<?php
/**
 * includes/BicycleDAO.php
 */

function getAllBikes($pdo) {
    $stmt = $pdo->query("
        SELECT b.*, u.first_name, u.last_name, u.email
        FROM bicycles b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getUserBikes($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM bicycles WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getBikeDetail($pdo, $id) {
    $stmt = $pdo->prepare("
        SELECT b.*, u.first_name, u.last_name, u.email
        FROM bicycles b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addBicycle($pdo, $userId, $data, $imagePath = null) {
    $stmt = $pdo->prepare("
        INSERT INTO bicycles (user_id, brand, model, type, colour, wheel_size, gears, brake_type, suspension, gender, age_group, mpn, image_path, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'safe')
    ");
    $stmt->execute([
        $userId,
        $data['brand'],
        $data['model'],
        $data['type'],
        $data['colour'],
        $data['wheel_size'] ?: null,
        $data['gears'] ?: null,
        $data['brake_type'] ?: null,
        $data['suspension'] ?: 'None',
        $data['gender'],
        $data['age_group'],
        $data['mpn'] ?: null,
        $imagePath
    ]);
    return $pdo->lastInsertId();
}
