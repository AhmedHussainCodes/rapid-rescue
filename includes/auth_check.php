<?php
// Authentication check helper
// Include this file in pages that require user login

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Optional: Check if user is admin (for admin pages)
function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: index.php');
        exit();
    }
}

// Optional: Check if user is regular user (for user pages)
function requireUser() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        header('Location: admin/dashboard.php');
        exit();
    }
}
?>
