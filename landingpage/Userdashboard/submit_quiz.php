<?php
include 'user_check.php';
include 'db.php';

$user_id = $_SESSION['user_id'];
$quiz_id = isset($_POST['quiz_id']) ? intval($_POST['quiz_id']) : 0;
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

if (!$quiz_id && !$course_id) {
    $_SESSION['error_message'] = 'Invalid quiz submission.';
    header('Location: dashboard.php');
    exit;
}

if (!$course_id) {
    $course_lookup = $conn->prepare("SELECT course_id FROM quizzes WHERE id = ? LIMIT 1");
    $course_lookup->bind_param('i', $quiz_id);
    $course_lookup->execute();
    $course_row = $course_lookup->get_result()->fetch_assoc();
    $course_lookup->close();
    $course_id = intval($course_row['course_id'] ?? 0);
}

$course_name = '';
if ($course_id) {
    $course_name_stmt = $conn->prepare("SELECT course_name FROM courses WHERE id = ? LIMIT 1");
    $course_name_stmt->bind_param('i', $course_id);
    $course_name_stmt->execute();
    $course_name_row = $course_name_stmt->get_result()->fetch_assoc();
    $course_name_stmt->close();
    $course_name = $course_name_row['course_name'] ?? '';
}

if ($course_name === '') {
    $_SESSION['error_message'] = 'Invalid quiz submission.';
    header('Location: dashboard.php');
    exit;
}

$purchase_stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? AND course_name = ? LIMIT 1");
$purchase_stmt->bind_param('is', $user_id, $course_name);
$purchase_stmt->execute();
$purchase_result = $purchase_stmt->get_result();
if ($purchase_result->num_rows === 0) {
    $_SESSION['error_message'] = 'You do not have permission to submit this quiz.';
    header('Location: dashboard.php');
    exit;
}
$purchase_stmt->close();

$questions_stmt = $conn->prepare("SELECT id, question, correct_answer FROM quiz_questions WHERE quiz_id = ? ORDER BY id");
$questions_stmt->bind_param('i', $quiz_id);
$questions_stmt->execute();
$questions_result = $questions_stmt->get_result();
$questions = [];
while ($row = $questions_result->fetch_assoc()) {
    $questions[] = $row;
}
$questions_stmt->close();

$total_questions = count($questions);
if ($total_questions === 0) {
    $_SESSION['error_message'] = 'This quiz currently has no questions yet.';
    header('Location: dashboard.php');
    exit;
}

$score = 0;
foreach ($questions as $qrow) {
    $qid = $qrow['id'];
    $correct = strtoupper(trim($qrow['correct_answer']));
    $user_answer = strtoupper(trim($_POST['answer_' . $qid] ?? ''));
    if (!in_array($user_answer, ['A', 'B', 'C', 'D'])) {
        $user_answer = '';
    }
    if ($user_answer !== '' && $user_answer === $correct) {
        $score++;
    }
}

$percentage = round(($score / $total_questions) * 100, 2);
$insert_result_stmt = $conn->prepare("INSERT INTO quiz_results (user_id, quiz_id, score, total_questions, percentage) VALUES (?, ?, ?, ?, ?)");
$insert_result_stmt->bind_param('iiiid', $user_id, $quiz_id, $score, $total_questions, $percentage);
$insert_result_stmt->execute();
$result_id = $insert_result_stmt->insert_id;
$insert_result_stmt->close();

// Ensure quiz_answers table exists (for older installs)
$create_answers_table = "CREATE TABLE IF NOT EXISTS quiz_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_id INT NOT NULL,
    user_answer ENUM('A','B','C','D') NOT NULL,
    correct_answer ENUM('A','B','C','D') NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (result_id) REFERENCES quiz_results(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id) ON DELETE CASCADE
)";
$conn->query($create_answers_table);

$answer_stmt = $conn->prepare("INSERT INTO quiz_answers (result_id, question_id, user_answer, correct_answer, is_correct) VALUES (?, ?, ?, ?, ?)");
if (!$answer_stmt) {
    $_SESSION['error_message'] = 'Quiz submitted but answer details could not be recorded.';
    header('Location: dashboard.php');
    exit;
}
foreach ($questions as $qrow) {
    $qid = $qrow['id'];
    $correct = strtoupper(trim($qrow['correct_answer']));
    $user_answer = strtoupper(trim($_POST['answer_' . $qid] ?? ''));
    if (!in_array($user_answer, ['A', 'B', 'C', 'D'])) {
        $user_answer = 'A';
    }
    $is_correct = ($user_answer === $correct) ? 1 : 0;
    $answer_stmt->bind_param('iissi', $result_id, $qid, $user_answer, $correct, $is_correct);
    $answer_stmt->execute();
}
$answer_stmt->close();

$_SESSION['success_message'] = "Quiz submitted. Your score: $score / $total_questions ($percentage%).";
header('Location: dashboard.php');
exit;
