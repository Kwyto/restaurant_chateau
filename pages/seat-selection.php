<?php 
include '../includes/config.php';

// Redirect if not logged in or no reservation data
if(!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_data'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Process booking form
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['booking_data'] = $_POST;
}

$reservation = $_SESSION['reservation_data'];
$guests = $reservation['guests'];
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-serif font-bold mb-12 text-center" data-aos="fade-up">Select Your Table</h1>
        
        <div class="mb-12 text-center" data-aos="fade-up" data-aos-delay="100">
            <p class="text-lg">Please select your preferred table for <?php echo $guests . ($guests == 1 ? ' person' : ' people'); ?></p>
        </div>
        
        <div class="relative" data-aos="zoom-in">
            <!-- Restaurant Layout Visualization -->
            <div class="restaurant-layout mx-auto mb-12 relative h-96 w-full bg-gray-900 rounded-lg overflow-hidden">
                <!-- Available tables based on guest count -->
                <?php
                $tables = [
                    ['id' => 1, 'capacity' => 2, 'position' => 'top-20 left-20', 'available' => ($guests <= 2)],
                    ['id' => 2, 'capacity' => 4, 'position' => 'top-20 right-20', 'available' => ($guests <= 4)],
                    ['id' => 3, 'capacity' => 6, 'position' => 'bottom-20 left-20', 'available' => ($guests <= 6)],
                    ['id' => 4, 'capacity' => 2, 'position' => 'bottom-20 right-20', 'available' => ($guests <= 2)],
                    ['id' => 5, 'capacity' => 4, 'position' => 'top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2', 'available' => ($guests <= 4)],
                ];
                
                foreach ($tables as $table): 
                    if ($table['available']): ?>
                        <div class="absolute <?php echo $table['position']; ?> w-16 h-16 rounded-full border-2 border-gold flex items-center justify-center cursor-pointer hover:bg-gold/20 transition duration-300 table-option" data-table-id="<?php echo $table['id']; ?>">
                            <span class="text-xs">Table <?php echo $table['id']; ?></span>
                        </div>
                    <?php endif;
                endforeach;
                
                if (count(array_filter($tables, function($t) use ($guests) { return $t['available'] && $t['capacity'] >= $guests; })) === 0): ?>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <p class="text-center text-red-500 p-4 bg-black/80 rounded-lg">No available tables for your party size. Please try a different time or party size.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Restaurant decor elements -->
                <div class="absolute bottom-0 left-0 w-full h-16 bg-gray-800"></div> <!-- Floor -->
                <div class="absolute top-0 left-0 w-full h-8 bg-gray-800"></div> <!-- Ceiling -->
                <div class="absolute top-0 left-0 h-full w-8 bg-gray-800"></div> <!-- Left wall -->
                <div class="absolute top-0 right-0 h-full w-8 bg-gray-800"></div> <!-- Right wall -->
                
                <!-- Windows -->
                <div class="absolute top-8 left-1/4 w-16 h-24 bg-blue-900/50 border border-blue-700"></div>
                <div class="absolute top-8 right-1/4 w-16 h-24 bg-blue-900/50 border border-blue-700"></div>
                
                <!-- Chandelier -->
                <div class="absolute top-8 left-1/2 transform -translate-x-1/2 w-8 h-16 bg-gold/30 flex flex-col items-center">
                    <div class="w-6 h-6 rounded-full bg-gold/50"></div>
                    <div class="w-1 h-10 bg-gold"></div>
                </div>
            </div>
            
            <div class="text-center hidden" id="selected-table-info" data-aos="fade-up">
                <p class="text-lg mb-4">You've selected <span id="selected-table-number" class="text-gold font-bold">Table 1</span></p>
                <form id="seat-form" action="payment.php" method="POST">
                    <input type="hidden" id="selected-table" name="selected-table">
                    <button type="submit" id="proceed-to-payment" class="px-8 py-3 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                        Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tables = document.querySelectorAll('.table-option');
    const selectedTableInfo = document.getElementById('selected-table-info');
    const selectedTableInput = document.getElementById('selected-table');
    
    if (tables.length > 0) {
        tables.forEach(table => {
            table.addEventListener('click', function() {
                // Remove active class from all tables
                tables.forEach(t => t.classList.remove('bg-gold/10', 'border-2', 'border-gold'));
                
                // Add active class to selected table
                this.classList.add('bg-gold/10', 'border-2', 'border-gold');
                
                // Show selected table info
                selectedTableInfo.classList.remove('hidden');
                document.getElementById('selected-table-number').textContent = 'Table ' + this.getAttribute('data-table-id');
                selectedTableInput.value = this.getAttribute('data-table-id');
            });
        });
    }
});
</script>