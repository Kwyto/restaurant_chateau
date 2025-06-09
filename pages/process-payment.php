<?php
include '../includes/config.php';

// Redirect if not logged in or no reservation data
if(!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_data']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Process payment
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservationData = $_SESSION['reservation_data'];
    $bookingData = $_SESSION['booking_data'];
    $userId = $_SESSION['user_id'];
    
    // Generate reservation number
    $reservationNumber = generateReservationNumber();
    
    // Calculate total
    $total = 50.00; // Base reservation fee
    if($reservationData['pickup'] == 'yes') {
        $total += 150.00; // Pickup service fee
    }
    $tax = $total * 0.10;
    $grandTotal = $total + $tax;
    
    // Start transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert reservation
        $reservationQuery = "INSERT INTO reservations (user_id, reservation_number, guests, reservation_date, reservation_time, table_number, occasion, special_requests, status) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')";
        $stmt = mysqli_prepare($conn, $reservationQuery);
        mysqli_stmt_bind_param($stmt, "isississ", $userId, $reservationNumber, $reservationData['guests'], $reservationData['date'], $reservationData['time'], $_POST['selected-table'], $reservationData['occasion'], $bookingData['special-requests']);
        mysqli_stmt_execute($stmt);
        $reservationId = mysqli_insert_id($conn);
        
        // Insert pickup service if selected
        if($reservationData['pickup'] == 'yes') {
            $pickupQuery = "INSERT INTO pickup_services (reservation_id, pickup_location, pickup_time, vehicle_type, vehicle_price, total_price) 
                            VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $pickupQuery);
            
            $vehiclePrices = [
                'mercedes-s-class' => 50000,
                'rolls-royce-phantom' => 100000,
                'bentley-mulsanne' => 80000,
                'maybach' => 70000,
                'limousine' => 150000
            ];
            
            $vehiclePrice = $vehiclePrices[$reservationData['vehicle-type']];
            $pickupTotal = $vehiclePrice; // For demo, we'll assume 1km distance
            
            mysqli_stmt_bind_param($stmt, "isssdd", $reservationId, $reservationData['pickup-location'], $reservationData['pickup-time'], $reservationData['vehicle-type'], $vehiclePrice, $pickupTotal);
            mysqli_stmt_execute($stmt);
        }
        
        // Insert payment
        $paymentQuery = "INSERT INTO payments (reservation_id, amount, payment_method, status) 
                         VALUES (?, ?, ?, 'completed')";
        $stmt = mysqli_prepare($conn, $paymentQuery);
        mysqli_stmt_bind_param($stmt, "ids", $reservationId, $grandTotal, $_POST['payment-method']);
        mysqli_stmt_execute($stmt);
        
        // Commit transaction
        mysqli_commit($conn);
        
        // Store reservation ID in session for receipt
        $_SESSION['reservation_id'] = $reservationId;
        
        // Redirect to receipt
        header("Location: receipt.php");
        exit();
        
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        $_SESSION['error'] = "Reservation failed. Please try again.";
        header("Location: payment.php");
        exit();
    }
} else {
    header("Location: payment.php");
    exit();
}
?>