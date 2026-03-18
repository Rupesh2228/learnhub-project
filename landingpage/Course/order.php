<?php
require_once('../include.php');

// --- Check if user is logged in ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login-signup/login_signup.php"); 
    exit();
}

// --- Setup PDO connection ---
try {
    $pdo = new PDO("mysql:host=localhost;dbname=learnhub_db", "root", ""); // adjust DB name, username, password
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize POST data
    $phone  = htmlspecialchars($_POST['phone'] ?? '');
    $course = htmlspecialchars($_POST['course_name'] ?? '');
    $price  = htmlspecialchars($_POST['price'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Validate required fields
    if (!empty($course) && !empty($price) && !empty($phone)) {
        $sql = "INSERT INTO orders (user_id, phone, course_name, price, purchase_date)
                VALUES (:user_id, :phone, :course, :price, NOW())";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':course', $course);
            $stmt->bindParam(':price', $price);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Order placed successfully! We will contact you soon.');
                        window.location.href='../landingpages.html';
                      </script>";
                exit();
            } else {
                echo "<script>alert('Database error occurred. Please try again.'); window.location.href='form.php';</script>";
                exit();
            }
        } catch(PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            echo "<script>alert('An error occurred. Please try again.'); window.location.href='form.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Please fill in all required fields.'); window.location.href='form.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request!'); window.location.href='form.php';</script>";
    exit();
}
?>