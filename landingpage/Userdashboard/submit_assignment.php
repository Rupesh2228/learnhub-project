<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// Get form data
$title = isset($_POST['title']) ? trim($_POST['title']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$course_name = isset($_POST['course_name']) ? trim($_POST['course_name']) : '';

// Get course_id from courses table using course_name
$course_id = null;
if ($course_name) {
    $course_stmt = $conn->prepare("SELECT id FROM courses WHERE course_name = ?");
    $course_stmt->bind_param("s", $course_name);
    $course_stmt->execute();
    $course_result = $course_stmt->get_result();
    
    // DEBUG: Log what we're searching for and what we find
    error_log("DEBUG: Looking for course_name: '$course_name'");
    error_log("DEBUG: Found " . $course_result->num_rows . " rows");
    
    if ($course_result->num_rows > 0) {
        $course_row = $course_result->fetch_assoc();
        $course_id = $course_row['id'];
        error_log("DEBUG: Found course_id: $course_id");
    } else {
        // Debug: show all available courses
        $all_courses = $conn->query("SELECT id, course_name FROM courses");
        error_log("DEBUG: Available courses in database:");
        while($row = $all_courses->fetch_assoc()) {
            error_log("  - ID: " . $row['id'] . ", Name: '" . $row['course_name'] . "'");
        }
    }
    $course_stmt->close();
}

// Handle File Upload
$assignment_file = $_FILES['assignment_img']['name'] ?? '';
$tmp = $_FILES['assignment_img']['tmp_name'] ?? '';
$upload_dir = ASSIGNMENTS_DIR . '/';

// Ensure directory exists with secure permissions
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$success_message = "";
$error_message = "";

// Security: Validate file upload
if ($assignment_file && $tmp) {
    // Validate upload
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $validation = Security::validateFileUpload($_FILES['assignment_img'], null, $allowedTypes);
    
    if (isset($validation['error'])) {
        $error_message = $validation['error'];
    } else {
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($assignment_file, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            // Generate unique filename to prevent overwriting
            $new_filename = uniqid() . "_" . time() . "." . $file_ext;
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($tmp, $upload_path)) {
                $assignment_file = $new_filename;
                // Set secure file permissions
                chmod($upload_path, 0644);
            } else {
                $error_message = "Error uploading file. Please check folder permissions.";
            }
        } else {
            $error_message = "Invalid file type. Only JPG, PNG, GIF allowed.";
        }
    }
}

// Insert into Assignments Table
if ($title && $course_id) {
    // Insert with course_id from courses table
    $sql = "INSERT INTO assignments (user_id, course_id, title, description, submitted_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("iiss", $user_id, $course_id, $title, $description);
        
        if ($stmt->execute()) {
            // Get the last inserted ID
            $last_id = $conn->insert_id;
            
            // Update the image field if file was uploaded
            if ($assignment_file) {
                $update_sql = "UPDATE assignments SET image = ? WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $assignment_file, $last_id);
                $update_stmt->execute();
                $update_stmt->close();
            }
            
            $_SESSION['success_message'] = "Assignment submitted successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Error submitting assignment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Failed to prepare statement: " . $conn->error;
    }
} else {
    if (!$title) {
        $_SESSION['error_message'] = "Assignment title is required!";
    } elseif (!$course_id) {
        $_SESSION['error_message'] = "Course not found!";
    }
}

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>