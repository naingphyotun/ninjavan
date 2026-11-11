<?php
session_start();
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session
header("Location: ../Pages/Home.php"); // Redirect back to home
exit();
?>