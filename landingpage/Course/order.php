<?php
// Removed form_include.php - using mysqli from include.php
require_once('../include.php');

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login-signup/login_signup.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (empty($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'])) {
        echo "<script>alert('Security validation failed. Please try again.'); window.location.href='form.php';</script>";
        exit();
    }

    // Sanitize POST data properly (no htmlspecialchars on input)
    $phone = Security::sanitizeInput($_POST['phone'] ?? '');
    $course = Security::sanitizeInput($_POST['course_name'] ?? '');
    $price = Security::sanitizeInput($_POST['price'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Validate required fields
    if(!empty($name) && !empty($email) && !empty($course) && !empty($price)) {

        // Prepare SQL with user_id
        $sql = "INSERT INTO orders (user_id,  phone, course_name, price, purchase_date)
                VALUES (:user_id, :phone, :course, :price, NOW())";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':course', $course);
            $stmt->bindParam(':price', $price);

            if($stmt->execute()){
                echo "<script>
                        alert('Order placed successfully! We will contact you soon.');
                        window.location.href='../landingpages.html';
                      </script>";
                exit();
            } else {
                echo "<script>
                        alert('Database error occurred. Please try again.');
                        window.location.href='form.php';
                      </script>";
                exit();
            }

        } catch(PDOException $e){
            error_log("Database error: " . $e->getMessage());
            echo "<script>
                    alert('An error occurred. Please try again.');
                    window.location.href='form.php';
                  </script>";
            exit();
        }

    } else {
        echo "<script>
                alert('Please fill in all required fields.');
                window.location.href='form.php';
              </script>";
        exit();
    }

} else {
    echo "<script>
            alert('Invalid request!');
            window.location.href='form.php';
          </script>";
    exit();
}
?>