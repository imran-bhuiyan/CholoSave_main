<?php
include 'session.php'; // Include the session management file

header("Location: /test_project/index.php");
exit();

// Check if the user is logged in
if (!isLoggedIn()) {
   
    exit();
}

// Destroy the session
session_start(); // Start the session
session_unset(); // Remove all session variables
session_destroy(); // Destroy the session


?>
