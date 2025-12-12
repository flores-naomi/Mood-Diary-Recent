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
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('m');

try {
    $pdo = getPDO();
    
    // Get all moods for the month with related data
    $sql = 'SELECT 
        m.id, m.date, m.combined_score, m.face_emotion, m.audio_emotion,
        COUNT(DISTINCT d.id) as has_diary,
        COUNT(DISTINCT mu.id) as has_media,
        GROUP_CONCAT(DISTINCT t.tag_name) as tags
        FROM mood_logs m
        LEFT JOIN diary_entries d ON m.user_id = d.user_id AND m.date = d.date
        LEFT JOIN media_uploads mu ON m.user_id = mu.user_id AND m.date = mu.date
        LEFT JOIN mood_tags t ON m.user_id = t.user_id AND m.date = t.date
        WHERE m.user_id = :uid 
        AND YEAR(m.date) = :year 
        AND MONTH(m.date) = :month
        GROUP BY m.date
        ORDER BY m.date ASC';
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':uid' => $user_id, ':year' => $year, ':month' => $month]);
    $moods = $stmt->fetchAll();
    
    echo json_encode(['moods' => $moods]);
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
?>
