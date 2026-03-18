<?php
include 'admin_check.php';
include 'admin_db.php';

$quiz_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$quiz_id) {
    $_SESSION['error_message'] = 'Invalid quiz ID';
    header('Location: admin_dashboard.php');
    exit;
}

// Delete quiz and cascade delete questions/results if FK constraints exist
$delete_stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ? LIMIT 1");
$delete_stmt->bind_param('i', $quiz_id);
if ($delete_stmt->execute()) {
    $_SESSION['success_message'] = 'Quiz removed successfully.';
} else {
    $_SESSION['error_message'] = 'Unable to remove quiz.';
}
$delete_stmt->close();

header('Location: admin_dashboard.php');
exit;
?>