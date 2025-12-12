<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$media_id = $_POST['media_id'] ?? null;

if (!$media_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Media ID required']);
    exit;
}

try {
    $pdo = getPDO();
    
    // Verify ownership and get file path
    $stmt = $pdo->prepare('SELECT file_path FROM media_uploads WHERE id = :id AND user_id = :uid');
    $stmt->execute([':id' => $media_id, ':uid' => $user_id]);
    $media = $stmt->fetch();
    
    if (!$media) {
        http_response_code(404);
        echo json_encode(['error' => 'Media not found']);
        exit;
    }
    
    // Delete file from filesystem
    $filePath = __DIR__ . '/../' . $media['file_path'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }
    
    // Delete from database
    $deleteStmt = $pdo->prepare('DELETE FROM media_uploads WHERE id = :id AND user_id = :uid');
    $deleteStmt->execute([':id' => $media_id, ':uid' => $user_id]);
    
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>

