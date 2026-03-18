<?php
require_once('../include.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and validate form inputs
    $name = isset($_POST['fullname']) ? $_POST['fullname'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $dob = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';

    // Validate required fields
    if (!$name || !$email || !$password) {
        echo "<script>alert('Please fill in all required fields!'); window.location.href='login_signup.php';</script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.location.href='login_signup.php';</script>";
        exit();
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='login_signup.php';</script>";
        $check_stmt->close();
        $conn->close();
        exit();
    }
    $check_stmt->close();

    // Insert new user
    $insert_stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    if (!$insert_stmt) {
        echo "<script>alert('Error preparing statement: " . $conn->error . "'); window.location.href='login_signup.html';</script>";
        $conn->close();
        exit();
    }

    $insert_stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($insert_stmt->execute()) {
        // Get the new user ID
        $user_id = $conn->insert_id;

        // Create profile record
        $default_image = 'default.png';
        $profile_stmt = $conn->prepare("INSERT INTO user_profiles (user_id, profile_image, date_of_birth) VALUES (?, ?, ?)");
        
        if ($profile_stmt) {
            $profile_stmt->bind_param("iss", $user_id, $default_image, $dob);
            $profile_stmt->execute();
            $profile_stmt->close();
        }

        echo "<script>alert('Account created successfully!'); window.location.href='../landingpages.html';</script>";
    } else {
        echo "<script>alert('Error creating account: " . htmlspecialchars($insert_stmt->error) . "'); window.location.href='login_signup.html';</script>";
    }
    
    $insert_stmt->close();
    $conn->close();
    exit();
}
?>