<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Determine base path
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_path = '';

if (strpos($current_dir, '/includes') !== false) {
    $base_path = '../';
}

// Redirect to login page with success message
header("Location: " . $base_path . "pages/auth/login.php?logout=success");
exit();