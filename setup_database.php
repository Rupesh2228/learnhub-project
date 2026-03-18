<?php
// ============================================
// COMPLETE DATABASE SETUP SCRIPT
// Run this file once to set up the entire database
// ============================================

require_once(__DIR__ . '/config/Database.php');

$conn = getDB();

echo "<h2 style='color: #4f46e5;'>LearnHub Database Setup</h2>";
echo "<hr>";

// Array to track creation status
$tables_created = [];

// 1. CREATE ADMINS TABLE
echo "<h3>Creating Admins Table...</h3>";
$sql_admins = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_admins) === TRUE) {
    echo "<p style='color: green;'>✓ Admins table created</p>";
    $tables_created[] = 'admins';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 2. CREATE USERS TABLE
echo "<h3>Creating Users Table...</h3>";
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql_users) === TRUE) {
    echo "<p style='color: green;'>✓ Users table created</p>";
    $tables_created[] = 'users';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 3. CREATE USER PROFILES TABLE
echo "<h3>Creating User Profiles Table...</h3>";
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
    echo "<p style='color: green;'>✓ User profiles table created</p>";
    $tables_created[] = 'user_profiles';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 4. CREATE COURSES TABLE
echo "<h3>Creating Courses Table...</h3>";
$sql_courses = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_courses) === TRUE) {
    echo "<p style='color: green;'>✓ Courses table created</p>";
    $tables_created[] = 'courses';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 5. CREATE ASSIGNMENTS TABLE
echo "<h3>Creating Assignments Table...</h3>";
$sql_assignments = "CREATE TABLE IF NOT EXISTS assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    file_path VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";

if ($conn->query($sql_assignments) === TRUE) {
    echo "<p style='color: green;'>✓ Assignments table created</p>";
    $tables_created[] = 'assignments';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 6. CREATE ORDERS TABLE
echo "<h3>Creating Orders Table...</h3>";
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    course_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";
if ($conn->query($sql_orders) === TRUE) {
    echo "<p style='color: green;'>✓ Orders table created</p>";
    $tables_created[] = 'orders';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 7. CREATE QUIZZES TABLE
echo "<h3>Creating Quizzes Table...</h3>";
$sql_quizzes = "CREATE TABLE IF NOT EXISTS quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    title VARCHAR(255) DEFAULT 'Course Quiz',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
)";
if ($conn->query($sql_quizzes) === TRUE) {
    echo "<p style='color: green;'>✓ Quizzes table created</p>";
    $tables_created[] = 'quizzes';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 8. CREATE QUIZ QUESTIONS TABLE
echo "<h3>Creating Quiz Questions Table...</h3>";
$sql_questions = "CREATE TABLE IF NOT EXISTS quiz_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT NOT NULL,
    question TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
)";
if ($conn->query($sql_questions) === TRUE) {
    echo "<p style='color: green;'>✓ Quiz questions table created</p>";
    $tables_created[] = 'quiz_questions';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 9. CREATE QUIZ RESULTS TABLE
echo "<h3>Creating Quiz Results Table...</h3>";
$sql_results = "CREATE TABLE IF NOT EXISTS quiz_results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT NOT NULL,
    total_questions INT NOT NULL,
    percentage DECIMAL(5,2) NOT NULL,
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
)";
if ($conn->query($sql_results) === TRUE) {
    echo "<p style='color: green;'>✓ Quiz results table created</p>";
    $tables_created[] = 'quiz_results';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// 10. CREATE QUIZ ANSWERS TABLE
echo "<h3>Creating Quiz Answers Table...</h3>";
$sql_answers = "CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    user_answer ENUM('A','B','C','D') NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (result_id) REFERENCES quiz_results(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
)";
if ($conn->query($sql_answers) === TRUE) {
    echo "<p style='color: green;'>✓ Quiz answers table created</p>";
    $tables_created[] = 'quiz_answers';
} else {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
}

// Summary
echo "<hr>";
echo "<h3 style='color: #10b981;'>✓ Setup Complete!</h3>";
echo "<p><strong>Tables created:</strong> " . implode(", ", $tables_created) . "</p>";
echo "<p style='color: #4f46e5;'><a href='../landingpage/landingpages.php'>← Go to Homepage</a></p>";

$conn->close();
?>
