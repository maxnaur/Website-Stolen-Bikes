<?php
// logout.php
session_start();
session_destroy();

// Redirect to login
header('Location: ../login/Login.html');
exit;
?>
