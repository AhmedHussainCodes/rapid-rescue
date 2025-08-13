<?php
// Database connection configuration for Rapid Rescue
// Simple and readable database connection

$password = "";
$database = "ambulance";
$server = "localhost";
$username = "root";

// Create connection
$conn = new mysqli($server, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection Error: " . $conn->connect_error);
}

// Set charset to utf8 for proper character handling
$conn->set_charset("utf8");

// Optional: Set timezone
date_default_timezone_set('America/New_York');
?>
