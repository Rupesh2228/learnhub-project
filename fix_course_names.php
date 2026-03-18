<?php
// ============================================
// FIX COURSE NAMES MAPPING
// Run this to sync course names between orders and courses table
// ============================================

require_once(__DIR__ . '/config/Database.php');

$conn = getDB();

echo "<h2>Fixing Course Names...</h2>";

// Mapping of old course names to new standardized names
$course_mapping = [
    'Full Stack Development' => 'Fullstack Development',
    'AI / Machine Learning' => 'AI/ML',
    'Cybersecurity' => 'Cybersecurity',
    'UI / UX Design' => 'UI/UX Design',
    'MERN Stack' => 'MERN Stack',
];

foreach($course_mapping as $old_name => $new_name) {
    // Update orders table
    $update_orders = $conn->prepare("UPDATE orders SET course_name = ? WHERE course_name = ?");
    $update_orders->bind_param("ss", $new_name, $old_name);
    $update_orders->execute();
    $orders_updated = $update_orders->affected_rows;
    $update_orders->close();
    
    // Update assignments table if course_name column exists
    $update_assignments = $conn->prepare("UPDATE assignments SET course_name = ? WHERE course_name = ?");
    $update_assignments->bind_param("ss", $new_name, $old_name);
    $update_assignments->execute();
    $assignments_updated = $update_assignments->affected_rows;
    $update_assignments->close();
    
    if ($orders_updated > 0 || $assignments_updated > 0) {
        echo "<p style='color: green;'>✓ Mapped '" . htmlspecialchars($old_name) . "' → '" . htmlspecialchars($new_name) . "' (Orders: $orders_updated, Assignments: $assignments_updated)</p>";
    }
}

// Verify all course names in orders exist in courses table
$orders = $conn->query("SELECT DISTINCT course_name FROM orders");
$missing_courses = [];

while($row = $orders->fetch_assoc()) {
    $course_name = $row['course_name'];
    $check = $conn->prepare("SELECT id FROM courses WHERE course_name = ?");
    $check->bind_param("s", $course_name);
    $check->execute();
    if($check->get_result()->num_rows === 0) {
        $missing_courses[] = $course_name;
    }
    $check->close();
}

if(count($missing_courses) > 0) {
    echo "<p style='color: orange;'><strong>⚠ Warning: Found course names in orders table that don't exist in courses table:</strong></p>";
    foreach($missing_courses as $course) {
        echo "<p>   - " . htmlspecialchars($course) . "</p>";
    }
} else {
    echo "<p style='color: green;'><strong>✓ All course names are now synchronized!</strong></p>";
}

echo "<hr>";
echo "<p><a href='landingpage/Userdashboard/dashboard.php'>Go to Dashboard</a></p>";
