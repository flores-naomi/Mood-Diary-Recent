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
$mood_id = $_POST['mood_id'] ?? null;
$date = $_POST['date'] ?? date('Y-m-d');
$tags = isset($_POST['tags']) ? (is_array($_POST['tags']) ? $_POST['tags'] : [$_POST['tags']]) : [];

if (empty($tags)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tags required']);
    exit;
}

try {
    $pdo = getPDO();
    
    // Clear existing tags for this date
    $pdo->prepare('DELETE FROM mood_tags WHERE user_id = :uid AND date = :date')->execute([':uid' => $user_id, ':date' => $date]);
    
    // Insert new tags
    $stmt = $pdo->prepare('INSERT INTO mood_tags (user_id, mood_id, date, tag_name) VALUES (:uid, :mid, :date, :tag)');
    foreach ($tags as $tag) {
        $tag = trim($tag);
        if (!empty($tag)) {
            $stmt->execute([':uid' => $user_id, ':mid' => $mood_id, ':date' => $date, ':tag' => $tag]);
        }
    }
    
    echo json_encode(['ok' => true, 'count' => count($tags)]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
