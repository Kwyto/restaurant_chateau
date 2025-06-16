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

function getCustomers($conn, $search = null, $page = 1, $perPage = 10, $limit = null) {
    $page = max(1, (int)$page);
    $perPage = max(1, (int)$perPage);
    $offset = ($page - 1) * $perPage;

    $query = "SELECT * FROM users";
    $params = [];
    $types = [];
    $conditions = [];

    // Tambahkan kondisi pencarian
    if (!empty($search)) {
        $conditions[] = '(CONCAT(first_name, " ", last_name) LIKE ?)';
        $params[] = '%' . $search . '%';
        $types[] = 's';
    }

    // Gabungkan kondisi ke dalam query
    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    // Tambahkan limit/pagination
    if ($limit !== null) {
        $query .= " LIMIT ?";
        $params[] = (int)$limit;
        $types[] = 'i';
    } else {
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types[] = 'ii';
    }

    // Prepare statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    // Bind parameters kalau ada
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, implode('', $types), ...$params);
    }

    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}


function countTotalCustomer($conn) {
    $query = 'SELECT COUNT(*) AS total FROM users';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return (int)$row['total'];
}

function getReservations($conn, $status = null, $search = null, $date = null, $page = 1, $perPage = 10, $limit = null) {
    // Validasi parameter
    $page = max(1, (int)$page);
    $perPage = max(1, (int)$perPage);
    $offset = ($page - 1) * $perPage;

    // Query dasar dengan JOIN yang benar dan kolom spesifik
    $query = "SELECT 
                r.*,
                u.id AS user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.membership_level
              FROM reservations r
              JOIN users u ON r.user_id = u.id
              WHERE 1=1";
    
    $params = [];
    $types = '';

    // Filter status
    if (!empty($status)) {
        $query .= " AND r.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    // Filter pencarian
    if (!empty($search)) {
        $query .= " AND (r.reservation_number LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $types .= 'ssss';
    }

    // Filter tanggal
    if (!empty($date)) {
        $query .= " AND r.reservation_date = ?";
        $params[] = $date;
        $types .= 's';
    }

    // Tambahkan sorting
    $query .= " ORDER BY r.reservation_date DESC, r.reservation_time DESC";

    // Jika ada limit yang spesifik (untuk keperluan lain)
    if ($limit !== null) {
        $query .= " LIMIT ?";
        $params[] = (int)$limit;
        $types .= 'i';
    } else {
        // Tambahkan pagination hanya jika tidak ada limit spesifik
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
    }

    // Persiapkan statement
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        error_log("Error preparing statement: " . mysqli_error($conn));
        throw new Exception("Database error occurred");
    }

    // Bind parameter jika ada
    if (!empty($params)) {
        if (!mysqli_stmt_bind_param($stmt, $types, ...$params)) {
            error_log("Error binding parameters: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            throw new Exception("Database error occurred");
        }
    }

    // Eksekusi query
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Error executing statement: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        throw new Exception("Database error occurred");
    }

    // Dapatkan hasil
    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        error_log("Error getting result: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        throw new Exception("Database error occurred");
    }
    
    // Tutup statement
    mysqli_stmt_close($stmt);

    return $result;
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

// Get menu items
function getMenuItems($conn, $category = null, $search = null, $page = 1, $perPage = 10, $limit = null) {
    $query = "SELECT * FROM menu_items WHERE 1=1";
    $params = [];
    $types = '';
    $offset = ($page - 1) * $perPage;
    
    if ($category) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($search) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm]);
        $types .= 'ss';
    }

    if ($limit !== null) {
        $query .= " LIMIT ?";
        $params[] = (int)$limit;
        $types .= 'i';
    } else {
        // Tambahkan pagination hanya jika tidak ada limit spesifik
        $query .= " LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        $types .= 'ii';
    }
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Error preparing statement: " . mysqli_error($conn));
    }
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    if (!mysqli_stmt_execute($stmt)) {
        die("Error executing statement: " . mysqli_stmt_error($stmt));
    }
    
    $result = mysqli_stmt_get_result($stmt);
    
    mysqli_stmt_close($stmt);
    
    return $result;
}

function countMenuItems($conn, $category = null, $search = null) {
    $query = "SELECT COUNT(*) as total FROM menu_items WHERE 1=1";
    $params = [];
    $types = '';
    
    if ($category) {
        $query .= " AND category = ?";
        $params[] = $category;
        $types .= 's';
    }
    
    if ($search) {
        $query .= " AND (name LIKE ? OR description LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm]);
        $types .= 'ss';
    }
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    
    return $row['total'];
}

// Generate random reservation number
function generateReservationNumber() {
    return 'RES-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

function getCoupon($conn, $search = null, $page = 1, $perPage = 10, $limit = null) {
    $page = max(1, (int)$page);
    $perPage = max(1, (int)$perPage);
    $offset = ($page - 1) * $perPage;

    $query = 'SELECT * FROM coupons';
    $conditions = [];
    $params = [];
    $types = '';

    // Search filter
    if (!empty($search)) {
        $conditions[] = '(code LIKE ?)';
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    // Gabung kondisi jika ada
    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }

    // Pagination or Limit
    if ($limit !== null) {
        $query .= ' LIMIT ?';
        $params[] = (int)$limit;
        $types .= 'i';
    } else {
        $query .= ' LIMIT ?, ?';
        $params[] = $offset;
        $params[] = $perPage;
        $types .= 'ii';
    }

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    // Bind param jika ada
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function countTotalCoupons($conn) {
    $query = 'SELECT COUNT(*) AS total FROM coupons';
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return (int)$row['total'];
}

function getMenuFromID($conn, $id) {
    $query = "SELECT * FROM menu_items WHERE id = ?";

    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}
?>