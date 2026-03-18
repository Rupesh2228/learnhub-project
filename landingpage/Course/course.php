<?php
require_once('../include.php');

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$profile_link = "../login-signup/login_signup.html";
$profile_text = "Login / Sign Up";
$show_profile_link = true;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    $profile_link = "../admin/admin_dashboard.php";
    $profile_text = "Admin Profile";
    $show_profile_link = false;
} elseif (isset($_SESSION['role']) && $_SESSION['role'] === 'user') {
    $profile_link = "../Userdashboard/dashboard.php";
    $profile_text = "My Profile";
    $show_profile_link = false;
}

$course_stmt = $conn->prepare("SELECT id, course_name, description, price, image_path FROM courses ORDER BY course_name");
$course_stmt->execute();
$courses_result = $course_stmt->get_result();
$courses = $courses_result->fetch_all(MYSQLI_ASSOC);
$course_stmt->close();
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
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="card-image img-stack">
                            <?php if (!empty($course['image_path'])): ?>
                                <img src="../uploads/<?php echo htmlspecialchars($course['image_path']); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                            <?php else: ?>
                                <img src="mern.jpg" alt="<?php echo htmlspecialchars($course['course_name']); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
                            <p><?php echo htmlspecialchars($course['description'] ?: 'Learn this course with our expert instructors.'); ?></p>
                            <p style="margin-top: 5px; font-weight: 600; color: #2f1f9f;">Price: Rs <?php echo number_format($course['price'] ?? 0, 2); ?></p>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="form.php?course_name=<?php echo urlencode($course['course_name']); ?>"><button class="btn-course">Purchase Course</button></a>
                            <?php else: ?>
                                <a href="../login-signup/login_signup.php"><button class="btn-course">Login to Purchase</button></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="course-card">
                    <div class="card-content">
                        <h2>No courses available</h2>
                        <p>Please check back later or contact admin.</p>
                    </div>
                </div>
            <?php endif; ?>
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
