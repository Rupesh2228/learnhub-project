<?php
require_once('../include.php');

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login-signup/login_signup.php");
    exit();
}

// Ensure price column exists in courses table (legacy DB fix)
$create_price_column = "ALTER TABLE courses ADD COLUMN IF NOT EXISTS price DECIMAL(10,2) NOT NULL DEFAULT 0";
$conn->query($create_price_column);

$course_stmt = $conn->prepare("SELECT course_name, price FROM courses ORDER BY course_name");
$course_stmt->execute();
$course_result = $course_stmt->get_result();
$courses = $course_result->fetch_all(MYSQLI_ASSOC);
$course_stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buy Course</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="form.css">
</head>
<body>

<div class="form-box">
  <!-- Arrow Link to Landing Pages -->
  <a href="course.php" class="back-link">
    <span>&#8592;</span>
  </a>

  <h2>Fill the form</h2>

  <form action="order.php" method="POST">
    <div class="form-group">
      <label>Phone Number</label>
      <input type="tel" name="phone" placeholder="123-456-7890" pattern="[0-9\s\-\+\$\$]{10,}" required>
    </div>

    <div class="form-group">
      <label>Select Course</label>
      <select id="course" name="course_name" onchange="setPrice()" required>
        <option value="">Choose a course...</option>
        <?php foreach ($courses as $course): ?>
          <option value="<?php echo htmlspecialchars($course['course_name']); ?>" data-price="<?php echo number_format($course['price'] ?? 0, 2, '.', ''); ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Total Price</label>
      <input type="text" id="price" name="price" readonly placeholder="Select a course">
    </div>

    <button type="submit">Proceed to Payment</button>
  </form>
</div>

<script>
const coursePrices = {
<?php foreach ($courses as $course): ?>
  "<?php echo addslashes($course['course_name']); ?>": "<?php echo addslashes($course['price'] ?? '0'); ?>",
<?php endforeach; ?>
};

function setPrice(){
  const courseSelect = document.getElementById("course");
  const selectedOption = courseSelect.options[courseSelect.selectedIndex];
  const priceField = document.getElementById("price");
  const selectedPrice = selectedOption ? selectedOption.dataset.price : '';
  priceField.value = selectedPrice !== '' && !isNaN(selectedPrice) ? parseFloat(selectedPrice).toFixed(2) : "";
}

function getParameterByName(name) {
  const url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
  const results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

const defaultCourse = getParameterByName('course_name');
if (defaultCourse) {
  const courseSelect = document.getElementById("course");
  for (let i = 0; i < courseSelect.options.length; i++) {
    if (courseSelect.options[i].value === defaultCourse) {
      courseSelect.selectedIndex = i;
      break;
    }
  }
}
setPrice();
</script>

</body>
</html>