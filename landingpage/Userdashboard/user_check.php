<?php
// ============================================
// USER SECURITY CHECK
// Include this file at the top of every user dashboard page
// ============================================

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is a regular user (not admin)
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    echo "<script>
    alert('Access Denied! User login required.');
    window.location.href='../login-signup/login_signup.html';
    </script>";
    exit();
}

// Check if user_id exists in session
if (!isset($_SESSION['user_id'])) {
    echo "<script>
    alert('Session Error! Please login again.');
    window.location.href='../login-signup/login_signup.html';
    </script>";
    exit();
}

// User is authenticated
?>