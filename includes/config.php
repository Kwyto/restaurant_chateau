<?php
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'luxury_restaurant');

$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Time-based greeting function
function getGreeting() {
    $hour = date('H');
    if ($hour >= 5 && $hour < 12) {
        return 'Good Morning';
    } elseif ($hour >= 12 && $hour < 17) {
        return 'Good Afternoon';
    } elseif ($hour >= 17 && $hour < 20) {
        return 'Good Evening';
    } else {
        return 'Good Night';
    }
}

// Get user data if logged in
function getUserData($conn, $userId) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// Get reservations for user
function getUserReservations($conn, $userId) {
    $query = "SELECT r.*, p.amount, p.payment_method, p.status as payment_status 
              FROM reservations r
              LEFT JOIN payments p ON r.id = p.reservation_id
              WHERE r.user_id = ?
              ORDER BY r.reservation_date DESC, r.reservation_time DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

// Generate random reservation number
function generateReservationNumber() {
    return 'RES-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Untuk menampilkan menu
function getMenuItems($conn, $category = null) {
    if ($category) {
        $query = "SELECT * FROM menu_items WHERE category = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $category);
    } else {
        $query = "SELECT * FROM menu_items";
        $stmt = mysqli_prepare($conn, $query);
    }
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

?>