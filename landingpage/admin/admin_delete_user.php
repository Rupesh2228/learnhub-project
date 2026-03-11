<?php
include 'admin_check.php';
include "admin_db.php";

// Get user ID
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id > 0) {
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User deleted successfully!";
    } else {
        $_SESSION['error_message'] = "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}

header("Location: admin_user.php");
exit();
?>