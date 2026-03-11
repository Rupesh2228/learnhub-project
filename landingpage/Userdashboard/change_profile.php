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

if (!$user) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - LearnHub</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --bg: #f8f9fa;
            --text: #2b2d42;
            --card-bg: #ffffff;
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
            max-width: 600px;
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

        .profile-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f0f0f0;
            margin-bottom: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }

        @media (max-width: 600px) {
            .container { margin: 10px auto; }
            .navbar { padding: 15px; }
            .action-buttons { flex-direction: column; }
            .btn { width: 100%; margin-right: 0; margin-bottom: 10px; }
        }
    </style>
</head>
<body>

    <div class="navbar">
        <h1><i class="fas fa-graduation-cap"></i> LearnHub</h1>
        <div class="user-info">
            Edit Profile | 
            <a href="dashboard.php" style="color: white; text-decoration: none;\">Dashboard</a> | 
            <a href=\"../login-signup/logout.php\" style="color: white; text-decoration: none;\">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="card">
            <h3><i class="fas fa-user-edit"></i> Edit Profile</h3>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?php echo htmlspecialchars($user['date_of_birth']); ?>">
                </div>

                <div class="form-group">
                    <label>Profile Image</label>
                    <?php if(isset($user['profile_image']) && !empty($user['profile_image'])): ?>
                        <img src="<?php echo WEB_UPLOADS . htmlspecialchars($user['profile_image']); ?>" class="profile-preview" alt="Profile">
                    <?php else: ?>
                        <div style="width:120px; height:120px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                            <i class="fas fa-user" style="font-size: 3rem; color:#ccc;"></i>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="profile_image" accept="image/*">
                    <small style="color: #666;">Leave empty to keep current image</small>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn">Save Changes</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

    </div>

</body>
</html>