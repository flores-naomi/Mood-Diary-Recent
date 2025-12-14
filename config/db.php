<?php
// config/db.php
declare(strict_types=1);

function getPDO(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        // Get the full MySQL URL from Railway environment variable
        $databaseUrl = getenv('mysql://root:RwYKZSFEKSQwKKQfKtqHJaEZzmyDttdY@mysql.railway.internal:3306/railway');

        if (!$databaseUrl) {
            die("Database URL not set in environment variables.");
        }

        // Parse the URL into components
        $urlParts = parse_url($databaseUrl);

        $host = $urlParts['host'] ?? '';
        $port = $urlParts['port'] ?? 3306;
        $dbname = ltrim($urlParts['path'] ?? '', '/'); // Remove leading slash
        $user = $urlParts['user'] ?? '';
        $pass = $urlParts['pass'] ?? '';

        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, $user, $pass, $options);
    }

    return $pdo;
}
