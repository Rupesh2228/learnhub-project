<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// Get form data
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';

// Handle Profile Image Upload
$image = $_FILES['profile_image']['name'];
$tmp = $_FILES['profile_image']['tmp_name'];
$upload_dir = UPLOADS_DIR . '/';

// Ensure directory exists
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$success_message = "";
$error_message = "";

// Security: Check if file is an image
if ($image) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_ext)) {
        // Generate unique filename to prevent overwriting
        $new_filename = uniqid() . "_" . time() . "." . $file_ext;
        $upload_path = $upload_dir . $new_filename;
        
        if (file_exists($upload_path)) {
            $new_filename = uniqid() . "_" . time() . "_" . $file_ext;
            $upload_path = $upload_dir . $new_filename;
        }
        
        if (move_uploaded_file($tmp, $upload_path)) {
            $image = $new_filename;
            $success_message = "Profile image updated successfully!";
        } else {
            $error_message = "Error uploading image file. Please check folder permissions.";
        }
    } else {
        $error_message = "Invalid file type. Only JPG, PNG, and GIF allowed.";
    }
}

// Update Users Table (Name and Email)
if ($full_name || $email) {
    $sql_users = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    $stmt_users = $conn->prepare($sql_users);
    
    if ($stmt_users) {
        $stmt_users->bind_param("ssi", $full_name, $email, $user_id);
        if ($stmt_users->execute()) {
            if (!$success_message) {
                $success_message = "User details updated successfully!";
            }
        } else {
            $error_message = "Error updating user details: " . $stmt_users->error;
        }
        $stmt_users->close();
    }
}

// Update User Profiles Table (DOB and Image)
if ($image || $dob) {
    if ($image) {
        $sql_profiles = "INSERT INTO user_profiles (user_id, profile_image, date_of_birth, updated_at)
                        VALUES (?, ?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        profile_image = VALUES(profile_image), 
                        date_of_birth = VALUES(date_of_birth), 
                        updated_at = NOW()";
        
        $stmt = $conn->prepare($sql_profiles);
        $stmt->bind_param("iss", $user_id, $image, $dob);
    } else {
        $sql_profiles = "INSERT INTO user_profiles (user_id, date_of_birth, updated_at)
                        VALUES (?, ?, NOW())
                        ON DUPLICATE KEY UPDATE 
                        date_of_birth = VALUES(date_of_birth), 
                        updated_at = NOW()";
        
        $stmt = $conn->prepare($sql_profiles);
        $stmt->bind_param("is", $user_id, $dob);
    }

    if ($stmt) {
        if ($stmt->execute()) {
            if (!$success_message) {
                $success_message = "Profile details updated successfully!";
            }
        } else {
            $error_message = "Error updating profile: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Failed to prepare statement: " . $conn->error;
    }
}

// Store success/error message in session
if ($success_message) {
    $_SESSION['success_message'] = $success_message;
}
if ($error_message) {
    $_SESSION['error_message'] = $error_message;
}

// Redirect to dashboard
header("Location: dashboard.php");
exit();
?>