<?php
/*
  Data Access Object for Bicycles
  */

function getAllBikes(PDO $pdo): array
{
    $stmt = $pdo->query("
        SELECT b.*, u.first_name, u.last_name, u.email
        FROM bicycles b
        JOIN users u ON b.user_id = u.id
        ORDER BY b.created_at DESC
    ");
    return $stmt->fetchAll();
}

function getUserBikes(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT * FROM bicycles WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function getBikeDetail(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare("
        SELECT b.*, u.first_name, u.last_name, u.email,
               (SELECT r.incident_location FROM report r WHERE r.bicycle_id = b.id ORDER BY r.report_date DESC LIMIT 1) as incident_location
        FROM bicycles b
        JOIN users u ON b.user_id = u.id
        WHERE b.id = ?
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function addBicycle(PDO $pdo, int $userId, array $data, ?string $imagePath = null): int
{
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
        $data['wheel_size'] ?? null,
        $data['gears'] ?? null,
        $data['brake_type'] ?? null,
        $data['suspension'] ?? 'None',
        $data['gender'],
        $data['age_group'],
        $data['mpn'] ?? null,
        $imagePath
    ]);

    return $pdo->lastInsertId();
}

