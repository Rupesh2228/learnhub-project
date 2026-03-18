<?php
include 'admin_check.php';
include 'admin_db.php';

$errors = [];
$success = '';

// Fetch all courses
$courses_stmt = $conn->prepare("SELECT id, course_name FROM courses ORDER BY course_name");
if (!$courses_stmt) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$courses_stmt->execute();
$courses = $courses_stmt->get_result();
$courses_stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
    $question = trim($_POST['question'] ?? '');
    $option_a = trim($_POST['option_a'] ?? '');
    $option_b = trim($_POST['option_b'] ?? '');
    $option_c = trim($_POST['option_c'] ?? '');
    $option_d = trim($_POST['option_d'] ?? '');
    $correct_answer = trim($_POST['correct_answer'] ?? '');

    if (!$course_id) {
        $errors[] = 'Please select a course.';
    }
    if ($question === '') {
        $errors[] = 'Please enter the quiz question.';
    }
    if ($option_a === '' || $option_b === '' || $option_c === '' || $option_d === '') {
        $errors[] = 'All options (A, B, C, D) are required.';
    }
    if (!in_array($correct_answer, ['A', 'B', 'C', 'D'])) {
        $errors[] = 'Correct answer must be A, B, C, or D.';
    }

    if (empty($errors)) {
        // Create or fetch quiz for selected course
        $quiz_stmt = $conn->prepare("SELECT id FROM quizzes WHERE course_id = ? LIMIT 1");
        $quiz_stmt->bind_param('i', $course_id);
        $quiz_stmt->execute();
        $quiz_result = $quiz_stmt->get_result();
        $quiz_row = $quiz_result->fetch_assoc();
        $quiz_stmt->close();

        if ($quiz_row) {
            $quiz_id = intval($quiz_row['id']);
        } else {
            $create_quiz_stmt = $conn->prepare("INSERT INTO quizzes (course_id, title) VALUES (?, 'Course Quiz')");
            $create_quiz_stmt->bind_param('i', $course_id);
            $create_quiz_stmt->execute();
            $quiz_id = $create_quiz_stmt->insert_id;
            $create_quiz_stmt->close();
        }

        $insert_question_stmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insert_question_stmt->bind_param('issssss', $quiz_id, $question, $option_a, $option_b, $option_c, $option_d, $correct_answer);
        if ($insert_question_stmt->execute()) {
            $success = 'Quiz question added successfully.';
        } else {
            $errors[] = 'Unable to add question: ' . htmlspecialchars($conn->error);
        }
        $insert_question_stmt->close();
    }
}

// After page actions, fetch existing question list
$questions = [];
$q_fetch_stmt = $conn->prepare("SELECT qq.id, c.course_name, qq.question, qq.option_a, qq.option_b, qq.option_c, qq.option_d, qq.correct_answer FROM quiz_questions qq JOIN quizzes q ON qq.quiz_id=q.id JOIN courses c ON q.course_id = c.id ORDER BY qq.id DESC LIMIT 100");
$q_fetch_stmt->execute();
$questions_result = $q_fetch_stmt->get_result();
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
$q_fetch_stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Add Quiz - LearnHub</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>
<div class="navbar">
    <nav class="nav-links">
      <a href="../landingpages.html">Home</a>
      <a href="admin_dashboard.php">Dashboard</a>
    </nav>
    <h1><i class="fas fa-question-circle"></i> Add Quiz Questions</h1>
    <div class="user-info">
        <a href="admin_logout.php" style="color: white; text-decoration: none;">Logout</a>
    </div>
</div>

<div class="container">
    <div class="card" style="padding: 20px;">
        <h3>Create Quiz Question</h3>
        <?php if (!empty($success)): ?>
            <div class="message-box success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="message-box error-message">
                <ul style="margin:0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="admin_add_quiz.php">
            <div class="form-group">
                <label>Course</label>
                <select name="course_id" required>
                    <option value="">-- Select Course --</option>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                        <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Question</label>
                <textarea name="question" required rows="3"></textarea>
            </div>
            <div class="form-group"><label>Option A</label><input type="text" name="option_a" required></div>
            <div class="form-group"><label>Option B</label><input type="text" name="option_b" required></div>
            <div class="form-group"><label>Option C</label><input type="text" name="option_c" required></div>
            <div class="form-group"><label>Option D</label><input type="text" name="option_d" required></div>
            <div class="form-group">
                <label>Correct Answer</label>
                <select name="correct_answer" required>
                    <option value="">Choose</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
            <button type="submit" class="btn">Add Question</button>
        </form>
    </div>

    <div class="card">
        <h3>Recent Quiz Questions</h3>
        <?php if (!empty($questions)): ?>
            <table>
                <thead><tr><th>Course</th><th>Question</th><th>Correct</th></tr></thead>
                <tbody>
                    <?php foreach ($questions as $q): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($q['course_name']); ?></td>
                            <td><?php echo htmlspecialchars($q['question']); ?></td>
                            <td><?php echo htmlspecialchars($q['correct_answer']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No quiz questions created yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
