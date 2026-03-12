<?php
session_start();
require_once('../include.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = Security::sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    // Check admin first
    $admin_stmt = $conn->prepare("SELECT id,email,password FROM admins WHERE email=?");
    $admin_stmt->bind_param("s",$email);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();

    if($admin_result->num_rows == 1){

        $admin = $admin_result->fetch_assoc();

        if(password_verify($password,$admin['password'])){

            session_regenerate_id(true);

            $_SESSION['user_id']=$admin['id'];
            $_SESSION['user_email']=$email;
            $_SESSION['role']='admin';
            $_SESSION['is_admin']=true;

            header("Location: ../admin/admin_dashboard.php");
            exit();
        }
    }
    $admin_stmt->close();


    // Check normal user
    $user_stmt = $conn->prepare("SELECT id,full_name,password FROM users WHERE email=?");
    $user_stmt->bind_param("s",$email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if($user_result->num_rows == 1){

        $user = $user_result->fetch_assoc();

        if(password_verify($password,$user['password'])){

            session_regenerate_id(true);

            $_SESSION['user_id']=$user['id'];
            $_SESSION['user_name']=$user['full_name'];
            $_SESSION['user_email']=$email;
            $_SESSION['role']='user';
            $_SESSION['is_admin']=false;

            // profile image
            $profile_stmt=$conn->prepare("SELECT profile_image FROM user_profiles WHERE user_id=?");
            $profile_stmt->bind_param("i",$user['id']);
            $profile_stmt->execute();
            $profile_result=$profile_stmt->get_result();

            if($profile_result->num_rows>0){
                $profile=$profile_result->fetch_assoc();
                $_SESSION['user_image']=$profile['profile_image'];
            }else{
                $_SESSION['user_image']='default.png';
            }

            header("Location: ../Userdashboard/dashboard.php");
            exit();
        }
    }

    $user_stmt->close();

    $_SESSION['error_message']="Invalid email or password!";
    header("Location: login_signup.php");

    $conn->close();
}
?>