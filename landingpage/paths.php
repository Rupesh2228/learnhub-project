<?php
// ============================================
// PATH CONFIGURATION
// Centralized path definitions for all files
// ============================================

// Get the base directory of the application
// This file is in /hlo/landingpage/, so dirname(__DIR__) = /hlo/
$base_dir = dirname(__DIR__);  // /hlo/
$landingpage_dir = __DIR__;  // /hlo/landingpage/
$admin_dir = $landingpage_dir . '/admin';
$userdashboard_dir = $landingpage_dir . '/Userdashboard';
$loginsignup_dir = $landingpage_dir . '/login-signup';
$assignments_dir = $base_dir . '/assignments';
$uploads_dir = $base_dir . '/uploads';

// Define file paths
define('BASE_DIR', $base_dir);
define('LANDINGPAGE_DIR', $landingpage_dir);
define('ADMIN_DIR', $admin_dir);
define('USERDASHBOARD_DIR', $userdashboard_dir);
define('LOGINSIGNUP_DIR', $loginsignup_dir);
define('ASSIGNMENTS_DIR', $assignments_dir);
define('UPLOADS_DIR', $uploads_dir);

// Define web-accessible paths (relative to document root /hlo/)
define('WEB_ASSIGNMENTS', '/hlo/assignments/');
define('WEB_UPLOADS', '/hlo/uploads/');
define('WEB_ADMIN', '/hlo/landingpage/admin/');
define('WEB_DASHBOARD', '/hlo/landingpage/Userdashboard/');
define('WEB_LOGIN', '/hlo/landingpage/login-signup/');

// Ensure required directories exist with secure permissions
foreach ([ASSIGNMENTS_DIR, UPLOADS_DIR] as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

?>

