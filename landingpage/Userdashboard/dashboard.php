<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// Fetch User Details
$stmt = $conn->prepare(
    "SELECT users.full_name, users.email, user_profiles.profile_image, user_profiles.date_of_birth
     FROM users
     LEFT JOIN user_profiles ON users.id = user_profiles.user_id
     WHERE users.id = ?"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch Purchased Courses using orders.id and LEFT JOIN courses
$orders_stmt = $conn->prepare(
    "SELECT 
         orders.id AS order_id,
         orders.course_name,
         orders.price,
         orders.purchase_date,
         courses.id AS course_id
     FROM orders
     LEFT JOIN courses ON orders.course_name = courses.course_name
     WHERE orders.user_id = ?
     ORDER BY orders.purchase_date DESC"
);
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
$orders_stmt->close();

$results_stmt = $conn->prepare(
    "SELECT qr.score, qr.total_questions, qr.percentage, qr.taken_at, c.course_name
     FROM quiz_results qr
     JOIN quizzes q ON qr.quiz_id = q.id
     JOIN courses c ON q.course_id = c.id
     WHERE qr.user_id = ?
     ORDER BY qr.taken_at DESC
     LIMIT 20"
);
$results_stmt->bind_param("i", $user_id);
$results_stmt->execute();
$quiz_results = $results_stmt->get_result();
$results_stmt->close();

$completed_stmt = $conn->prepare(
    "SELECT DISTINCT q.course_id
     FROM quiz_results qr
     JOIN quizzes q ON qr.quiz_id = q.id
     WHERE qr.user_id = ?"
);
$completed_stmt->bind_param("i", $user_id);
$completed_stmt->execute();
$completed_result = $completed_stmt->get_result();
$completed_course_ids = [];
while ($row = $completed_result->fetch_assoc()) {
    $completed_course_ids[] = intval($row['course_id']);
}
$completed_stmt->close();

$assignment_stmt = $conn->prepare(
    "SELECT DISTINCT course_id FROM assignments WHERE user_id = ?"
);
$assignment_stmt->bind_param("i", $user_id);
$assignment_stmt->execute();
$assignment_result = $assignment_stmt->get_result();
$submitted_course_ids = [];
while ($row = $assignment_result->fetch_assoc()) {
    $submitted_course_ids[] = intval($row['course_id']);
}
$assignment_stmt->close();

// Fetch quiz results to show in dashboard quick section if needed
$quiz_completed_for_course_stmt = $conn->prepare(
    "SELECT q.course_id, MAX(qr.taken_at) as last_taken
     FROM quiz_results qr
     JOIN quizzes q ON qr.quiz_id = q.id
     WHERE qr.user_id = ?
     GROUP BY q.course_id"
);
$quiz_completed_for_course_stmt->bind_param("i", $user_id);
$quiz_completed_for_course_stmt->execute();
$quiz_completed_for_course_result = $quiz_completed_for_course_stmt->get_result();
$quiz_completed_course_ids = [];
while ($row = $quiz_completed_for_course_result->fetch_assoc()) {
    $quiz_completed_course_ids[] = intval($row['course_id']);
}
$quiz_completed_for_course_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LearnHub Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="navbar">
    <a href="../landingpages.html"><h1><i class="fas fa-graduation-cap"></i> LearnHub</h1></a>
    <div class="user-info">
        Welcome, <?php echo htmlspecialchars($user['full_name']); ?> |
        <a href="../login-signup/logout.php" style="color:white;text-decoration:none;">Logout</a>
    </div>
</div>

<div class="container">

<!-- SUCCESS MESSAGE -->
<?php if(isset($_SESSION['success_message'])): ?>
<div class="message-box success-message">
    <i class="fas fa-check-circle"></i>
    <?php echo $_SESSION['success_message']; ?>
</div>
<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<!-- ERROR MESSAGE -->
<?php if(isset($_SESSION['error_message'])): ?>
<div class="message-box error-message">
    <i class="fas fa-exclamation-circle"></i>
    <?php echo $_SESSION['error_message']; ?>
</div>
<?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- PROFILE SECTION -->
<div class="card">
    <h3><i class="fas fa-user-circle"></i> My Profile</h3>
    <div class="profile-details">
        <div class="profile-image-container">
            <?php if(!empty($user['profile_image'])): ?>
                <img src="<?php echo WEB_UPLOADS . htmlspecialchars($user['profile_image']); ?>" class="profile-image">
            <?php else: ?>
                <div style="width:150px;height:150px;background:#eee;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                    <i class="fas fa-user" style="font-size:4rem;color:#ccc;"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="profile-info">
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth'] ?? 'Not Set'); ?></p>
        </div>
    </div>
    <div class="action-buttons">
        <a href="change_profile.php" class="btn">Edit Profile</a>
    </div>
</div>

<div class="card">
    <h3><i class="fas fa-file-alt"></i> Quiz Results</h3>
    <?php if ($quiz_results && $quiz_results->num_rows > 0): ?>
        <table>
            <thead>
                <tr><th>Course</th><th>Score</th><th>Percent</th><th>Date</th></tr>
            </thead>
            <tbody>
                <?php while ($qr = $quiz_results->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($qr['course_name']); ?></td>
                        <td><?php echo intval($qr['score']) . ' / ' . intval($qr['total_questions']); ?></td>
                        <td><?php echo number_format($qr['percentage'], 2); ?>%</td>
                        <td><?php echo date('M d, Y H:i', strtotime($qr['taken_at'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="margin: 10px 0; color: #555;">No quiz results yet. Complete a quiz to see results here.</p>
    <?php endif; ?>
</div>

<!-- COURSES & ASSIGNMENT FORM -->
<div class="card">
<h3><i class="fas fa-shopping-bag"></i> My Courses</h3>

<?php if($orders && $orders->num_rows > 0): ?>
    <?php while($order = $orders->fetch_assoc()): 
        $course_id_for_order = intval($order['course_id']);
        if ($course_id_for_order <= 0 && !empty($order['course_name'])) {
            $lookup_course_stmt = $conn->prepare("SELECT id FROM courses WHERE course_name = ? LIMIT 1");
            $lookup_course_stmt->bind_param("s", $order['course_name']);
            $lookup_course_stmt->execute();
            $lookup_row = $lookup_course_stmt->get_result()->fetch_assoc();
            $course_id_for_order = intval($lookup_row['id'] ?? 0);
            $lookup_course_stmt->close();
        }
    ?>
        <div class="course-card">
            <div class="course-header">
                <div>
                    <span class="course-title"><?php echo htmlspecialchars($order['course_name']); ?></span>
                    <div class="course-meta">
                        <span><i class="fas fa-calendar"></i> Purchased: <?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></span>
                        <span><i class="fas fa-rupee-sign"></i> Price: Rs <?php echo number_format($order['price'],2); ?></span>
                    </div>
                </div>
            </div>

            <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?php echo $course_id_for_order; ?>">
                <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($order['course_name']); ?>">

                <div class="form-group">
                    <label>Assignment Title</label>
                    <input type="text" name="title" required placeholder="e.g., Final Project">
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="3" required placeholder="Describe your submission..."></textarea>
                </div>

                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" name="assignment_img" required>
                    <small style="color:#666;">JPG, PNG, GIF, PDF, DOCX allowed</small>
                </div>

                <div class="action-buttons" style="display:flex;gap:8px;flex-wrap:wrap; margin-bottom:5px;">
                    <?php
                        $quiz_course_id = intval($course_id_for_order);
                        $course_name = trim($order['course_name']);
                        if ($quiz_course_id <= 0 && $course_name !== '') {
                            $lookup_stmt = $conn->prepare("SELECT id, course_name FROM courses WHERE course_name = ? LIMIT 1");
                            $lookup_stmt->bind_param('s', $course_name);
                            $lookup_stmt->execute();
                            $lookup_row = $lookup_stmt->get_result()->fetch_assoc();
                            $lookup_stmt->close();
                            $quiz_course_id = intval($lookup_row['id'] ?? 0);
                            if ($quiz_course_id > 0 && $course_name === '') {
                                $course_name = trim($lookup_row['course_name'] ?? '');
                            }
                        }

                        $has_quiz = false;
                        if ($quiz_course_id > 0) {
                            $quiz_check = $conn->prepare("SELECT id FROM quizzes WHERE course_id = ? LIMIT 1");
                            $quiz_check->bind_param('i', $quiz_course_id);
                            $quiz_check->execute();
                            $quiz_exists = $quiz_check->get_result()->fetch_assoc();
                            $quiz_check->close();
                            $has_quiz = !empty($quiz_exists);
                        }

                        if ($course_name === '' && $quiz_course_id > 0) {
                            $name_stmt = $conn->prepare("SELECT course_name FROM courses WHERE id = ? LIMIT 1");
                            $name_stmt->bind_param('i', $quiz_course_id);
                            $name_stmt->execute();
                            $name_row = $name_stmt->get_result()->fetch_assoc();
                            $name_stmt->close();
                            $course_name = trim($name_row['course_name'] ?? '');
                        }

                        $is_completed = ($quiz_course_id > 0 && in_array($quiz_course_id, $quiz_completed_course_ids));
                        $has_submitted_assignment = ($course_id_for_order > 0 && in_array($course_id_for_order, $submitted_course_ids));
                    ?>
                    <?php if (!$has_submitted_assignment): ?>
                        <button type="submit" class="btn">Submit Assignment</button>
                    <?php else: ?>
                        <button type="button" class="btn" disabled style="background:#6b7280;">Already Submitted</button>
                    <?php endif; ?>
                    <?php if ($has_quiz): ?>
                        <a href="user_quiz.php?course_id=<?php echo $quiz_course_id; ?>&course_name=<?php echo urlencode($course_name); ?>" class="btn" style="background:#2563eb; border:none; color:#fff;" >Take Quiz</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div style="text-align:center;padding:40px;color:#777;">
        <i class="fas fa-inbox" style="font-size:3rem;margin-bottom:15px;"></i>
        <p>You haven't purchased any courses yet.</p>
        <a href="../Course/course.php" class="btn">Browse Courses</a>
    </div>
<?php endif; ?>
</div>

</div>
</body>
</html>