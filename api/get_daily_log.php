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
$date = $_GET['date'] ?? date('Y-m-d');

try {
    $pdo = getPDO();
    
    // Get mood log
    $mood = $pdo->prepare('SELECT * FROM mood_logs WHERE user_id = :uid AND date = :date ORDER BY created_at DESC LIMIT 1');
    $mood->execute([':uid' => $user_id, ':date' => $date]);
    $moodData = $mood->fetch();
    
    // Get diary
    $diary = $pdo->prepare('SELECT * FROM diary_entries WHERE user_id = :uid AND date = :date');
    $diary->execute([':uid' => $user_id, ':date' => $date]);
    $diaryData = $diary->fetch();
    
    // Get tags
    $tags = $pdo->prepare('SELECT tag_name FROM mood_tags WHERE user_id = :uid AND date = :date');
    $tags->execute([':uid' => $user_id, ':date' => $date]);
    $tagList = $tags->fetchAll(PDO::FETCH_COLUMN);
    
    // Get media
    $media = $pdo->prepare('SELECT id, media_type, file_path FROM media_uploads WHERE user_id = :uid AND date = :date');
    $media->execute([':uid' => $user_id, ':date' => $date]);
    $mediaList = $media->fetchAll();
    
    echo json_encode([
        'found' => true,
        'mood' => $moodData ?: null,
        'diary' => $diaryData ?: null,
        'tags' => $tagList,
        'media' => $mediaList
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
