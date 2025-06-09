<?php 
include '../includes/config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Process form data
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['reservation_data'] = $_POST;
} elseif(!isset($_SESSION['reservation_data'])) {
    header("Location: home.php");
    exit();
}

$reservation = $_SESSION['reservation_data'];
$userData = getUserData($conn, $_SESSION['user_id']);
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-serif font-bold mb-12 text-center" data-aos="fade-up">Complete Your Reservation</h1>
        
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-right">Reservation Details</h2>
                
                <form id="booking-form" action="seat-selection.php" method="POST">
                    <div class="space-y-6">
                        <div>
                            <label for="full-name" class="block text-sm font-medium mb-2">Full Name</label>
                            <input type="text" id="full-name" name="full-name" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" value="<?php echo $userData['first_name'] . ' ' . $userData['last_name']; ?>" required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" value="<?php echo $userData['email']; ?>" required>
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" value="<?php echo $userData['phone'] ?? ''; ?>" required>
                        </div>
                        
                        <div>
                            <label for="special-requests" class="block text-sm font-medium mb-2">Special Requests</label>
                            <textarea id="special-requests" name="special-requests" rows="4" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div>
                <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-left">Reservation Summary</h2>
                
                <div class="border border-gray-800 p-6 mb-8" data-aos="zoom-in">
                    <div class="space-y-4">
                        <div class="flex justify-between border-b border-gray-800 pb-4">
                            <span class="text-gray-400">Date</span>
                            <span><?php echo date('F j, Y', strtotime($reservation['date'])); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-4">
                            <span class="text-gray-400">Time</span>
                            <span><?php echo date('g:i A', strtotime($reservation['time'])); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-4">
                            <span class="text-gray-400">Guests</span>
                            <span><?php echo $reservation['guests'] . ($reservation['guests'] == 1 ? ' Person' : ' People'); ?></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-800 pb-4">
                            <span class="text-gray-400">Occasion</span>
                            <span><?php echo ucfirst($reservation['occasion']); ?></span>
                        </div>
                        
                        <?php if($reservation['pickup'] == 'yes'): ?>
                        <div class="pt-4 border-t border-gray-800">
                            <div class="flex justify-between border-b border-gray-800 pb-4">
                                <span class="text-gray-400">Pickup Service</span>
                                <span>Yes</span>
                            </div>
                            <div class="flex justify-between border-b border-gray-800 pb-4">
                                <span class="text-gray-400">Pickup Location</span>
                                <span><?php echo $reservation['pickup-location']; ?></span>
                            </div>
                            <div class="flex justify-between border-b border-gray-800 pb-4">
                                <span class="text-gray-400">Pickup Time</span>
                                <span><?php echo date('g:i A', strtotime($reservation['pickup-time'])); ?></span>
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
                                    echo $vehicleMap[$reservation['vehicle-type']];
                                    ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <button type="submit" form="booking-form" id="select-seats" class="w-full py-4 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                    Select Your Table
                </button>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>