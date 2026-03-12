<?php
require_once('../include.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login-signup/login_signup.php"); 
    exit();
}

// Generate CSRF token
$csrf_token = Security::generateCSRFToken();
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

<a href="course.html" class="back-link">
<span>&#8592;</span>
</a>

<h2>Checkout</h2>

<form action="order.php" method="POST">

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">


<div class="form-group">
<label>Phone Number</label>
<input type="tel" name="phone" placeholder="9800000000" required>
</div>

<div class="form-group">
<label>Select Course</label>
<select id="course" name="course_name" onchange="setPrice()" required>

<option value="">Choose a course...</option>
<option value="Fullstack Development">Fullstack Development</option>
<option value="AI/ML">AI / Machine Learning</option>
<option value="Cybersecurity">Cybersecurity</option>
<option value="UI/UX Design">UI / UX Design</option>

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

function setPrice(){

let course = document.getElementById("course").value;
let priceField = document.getElementById("price");

if(course === ""){
priceField.value = "";
return;
}

switch(course) {

case "Fullstack Development":
priceField.value = "3000.00";
break;

case "AI/ML":
priceField.value = "5000.00";
break;

case "Cybersecurity":
priceField.value = "7000.00";
break;

case "UI/UX Design":
priceField.value = "1500.00";
break;

default:
priceField.value = "";

}

}

</script>

</body>
</html>