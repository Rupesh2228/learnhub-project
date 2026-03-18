<?php
require_once('../include.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // ============================================
    // STEP 1: Check if user is an ADMIN
    // ============================================
    $admin_stmt = $conn->prepare("SELECT id, email, password FROM admins WHERE email = ?");
    $admin_stmt->bind_param("s", $email);
    $admin_stmt->execute();
    $admin_result = $admin_stmt->get_result();

    if ($admin_result->num_rows === 1) {
        $admin = $admin_result->fetch_assoc();

        // Verify admin password
        if (password_verify($password, $admin['password'])) {
            // ============================================
            // ADMIN LOGIN SUCCESSFUL
            // ============================================
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'admin';
            $_SESSION['is_admin'] = true;

            echo "<script>
            alert('Admin Login Successful!');
            window.location.href='../admin/admin_dashboard.php';
            </script>";
            exit();
        } else {
            echo "<script>
            alert('Invalid password!');
            window.location.href='login_signup.php';
            </script>";
            $admin_stmt->close();
            $conn->close();
            exit();
        }
    }
    $admin_stmt->close();

    // ============================================
    // STEP 2: Check if user is a REGULAR USER
    // ============================================
    $user_stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
    $user_stmt->bind_param("s", $email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 1) {
        $user = $user_result->fetch_assoc();

        // Verify user password
        if (password_verify($password, $user['password'])) {
            // ============================================
            // USER LOGIN SUCCESSFUL
            // ============================================
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $email;
            $_SESSION['role'] = 'user';
            $_SESSION['is_admin'] = false;

            // Get profile image from user_profiles table
            $profile_stmt = $conn->prepare("SELECT profile_image FROM user_profiles WHERE user_id = ?");
            $profile_stmt->bind_param("i", $user['id']);
            $profile_stmt->execute();
            $profile_result = $profile_stmt->get_result();
            
            if ($profile_result->num_rows > 0) {
                $profile = $profile_result->fetch_assoc();
                $_SESSION['user_image'] = $profile['profile_image'];
            } else {
                $_SESSION['user_image'] = 'default.png';
            }
            $profile_stmt->close();

            // Redirect to user dashboard
            echo "<script>
            alert('Login Successful!');
            window.location.href='../Userdashboard/dashboard.php';
            </script>";
            exit();
        } else {
            echo "<script>
            alert('Invalid password!');
            window.location.href='login_signup.php';
            </script>";
            $user_stmt->close();
            $conn->close();
            exit();
        }
    }
    $user_stmt->close();

    // ============================================
    // NO MATCH FOUND (neither admin nor user)
    // ============================================
    echo "<script>
    alert('Email not found! Please sign up first.');
    window.location.href='login_signup.php';
    </script>";
    $conn->close();
    exit();
}
?>