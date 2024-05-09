<?php
session_start();

// Destroy all session data
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session

// Redirect to a login page or homepage
header("Location: sign-in.html"); // Redirect to the login page
exit();
?>