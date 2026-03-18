<?php
require_once(__DIR__ . '/config/Database.php');

$conn = getDB();

echo "<h2>Database Course Diagnosis</h2>";
echo "<hr>";

// Show all courses in courses table
echo "<h3>Courses in DATABASE:</h3>";
$courses = $conn->query("SELECT id, course_name FROM courses ORDER BY course_name");
if($courses->num_rows > 0) {
    echo "<ul>";
    while($row = $courses->fetch_assoc()) {
        echo "<li>ID: " . (int)$row['id'] . " | Name: <strong>'" . htmlspecialchars($row['course_name'], ENT_QUOTES, 'UTF-8') . "'</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: red;'><strong>⚠ No courses found in database!</strong></p>";
}

echo "<hr>";

// Show unique course names in orders table
echo "<h3>Course Names in ORDERS table (what users purchased):</h3>";
$orders = $conn->query("SELECT DISTINCT course_name FROM orders ORDER BY course_name");
if($orders->num_rows > 0) {
    echo "<ul>";
    $missing = [];
    while($row = $orders->fetch_assoc()) {
        $course_name = $row['course_name'];
        $check = $conn->prepare("SELECT id FROM courses WHERE course_name = ?");
        $check->bind_param("s", $course_name);
        $check->execute();
        $check_result = $check->get_result();
        
        if($check_result->num_rows > 0) {
            echo "<li style='color: green;'>✓ <strong>'" . htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') . "'</strong> - EXISTS in courses table</li>";
        } else {
            echo "<li style='color: red;'>✗ <strong>'" . htmlspecialchars($course_name, ENT_QUOTES, 'UTF-8') . "'</strong> - MISSING in courses table! (This is the problem)</li>";
            $missing[] = $course_name;
        }
        $check->close();
    }
    echo "</ul>";
    
    if(count($missing) > 0) {
        echo "<h3 style='color: red;'>❌ PROBLEM: These courses need to be added:</h3>";
        echo "<p>Run this SQL to fix:</p>";
        echo "<pre style='background: #f0f0f0; padding: 10px; border-radius: 5px;'>";
        foreach($missing as $course) {
            $escaped = htmlspecialchars(addslashes($course));
            echo "INSERT INTO courses (course_name) VALUES ('".$escaped."');<br>";
        }
        echo "</pre>";
    } else {
        echo "<p style='color: green;'><strong>✓ All course names are synchronized!</strong></p>";
    }
} else {
    echo "<p style='color: orange;'>No orders found yet.</p>";
}

echo "<hr>";
echo "<p><a href='landingpage/Userdashboard/dashboard.php'>Go to Dashboard</a></p>";
