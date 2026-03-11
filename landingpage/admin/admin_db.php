<?php
// Include path configuration
require_once(__DIR__ . '/../paths.php');
require_once(__DIR__ . '/../../config/Database.php');
require_once(__DIR__ . '/../../config/Security.php');

$conn = getDB();
Security::initializeSession();

?>