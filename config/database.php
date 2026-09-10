<?php
/**
 * Database Configuration & Connection (PDO)
 * Graphic Design Portfolio & CMS
 */

declare(strict_types=1);

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'porto_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function get_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            die('<div style="font-family:sans-serif;padding:2rem;background:#FEF2F2;color:#991B1B;border-radius:8px;max-width:600px;margin:2rem auto;border:1px solid #F87171;">
                <h2 style="margin-top:0;">Database Connection Failed</h2>
                <p>Could not connect to MySQL database <strong>' . htmlspecialchars(DB_NAME) . '</strong>.</p>
                <p><small>' . htmlspecialchars($e->getMessage()) . '</small></p>
            </div>');
        }
    }

    return $pdo;
}
