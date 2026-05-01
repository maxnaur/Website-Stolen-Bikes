<?php
/**
 * includes/UserDAO.php
 * Data Access Object for Users
 */

function listUsers($pdo) {
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function createUser($pdo, $data) {
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password, role, address_line1, city, postcode)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $hashedPassword,
        $data['role'],
        $data['address_line1'] ?? null,
        $data['city'] ?? null,
        $data['postcode'] ?? null
    ]);
    return $pdo->lastInsertId();
}

function getUserDetail($pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if ($user) unset($user['password']);
    return $user;
}

function updateUserProfile($pdo, $userId, $data) {
    $stmt = $pdo->prepare("
        UPDATE users SET 
        phone = ?, address_line1 = ?, address_line2 = ?, 
        city = ?, county = ?, postcode = ?
        WHERE id = ?
    ");
    return $stmt->execute([
        $data['phone'], $data['address_line1'], $data['address_line2'],
        $data['city'], $data['county'], $data['postcode'],
        $userId
    ]);
}
