<?php
include 'admin_check.php';
include 'admin_db.php';

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$course_id) {
    $_SESSION['error_message'] = 'Invalid course ID.';
    header('Location: admin_dashboard.php');
    exit;
}

// Get course name for order cleanup
$course_result = $conn->prepare("SELECT course_name FROM courses WHERE id = ? LIMIT 1");
$course_result->bind_param('i', $course_id);
$course_result->execute();
$course_row = $course_result->get_result()->fetch_assoc();
$course_result->close();

$course_name = trim($course_row['course_name'] ?? '');

// Start transaction
$conn->begin_transaction();

try {

    // ✅ Delete orders using course_name (since no course_id column)
    if ($course_name !== '') {
        $stmt2 = $conn->prepare("DELETE FROM orders WHERE course_name = ?");
        $stmt2->bind_param('s', $course_name);
        $stmt2->execute();
        $stmt2->close();
    }

    // ✅ Get quiz IDs related to this course
    $quiz_ids_stmt = $conn->prepare("SELECT id FROM quizzes WHERE course_id = ?");
    $quiz_ids_stmt->bind_param('i', $course_id);
    $quiz_ids_stmt->execute();
    $quiz_ids_result = $quiz_ids_stmt->get_result();

    $quiz_ids = [];
    while ($row = $quiz_ids_result->fetch_assoc()) {
        $quiz_ids[] = intval($row['id']);
    }
    $quiz_ids_stmt->close();

    // ✅ Delete quiz related data
    if (!empty($quiz_ids)) {
        $ids_str = implode(',', $quiz_ids);

        $conn->query("DELETE FROM quiz_answers 
                      WHERE question_id IN (
                          SELECT id FROM quiz_questions WHERE quiz_id IN ($ids_str)
                      )");

        $conn->query("DELETE FROM quiz_questions WHERE quiz_id IN ($ids_str)");
        $conn->query("DELETE FROM quiz_results WHERE quiz_id IN ($ids_str)");
        $conn->query("DELETE FROM quizzes WHERE id IN ($ids_str)");
    }

    // ✅ Delete course
    $delete_course_stmt = $conn->prepare("DELETE FROM courses WHERE id = ? LIMIT 1");
    $delete_course_stmt->bind_param('i', $course_id);
    $delete_course_stmt->execute();
    $delete_course_stmt->close();

    // ✅ Commit if everything successful
    $conn->commit();

    $_SESSION['success_message'] = 'Course deleted successfully, including related orders and quizzes.';

} catch (Exception $e) {

    // ❌ Rollback on error
    $conn->rollback();
    $_SESSION['error_message'] = 'Error deleting course: ' . $e->getMessage();
}

// Redirect
header('Location: admin_dashboard.php');
exit;
?>