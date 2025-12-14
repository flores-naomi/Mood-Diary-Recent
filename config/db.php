<?php
// config/db.php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'mood_tracker';
const DB_USER = 'root';
const DB_PASS = ''; 

function getPDO(): PDO {
    static $pdo = null;
        if ($pdo === null) {
         $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
         $options = [
         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        }
        return $pdo;
       }
