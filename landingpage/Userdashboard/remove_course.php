<?php
include 'user_check.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error_message'] = 'Invalid request method.';
    header('Location: dashboard.php');
    exit;
}

$order_id = intval($_POST['order_id'] ?? 0);
if ($order_id <= 0) {
    $_SESSION['error_message'] = 'Invalid order selected.';
    header('Location: dashboard.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure order belongs to user
$check_stmt = $conn->prepare("SELECT id FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$check_stmt->bind_param('ii', $order_id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();
$check_stmt->close();

if ($check_result->num_rows === 0) {
    $_SESSION['error_message'] = 'Order not found or unauthorized.';
    header('Location: dashboard.php');
    exit;
}

$del_stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$del_stmt->bind_param('ii', $order_id, $user_id);
if ($del_stmt->execute()) {
    $_SESSION['success_message'] = 'Purchased course removed successfully.';
} else {
    $_SESSION['error_message'] = 'Unable to remove purchased course.';
}
$del_stmt->close();
header('Location: dashboard.php');
exit;
