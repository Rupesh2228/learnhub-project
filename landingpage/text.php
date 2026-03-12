<?php
$password = "admin123"; // Change this to your desired password
$hashed = password_hash($password, PASSWORD_BCRYPT);
echo $hashed; // Copy this hash
?>