<?php
/**
 * Security Configuration & Headers
 */

class Security {
    public static function initializeSession() {
        // Check if session is already active
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $options = [
            'cookie_httponly' => true,
            'cookie_samesite' => getenv('SESSION_SAMESITE') ?: 'Strict',
        ];

        // Set secure flag only in HTTPS
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            $options['cookie_secure'] = true;
        }

        session_start($options);

        // Regenerate session ID on login (call explicitly from login.php)
        // Prevent session fixation attacks
    }

    public static function setSecurityHeaders() {
        // Prevent MIME type sniffing
        header("X-Content-Type-Options: nosniff");

        // Prevent clickjacking
        header("X-Frame-Options: DENY");

        // Legacy XSS protection
        header("X-XSS-Protection: 1; mode=block");

        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");

        // Referrer Policy
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Don't cache sensitive pages
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Pragma: no-cache");
    }

    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        return trim(stripslashes($input));
    }

    public static function escapeOutput($output) {
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCSRFToken($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function validateFileUpload($file, $maxSize = null, $allowedTypes = []) {
        $maxSize = $maxSize ?: (int)getenv('MAX_FILE_SIZE') ?: 5242880; // 5MB default

        // Check if file exists
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['error' => 'Invalid file upload'];
        }

        // Check file size
        if ($file['size'] > $maxSize) {
            return ['error' => 'File size exceeds limit'];
        }

        // Check MIME type if allowed types specified
        if (!empty($allowedTypes)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowedTypes, true)) {
                return ['error' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes)];
            }
        }

        return ['success' => true];
    }
}

// Initialize security on every page
Security::setSecurityHeaders();
?>
