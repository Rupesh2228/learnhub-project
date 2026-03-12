<?php
session_start();
require_once('../include.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = isset($_POST['fullname']) ? Security::sanitizeInput($_POST['fullname']) : '';
    $email = isset($_POST['email']) ? Security::sanitizeInput($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $dob = isset($_POST['date_of_birth']) ? Security::sanitizeInput($_POST['date_of_birth']) : '';

    // Required fields
    if (!$name || !$email || !$password) {
        $_SESSION['error_message'] = "Please fill all required fields!";
        header("Location: login_signup.php");
        exit();
    }

    // Password match
    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Passwords do not match!";
        header("Location: login_signup.php");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check email
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check_stmt->bind_param("s",$email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if($result->num_rows > 0){
        $_SESSION['error_message']="Email already exists!";
        header("Location: login_signup.php");
        exit();
    }
    $check_stmt->close();

    // Insert user
    $insert_stmt = $conn->prepare("INSERT INTO users (full_name,email,password) VALUES (?,?,?)");
    $insert_stmt->bind_param("sss",$name,$email,$hashed_password);

    if($insert_stmt->execute()){

        $user_id = $conn->insert_id;

        $default_image="default.png";

        $profile_stmt = $conn->prepare("INSERT INTO user_profiles (user_id,profile_image,date_of_birth) VALUES (?,?,?)");
        $profile_stmt->bind_param("iss",$user_id,$default_image,$dob);
        $profile_stmt->execute();
        $profile_stmt->close();

        echo "<script>alert('Account created successfully');window.location='../landingpages.html';</script>";

    }else{
        echo "<script>alert('Error creating account');window.location='login_signup.php';</script>";
    }

    $insert_stmt->close();
    $conn->close();
}
?>