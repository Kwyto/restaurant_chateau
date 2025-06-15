<?php
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection constants
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'luxury_restaurant');

// Establish database connection
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if(!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Wrap each function in a !function_exists check for robustness.

if (!function_exists('getGreeting')) {
    /**
     * Returns a greeting based on the current time of day.
     * @return string The greeting message.
     */
    function getGreeting() {
        // Set timezone to avoid server configuration issues
        date_default_timezone_set('Asia/Jakarta');
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
}

if (!function_exists('getUserData')) {
    /**
     * Gets a user's data from the database.
     * @param mysqli $conn The database connection object.
     * @param int $userId The ID of the user.
     * @return array|null An associative array of user data or null if not found.
     */
    function getUserData($conn, $userId) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }
}

if (!function_exists('getUserReservations')) {
    /**
     * Gets all reservations for a specific user.
     * @param mysqli $conn The database connection object.
     * @param int $userId The ID of the user.
     * @return mysqli_result|false The result object for the query.
     */
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
}

if (!function_exists('generateReservationNumber')) {
    /**
     * Generates a unique reservation number.
     * @return string The reservation number.
     */
    function generateReservationNumber() {
        return 'RES-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
}

if (!function_exists('getMenuItems')) {
    /**
     * Gets menu items, optionally filtered by category.
     * @param mysqli $conn The database connection object.
     * @param string|null $category The category to filter by.
     * @return mysqli_result|false The result object for the query.
     */
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
}

?>