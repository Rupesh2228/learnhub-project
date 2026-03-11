<?php
// ============================================
// USER SECURITY CHECK
// Include this file at the top of every user dashboard page
// ============================================

session_start();

// Check if user is logged in and is a regular user (not admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    // Not a regular user - redirect to login
    echo "<script>
    alert('Access Denied! User login required.');
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

// User is authenticated as regular user
?>
