<?php
session_start();
$_SESSION = array();
session_destroy();
echo "<script>
alert('Logged out successfully!');
window.location.href='login_signup.html';
</script>";
exit();
?>
