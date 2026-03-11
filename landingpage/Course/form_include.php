<?php
require_once(__DIR__ . '/../include.php');

// For backward compatibility with PDO code in this directory
class CompatPDO {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $conn = getDB();
        // Create a PDO wrapper by simulating PDO methods
        $this->pdo = null;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function prepare($sql) {
        return getDB()->prepare($sql);
    }
}

// Create PDO-compatible connection
$pdo = null;
try {
    // Alternative: Use MySQLi directly as it's already set up
    // For PDO compatibility, we'll create a wrapper
    $host = getenv('DB_HOST') ?: 'localhost';
    $db   = getenv('DB_NAME') ?: 'learnhub_db';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    error_log("Database connection failed: ".$e->getMessage());
    die("Database connection error. Please contact administrator.");
}
?>