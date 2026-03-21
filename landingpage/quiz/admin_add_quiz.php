<?php
include 'db.php';

if(isset($_POST['submit'])){
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];

    $sql = "INSERT INTO quizzes (course_id, title) 
            VALUES ('$course_id', '$title')";
    mysqli_query($conn, $sql);

    echo "Quiz Added Successfully!";
}
?>

<form method="POST">
    <h2>Add Quiz</h2>

    <select name="course_id" required>
        <option value="">Select Course</option>
        <?php
        $courses = mysqli_query($conn, "SELECT * FROM courses");
        while($row = mysqli_fetch_assoc($courses)){
            echo "<option value='{$row['id']}'>{$row['course_name']}</option>";
        }
        ?>
    </select>

    <input type="text" name="title" placeholder="Quiz Title" required>
    <button name="submit">Add Quiz</button>
</form>