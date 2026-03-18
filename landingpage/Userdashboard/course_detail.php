<?php
include 'user_check.php';
include 'db.php';

$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
if ($course_id <= 0) {
    $_SESSION['error_message'] = 'Invalid course ID.';
    header('Location: dashboard.php');
    exit;
}

$course_stmt = $conn->prepare("SELECT course_name, description, price, image_path FROM courses WHERE id = ? LIMIT 1");
$course_stmt->bind_param('i', $course_id);
$course_stmt->execute();
$course = $course_stmt->get_result()->fetch_assoc();
$course_stmt->close();

if (!$course) {
    $_SESSION['error_message'] = 'Course not found.';
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars($course['course_name']); ?> - Read Course</title>
<link rel="stylesheet" href="dashboard.css">
</head>
<body>
<div class="navbar">
    <a href="../landingpages.html"><h1><i class="fas fa-graduation-cap"></i> LearnHub</h1></a>
    <div class="user-info"><a href="dashboard.php" style="color:white;text-decoration:none;">Back to Dashboard</a></div>
</div>
<div class="container">
    <div class="card">
        <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
        <?php if (!empty($course['image_path'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($course['image_path']); ?>" alt="<?php echo htmlspecialchars($course['course_name']); ?>" style="max-width:100%;height:auto;border-radius:8px;">
        <?php endif; ?>
        <p style="margin-top: 15px;"><strong>Price:</strong> Rs <?php echo number_format($course['price'],2); ?></p>
        <p><strong>Description:</strong> <?php echo htmlspecialchars($course['description'] ?: 'No description available.'); ?></p>
        <p style="margin-top: 12px; color:#555;">Course reading content can go here. This is a placeholder for course lessons.</p>
    </div>
</div>
</body>
</html>