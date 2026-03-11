<?php
// ============================================
// ADMIN SECURITY CHECK
// Include this file at the top of every admin page
// ============================================

session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Not an admin - redirect to login
    echo "<script>
    alert('Access Denied! Admin access required.');
    window.location.href='../login-signup/login_signup.html';
    </script>";
    exit();
}

// Check if user_id exists in session
if (!isset($_SESSION['user_id'])) {
    // Session tampered with - redirect to login
    echo "<script>
    alert('Session Error! Please login again.');
    window.location.href='../login-signup/login_signup.html';
    </script>";
    exit();
}

// User is authenticated as admin
?>
