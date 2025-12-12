<?php
// api/login.php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$raw = file_get_contents('php://input');
if ($raw && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $data = json_decode($raw, true);
} else {
    $data = $_POST;
}

$username = isset($data['username']) ? trim($data['username']) : '';
$password = isset($data['password']) ? $data['password'] : '';

if ($username === '' || $password === '') {
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid input']);
        exit;
    }
    header('Location: ../login.php?error=1');
    exit;
}

try {
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $row = $stmt->fetch();
    if (!$row || !password_verify($password, $row['password_hash'])) {
        if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            exit;
        }
        header('Location: ../login.php?error=invalid');
        exit;
    }

    $_SESSION['user_id'] = (int)$row['id'];
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        echo json_encode(['ok' => true, 'id' => $_SESSION['user_id']]);
    } else {
        header('Location: ../home.php');
    }
} catch (Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    if (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
        echo json_encode(['error' => 'Server error']);
    } else {
        header('Location: ../login.php?error=server');
    }
}
