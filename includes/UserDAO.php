<?php
/**
 * includes/UserDAO.php
 * Data Access Object for Users
 */

function listUsers(PDO $pdo): array
{
    $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function createUser(PDO $pdo, array $data): int
{
    $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare("
        INSERT INTO users (first_name, last_name, email, password, role, address_line1, address_line2, city, postcode, county, date_of_birth)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $hashedPassword,
        $data['role'],
        $data['address_line1'] ?? '',
        !empty($data['address_line2']) ? $data['address_line2'] : null,
        $data['city'] ?? '',
        $data['postcode'] ?? '',
        $data['county'] ?? '',
        $data['date_of_birth'] ?? '1990-01-01'
    ]);
    
    return (int)$pdo->lastInsertId();
}

function getUserDetail(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user) {
        unset($user['password']);
    }
    return $user;
}

function updateUserProfile(PDO $pdo, int $userId, array $data): bool
{
    $params = [
        $data['email'],
        $data['phone'], 
        $data['address_line1'], 
        !empty($data['address_line2']) ? $data['address_line2'] : null,
        $data['city'], 
        $data['county'],
        $data['postcode']
    ];
    
    $passwordSql = "";
    if (!empty($data['password'])) {
        $passwordSql = ", password = ?";
        $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
    }
    
    $params[] = $userId;
    
    $stmt = $pdo->prepare("
        UPDATE users SET 
            email = ?,
            phone = ?, address_line1 = ?, address_line2 = ?, 
            city = ?, county = ?, postcode = ?
            $passwordSql
        WHERE id = ?
    ");
    
    return $stmt->execute($params);
}