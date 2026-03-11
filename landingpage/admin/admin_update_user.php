<?php
include 'admin_check.php';
include "admin_db.php";

// Get user ID
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['error_message'] = "User not found!";
    header("Location: admin_user.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    
    if ($full_name && $email) {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $full_name, $email, $user_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "User updated successfully!";
            header("Location: admin_update_user.php?id=" . $user_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Error updating user: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "All fields are required!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - LearnHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

    <div class="navbar">
        <h1><i class="fas fa-user-shield"></i> Admin Panel</h1>
        <div class="user-info">
            Welcome, Admin | 
            <a href="admin_dashboard.php" style="color: white; text-decoration: none;">Profile</a> | 
            <a href="admin_logout.php" style="color: white; text-decoration: none;">Logout</a>
        </div>
    </div>

    <div class="container">

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message-box success-message">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message-box error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error_message']; ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <h2><i class="fas fa-user-edit"></i> Edit User</h2>

        <div class="card">
            <form method="POST" action="">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="admin_user.php" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>

</body>
</html>