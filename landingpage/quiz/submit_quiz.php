<?php
include 'db.php';

$user_id = $_SESSION['user_id'];
$quiz_id = $_POST['quiz_id'];

$questions = mysqli_query($conn, 
"SELECT * FROM quiz_questions WHERE quiz_id='$quiz_id'");

$score = 0;
$total = mysqli_num_rows($questions);

while($q = mysqli_fetch_assoc($questions)){
    $qid = $q['id'];
    $correct = $q['correct_answer'];

    if(isset($_POST["q_$qid"])){
        $user_ans = $_POST["q_$qid"];

        if($user_ans == $correct){
            $score++;
            $is_correct = 1;
        } else {
            $is_correct = 0;
        }

        mysqli_query($conn, "INSERT INTO quiz_answers
        (result_id, question_id, user_answer, correct_answer, is_correct)
        VALUES (0, '$qid', '$user_ans', '$correct', '$is_correct')");
    }
}

$percentage = ($score / $total) * 100;

mysqli_query($conn, "INSERT INTO quiz_results
(user_id, quiz_id, score, total_questions, percentage)
VALUES ('$user_id','$quiz_id','$score','$total','$percentage')");

echo "✅ Your Score: $score / $total";