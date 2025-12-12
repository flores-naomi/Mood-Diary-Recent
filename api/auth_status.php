<?php
// api/auth_status.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    echo json_encode(['logged_in' => false]);
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, username, created_at FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['logged_in' => false]);
        exit;
    }
    echo json_encode(['logged_in' => true, 'user' => $row]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['logged_in' => false]);
}
