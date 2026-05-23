<?php
/*
  Data Access Object for Reports
  */

function getAllReports(PDO $pdo, ?string $status = null): array
{
    $query = "
    SELECT r.*, b.brand, b.model, b.colour, u.first_name, u.last_name, u.email
    FROM report r
    JOIN bicycles b ON r.bicycle_id = b.id
    JOIN users u ON b.user_id = u.id
    ";

    $params = [];
    if ($status) {
        $query .= " WHERE r.bike_status = ?";
        $params[] = $status;
    }

    $query .= " ORDER BY r.report_date DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getUserReports(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("
        SELECT r.*, b.brand, b.model, b.colour
        FROM report r
        JOIN bicycles b ON r.bicycle_id = b.id
        WHERE b.user_id = ?
        ORDER BY r.report_date DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function createReportEntry(PDO $pdo, array $data): int
{
    $stmt = $pdo->prepare("
        INSERT INTO report (bicycle_id, investigation_status, investigation_notes, bike_status, incident_location)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $data['bicycle_id'],
        $data['investigation_status'] ?? 'open',
        $data['investigation_notes'] ?? '',
        $data['bike_status'],
        $data['incident_location']
    ]);

    $reportId = $pdo->lastInsertId();
    
    // Update bike status too
    $bikeTableStatus = $data['bike_status'];
    if ($bikeTableStatus === 'returned' || $bikeTableStatus === 'recovered' || $bikeTableStatus === 'captured') {
        $bikeTableStatus = 'safe';
    }
    
    $stmt = $pdo->prepare("UPDATE bicycles SET status = ? WHERE id = ?");
    $stmt->execute([$bikeTableStatus, $data['bicycle_id']]);
    
    return $reportId;
}

function updateReportEntry(PDO $pdo, int $reportId, array $data): bool
{
    $allowedFields = ['investigation_status', 'investigation_notes', 'bike_status'];
    $updates = [];
    $params = [];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }
    
    if (empty($updates)) {
        return false;
    }
    
    if (isset($data['investigation_status']) && $data['investigation_status'] === 'closed') {
        $updates[] = "closed_at = NOW()";
    }

    $query = "UPDATE report SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $reportId;
    
    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);
}

