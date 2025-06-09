<?php 
include '../includes/config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userData = getUserData($conn, $_SESSION['user_id']);
$menuItems = getMenuItems($conn);
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white">
    <!-- Carousel -->
    <section class="relative h-96 overflow-hidden">
        <div class="absolute inset-0 bg-black/30 z-10"></div>
        <div class="absolute inset-0 flex items-center justify-center z-20">
            <h2 class="text-5xl font-serif font-bold" data-aos="zoom-in">Our Menu</h2>
        </div>
        <div class="carousel h-full w-full">
            <div class="carousel-inner h-full">
                <div class="carousel-item h-full w-full active">
                    <img src="../../assets/images/carousel-1.jpg" class="h-full w-full object-cover" alt="Fine Dining">
                </div>
                <div class="carousel-item h-full w-full">
                    <img src="../../assets/images/carousel-2.jpg" class="h-full w-full object-cover" alt="Chef's Special">
                </div>
                <div class="carousel-item h-full w-full">
                    <img src="../../assets/images/carousel-3.jpg" class="h-full w-full object-cover" alt="Wine Selection">
                </div>
            </div>
        </div>
    </section>

    <!-- Menu Section -->
    <section class="py-16 px-4 max-w-7xl mx-auto">
        <h2 class="text-3xl font-serif font-bold mb-12 text-center" data-aos="fade-up">Signature Dishes</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-12">
            <?php while($item = mysqli_fetch_assoc($menuItems)): ?>
                <div class="border border-gray-800 hover:border-gold transition duration-500 transform hover:-translate-y-2 hover:shadow-lg hover:shadow-gold/20" data-aos="zoom-in">
                    <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-64 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-serif font-bold mb-2"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-400 mb-4"><?php echo $item['description']; ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-gold text-lg">$<?php echo number_format($item['price'], 2); ?></span>
                            <button class="text-gold hover:text-white transition duration-300">
                                + Add to Reservation
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Reservation Form -->
    <section class="py-16 px-4 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-12">
            <div class="md:col-span-2">
                <h2 class="text-3xl font-serif font-bold mb-6" data-aos="fade-right">Make a Reservation</h2>
                
                <form id="reservation-form" action="booking.php" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="guests" class="block text-sm font-medium mb-2">Number of Guests</label>
                            <select id="guests" name="guests" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == 2 ? 'selected' : ''; ?>>
                                        <?php echo $i . ($i == 1 ? ' Person' : ' People'); ?>
                                    </option>
                                <?php endfor; ?>
                                <option value="13">13+ People (Contact Us)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date" class="block text-sm font-medium mb-2">Date</label>
                            <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                        </div>
                        
                        <div>
                            <label for="time" class="block text-sm font-medium mb-2">Time</label>
                            <select id="time" name="time" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" required>
                                <?php 
                                $start = strtotime('17:00');
                                $end = strtotime('22:00');
                                for($i = $start; $i <= $end; $i += 1800): ?>
                                    <option value="<?php echo date('H:i', $i); ?>" <?php echo date('H:i', $i) == '19:30' ? 'selected' : ''; ?>>
                                        <?php echo date('g:i A', $i); ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label for="occasion" class="block text-sm font-medium mb-2">Special Occasion</label>
                            <select id="occasion" name="occasion" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                <option value="none">None</option>
                                <option value="anniversary">Anniversary</option>
                                <option value="birthday">Birthday</option>
                                <option value="engagement">Engagement</option>
                                <option value="business">Business Dinner</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-8">
                        <h3 class="text-xl font-serif font-bold mb-4">Would you like to be picked up?</h3>
                        <div class="flex items-center gap-4 mb-4">
                            <input type="radio" id="pickup-no" name="pickup" value="no" checked class="h-4 w-4 text-gold focus:ring-gold">
                            <label for="pickup-no" class="text-sm">No, I'll come myself</label>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="radio" id="pickup-yes" name="pickup" value="yes" class="h-4 w-4 text-gold focus:ring-gold">
                            <label for="pickup-yes" class="text-sm">Yes, please arrange pickup</label>
                        </div>
                        
                        <div id="pickup-details" class="mt-4 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="pickup-location" class="block text-sm font-medium mb-2">Pickup Location</label>
                                    <input type="text" id="pickup-location" name="pickup-location" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                </div>
                                <div>
                                    <label for="pickup-time" class="block text-sm font-medium mb-2">Pickup Time</label>
                                    <input type="time" id="pickup-time" name="pickup-time" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="vehicle-type" class="block text-sm font-medium mb-2">Vehicle Preference</label>
                                    <select id="vehicle-type" name="vehicle-type" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                        <option value="mercedes-s-class">Mercedes S-Class ($50,000/km)</option>
                                        <option value="rolls-royce-phantom">Rolls-Royce Phantom ($100,000/km)</option>
                                        <option value="bentley-mulsanne">Bentley Mulsanne ($80,000/km)</option>
                                        <option value="maybach">Maybach ($70,000/km)</option>
                                        <option value="limousine">Limousine ($150,000/km)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" id="book-now" class="w-full py-4 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                        Book Now
                    </button>
                </form>
            </div>
            
            <div class="border border-gray-800 p-8 h-fit sticky top-8" data-aos="fade-left">
                <h3 class="text-xl font-serif font-bold mb-6">Reservation Summary</h3>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Guests</span>
                        <span id="summary-guests">2 People</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date</span>
                        <span id="summary-date"><?php echo date('F j, Y', strtotime('+1 day')); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Time</span>
                        <span id="summary-time">7:30 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Occasion</span>
                        <span id="summary-occasion">None</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pickup Service</span>
                        <span id="summary-pickup">No</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-800 pt-4">
                    <div class="flex justify-between font-medium">
                        <span>Estimated Total</span>
                        <span id="summary-total">$55.00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set default date to tomorrow
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const dd = String(tomorrow.getDate()).padStart(2, '0');
    const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const yyyy = tomorrow.getFullYear();
    
    document.getElementById('date').value = `${yyyy}-${mm}-${dd}`;
    
    // Format date for display
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('summary-date').textContent = tomorrow.toLocaleDateString('en-US', options);
    
    // Update summary when form changes
    const form = document.getElementById('reservation-form');
    form.addEventListener('change', updateSummary);
    
    // Pickup service toggle
    document.getElementById('pickup-yes').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('pickup-details').classList.remove('hidden');
            document.getElementById('summary-pickup').textContent = 'Yes';
            updateTotal(true);
        }
    });
    
    document.getElementById('pickup-no').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('pickup-details').classList.add('hidden');
            document.getElementById('summary-pickup').textContent = 'No';
            updateTotal(false);
        }
    });
    
    function updateSummary() {
        const guests = document.getElementById('guests').value;
        const date = new Date(document.getElementById('date').value);
        const time = document.getElementById('time').options[document.getElementById('time').selectedIndex].text;
        const occasion = document.getElementById('occasion').options[document.getElementById('occasion').selectedIndex].text;
        
        document.getElementById('summary-guests').textContent = guests + (guests == 1 ? ' Person' : ' People');
        
        if(!isNaN(date.getTime())) {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('summary-date').textContent = date.toLocaleDateString('en-US', options);
        }
        
        document.getElementById('summary-time').textContent = time;
        document.getElementById('summary-occasion').textContent = occasion;
    }
    
    function updateTotal(includePickup) {
        let total = 50.00; // Base reservation fee
        if(includePickup) {
            total += 150.00; // Pickup service fee
        }
        const tax = total * 0.10;
        total += tax;
        
        document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
    }
    
    // Initialize summary
    updateSummary();
});
</script>