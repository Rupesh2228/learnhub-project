<?php
include 'db.php';

$user_id = $_SESSION['user_id'];
$course_id = $_GET['course_id'];

// Check purchase
$check = mysqli_query($conn, 
"SELECT * FROM orders WHERE user_id='$user_id' AND course_id='$course_id'");

if(mysqli_num_rows($check) == 0){
    echo "❌ You must purchase this course first!";
    exit();
}

// Show quizzes
$quizzes = mysqli_query($conn, 
"SELECT * FROM quizzes WHERE course_id='$course_id'");

while($q = mysqli_fetch_assoc($quizzes)){
    echo "<a href='take_quiz.php?quiz_id={$q['id']}'>
            {$q['title']}
          </a><br>";
}