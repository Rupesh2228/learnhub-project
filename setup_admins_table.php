<?php
// ============================================
// ADMIN TABLE SETUP SCRIPT
// Run this file once to create admins table
// ============================================

require_once(__DIR__ . '/config/Database.php');
require_once(__DIR__ . '/config/Security.php');

$conn = getDB();

echo "<h2>Setting up Admin Table...</h2>";

// Create admins table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Admins table created successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating table: " . $conn->error . "</p>";
    exit();
}

// Check if default admin already exists
$check_admin = $conn->prepare("SELECT id FROM admins WHERE email = ?");
$admin_email = "admin@learnhub.com";
$check_admin->bind_param("s", $admin_email);
$check_admin->execute();
$result = $check_admin->get_result();

if ($result->num_rows > 0) {
    echo "<p style='color: orange;'>⚠ Admin user already exists!</p>";
} else {
    // Insert default admin user
    // Password: admin123 (hashed with bcrypt)
    $hashed_password = password_hash("admin123", PASSWORD_BCRYPT);
    
    $insert_stmt = $conn->prepare("INSERT INTO admins (email, password) VALUES (?, ?)");
    $insert_stmt->bind_param("ss", $admin_email, $hashed_password);
    
    if ($insert_stmt->execute()) {
        echo "<p style='color: green;'>✓ Default admin user created!</p>";
        echo "<p><strong>Email:</strong> admin@learnhub.com</p>";
        echo "<p><strong>Password:</strong> Check in .env file or contact administrator. Password is hashed.</p>";
    } else {
        echo "<p style='color: red;'>✗ Error inserting admin user: " . $conn->error . "</p>";
    }
    $insert_stmt->close();
}

$check_admin->close();
$conn->close();

echo "<hr>";
echo "<p><strong>Setup Complete!</strong> You can now use the dual login system.</p>";
echo "<p><a href='landingpage/login-signup/login_signup.html'>Go to Login Page</a></p>";
?>
