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

// Get user course purchases
$recent_orders = $conn->query("SELECT o.id, o.user_id, o.course_name, o.price, o.purchase_date, u.full_name
                              FROM orders o
                              JOIN users u ON o.user_id = u.id
                              ORDER BY o.purchase_date DESC
                              LIMIT 50");

// Get existing quizzes and question counts
$quizzes = $conn->query("SELECT q.id, q.course_id, c.course_name, COUNT(qq.id) AS question_count
                         FROM quizzes q
                         LEFT JOIN courses c ON q.course_id = c.id
                         LEFT JOIN quiz_questions qq ON qq.quiz_id = q.id
                         GROUP BY q.id, q.course_id, c.course_name
                         ORDER BY q.id DESC");

// Get quiz results for admin view
$quiz_results = $conn->query("SELECT qr.id, u.full_name, c.course_name, qr.score, qr.total_questions, qr.percentage, qr.taken_at
                             FROM quiz_results qr
                             JOIN users u ON qr.user_id = u.id
                             JOIN quizzes q ON qr.quiz_id = q.id
                             JOIN courses c ON q.course_id = c.id
                             ORDER BY qr.taken_at DESC LIMIT 100");

// Get all courses for delete management
$courses_list = $conn->query("SELECT id, course_name, price FROM courses ORDER BY course_name");
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
    <link rel="stylesheet" href="admin_dashboard.css">
</head>
<body>

    <div class="navbar">
          <nav class="nav-links">
            <a href="../landingpages.html">Home</a>
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

        <!-- Admin Actions -->
        <div class="card" style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="admin_add_user.php" class="btn btn-success" style="margin-bottom:5px;">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
            <a href="admin_add_quiz.php" class="btn btn-primary" style="margin-bottom:5px;">
                <i class="fas fa-question-circle"></i> Add Quiz Question
            </a>
            <a href="admin_add_course.php" class="btn btn-primary" style="margin-bottom:5px;">
                <i class="fas fa-book"></i> Add Course
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

        <!-- Quiz Management -->
        <div class="card">
            <h3><i class="fas fa-question-circle"></i> Existing Quizzes</h3>
            <?php if ($quizzes && $quizzes->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>Quiz ID</th><th>Course</th><th>Questions</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $quiz['id']; ?></td>
                                <td><?php echo htmlspecialchars($quiz['course_name'] ?? 'Unknown'); ?></td>
                                <td><?php echo intval($quiz['question_count']); ?></td>
                                <td>
                                    <a href="admin_add_quiz.php?course_id=<?php echo $quiz['course_id']; ?>" class="edit">Edit</a>
                                    <a href="admin_delete_quiz.php?id=<?php echo $quiz['id']; ?>" class="delete" onclick="return confirm('Delete this quiz and all related data?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No quizzes created yet.</p>
            <?php endif; ?>
        </div>

        <!-- Quiz Results -->
        <div class="card">
            <h3><i class="fas fa-chart-line"></i> User Quiz Results</h3>
            <?php if ($quiz_results && $quiz_results->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>ID</th><th>User</th><th>Course</th><th>Score</th><th>Percent</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($qr = $quiz_results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo intval($qr['id']); ?></td>
                            <td><?php echo htmlspecialchars($qr['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($qr['course_name']); ?></td>
                            <td><?php echo intval($qr['score']) . ' / ' . intval($qr['total_questions']); ?></td>
                            <td><?php echo number_format($qr['percentage'], 2); ?>%</td>
                            <td><?php echo date('Y-m-d H:i', strtotime($qr['taken_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No quiz results yet.</p>
            <?php endif; ?>
        </div>

        <!-- Course Management -->
        <div class="card">
            <h3><i class="fas fa-book"></i> Courses</h3>
            <?php if ($courses_list && $courses_list->num_rows > 0): ?>
            <table>
                <thead>
                    <tr><th>ID</th><th>Course</th><th>Price</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php while ($course = $courses_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo intval($course['id']); ?></td>
                        <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                        <td>Rs <?php echo number_format($course['price'], 2); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="admin_add_course.php" class="edit">Edit</a>
                                <a href="admin_delete_course.php?id=<?php echo intval($course['id']); ?>" class="delete" onclick="return confirm('Delete course and related user purchases?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No courses found.</p>
            <?php endif; ?>
        </div>

        <!-- User Purchases -->
        <div class="card">
            <h3><i class="fas fa-shopping-cart"></i> User Course Purchases</h3>
            <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr><th>Order ID</th><th>User</th><th>Course</th><th>Price</th><th>Date</th><th>Action</th></tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo intval($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['course_name']); ?></td>
                                <td>Rs <?php echo number_format($order['price'], 2); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($order['purchase_date'])); ?></td>
                                <td><a href="admin_delete_order.php?id=<?php echo intval($order['id']); ?>" class="delete" onclick="return confirm('Remove this user purchase?');">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No purchases found.</p>
            <?php endif; ?>
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
                        <div class="assignment-image-container" onclick="viewFile(this)" data-file="<?php echo $assignment['file_path'] ? WEB_ASSIGNMENTS . htmlspecialchars($assignment['file_path']) : ''; ?>">
                            <?php if($assignment['file_path']): ?>
                                <?php $file_ext = strtolower(pathinfo($assignment['file_path'], PATHINFO_EXTENSION)); ?>
                                <?php if(in_array($file_ext, ['jpg','jpeg','png','gif'])): ?>
                                    <img src="<?php echo WEB_ASSIGNMENTS . htmlspecialchars($assignment['file_path']); ?>" class="assignment-image" alt="Assignment" style="cursor: pointer;" onerror="displayNoImage(this)">
                                <?php else: ?>
                                    <div class="file-icon">
                                        <i class="fas fa-file-<?php echo ($file_ext == 'pdf') ? 'pdf' : 'alt'; ?>"></i>
                                        <span><?php echo strtoupper($file_ext); ?></span>
                                    </div>
                                <?php endif; ?>
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
            <div class="modal-header">
                <button class="delete-modal-btn" id="modalDeleteBtn" onclick="deleteFromModal()" title="Delete Assignment">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="close-modal" onclick="closeImageModal()">&times;</button>
            </div>
            <img id="modalImage" src="" alt="Assignment">
        </div>
    </div>

    <script>
        function displayNoImage(img) {
            const container = img.parentElement;
            container.innerHTML = '<div class="no-image"><i class="fas fa-image"></i></div>';
        }

        function viewFile(element) {
            const fileSrc = element.getAttribute('data-file');
            if (fileSrc) {
                const img = element.querySelector('img');
                if (img) {
                    // It's an image, show modal
                    document.getElementById('modalImage').src = img.src;
                    // Get assignment ID from the card
                    const assignmentCard = element.closest('.assignment-card');
                    const idText = assignmentCard.querySelector('.assignment-info p').textContent;
                    const id = idText.replace('ID: ', '').trim();
                    document.getElementById('imageModal').setAttribute('data-assignment-id', id);
                    document.getElementById('imageModal').classList.add('show');
                } else {
                    // It's a file, open in new tab
                    window.open(fileSrc, '_blank');
                }
            }
        }

        function deleteFromModal() {
            const id = document.getElementById('imageModal').getAttribute('data-assignment-id');
            if (id && confirm('Are you sure you want to delete this assignment?')) {
                window.location.href = 'admin_delete_assignment.php?id=' + id;
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
</html> i have this code on admin_dashboard.php