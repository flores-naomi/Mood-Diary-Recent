<?php
// api/get_today_mood.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $userId = $_SESSION['user_id'] ?? null;
    if ($userId === null) {
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }

    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM mood_logs WHERE date = CURDATE() AND user_id = :uid ORDER BY created_at DESC LIMIT 1');
    $stmt->execute([':uid' => $userId]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(['found' => false]);
    } else {
        echo json_encode(['found' => true, 'data' => $row]);
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
