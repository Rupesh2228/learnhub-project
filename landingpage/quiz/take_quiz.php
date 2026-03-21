<?php
include 'db.php';

$quiz_id = $_GET['quiz_id'];
$user_id = $_SESSION['user_id'];

$questions = mysqli_query($conn, 
"SELECT * FROM quiz_questions WHERE quiz_id='$quiz_id'");
?>

<form method="POST" action="submit_quiz.php">
<input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

<?php
$i = 1;
while($q = mysqli_fetch_assoc($questions)){
?>

<p><?php echo $i++ . ". " . $q['question']; ?></p>

<input type="radio" name="q_<?php echo $q['id']; ?>" value="A"> <?php echo $q['option_a']; ?><br>
<input type="radio" name="q_<?php echo $q['id']; ?>" value="B"> <?php echo $q['option_b']; ?><br>
<input type="radio" name="q_<?php echo $q['id']; ?>" value="C"> <?php echo $q['option_c']; ?><br>
<input type="radio" name="q_<?php echo $q['id']; ?>" value="D"> <?php echo $q['option_d']; ?><br>

<hr>

<?php } ?>

<button type="submit">Submit Quiz</button>
</form>