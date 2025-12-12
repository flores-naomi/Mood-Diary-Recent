<?php
// api/save_mood.php
declare(strict_types=1);
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
// start session and require a logged-in user
if (session_status() === PHP_SESSION_NONE) session_start();
$userId = $_SESSION['user_id'] ?? null;
if ($userId === null) {
    http_response_code(401);
    echo json_encode(['error' => 'Authentication required']);
    exit;
}

$maxPayload = 10 * 1024; // limit payload size (10KB)
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > $maxPayload) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid payload']);
    exit;
}

$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

// Whitelist and validate fields:
$face_emotion = isset($data['face_emotion']) ? trim(substr($data['face_emotion'], 0, 64)) : null;
$face_confidence = isset($data['face_confidence']) ? floatval($data['face_confidence']) : null;
$audio_emotion = isset($data['audio_emotion']) ? trim(substr($data['audio_emotion'], 0, 64)) : null;
$audio_score = isset($data['audio_score']) ? floatval($data['audio_score']) : null;
$combined_score = isset($data['combined_score']) ? intval($data['combined_score']) : null;
$date = isset($data['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date']) ? $data['date'] : date('Y-m-d');
$time = isset($data['time']) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $data['time']) ? $data['time'] : date('H:i:s');
$diary_id = null;
$metaArr = [];
if (isset($data['meta']) && is_array($data['meta'])) {
    $metaArr = $data['meta'];
    if (isset($metaArr['diary_id'])) {
        $diary_id = intval($metaArr['diary_id']);
        // remove diary_id from meta payload to avoid duplication
        unset($metaArr['diary_id']);
    }
}
$meta = !empty($metaArr) ? json_encode($metaArr) : null;

if ($combined_score === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing combined_score']);
    exit;
}

try {
    $pdo = getPDO();

    // Insert with user_id (session guaranteed above)
    try {
        // include diary_id if provided
        $cols = 'user_id, date, time, face_emotion, face_confidence, audio_emotion, audio_score, combined_score';
        $vals = ':user_id, :date, :time, :face_emotion, :face_confidence, :audio_emotion, :audio_score, :combined_score';
        if ($diary_id !== null) { $cols .= ', diary_id'; $vals .= ', :diary_id'; }
        if ($meta !== null) { $cols .= ', meta'; $vals .= ', :meta'; }

        $sql = "INSERT INTO mood_logs ({$cols}) VALUES ({$vals})";
        $stmt = $pdo->prepare($sql);
        $params = [
            ':user_id' => $userId,
            ':date' => $date,
            ':time' => $time,
            ':face_emotion' => $face_emotion,
            ':face_confidence' => $face_confidence,
            ':audio_emotion' => $audio_emotion,
            ':audio_score' => $audio_score,
            ':combined_score' => $combined_score
        ];
        if ($diary_id !== null) $params[':diary_id'] = $diary_id;
        if ($meta !== null) $params[':meta'] = $meta;
        $stmt->execute($params);
    } catch (Exception $e) {
        // Fallback: try a simpler insert without user_id
        $cols = 'date, time, face_emotion, face_confidence, audio_emotion, audio_score, combined_score';
        $vals = ':date, :time, :face_emotion, :face_confidence, :audio_emotion, :audio_score, :combined_score';
        if ($diary_id !== null) { $cols .= ', diary_id'; $vals .= ', :diary_id'; }
        if ($meta !== null) { $cols .= ', meta'; $vals .= ', :meta'; }
        $sql = "INSERT INTO mood_logs ({$cols}) VALUES ({$vals})";
        $stmt = $pdo->prepare($sql);
        $params = [
            ':date' => $date,
            ':time' => $time,
            ':face_emotion' => $face_emotion,
            ':face_confidence' => $face_confidence,
            ':audio_emotion' => $audio_emotion,
            ':audio_score' => $audio_score,
            ':combined_score' => $combined_score
        ];
        if ($diary_id !== null) $params[':diary_id'] = $diary_id;
        if ($meta !== null) $params[':meta'] = $meta;
        $stmt->execute($params);
    }

    echo json_encode(['ok' => true, 'id' => (int)$pdo->lastInsertId()]);
} catch (Exception $e) {
    http_response_code(500);
    // don't leak exception messages in production; log them instead
    error_log($e->getMessage());
    echo json_encode(['error' => 'Server error']);
}
