<?php
session_start();

// Determine profile link based on session role
$profile_link = "login-signup/login_signup.html";
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        $profile_link = "admin/admin_dashboard.php";
    } elseif ($_SESSION['role'] === 'user') {
        $profile_link = "Userdashboard/dashboard.php";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnHub</title>
    <!-- Google Fonts for a modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="landingpages.css">
</head>
<body>

    <!-- --- Header --- -->
    <header class="navbar">
        <div class="nav-container">
            <div class="logo">
                <a href="landingpages.php"><h1 class="adi" style="color: blue;">LearnHub</h1></a>
            </div>
            <nav class="nav-links">
                <a href="landingpages.php" style="color: blue;">Home</a>
                <a href="Course/course.php">Courses</a>
                <a href="About/about.php">About</a>
                <a href="<?php echo $profile_link; ?>">Profile</a>
            </nav>
            <div class="nav-actions">
                
                    <a href="login-signup/login_signup.html"><button class="btn-text">Login / Sign Up</button></a>
                
                
               
            </div>
        </div>
    </header>

    <!-- --- Hero Section --- -->
    <section id="home" class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Unlock Your Potential <br /> With Expert Courses</h1>
            <p>
                Explore high-quality courses, gain practical skills, and achieve your
                career goals with Nepal's most trusted learning community.
            </p>
        </div>
    </section>

    <!-- --- Features Section --- -->
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose LearnHub?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon">🎓</div>
                    <h3>Expert Instructors</h3>
                    <p>Learn from industry professionals with real-world experience.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">⏰</div>
                    <h3>Flexible Learning</h3>
                    <p>Study at your own pace, anytime, anywhere on any device.</p>
                </div>
                <div class="feature-card">
                    <div class="icon">📜</div>
                    <h3>Certified Courses</h3>
                    <p>Get recognized certificates upon completion of your courses.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- --- About Section --- -->
    <section class="about-cta">
        <div class="container about-container">
            <div class="about-content">
                <h2>Committed to Your Excellence</h2>
                <p>
                    As a dedicated e-learning platform in Nepal, we focus on details and
                    excellence. Your goals, vision, and growth are our priority. We
                    connect learners, educators, and opportunities to create memorable
                    learning experiences.
                </p>
                            </div>
            <div class="about-image">
                <img
                    src="./landingpages.avif"
                    alt="Team Learning"
                />
            </div>
        </div>
    </section>

    <!-- --- Footer --- -->
    <footer>
        <div class="footer-container">
            <div class="footer-col">
                <h3>LearnHub</h3>
                <p>Empowering learners across Nepal with quality education.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="Course/course.php">Courses</a></li>
                    <li><a href="About/about.php">About Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Us</h4>
                <ul>
                    <li><a href="mailto:info@learnhub.com">info@learnhub.com</a></li>
                    <li><a href="tel:+9771332345678">+977 1332345678</a></li>
                </ul> 
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 LearnHub. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>