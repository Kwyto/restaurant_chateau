<?php 
include '../includes/config.php';

// Redirect if not logged in or no reservation data
if(!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get reservation details
$reservationId = $_SESSION['reservation_id'];
$userId = $_SESSION['user_id'];

$query = "SELECT r.*, p.amount, p.payment_method, p.status as payment_status, 
                 ps.pickup_location, ps.pickup_time, ps.vehicle_type, ps.total_price as pickup_price
          FROM reservations r
          LEFT JOIN payments p ON r.id = p.reservation_id
          LEFT JOIN pickup_services ps ON r.id = ps.reservation_id
          WHERE r.id = ? AND r.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $reservationId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reservation = mysqli_fetch_assoc($result);

if(!$reservation) {
    header("Location: ../index.php");
    exit();
}

$userData = getUserData($conn, $userId);
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-12" data-aos="fade-up">
            <svg class="w-20 h-20 text-gold mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <h1 class="text-4xl font-serif font-bold mb-4">Reservation Confirmed</h1>
            <p class="text-lg text-gray-400">Your reservation at ÉLÉGANCE has been confirmed</p>
        </div>
        
        <div class="border border-gold p-8 mb-12" data-aos="zoom-in">
            <div class="text-center mb-8">
                <div class="mx-auto w-64 h-64 bg-white flex items-center justify-center mb-4">
                    <!-- Barcode would be generated here -->
                    <div class="text-black font-mono text-center p-4">
                        <div class="text-xl font-bold mb-2">ÉLÉGANCE</div>
                        <div class="text-sm mb-4"><?php echo $reservation['reservation_number']; ?></div>
                        <div class="flex justify-center space-x-1 mb-4">
                            <div class="w-1 h-10 bg-black"></div>
                            <div class="w-1 h-14 bg-black"></div>
                            <div class="w-1 h-8 bg-black"></div>
                            <div class="w-1 h-12 bg-black"></div>
                            <div class="w-1 h-16 bg-black"></div>
                            <div class="w-1 h-6 bg-black"></div>
                            <div class="w-1 h-14 bg-black"></div>
                            <div class="w-1 h-10 bg-black"></div>
                        </div>
                        <div class="text-xs">Scan at reception</div>
                    </div>
                </div>
                <p class="text-sm text-gray-400">Please present this barcode upon arrival</p>
            </div>
            
            <div class="space-y-6">
                <div class="flex justify-between border-b border-gray-800 pb-4">
                    <span class="text-gray-400">Reservation Number</span>
                    <span><?php echo $reservation['reservation_number']; ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-4">
                    <span class="text-gray-400">Date</span>
                    <span><?php echo date('F j, Y', strtotime($reservation['reservation_date'])); ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-4">
                    <span class="text-gray-400">Time</span>
                    <span><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-4">
                    <span class="text-gray-400">Guests</span>
                    <span><?php echo $reservation['guests'] . ($reservation['guests'] == 1 ? ' person' : ' people'); ?></span>
                </div>
                <div class="flex justify-between border-b border-gray-800 pb-4">
                    <span class="text-gray-400">Table Number</span>
                    <span><?php echo $reservation['table_number']; ?></span>
                </div>
                
                <?php if($reservation['pickup_location']): ?>
                <div class="pt-4 border-t border-gray-800">
                    <div class="flex justify-between border-b border-gray-800 pb-4">
                        <span class="text-gray-400">Pickup Service</span>
                        <span>Yes</span>
                    </div>
                    <div class="flex justify-between border-b border-gray-800 pb-4">
                        <span class="text-gray-400">Pickup Location</span>
                        <span><?php echo $reservation['pickup_location']; ?></span>
                    </div>
                    <div class="flex justify-between border-b border-gray-800 pb-4">
                        <span class="text-gray-400">Pickup Time</span>
                        <span><?php echo date('g:i A', strtotime($reservation['pickup_time'])); ?></span>
                    </div>
                    <div class="flex justify-between border-b border-gray-800 pb-4">
                        <span class="text-gray-400">Vehicle</span>
                        <span>
                            <?php 
                            $vehicleMap = [
                                'mercedes-s-class' => 'Mercedes S-Class',
                                'rolls-royce-phantom' => 'Rolls-Royce Phantom',
                                'bentley-mulsanne' => 'Bentley Mulsanne',
                                'maybach' => 'Maybach',
                                'limousine' => 'Limousine'
                            ];
                            echo $vehicleMap[$reservation['vehicle_type']];
                            ?>
                        </span>
                    </div>
                    <div class="flex justify-between border-b border-gray-800 pb-4">
                        <span class="text-gray-400">Pickup Fee</span>
                        <span>$<?php echo number_format($reservation['pickup_price'] / 100, 2); ?></span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="pt-4 border-t border-gray-800 font-medium">
                    <div class="flex justify-between">
                        <span>Total Paid</span>
                        <span>$<?php echo number_format($reservation['amount'], 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12" data-aos="fade-up">
            <p class="mb-6">We look forward to serving you at ÉLÉGANCE</p>
            <a href="../../index.php" class="inline-block px-8 py-3 border border-gold text-gold hover:bg-gold hover:text-black transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                Back to Home
            </a>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<?php
// Clear reservation data from session
unset($_SESSION['reservation_data']);
unset($_SESSION['booking_data']);
unset($_SESSION['reservation_id']);
?>