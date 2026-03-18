<?php
include 'admin_check.php';
include 'admin_db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure image_path exists for legacy DB
    $conn->query("ALTER TABLE courses ADD COLUMN IF NOT EXISTS image_path VARCHAR(255) DEFAULT NULL");

    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');

    if ($course_name === '') {
        $errors[] = 'Course name is required.';
    }
    if ($price === '' || !is_numeric($price) || floatval($price) < 0) {
        $errors[] = 'Please enter a valid non-negative price.';
    }

    $image_path = '';
    if (!empty($_FILES['course_image']['name'])) {
        $file = $_FILES['course_image'];
        $tmp = $file['tmp_name'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_image_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed_image_ext)) {
            $errors[] = 'Course image must be JPG, JPEG, PNG, or GIF.';
        } else {
            $upload_dir = __DIR__ . '/../uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0755, true);
            $image_path = uniqid('course_') . '.' . $ext;
            if (!move_uploaded_file($tmp, $upload_dir . $image_path)) {
                $errors[] = 'Failed to upload course image.';
                $image_path = '';
            }
        }
    }

    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT id FROM courses WHERE course_name = ? LIMIT 1");
        $check_stmt->bind_param('s', $course_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $errors[] = 'This course already exists.';
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO courses (course_name, description, price, image_path) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param('sdss', $course_name, $description, $price, $image_path);
            if ($insert_stmt->execute()) {
                $success = 'Course added successfully.';
            } else {
                $errors[] = 'Database error: ' . htmlspecialchars($conn->error);
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Course - Admin</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
<div class="navbar">
    <nav class="nav-links">
      <a href="../landingpages.html">Home</a>
      <a href="admin_dashboard.php">Dashboard</a>
    </nav>
    <h1><i class="fas fa-plus-circle"></i> Add Course</h1>
    <div class="user-info">
        <a href="admin_logout.php" style="color: white; text-decoration: none;">Logout</a>
    </div>
</div>
<div class="container">
    <div class="card" style="max-width: 700px; margin: 20px auto;">
        <h3>Add New Course</h3>
        <?php if ($success): ?>
            <div class="message-box success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="message-box error-message"><ul style="margin:0; padding-left: 18px;">
            <?php foreach ($errors as $err): ?><li><?php echo htmlspecialchars($err); ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>

        <form action="admin_add_course.php" method="post" enctype="multipart/form-data">
            <div class="form-group"><label>Course Name</label><input type="text" name="course_name" required value="<?php echo htmlspecialchars($course_name ?? ''); ?>"></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?php echo htmlspecialchars($description ?? ''); ?></textarea></div>
            <div class="form-group"><label>Price (Rs)</label><input type="number" step="0.01" min="0" name="price" required value="<?php echo htmlspecialchars($price ?? '0.00'); ?>"></div>
            <div class="form-group"><label>Course Image</label><input type="file" name="course_image" accept="image/*"></div>
            <button type="submit" class="btn">Add Course</button>
            <a href="admin_dashboard.php" class="btn" style="background:#6b7280;">Back to Dashboard</a>
        </form>
    </div>
</div>
</body>
</html>