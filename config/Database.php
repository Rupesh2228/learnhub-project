<?php
/**
 * Secure Database Configuration
 * Load credentials from .env file
 */

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        // Load environment variables
        $envFile = __DIR__ . '/../.env';
        if (!file_exists($envFile)) {
            throw new Exception(".env file not found at: " . $envFile);
        }

        $this->loadEnv($envFile);

        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dbname = getenv('DB_NAME') ?: 'learnhub_db';

        // Create connection
        $this->conn = new mysqli($host, $user, $pass, $dbname);

        // Check connection
        if ($this->conn->connect_error) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            throw new Exception("Database connection failed. Please contact administrator.");
        }

        // Set charset
        $this->conn->set_charset("utf8mb4");
    }

    private function loadEnv($filePath) {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                if ($key && $value) {
                    putenv("$key=$value");
                }
            }
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}

// Create shortcut function
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
