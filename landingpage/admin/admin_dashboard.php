<?php
include 'admin_check.php';
include "admin_db.php";

// Get statistics
$users_count = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$assignments_count = $conn->query("SELECT COUNT(*) as count FROM assignments")->fetch_assoc()['count'];
$courses_count = $conn->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc()['count'];

// Get recent users
$recent_users = $conn->query("SELECT * FROM users ORDER BY id DESC LIMIT 10");

// Get recent assignments with course names
$recent_assignments = $conn->query("SELECT a.*, u.full_name, c.course_name 
                                     FROM assignments a 
                                     JOIN users u ON a.user_id = u.id 
                                     LEFT JOIN courses c ON a.course_id = c.id 
                                     ORDER BY a.id DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - LearnHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #3f37c9;
            --accent: #ec4899;
            --bg: #f8fafc;
            --surface: #f1f5f9;
            --text: #1e293b;
            --text-light: #64748b;
            --card-bg: #ffffff;
            --success: #10b981;
            --success-light: #d1fae5;
            --error: #ef4444;
            --error-light: #fee2e2;
            --warning: #f59e0b;
            --info: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.15);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar h1 i {
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            flex: 1;
            margin-left: 50px;
        }

        .nav-links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .nav-links a:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.95rem;
        }

        .user-info a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .user-info a:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        /* ===== CONTAINER ===== */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* ===== CARDS ===== */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .card-header {
            padding: 25px;
            border-bottom: 1px solid var(--surface);
            background: var(--surface);
        }

        .card-body {
            padding: 25px;
        }

        .card h3 {
            margin: 0;
            color: var(--text);
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card h3 i {
            color: var(--primary);
            font-size: 1.5rem;
        }

        /* ===== STATISTICS SECTION ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-card.users { border-left-color: var(--info); }
        .stat-card.assignments { border-left-color: var(--success); }
        .stat-card.courses { border-left-color: var(--warning); }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text);
            margin: 10px 0;
        }

        .stat-label {
            font-size: 0.95rem;
            color: var(--text-light);
            font-weight: 500;
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        .stat-card.users .stat-icon { color: var(--info); }
        .stat-card.assignments .stat-icon { color: var(--success); }
        .stat-card.courses .stat-icon { color: var(--warning); }

        /* ===== TABLE STYLES ===== */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background: var(--surface);
        }

        table th {
            padding: 15px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        table td {
            padding: 15px 20px;
            border-bottom: 1px solid #e2e8f0;
            color: var(--text);
        }

        table tbody tr {
            transition: all 0.2s ease;
        }

        table tbody tr:hover {
            background: var(--surface);
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-user {
            background: #dbeafe;
            color: #0c4a6e;
        }

        .badge-email {
            background: #f3e8ff;
            color: #6b21a8;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .edit, .delete {
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .edit {
            background: var(--primary);
            color: white;
        }

        .edit:hover {
            background: var(--secondary);
        }

        .delete {
            background: var(--error);
            color: white;
        }

        .delete:hover {
            background: #c82333;
        }

        /* Assignment Grid Styles */
        .assignment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .course-section {
            margin-bottom: 30px;
        }

        .course-section h4 {
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 1.1rem;
        }

        .assignment-card {
            background: var(--card-bg);
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .assignment-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }

        .assignment-image-container {
            position: relative;
            width: 100%;
            height: 200px;
            background: #f0f0f0;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .assignment-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .no-image {
            font-size: 3rem;
            color: #ccc;
        }

        .delete-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s;
            border: none;
        }

        .delete-btn:hover {
            background: rgba(200, 35, 51, 1);
            transform: scale(1.1);
        }

        .assignment-info {
            padding: 15px;
        }

        .assignment-info p {
            margin: 8px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .assignment-info strong {
            color: var(--secondary);
        }

        .btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

        .btn:hover {
            background: var(--secondary);
        }

        .btn-success {
            background: var(--success);
        }

        .btn-success:hover {
            background: #218838;
        }

        .message-box {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

    
    </style>
</head>
<body>

    <div class="navbar">
          <nav class="nav-links">
                <a href="../landingpages.html">Home</a>
                <a href="../Course/course.html">Courses</a>
                <a href="../About/about.html">About</a>
                <a href="../Userdashboard/dashboard.php">Profile</a>
            </nav>
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

        <!-- Add New User -->
        <div class="card">
            <a href="admin_add_user.php" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>

        <!-- Recent Users -->
        <div class="card">
            <h3><i class="fas fa-users"></i> Recent Users</h3>
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
                    <?php if ($recent_users->num_rows > 0): ?>
                        <?php while($user = $recent_users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="admin_update_user.php?id=<?php echo $user['id']; ?>" class="edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="admin_delete_user.php?id=<?php echo $user['id']; ?>" class="delete" onclick="return confirm('Are you sure?')">
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
        </div>

        <!-- Recent Assignments -->
        <div class="card">
            <h3><i class="fas fa-file-alt"></i> Recent Assignments</h3>
            <div class="assignment-grid">
                <?php if ($recent_assignments->num_rows > 0): ?>
                    <?php 
                    $current_course = '';
                    $recent_assignments->data_seek(0);
                    while($assignment = $recent_assignments->fetch_assoc()): 
                        // Display course header when course changes
                        if ($current_course !== $assignment['course_name']) {
                            if ($current_course !== '') {
                                echo '</div></div>';
                            }
                            $current_course = $assignment['course_name'];
                            echo '<div class="course-section">';
                            echo '<h4 style="color: var(--primary); margin-top: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--primary);">' . htmlspecialchars($assignment['course_name'] ?? 'Uncategorized') . '</h4>';
                            echo '<div class="assignment-grid">';
                        }
                    ?>
                    <div class="assignment-card">
                        <div class="assignment-image-container" onclick="viewImage(this)">
                            <?php if($assignment['image']): ?>
                                <img src="<?php echo WEB_ASSIGNMENTS . htmlspecialchars($assignment['image']); ?>" class="assignment-image" alt="Assignment" style="cursor: pointer;" onerror="displayNoImage(this)">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-image"></i>
                                </div>
                            <?php endif; ?>
                            <a href="admin_delete_assignment.php?id=<?php echo $assignment['id']; ?>" 
                               class="delete-btn" 
                               onclick="event.stopPropagation(); return confirm('Are you sure you want to delete this assignment?')" 
                               title="Delete Assignment">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                        <div class="assignment-info">
                            <p><strong>ID:</strong> <?php echo $assignment['id']; ?></p>
                            <p><strong>User:</strong> <?php echo htmlspecialchars($assignment['full_name']); ?></p>
                            <p><strong>Title:</strong> <?php echo htmlspecialchars($assignment['title']); ?></p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php if ($current_course !== '') echo '</div></div>'; // Close last section ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; width: 100%;">
                        <i class="fas fa-file" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
                        <p style="color: #999;">No assignments found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeImageModal()">&times;</button>
            <img id="modalImage" src="" alt="Assignment">
        </div>
    </div>

    <script>
        function displayNoImage(img) {
            const container = img.parentElement;
            container.innerHTML = '<div class="no-image"><i class="fas fa-image"></i></div>';
        }

        function viewImage(element) {
            const img = element.querySelector('img');
            if (img && img.src) {
                document.getElementById('modalImage').src = img.src;
                document.getElementById('imageModal').classList.add('show');
            }
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.remove('show');
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>

</body>
</html>