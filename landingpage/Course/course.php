<?php
session_start();

// Determine profile link based on session role
$profile_link = "../login-signup/login_signup.html"; // Default: not logged in
$profile_text = "Login / Sign Up";
$show_profile_link = true;

// Check if admin is logged in
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $profile_link = "../admin/admin_dashboard.php";
    $profile_text = "Admin Profile";
    $show_profile_link = false; // Hide login button for admins
}
// Check if regular user is logged in
elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    $profile_link = "../Userdashboard/dashboard.php";
    $profile_text = "My Profile";
    $show_profile_link = false; // Hide login button for users
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LearnHub - Courses</title>
<!-- Importing a nice font from Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<!-- Importing FontAwesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="course.css">
</head>

<body>

    <!-- Navigation -->
    <div class="nav-container">
        <div class="logo">
            <a href="../landingpages.php"><h1>LearnHub</h1></a>
        </div>
        <nav class="nav-links">
            <a href="../landingpages.php">Home</a>
            <a href="course.php" class="active">Courses</a>
            <a href="../About/about.php">About</a>
            <a href="<?php echo $profile_link; ?>"><?php echo $profile_text; ?></a>
        </nav>
        <div class="nav-actions">
            <?php if ($show_profile_link): ?>
                <a href="../login-signup/login_signup.html"><button class="btn-text">Login / Sign Up</button></a>
            <?php else: ?>
                <a href="../login-signup/logout.php"><button class="btn-text">Logout</button></a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <section class="courses">
        <div class="section-header">
            <h1>Explore Our Courses</h1>
            <p>Master new skills with our industry-leading curriculum.</p>
        </div>

        <div class="course-container">

            <!-- Card 1 -->
            <div class="course-card">
                <div class="card-image img-stack">
                    <img src="mern.jpg" alt="Full Stack Development">
                </div>
                <div class="card-content">
                    <h2>Full Stack Development</h2>
                    <p>Learn HTML, CSS, JavaScript, React, Node.js and databases to build complete web applications from scratch.</p>
                    <a href="form.php"><button class="btn-course">View Course</button></a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="course-card">
                <div class="card-image img-ai">
                    <img src="AI.jpg" alt="AI / Machine Learning">
                </div>
                <div class="card-content">
                    <h2>AI / Machine Learning</h2>
                    <p>Understand Artificial Intelligence and Machine Learning concepts with practical Python examples.</p>
                    <a href="form.php"><button class="btn-course">View Course</button></a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="course-card">
                <div class="card-image img-sec">
                    <img src="cyber-security.jpg" alt="cyber-security">
                </div>
                <div class="card-content">
                    <h2>Cybersecurity</h2>
                    <p>Learn how to protect systems, networks and data from cyber attacks and digital threats.</p>
                    <a href="form.php"><button class="btn-course">View Course</button></a>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="course-card">
                <div class="card-image img-design">
                    <img src="ui.webp" alt="UI / UX Design">
                </div>
                <div class="card-content">
                    <h2>UI / UX Design</h2>
                    <p>Learn to design modern, user-friendly interfaces and improve user experience using Figma.</p>
                    <a href="form.php"><button class="btn-course">View Course</button></a>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <!-- Column 1: About -->
            <div class="footer-col">
                <h3>LearnHub</h3>
                <p>Empowering learners worldwide with high-quality education in technology and design.</p>
                <div class="social-links">
                    <a href="https://www.facebook.com/"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="footer-col">
                <h3>Quick Links</h3>
                <ul class="footer-links">
                    <li><a href="../landingpages.php">Home</a></li>
                    <li><a href="course.php">Courses</a></li>
                    <li><a href="../About/about.php">About Us</a></li>
                </ul>
            </div>

            <!-- Column 3: Contact -->
            <div class="footer-col">
                <h3>Contact Us</h3>
                <ul class="footer-links">
                    <li><i class="fas fa-map-marker-alt"></i>Godawari, Lalitpur</li>
                    <li><i class="fas fa-phone"></i>+977 9845536230</li>
                    <li><i class="fas fa-envelope"></i> learnhub123@gmail.com</li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; 2024 LearnHub. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
