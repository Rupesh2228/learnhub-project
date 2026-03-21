<?php
include 'db.php';

$quiz_id = $_GET['quiz_id'];

if(isset($_POST['submit'])){
    $question = $_POST['question'];
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $correct = $_POST['correct'];

    $sql = "INSERT INTO quiz_questions 
    (quiz_id, question, option_a, option_b, option_c, option_d, correct_answer)
    VALUES ('$quiz_id','$question','$a','$b','$c','$d','$correct')";

    mysqli_query($conn, $sql);

    echo "Question Added!";
}
?>

<form method="POST">
    <h2>Add Question</h2>

    <input type="text" name="question" placeholder="Question" required><br>
    <input type="text" name="a" placeholder="Option A"><br>
    <input type="text" name="b" placeholder="Option B"><br>
    <input type="text" name="c" placeholder="Option C"><br>
    <input type="text" name="d" placeholder="Option D"><br>

    <select name="correct">
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="D">D</option>
    </select>

    <button name="submit">Add Question</button>
</form>