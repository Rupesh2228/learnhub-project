<?php
include 'admin_check.php';
include 'admin_db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'Invalid purchase ID.';
    header('Location: admin_dashboard.php');
    exit;
}

$orderId = intval($_GET['id']);

$stmt = $conn->prepare('DELETE FROM orders WHERE id = ?');
$stmt->bind_param('i', $orderId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION['success_message'] = 'User purchase removed successfully.';
} else {
    $_SESSION['error_message'] = 'User purchase not found or already removed.';
}

$stmt->close();
header('Location: admin_dashboard.php');
exit;
