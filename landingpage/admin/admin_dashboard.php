<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// Fetch User Details
$stmt = $conn->prepare("SELECT users.full_name, users.email, user_profiles.profile_image, user_profiles.date_of_birth 
                        FROM users 
                        LEFT JOIN user_profiles ON users.id = user_profiles.user_id 
                        WHERE users.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch Course Orders
$orders_stmt = $conn->prepare("SELECT id, course_name, price, purchase_date FROM orders WHERE user_id = ?");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnHub Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="user.css">
</head>
<body>

<div class="navbar">
    <a href="../landingpages.html"><h1><i class="fas fa-graduation-cap"></i> LearnHub</h1></a>
    <div class="user-info">
        Welcome, <?php echo htmlspecialchars($user['full_name']); ?> | 
        <a href="../login-signup/logout.php" style="color: white;">Logout</a>
    </div>
</div>

<div class="container">

<!-- Profile -->
<div class="card">
    <h3><i class="fas fa-user-circle"></i> My Profile</h3>

    <div class="profile-details">
        <div class="profile-image-container">
            <?php if(!empty($user['profile_image'])): ?>
                <img src="<?php echo WEB_UPLOADS . htmlspecialchars($user['profile_image']); ?>" class="profile-image">
            <?php else: ?>
                <div style="width:150px;height:150px;background:#eee;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-user" style="font-size: 4rem;color:#ccc;"></i>
                </div>
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>DOB:</strong> <?php echo htmlspecialchars($user['date_of_birth'] ?? 'Not Set'); ?></p>
        </div>
    </div>

    <a href="change_profile.php" class="btn">Edit Profile</a>
</div>

<!-- Courses -->
<div class="card">
<h3><i class="fas fa-shopping-bag"></i> My Courses</h3>

<?php if($orders->num_rows > 0): ?>

<?php while($order = $orders->fetch_assoc()): ?>
<div class="course-card">

    <!-- Course Info -->
    <h4><?php echo htmlspecialchars($order['course_name']); ?></h4>

    <p>
        <i class="fas fa-calendar"></i>
        <?php echo date('M d, Y', strtotime($order['purchase_date'])); ?>
    </p>

    <p>
        <i class="fas fa-rupee-sign"></i>
        Rs <?php echo number_format($order['price'], 2); ?>
    </p>

    <!-- Assignment Form -->
    <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($order['course_name']); ?>">

        <input type="text" name="title" placeholder="Assignment Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="assignment_img" required>

        <button type="submit" class="btn">Submit Assignment</button>
    </form>

    <!-- 🔥 QUIZ SECTION START -->
    <?php
    $course_name = $order['course_name'];

    $quiz_query = $conn->prepare("
        SELECT q.* FROM quizzes q
        JOIN courses c ON q.course_id = c.id
        WHERE c.course_name = ?
    ");
    $quiz_query->bind_param("s", $course_name);
    $quiz_query->execute();
    $quizzes = $quiz_query->get_result();
    ?>

    <div class="quiz-section" style="margin-top:20px;">
        <h4>📝 Available Quizzes</h4>

        <?php if($quizzes->num_rows > 0): ?>
            <?php while($quiz = $quizzes->fetch_assoc()): ?>
                <div style="margin-bottom:10px;">
                    <strong><?php echo htmlspecialchars($quiz['title']); ?></strong>

                    <a href="../quiz/take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" 
                       class="btn" style="margin-left:10px;">
                       Start Quiz
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="color:gray;">No quiz available</p>
        <?php endif; ?>
    </div>
    <!-- 🔥 QUIZ SECTION END -->

</div>
<hr>
<?php endwhile; ?>

<?php else: ?>
<p>No courses purchased</p>
<?php endif; ?>

</div>

</div>

</body>
</html>