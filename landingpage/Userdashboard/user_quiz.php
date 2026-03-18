<?php
include 'user_check.php';
include 'db.php';

$user_id = $_SESSION['user_id'];
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
$course_name = trim($_GET['course_name'] ?? '');

if ($course_id > 0) {
    $course_stmt = $conn->prepare("SELECT course_name FROM courses WHERE id = ? LIMIT 1");
    $course_stmt->bind_param('i', $course_id);
    $course_stmt->execute();
    $course_row = $course_stmt->get_result()->fetch_assoc();
    $course_stmt->close();
    if (!empty($course_row['course_name'])) {
        $course_name = trim($course_row['course_name']);
    }
}

if ($course_name === '') {
    header('Location: dashboard.php');
    exit;
}

// Verify user purchased course by name
$purchase_stmt = $conn->prepare("SELECT id FROM orders WHERE user_id = ? AND course_name = ? LIMIT 1");
$purchase_stmt->bind_param('is', $user_id, $course_name);
$purchase_stmt->execute();
$purchase_result = $purchase_stmt->get_result();
if ($purchase_result->num_rows === 0) {
    $_SESSION['error_message'] = 'You must purchase this course before taking its quiz.';
    header('Location: dashboard.php');
    exit;
}
$purchase_stmt->close();

if ($course_id <= 0) {
    $course_get = $conn->prepare("SELECT id FROM courses WHERE course_name = ? LIMIT 1");
    $course_get->bind_param('s', $course_name);
    $course_get->execute();
    $course_row = $course_get->get_result()->fetch_assoc();
    $course_get->close();
    $course_id = intval($course_row['id'] ?? 0);
}

if ($course_id <= 0) {
    $_SESSION['error_message'] = 'Invalid course selected for quiz. Please try again.';
    header('Location: dashboard.php');
    exit;
}


if ($course_id > 0) {
    $quiz_stmt = $conn->prepare("SELECT id FROM quizzes WHERE course_id = ? LIMIT 1");
    $quiz_stmt->bind_param('i', $course_id);
    $quiz_stmt->execute();
    $quiz_row = $quiz_stmt->get_result()->fetch_assoc();
    $quiz_stmt->close();
} else {
    $quiz_row = null;
}

if (!$quiz_row && $course_name !== '') {
    // Fallback find quiz by course_name
    $course_lookup = $conn->prepare("SELECT id FROM courses WHERE course_name = ? LIMIT 1");
    $course_lookup->bind_param('s', $course_name);
    $course_lookup->execute();
    $course_lookup_row = $course_lookup->get_result()->fetch_assoc();
    $course_lookup->close();
    if ($course_lookup_row) {
        $course_id = intval($course_lookup_row['id']);
        $quiz_stmt2 = $conn->prepare("SELECT id FROM quizzes WHERE course_id = ? LIMIT 1");
        $quiz_stmt2->bind_param('i', $course_id);
        $quiz_stmt2->execute();
        $quiz_row = $quiz_stmt2->get_result()->fetch_assoc();
        $quiz_stmt2->close();
    }
}

if (!$quiz_row) {
    $_SESSION['error_message'] = 'No quiz created yet for this course.';
    header('Location: dashboard.php');
    exit;
}
$quiz_id = intval($quiz_row['id']);

$questions_stmt = $conn->prepare("SELECT id, question, option_a, option_b, option_c, option_d FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
$questions_stmt->bind_param('i', $quiz_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
$questions_stmt->close();

if (count($questions) === 0) {
    $_SESSION['error_message'] = 'No questions found for this quiz yet.';
    header('Location: dashboard.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - <?php echo htmlspecialchars($course_name); ?></title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="navbar">
        <a href="../landingpages.html"><h1><i class="fas fa-graduation-cap"></i> LearnHub</h1></a>
        <div class="user-info"><a href="dashboard.php" style="color:white;text-decoration:none;">Back to Dashboard</a></div>
    </div>
    <div class="container">
        <div class="card">
            <h2>Quiz: <?php echo htmlspecialchars($course_name); ?></h2>
            <form action="submit_quiz.php" method="POST">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <?php foreach ($questions as $index => $q): ?>
                    <div class="form-group" style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;"> 
                        <p><strong>Q<?php echo $index + 1; ?>:</strong> <?php echo htmlspecialchars($q['question']); ?></p>
                        <div>
                            <label><input type="radio" name="answer_<?php echo $q['id']; ?>" value="A" required> A. <?php echo htmlspecialchars($q['option_a']); ?></label><br>
                            <label><input type="radio" name="answer_<?php echo $q['id']; ?>" value="B"> B. <?php echo htmlspecialchars($q['option_b']); ?></label><br>
                            <label><input type="radio" name="answer_<?php echo $q['id']; ?>" value="C"> C. <?php echo htmlspecialchars($q['option_c']); ?></label><br>
                            <label><input type="radio" name="answer_<?php echo $q['id']; ?>" value="D"> D. <?php echo htmlspecialchars($q['option_d']); ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn">Submit Quiz</button>
            </form>
        </div>
    </div>
</body>
</html>
