<?php
// ============================================
// USERS TABLE SETUP SCRIPT
// Run this file once to create users table
// ============================================

require_once(__DIR__ . '/config/Database.php');
require_once(__DIR__ . '/config/Security.php');

$conn = getDB();

echo "<h2>Setting up Users Table...</h2>";

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Users table created successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating table: " . htmlspecialchars($conn->error) . "</p>";
    exit();
}

// Create user_profiles table for additional user information
$sql_profiles = "CREATE TABLE IF NOT EXISTS user_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNIQUE NOT NULL,
    profile_image VARCHAR(255) DEFAULT 'default.png',
    phone VARCHAR(20),
    date_of_birth DATE,
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($conn->query($sql_profiles) === TRUE) {
    echo "<p style='color: green;'>✓ User profiles table created successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating user_profiles table: " . htmlspecialchars($conn->error) . "</p>";
    exit();
}

echo "<p style='color: blue;'><strong>✓ All user-related tables are ready!</strong></p>";
echo "<p>You can now start registering users in your application.</p>";

$conn->close();
?>
