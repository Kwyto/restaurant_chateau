<?php
include '../../../includes/config.php';

header('Content-Type: application/json');

// Validasi input
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Valid reservation ID is required']);
    exit;
}

$id = intval($_GET['id']);
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid reservation ID']);
    exit;
}

try {
    // Query dengan JOIN yang benar
    $query = "SELECT 
                r.*, 
                u.first_name, 
                u.last_name, 
                u.email, 
                u.phone 
              FROM reservations r
              JOIN users u ON r.user_id = u.id
              WHERE r.id = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, 'i', $id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Execution error: " . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    $reservation = mysqli_fetch_assoc($result);
    
    if ($reservation) {
        echo json_encode([
            'success' => true,
            'data' => $reservation
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'error' => 'Reservation not found'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
}
?>