<?php
include 'admin_check.php';
include 'admin_db.php';

// Add quiz
if(isset($_POST['submit'])){
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];

    mysqli_query($conn, "INSERT INTO quizzes (course_id, title) 
                         VALUES ('$course_id','$title')");
}

// Get courses
$courses = mysqli_query($conn, "SELECT * FROM courses");

// Get quizzes
$quizzes = mysqli_query($conn, 
"SELECT q.*, c.course_name 
 FROM quizzes q 
 JOIN courses c ON q.course_id = c.id");
?>

<h2>Add Quiz</h2>

<form method="POST">
    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php while($c = mysqli_fetch_assoc($courses)){ ?>
            <option value="<?php echo $c['id']; ?>">
                <?php echo $c['course_name']; ?>
            </option>
        <?php } ?>
    </select>

    <input type="text" name="title" placeholder="Quiz Title" required>
    <button name="submit">Add Quiz</button>
</form>

<hr>

<h2>All Quizzes</h2>

<?php while($q = mysqli_fetch_assoc($quizzes)){ ?>
    <p>
        <?php echo $q['title']; ?> (<?php echo $q['course_name']; ?>)
        👉 <a href="add_questions.php?quiz_id=<?php echo $q['id']; ?>">
            Add Questions
        </a>
    </p>
<?php } ?>