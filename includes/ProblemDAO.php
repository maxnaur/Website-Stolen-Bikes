<?php
/**
 * includes/ProblemDAO.php
 */

function initProblemTable($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS system_problems (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        user_email VARCHAR(100),
        problem_type VARCHAR(50),
        description TEXT,
        page_url VARCHAR(255),
        status ENUM('new', 'in_review', 'resolved') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
}

function createProblemReport($pdo, $userId, $email, $type, $desc, $url) {
    $stmt = $pdo->prepare("
        INSERT INTO system_problems (user_id, user_email, problem_type, description, page_url)
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$userId, $email, $type, $desc, $url]);
}

function getAllProblems($pdo) {
    $stmt = $pdo->query("SELECT * FROM system_problems ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function updateProblemStatus($pdo, $id, $status) {
    $stmt = $pdo->prepare("UPDATE system_problems SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}
