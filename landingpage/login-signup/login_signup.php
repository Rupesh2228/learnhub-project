<?php
require_once('../include.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login / Sign Up - LearnHub</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="login_signup.css">

</head>

<body>

<div class="auth-container">

<a href="../landingpages.html" class="back-btn" title="Back to Home">
<i class="fas fa-arrow-left"></i>
</a>

<!-- LOGIN FORM -->
<div id="login-form">

<div class="auth-header">
<h2>Welcome Back</h2>
<p>Please enter your details to sign in</p>
</div>

<form action="login.php" method="POST">

<div class="form-group">
<label>Email Address</label>
<input type="email" name="email" placeholder="Enter your email" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" id="login-password" placeholder="Enter your password" required>

<i class="fas fa-eye password-toggle" onclick="togglePassword('login-password', this)"></i>
</div>

<button type="submit" class="btn-submit">Sign In</button>

</form>

<div class="toggle-text">
Don't have an account? <a onclick="toggleForm()">Sign Up</a>
</div>

</div>


<!-- SIGNUP FORM -->
<div id="signup-form" class="hidden">

<div class="auth-header">
<h2>Create Account</h2>
<p>Join us and start learning today</p>
</div>

<form action="signup.php" method="POST">

<div class="form-group">
<label>Full Name</label>
<input type="text" name="fullname" placeholder="John Doe" required>
</div>

<div class="form-group">
<label>Email Address</label>
<input type="email" name="email" placeholder="Enter your email" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" id="signup-password" placeholder="Create a password" required>

<i class="fas fa-eye password-toggle" onclick="togglePassword('signup-password', this)"></i>
</div>

<div class="form-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" id="signup-confirm" placeholder="Confirm your password" required>

<i class="fas fa-eye password-toggle" onclick="togglePassword('signup-confirm', this)"></i>
</div>

<button type="submit" class="btn-submit">Create Account</button>

</form>

<div class="toggle-text">
Already have an account? <a onclick="toggleForm()">Sign In</a>
</div>

</div>

<script>
function toggleForm() {
    document.getElementById('login-form').classList.toggle('hidden');
    document.getElementById('signup-form').classList.toggle('hidden');
}

function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Ensure Font Awesome icons are loaded
window.addEventListener('load', function() {
    // Check if Font Awesome is loaded
    const testIcon = document.querySelector('.fas');
    if (testIcon) {
        const computedStyle = window.getComputedStyle(testIcon, ':before');
        const content = computedStyle.getPropertyValue('content');
        
        // If Font Awesome didn't load, add fallback
        if (!content || content === 'none' || content === '') {
            console.warn('Font Awesome icons may not be loading properly');
            // Reload Font Awesome from alternative CDN
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://use.fontawesome.com/releases/v6.4.0/css/all.css';
            link.crossOrigin = 'anonymous';
            document.head.appendChild(link);
        }
    }
});
</script>

</body>
</html>
