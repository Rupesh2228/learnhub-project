<?php
include 'admin_check.php';
include "admin_db.php";

// Get assignment ID
$assignment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($assignment_id > 0) {
    // Get assignment details first
    $stmt = $conn->prepare("SELECT image FROM assignments WHERE id = ?");
    $stmt->bind_param("i", $assignment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $assignment = $result->fetch_assoc();
    
    if ($assignment) {
        // Delete the image file if it exists
        if ($assignment['image']) {
            $image_path = ASSIGNMENTS_DIR . '/' . $assignment['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Delete the assignment from database
        $delete_stmt = $conn->prepare("DELETE FROM assignments WHERE id = ?");
        $delete_stmt->bind_param("i", $assignment_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "Assignment deleted successfully!";
        } else {
            $_SESSION['error_message'] = "Error deleting assignment: " . $conn->error;
        }
        $delete_stmt->close();
    } else {
        $_SESSION['error_message'] = "Assignment not found!";
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid assignment ID!";
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit();
?>
