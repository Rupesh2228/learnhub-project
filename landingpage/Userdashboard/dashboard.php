<?php
include 'user_check.php';
include "db.php";

$user_id = $_SESSION['user_id'];

// Fetch User Details from Database (JOIN with user_profiles)
$stmt = $conn->prepare("SELECT users.full_name, users.email, user_profiles.profile_image, user_profiles.date_of_birth 
                        FROM users 
                        LEFT JOIN user_profiles ON users.id = user_profiles.user_id 
                        WHERE users.id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch Course Orders from Orders Table
$orders_stmt = $conn->prepare("SELECT id, course_name, price, purchase_date FROM orders WHERE user_id = ?");
$orders_stmt->bind_param("i", $user_id);
$orders_stmt->execute();
$orders = $orders_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnHub Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --bg: #f8f9fa;
            --text: #2b2d42;
            --card-bg: #ffffff;
            --success: #28a745;
            --error: #dc3545;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
        }

        .navbar {
            background: var(--primary);
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar h1 { margin: 0; font-size: 1.5rem; }
        .user-info { font-size: 0.9rem; }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        h2, h3 { color: var(--secondary); margin-top: 0; }

        /* Profile Details Display */
        .profile-details {
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #f0f0f0;
        }

        .profile-info {
            flex: 1;
            min-width: 250px;
        }

        .profile-info p {
            margin: 10px 0;
            font-size: 1rem;
        }

        .profile-info strong {
            color: var(--secondary);
            min-width: 120px;
            display: inline-block;
        }

        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        
        input[type="text"], input[type="email"], input[type="date"], textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border 0.3s;
        }

        input:focus, textarea:focus { border-color: var(--primary); outline: none; }

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
        .btn:hover { background: var(--secondary); }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }

        /* Course Card Styles */
        .course-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            background: #fff;
            border-left: 5px solid var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .course-title { 
            font-size: 1.3rem; 
            font-weight: bold; 
            color: var(--text); 
        }

        .course-meta {
            display: flex;
            gap: 20px;
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 15px;
        }

        .course-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        /* Success/Error Messages */
        .message-box {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 500;
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
        <a href="../landingpages.html"><h1><i class="fas fa-graduation-cap"></i> LearnHub</h1></a>
        <div class="user-info">
            Welcome, <?php echo htmlspecialchars($user['full_name']); ?> | 
            <a href="../login-signup/logout.php" style="color: white; text-decoration: none;">Logout</a>
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

        <!-- Profile Section (Display Mode) -->
        <div class="card">
            <h3><i class="fas fa-user-circle"></i> My Profile</h3>
            
            <div class="profile-details">
                <div class="profile-image-container">
                    <?php if(isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                        <img src="<?php echo WEB_UPLOADS . htmlspecialchars($user['profile_image']); ?>" class="profile-image" alt="Profile">
                    <?php else: ?>
                        <div style="width:150px; height:150px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto;">
                            <i class="fas fa-user" style="font-size: 4rem; color:#ccc;"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                    <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['date_of_birth'] ?? 'Not Set'); ?></p>
                </div>
            </div>

            <div class="action-buttons">
                <a href="change_profile.php" class="btn">Edit Profile</a>
            </div>
        </div>

        <!-- Courses Section (From Orders Table) -->
        <div class="card">
            <h3><i class="fas fa-shopping-bag"></i> My Courses</h3>
            
            <?php if($orders->num_rows > 0): ?>
                <?php while($order = $orders->fetch_assoc()): ?>
                <div class="course-card">
                    <div class="course-header">
                        <div>
                            <span class="course-title"><?php echo htmlspecialchars($order['course_name']); ?></span>
                            <div class="course-meta">
                                <span><i class="fas fa-calendar"></i> Purchased: <?php echo date('M d, Y', strtotime($order['purchase_date'])); ?></span>
                                <span><i class="fas fa-rupee-sign"></i> Price: Rs <?php echo number_format($order['price'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Course</label>
                            <input type="hidden" name="course_name" value="<?php echo htmlspecialchars($order['course_name']); ?>">
                            <input type="text" value="<?php echo htmlspecialchars($order['course_name']); ?>" disabled style="background: #f0f0f0;">
                        </div>

                        <div class="form-group">
                            <label>Assignment Title</label>
                            <input type="text" name="title" required placeholder="e.g., Final Project">
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" rows="3" required placeholder="Describe your submission..."></textarea>
                        </div>

                       <div class="form-group">
    <label>Upload Image (JPG, PNG, GIF)</label>
    <input type="file" name="assignment_img" accept="image/*" required>
    <small style="color: #666;">Only JPG, PNG, GIF allowed</small>
</div>

                        <div class="action-buttons">
                            <button type="submit" class="btn">Submit Assignment</button>
                        </div>
                    </form>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #777;">
                    <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px;"></i>
                    <p>You haven't purchased any courses yet.</p>
                    <a href="../Course/course.html" class="btn">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>