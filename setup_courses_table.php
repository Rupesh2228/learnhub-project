<?php
// ============================================
// COURSES TABLE SETUP SCRIPT
// Run this file once to create courses table
// ============================================

require_once(__DIR__ . '/config/Database.php');
require_once(__DIR__ . '/config/Security.php');

$conn = getDB();

echo "<h2>Setting up Courses Table...</h2>";

// Create courses table
$sql = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>✓ Courses table created successfully!</p>";
} else {
    echo "<p style='color: red;'>✗ Error creating table: " . htmlspecialchars($conn->error) . "</p>";
    exit();
}

// Insert default courses
$courses = [
    'Fullstack Development',
    'AI/ML',
    'Cybersecurity',
    'UI/UX Design',
    'MERN Stack',
    'Python Programming',
    'Web Development',
    'Data Science',
    'Mobile App Development',
    'Cloud Computing'
];

foreach($courses as $course_name) {
    $check_course = $conn->prepare("SELECT id FROM courses WHERE course_name = ?");
    $check_course->bind_param("s", $course_name);
    $check_course->execute();
    $result = $check_course->get_result();
    
    if ($result->num_rows === 0) {
        $insert_stmt = $conn->prepare("INSERT INTO courses (course_name) VALUES (?)");
        $insert_stmt->bind_param("s", $course_name);
        $insert_stmt->execute();
        echo "<p style='color: green;'>✓ Added course: " . htmlspecialchars($course_name) . "</p>";
        $insert_stmt->close();
    }
    $check_course->close();
}

// Alter assignments table to add course name field (if not exists)
$alter_sql = "ALTER TABLE assignments ADD COLUMN IF NOT EXISTS course_name VARCHAR(255)";
if ($conn->query($alter_sql) === TRUE) {
    echo "<p style='color: green;'>✓ Assignments table updated!</p>";
} else {
    if (strpos($conn->error, "Duplicate column") === false) {
        echo "<p style='color: orange;'>⚠ " . htmlspecialchars($conn->error) . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>Setup Complete!</strong> Courses table created with default courses.</p>";
echo "<p><a href='landingpage/admin/admin_dashboard.php'>Go to Admin Dashboard</a></p>";;
?>
