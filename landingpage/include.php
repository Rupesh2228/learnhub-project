<?php
// Include path configuration
require_once(__DIR__ . '/paths.php');
require_once(__DIR__ . '/../config/Database.php');

$conn = getDB();
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

?>