<?php
// Logout functionality for Rapid Rescue
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page with logout message
header('Location: index.php?logout=1');
exit();
?>
