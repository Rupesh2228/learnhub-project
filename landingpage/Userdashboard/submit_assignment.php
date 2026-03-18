<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');
$course_id = intval($_POST['course_id'] ?? 0);
$course_name = trim($_POST['course_name'] ?? '');
$assignment_file = '';

// Validate input
if (!$title || !$course_name) {
    $_SESSION['error_message'] = "Please fill all required fields!";
    header("Location: dashboard.php");
    exit();
}

// Resolve course_id from course name if missing
if (!$course_id && $course_name) {
    $course_lookup = $conn->prepare("SELECT id FROM courses WHERE course_name = ? LIMIT 1");
    $course_lookup->bind_param('s', $course_name);
    $course_lookup->execute();
    $course_row = $course_lookup->get_result()->fetch_assoc();
    $course_lookup->close();
    $course_id = intval($course_row['id'] ?? 0);
}

if (!$course_id) {
    $_SESSION['error_message'] = "Unable to resolve course. Please try again.";
    header("Location: dashboard.php");
    exit();
}

// Prevent duplicate submissions for same course
if ($course_id > 0) {
    $check_stmt = $conn->prepare("SELECT id FROM assignments WHERE user_id = ? AND course_id = ? LIMIT 1");
    $check_stmt->bind_param("ii", $user_id, $course_id);
} else {
    $check_stmt = $conn->prepare("SELECT id FROM assignments WHERE user_id = ? AND course_name = ? LIMIT 1");
    $check_stmt->bind_param("is", $user_id, $course_name);
}
$check_stmt->execute();
$check_res = $check_stmt->get_result();
if ($check_res->num_rows > 0) {
    $_SESSION['error_message'] = "You have already submitted this assignment.";
    header("Location: dashboard.php");
    exit();
}
$check_stmt->close();

// Handle file upload
if (empty($_FILES['assignment_img']['name'])) {
    $_SESSION['error_message'] = "Please upload your assignment file.";
    header("Location: dashboard.php");
    exit();
}

if (!empty($_FILES['assignment_img']['name'])) {
    $tmp = $_FILES['assignment_img']['tmp_name'];
    $file_name = $_FILES['assignment_img']['name'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','pdf','docx'];

    if (!in_array($ext, $allowed)) {
        $_SESSION['error_message'] = "Invalid file type!";
        header("Location: dashboard.php");
        exit();
    }

    $assignment_file = uniqid() . "_" . time() . "." . $ext;
    $upload_dir = ASSIGNMENTS_DIR . '/';

    if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);

    if (!move_uploaded_file($tmp, $upload_dir . $assignment_file)) {
        $_SESSION['error_message'] = "Failed to upload file.";
        header("Location: dashboard.php");
        exit();
    }

    chmod($upload_dir . $assignment_file, 0644);
}

// Insert assignment
$sql = "INSERT INTO assignments (user_id, course_id, course_name, file_path, submitted_at) 
        VALUES (?, ?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $course_id, $course_name, $assignment_file);
if ($stmt->execute()) {
    $_SESSION['success_message'] = "Assignment submitted successfully!";
} else {
    $_SESSION['error_message'] = "Database error: " . $stmt->error;
}
$stmt->close();

header("Location: dashboard.php");
exit();
?>