<?php
include 'admin_check.php';
include "admin_db.php";

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users - LearnHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
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

        <h2><i class="fas fa-users"></i> All Users</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin_update_user.php?id=<?php echo $user['id']; ?>" class="edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 20px;">
                            <i class="fas fa-user-slash"></i> No users found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="btn" style="margin-top: 20px;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

    </div>

</body>
</html>