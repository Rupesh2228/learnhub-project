<?php
include 'admin_check.php';
include "admin_db.php";

// Get all assignments with their image paths
$assignments = $conn->query("SELECT id, image FROM assignments WHERE image IS NOT NULL ORDER BY id DESC LIMIT 20");

echo "<h2>Assignment Image Path Debug</h2>";
echo "<p>Checking image accessibility from admin dashboard...</p>";
echo "<hr>";

if ($assignments->num_rows > 0) {
    while($assignment = $assignments->fetch_assoc()) {
        $image_name = htmlspecialchars($assignment['image']);
        $relative_path = "../../assignments/" . $image_name;
        $absolute_path = __DIR__ . "/../../assignments/" . $image_name;
        $full_url = "/hlo/assignments/" . $image_name;
        
        // Check if file exists
        $file_exists = file_exists($absolute_path) ? "✓ YES" : "✗ NO";
        
        echo "<div style='border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
        echo "<p><strong>Assignment ID:</strong> " . $assignment['id'] . "</p>";
        echo "<p><strong>Image Name:</strong> " . $image_name . "</p>";
        echo "<p><strong>Relative Path:</strong> " . $relative_path . "</p>";
        echo "<p><strong>Absolute Path:</strong> " . $absolute_path . "</p>";
        echo "<p><strong>File Exists (filesystem):</strong> " . $file_exists . "</p>";
        
        if (file_exists($absolute_path)) {
            $file_size = filesize($absolute_path);
            echo "<p><strong>File Size:</strong> " . ($file_size / 1024) . " KB</p>";
            echo "<p><strong>Web URL:</strong> <a href='$full_url' target='_blank'>$full_url</a></p>";
            echo "<p><strong>Preview:</strong><br>";
            echo "<img src='$relative_path' style='max-width: 200px; border: 2px solid #ddd; padding: 5px;' alt='Preview' onerror='this.src=\"https://via.placeholder.com/200?text=Image+Error\"'>";
            echo "</p>";
        }
        
        echo "</div>";
    }
} else {
    echo "<p style='color: #999;'>No assignments with images found.</p>";
}

$conn->close();
?>
