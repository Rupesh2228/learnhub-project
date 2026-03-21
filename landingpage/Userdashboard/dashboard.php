<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// User Info
$stmt = $conn->prepare("SELECT users.full_name, users.email, user_profiles.profile_image, user_profiles.date_of_birth 
                        FROM users 
                        LEFT JOIN user_profiles ON users.id = user_profiles.user_id 
                        WHERE users.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Orders (Courses)
$orders_stmt = $conn->prepare("SELECT id, course_name, price, purchase_date FROM orders WHERE user_id = ?");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link rel="stylesheet" href="user.css">
</head>

<body>

<div class="navbar">
    <h2>🎓 LearnHub</h2>
    <div>
        Welcome, <?php echo $user['full_name']; ?> |
        <a href="../login-signup/logout.php">Logout</a>
    </div>
</div>

<div class="container">

<!-- PROFILE -->
<div class="card">
    <h3>👤 Profile</h3>

    <p><b>Name:</b> <?php echo $user['full_name']; ?></p>
    <p><b>Email:</b> <?php echo $user['email']; ?></p>
    <p><b>DOB:</b> <?php echo $user['date_of_birth'] ?? 'Not Set'; ?></p>

    <a href="change_profile.php" class="btn">Edit Profile</a>
</div>

<!-- COURSES -->
<div class="card">
<h3>📚 My Courses</h3>

<?php if($orders->num_rows > 0): ?>

<?php while($order = $orders->fetch_assoc()): ?>

<div class="course-card">

    <h4><?php echo $order['course_name']; ?></h4>
    <p>📅 <?php echo $order['purchase_date']; ?></p>
    <p>💰 Rs <?php echo $order['price']; ?></p>

    <!-- ASSIGNMENT -->
    <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="course_name" value="<?php echo $order['course_name']; ?>">

        <input type="text" name="title" placeholder="Assignment Title" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="assignment_img" required>

        <button type="submit" class="btn">Submit Assignment</button>
    </form>

    <!-- QUIZ SECTION -->
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

    <div class="quiz-box">
        <h4>📝 Quizzes</h4>

        <?php if($quizzes->num_rows > 0): ?>
            <?php while($quiz = $quizzes->fetch_assoc()): ?>
                <div class="quiz-item">
                    <span><?php echo $quiz['title']; ?></span>

                    <a href="../quiz/take_quiz.php?quiz_id=<?php echo $quiz['id']; ?>" 
                       class="btn small">
                        Start
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No quiz available</p>
        <?php endif; ?>
    </div>

</div>

<?php endwhile; ?>

<?php else: ?>
<p>No courses purchased</p>
<?php endif; ?>

</div>

</div>

</body>
</html>