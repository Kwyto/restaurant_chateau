<?php 
include '../includes/config.php';

// Redirect if not logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$userData = getUserData($conn, $_SESSION['user_id']);
$menuItems = getMenuItems($conn);

// Add this near the top after includes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_reservation'])) {
    $userId = $_SESSION['user_id'];
    $guests = $_POST['guests'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $occasion = $_POST['occasion'];
    $pickup = $_POST['pickup'] === 'yes' ? 1 : 0;
    $pickupLocation = $pickup ? $_POST['pickup-location'] : null;
    $pickupTime = $pickup ? $_POST['pickup-time'] : null;
    $vehicleType = $pickup ? $_POST['vehicle-type'] : null;
    
    // Calculate costs
    $pickupCost = isset($_POST['pickup_cost']) ? floatval($_POST['pickup_cost']) : 0;
    $foodCost = isset($_POST['food_cost']) ? floatval($_POST['food_cost']) : 0;
    $taxAmount = ($pickupCost + $foodCost + 50) * 0.10; // 50 is reservation fee
    $totalAmount = $pickupCost + $foodCost + 50 + $taxAmount;

    try {
        // Start transaction
        $conn->begin_transaction();

        // Insert main reservation
        $stmt = $conn->prepare("
            INSERT INTO reservations (
                user_id, guests, reservation_date, reservation_time, 
                special_occasion, pickup_service, pickup_location, 
                pickup_time, vehicle_type, pickup_cost, food_cost, 
                tax_amount, total_amount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "iisssississdd",
            $userId, $guests, $date, $time, $occasion, $pickup,
            $pickupLocation, $pickupTime, $vehicleType, $pickupCost,
            $foodCost, $taxAmount, $totalAmount
        );

        $stmt->execute();
        $reservationId = $conn->insert_id;

        // Insert ordered items if any
        if (isset($_POST['ordered_items']) && !empty($_POST['ordered_items'])) {
            $items = json_decode($_POST['ordered_items'], true);
            
            $stmt = $conn->prepare("
                INSERT INTO reservation_items (
                    reservation_id, menu_item_id, quantity, price
                ) VALUES (?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                $stmt->bind_param(
                    "iiid",
                    $reservationId, $item['id'], $item['quantity'], $item['price']
                );
                $stmt->execute();
            }
        }

        // Commit transaction
        $conn->commit();

        // Store reservation data in session for next step
        $_SESSION['reservation_data'] = [
            'id' => $reservationId,
            'guests' => $guests,
            'date' => $date,
            'time' => $time
        ];

        // Redirect to seat selection
        header("Location: seat-selection.php");
        exit();

    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $error = "Failed to process reservation. Please try again.";
    }
}
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white">
    <!-- Carousel -->
    <section class="relative h-96 overflow-hidden">
        <div class="absolute inset-0 bg-black/30 z-10"></div>
        <div class="absolute inset-0 flex items-center justify-center z-20">
            <h2 class="text-5xl font-serif font-bold text-gold" data-aos="zoom-in">Reservation</h2>
        </div>
        <div class="carousel h-full w-full">
            <div class="carousel-inner h-full">
                <div class="carousel-item h-full w-full active">
                    <img src="../assets/images/reservasi.jpg" class="h-full w-full object-cover" alt="Fine Dining">
                </div>
                <div class="carousel-item h-full w-full">
                    <img src="../assets/images/chef-special.jpg" class="h-full w-full object-cover" alt="Chef's Special">
                </div>
                <div class="carousel-item h-full w-full">
                    <img src="../assets/images/wine-selection.jpg" class="h-full w-full object-cover" alt="Wine Selection">
                </div>
            </div>
        </div>
    </section>

    <!-- Signature Dishes Section -->
    <section class="py-16 px-4 max-w-7xl mx-auto">
        <h2 class="text-3xl font-serif font-bold mb-4 text-gold" data-aos="fade-up">Signature Dishes</h2>
        <button id="view-menu-btn" class="bg-gold text-black px-6 py-2 text-sm font-semibold hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30" data-aos="zoom-in">
            View Menu
        </button>
    </section>

    <!-- Menu Popup (Black Background) -->
    <div id="menu-popup" class="fixed inset-0 bg-black z-50 hidden">
        <div class="flex items-center justify-center min-h-screen p-4 relative">
            <!-- Fixed Left Arrow -->
            <button id="prev-page" class="fixed left-8 top-1/2 transform -translate-y-1/2 bg-black/80 hover:bg-black text-white border-2 border-gold hover:border-yellow-400 rounded-lg px-4 py-3 flex items-center gap-2 text-sm font-semibold transition-all duration-300 z-30 shadow-xl backdrop-blur-sm">
                <span class="text-lg">‹</span>
                <span>PREV</span>
            </button>
            
            <!-- Menu Book Container -->
            <div class="relative w-full max-w-6xl">
                <!-- Menu Book -->
                <div class="bg-black border-4 border-gold w-full max-h-[90vh] overflow-hidden rounded-lg shadow-2xl relative">
                    <!-- Fixed Header with Close Button -->
                    <div class="sticky top-0 z-20 bg-black border-b-2 border-gold px-8 py-4">
                        <!-- Close Button -->
                        <button id="close-menu" class="absolute top-4 right-4 bg-gold text-black rounded-full w-10 h-10 flex items-center justify-center hover:bg-white transition text-2xl font-bold shadow-lg">
                            ×
                        </button>
                        
                        <!-- Category Title -->
                        <h3 id="category-title" class="text-4xl font-serif font-bold text-center text-gold pr-16">Category Title</h3>
                    </div>
                    
                    <!-- Scrollable Content Area -->
                    <div id="menu-book" class="overflow-y-auto" style="height: calc(90vh - 180px);">
                        <!-- Single Category Page Content -->
                        <div class="w-full p-8">
                            <div id="category-content" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Menu items will be populated here -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fixed Footer with Navigation Info -->
                    <div class="sticky bottom-0 z-20 bg-black border-t-2 border-gold px-8 py-4">
                        <div class="flex items-center justify-center gap-6">
                            <!-- Page Dots -->
                            <div id="page-dots" class="flex items-center gap-2">
                                <!-- Dots will be populated here -->
                            </div>
                            
                            <!-- Page Info -->
                            <div class="bg-gold text-black px-4 py-2 rounded-full">
                                <span id="page-info" class="font-semibold text-sm">Page 1 of 1</span>
                            </div>
                            
                            <!-- Current Category -->
                            <div class="bg-gold/20 text-gold px-4 py-2 rounded-full">
                                <span id="current-category" class="font-semibold text-sm">Current Category</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                
            <!-- Fixed Right Arrow -->
            <button id="next-page" class="fixed right-8 top-1/2 transform -translate-y-1/2 bg-black/80 hover:bg-black text-white border-2 border-gold hover:border-yellow-400 rounded-lg px-4 py-3 flex items-center gap-2 text-sm font-semibold transition-all duration-300 z-30 shadow-xl backdrop-blur-sm">
                <span>NEXT</span>
                <span class="text-lg">›</span>
            </button>
        </div>
    </div>

    <!-- Reservation Form -->
    <section class="py-16 px-4 max-w-7xl mx-auto">
        <div class="grid md:grid-cols-3 gap-12">
            <div class="md:col-span-2">
                <h2 class="text-3xl font-serif font-bold mb-6 text-gold" data-aos="fade-right">Make a Reservation</h2>
                
                <form id="reservation-form" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="guests" class="block text-sm font-medium mb-2 text-gold">Number of Guests</label>
                            <select id="guests" name="guests" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white" required>
                                <?php for($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == 2 ? 'selected' : ''; ?>>
                                        <?php echo $i . ($i == 1 ? ' Person' : ' People'); ?>
                                    </option>
                                <?php endfor; ?>
                                
                            </select>
                        </div>
                        
                        <div>
                            <label for="date" class="block text-sm font-medium mb-2 text-gold">Date</label>
                            <div class="relative">
                                <input type="text" 
                                    id="date" 
                                    name="date" 
                                    readonly
                                    placeholder="Click to select date"
                                    class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-gold cursor-pointer"
                                    required>
                                <!-- Calendar Dropdown -->
                                <div id="calendar-popup" class="hidden absolute top-full left-0 mt-1 p-4 bg-[#1a1a1a] border-2 border-gold rounded-lg shadow-xl z-[100] w-[300px]">
                                    <div class="flex justify-between items-center mb-4">
                                        <button type="button" id="prev-month" class="text-gold hover:text-white text-xl p-1">‹</button>
                                        <span id="current-month" class="text-gold font-bold"></span>
                                        <button type="button" id="next-month" class="text-gold hover:text-white text-xl p-1">›</button>
                                    </div>
                                    <div class="grid grid-cols-7 gap-1 mb-2">
                                        <div class="text-gold/70 text-sm text-center">S</div>
                                        <div class="text-gold/70 text-sm text-center">M</div>
                                        <div class="text-gold/70 text-sm text-center">T</div>
                                        <div class="text-gold/70 text-sm text-center">W</div>
                                        <div class="text-gold/70 text-sm text-center">T</div>
                                        <div class="text-gold/70 text-sm text-center">F</div>
                                        <div class="text-gold/70 text-sm text-center">S</div>
                                    </div>
                                    <div id="calendar-days" class="grid grid-cols-7 gap-1"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="time" class="block text-sm font-medium mb-2 text-gold">Time</label>
                            <select id="time" name="time" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white" required>
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
                            <label for="occasion" class="block text-sm font-medium mb-2 text-gold">Special Occasion</label>
                            <select id="occasion" name="occasion" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white">
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
                        <h3 class="text-xl font-serif font-bold mb-4 text-gold">Would you like to be picked up?</h3>
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
                                <div class="md:col-span-2">
                                    <label for="pickup-location" class="block text-sm font-medium mb-2 text-gold">Pickup Location</label>
                                    <div class="relative">
                                        <input type="text" id="pickup-location" name="pickup-location" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white" placeholder="Search for your pickup location...">
                                        <button type="button" id="get-current-location" class="absolute right-2 top-2 bg-gold text-black px-3 py-2 rounded hover:bg-yellow-400 transition text-sm">
                                            📍 Current Location
                                        </button>
                                    </div>
                                    <div id="location-suggestions" class="bg-black border border-gold rounded mt-1 hidden max-h-40 overflow-y-auto"></div>
                                    
                                    <!-- Distance Warning -->
                                    <div id="distance-warning" class="mt-2 hidden">
                                        <div class="flex items-center gap-2 text-red-400 text-sm">
                                            <span>⚠️</span>
                                            <span id="warning-message">Location is too far from restaurant. Maximum pickup distance is 15km.</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Distance Info -->
                                    <div id="distance-info" class="mt-2 hidden">
                                        <div class="flex items-center gap-2 text-green-400 text-sm">
                                            <span>✅</span>
                                            <span id="distance-message">Distance: 0 km from restaurant</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="pickup-time" class="block text-sm font-medium mb-2 text-gold">Pickup Time</label>
                                    <select id="pickup-time" 
                                        name="pickup-time" 
                                        class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white">
                                        <?php 
                                        $start = strtotime('16:00');
                                        $end = strtotime('22:00');
                                        for($i = $start; $i <= $end; $i += 1800): ?>
                                            <option value="<?php echo date('H:i', $i); ?>">
                                                <?php echo date('g:i A', $i); ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                
                                <div>
                                    <label for="vehicle-type" class="block text-sm font-medium mb-2 text-gold">Vehicle Preference</label>
                                    <select id="vehicle-type" name="vehicle-type" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white">
                                        <option value="mercedes-s-class" data-price="25">Mercedes S-Class ($25)</option>
                                        <option value="rolls-royce-phantom" data-price="50">Rolls-Royce Phantom ($50)</option>
                                        <option value="bentley-mulsanne" data-price="40">Bentley Mulsanne ($40)</option>
                                        <option value="maybach" data-price="35">Maybach ($35)</option>
                                        <option value="limousine" data-price="75">Limousine ($75)</option>
                                    </select>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-400">Distance: <span id="estimated-distance" class="text-gold">0 km</span></p>
                                    <p class="text-sm text-gray-400">Pickup cost: <span id="pickup-cost" class="text-gold">$0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add hidden fields for costs -->
                    <input type="hidden" name="pickup_cost" id="pickup_cost" value="0">
                    <input type="hidden" name="food_cost" id="food_cost" value="0">
                    <input type="hidden" name="ordered_items" id="ordered_items" value="">
                    <input type="hidden" name="book_reservation" value="1">
                    
                    <button type="submit" id="book-now" class="w-full py-4 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30 font-semibold">
                        Book Now
                    </button>
                </form>
            </div>
            
            <div class="border-2 border-gold p-8 h-fit sticky top-8 bg-gradient-to-b from-black to-gray-900 rounded-lg shadow-xl" data-aos="fade-left">
                <h3 class="text-xl font-serif font-bold mb-6 text-gold border-b-2 border-gold pb-2">Reservation Summary</h3>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Guests</span>
                        <span id="summary-guests" class="text-gold">2 People</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Date</span>
                        <span id="summary-date" class="text-gold"><?php echo date('F j, Y', strtotime('+1 day')); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Time</span>
                        <span id="summary-time" class="text-gold">7:30 PM</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Occasion</span>
                        <span id="summary-occasion" class="text-gold">None</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pickup Service</span>
                        <span id="summary-pickup" class="text-gold">No</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Pickup Time</span>
                        <span id="summary-pickup-time" class="text-gold">-</span>
                    </div>
                </div>
                
                <!-- Ordered Items Section -->
                <div id="ordered-items-section" class="mb-6 hidden">
                    <h4 class="text-lg font-serif font-bold mb-4 text-gold border-b border-gold pb-2">Ordered Items</h4>
                    <div id="ordered-items" class="space-y-2 mb-4">
                        <!-- Ordered items will be populated here -->
                    </div>
                </div>
                
                <div class="border-t-2 border-gold pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Reservation Fee</span>
                        <span class="text-gold">$50.00</span>
                    </div>
                    <div id="food-cost-row" class="flex justify-between hidden">
                        <span class="text-gray-400">Food Cost</span>
                        <span id="food-cost" class="text-gold">$0.00</span>
                    </div>
                    <div id="pickup-cost-row" class="flex justify-between hidden">
                        <span class="text-gray-400">Pickup Cost</span>
                        <span id="pickup-cost-summary" class="text-gold">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Tax (10%)</span>
                        <span id="tax-amount" class="text-gold">$5.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t border-gold pt-2">
                        <span class="text-gold">Total</span>
                        <span id="summary-total" class="text-gold">$55.00</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Google Maps API -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap"></script>

<script>
// Restaurant location (you need to set this to your actual restaurant coordinates)
const RESTAURANT_LOCATION = { 
    lat: 3.558874, // Lokasi USU
    lng: 98.654880
}; 
const MAX_PICKUP_DISTANCE = 15; // Maximum pickup distance in kilometers
const USU_LOCATION = {
    lat: 3.558874,
    lng: 98.654880,
    address: "Universitas Sumatera Utara, Jl. Dr. Mansur No.9, Padang Bulan, Medan"
};

let map, directionsService, geocoder, autocomplete;
let currentDistance = 0;
let isLocationValid = false;

// Initialize Google Maps
function initMap() {
    // Initialize services
    directionsService = new google.maps.DirectionsService();
    geocoder = new google.maps.Geocoder();
    
    // Initialize autocomplete for pickup location
    const pickupInput = document.getElementById('pickup-location');
    autocomplete = new google.maps.places.Autocomplete(pickupInput, {
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(RESTAURANT_LOCATION.lat - 0.2, RESTAURANT_LOCATION.lng - 0.2),
            new google.maps.LatLng(RESTAURANT_LOCATION.lat + 0.2, RESTAURANT_LOCATION.lng + 0.2)
        ),
        strictBounds: true,
        types: ['establishment', 'geocode']
    });
    
    // Handle place selection
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (place.geometry) {
            calculateDistance(place.geometry.location);
        }
    });
}

// Get current location - ganti fungsi ini
document.getElementById('get-current-location').addEventListener('click', function() {
    if (navigator.geolocation) {
        this.textContent = '📍';
        this.disabled = true;
        
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                
                // Set lokasi dan kalkulasi jarak langsung
                document.getElementById('pickup-location').value = `(${userLocation.lat.toFixed(4)}, ${userLocation.lng.toFixed(4)})`;
                calculateDistance(new google.maps.LatLng(userLocation.lat, userLocation.lng));
                
                // Aktifkan pickup service
                document.getElementById('pickup-yes').checked = true;
                document.getElementById('pickup-details').classList.remove('hidden');
                document.getElementById('summary-pickup').textContent = 'Yes';
                updatePickupCost();
                updateTotal();
                
                this.textContent = '📍 Current Location';
                this.disabled = false;
            },
            (error) => {
                alert('Could not get your location');
                this.textContent = '📍 Current Location';
                this.disabled = false;
            },
            {
                enableHighAccuracy: false,
                timeout: 3000,
                maximumAge: 600000
            }
        );
    } else {
        alert('Geolocation is not supported');
    }
});

// Calculate distance between pickup location and restaurant
function calculateDistance(pickupLocation) {
    const service = new google.maps.DistanceMatrixService();
    currentDistance = 5.0;
    document.getElementById('estimated-distance').textContent = '5 km';
    
    service.getDistanceMatrix({
        origins: [pickupLocation],
        destinations: [RESTAURANT_LOCATION],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.METRIC,
        avoidHighways: false,
        avoidTolls: false
    }, function(response, status) {
        if (status === google.maps.DistanceMatrixStatus.OK) {
            const duration = response.rows[0].elements[0].duration;
            document.getElementById('distance-message').textContent = 
                `Distance: 5km (${duration.text} drive) from restaurant`;
            hideDistanceWarning();
            document.getElementById('distance-info').classList.remove('hidden');
            isLocationValid = true;
            updatePickupCost();
        }
    });
}

function showDistanceInfo(distance, duration) {
    const infoDiv = document.getElementById('distance-info');
    const messageSpan = document.getElementById('distance-message');
    
    messageSpan.textContent = `Fixed Distance: 5km from restaurant`;
    infoDiv.classList.remove('hidden');
}

function updatePickupCost() {
    const pickupCostElem = document.getElementById('pickup-cost');
    const pickupCostSummary = document.getElementById('pickup-cost-summary');
    const pickupCostRow = document.getElementById('pickup-cost-row');
    let pickupCost = 0;

    if (document.getElementById('pickup-yes').checked) {
        const vehicleSelect = document.getElementById('vehicle-type');
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const pricePerKm = parseInt(selectedOption.dataset.price);
        pickupCost = pricePerKm * 5; // Fixed 5km distance
        
        pickupCostElem.textContent = `$${pickupCost}`;
        pickupCostSummary.textContent = `$${pickupCost}`;
        pickupCostRow.style.display = 'flex';
    } else {
        pickupCostElem.textContent = '$0';
        pickupCostSummary.textContent = '$0';
        pickupCostRow.style.display = 'none';
    }

    document.getElementById('pickup_cost').value = pickupCost;
    updateTotal();
}

function updateTotal() {
    const baseReservationFee = 50.00;
    let subtotal = baseReservationFee;

    // Add food cost
    const foodCost = orderedItems.reduce((total, item) => {
        return total + (parseFloat(item.price) * parseInt(item.quantity));
    }, 0);
    
    if (foodCost > 0) {
        document.getElementById('food-cost-row').style.display = 'flex';
        document.getElementById('food-cost').textContent = `$${foodCost.toFixed(2)}`;
        subtotal += foodCost;
    }

    // Add pickup cost
    const pickupCost = document.getElementById('pickup-yes').checked ? 
        parseFloat(document.getElementById('pickup-cost').textContent.replace('$', '')) : 0;
    subtotal += pickupCost;

    // Calculate tax and total
    const tax = subtotal * 0.10;
    const total = subtotal + tax;

    // Update displays
    document.getElementById('tax-amount').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('summary-total').textContent = `$${total.toFixed(2)}`;

    // Update hidden fields
    document.getElementById('pickup_cost').value = pickupCost;
    document.getElementById('food_cost').value = foodCost;
}

// Add event listeners for real-time updates
document.addEventListener('DOMContentLoaded', function() {
    // Menu items data with categories
    const menuItems = [
        <?php 
        // Reset pointer to beginning
        mysqli_data_seek($menuItems, 0);
        $items = [];
        while($item = mysqli_fetch_assoc($menuItems)): 
            $items[] = json_encode($item);
        endwhile;
        echo implode(',', $items);
        ?>
    ];
    
    // Group menu items by category
    const menuCategories = {
        'starter': { title: 'Starters & Appetizers', items: [] },
        'soup': { title: 'Soups', items: [] },
        'salad': { title: 'Fresh Salads', items: [] },
        'main': { title: 'Main Courses', items: [] },
        'pasta': { title: 'Pasta & Italian', items: [] },
        'dessert': { title: 'Desserts', items: [] },
        'beverage': { title: 'Beverages', items: [] },
        'side': { title: 'Side Dishes', items: [] },
        'special': { title: "Chef's Specials", items: [] }
    };
    
    // Categorize menu items
    menuItems.forEach(item => {
        if (menuCategories[item.category]) {
            menuCategories[item.category].items.push(item);
        }
    });
    
    // Filter out empty categories and create single category pages
    const categories = Object.keys(menuCategories)
        .filter(key => menuCategories[key].items.length > 0)
        .map(key => menuCategories[key]);
    
    let currentPage = 1;
    const totalPages = categories.length;
    let orderedItems = [];
    
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
            document.getElementById('pickup-location').value = USU_LOCATION.address;
            calculateDistance(new google.maps.LatLng(USU_LOCATION.lat, USU_LOCATION.lng));
            document.getElementById('pickup-details').classList.remove('hidden');
            document.getElementById('summary-pickup').textContent = 'Yes';
        }
    });
    
    document.getElementById('pickup-no').addEventListener('change', function() {
        if(this.checked) {
            document.getElementById('pickup-details').classList.add('hidden');
            document.getElementById('summary-pickup').textContent = 'No';
            updateTotal();
        }
    });
    
    // Vehicle type change
    document.getElementById('vehicle-type').addEventListener('change', function() {
        updatePickupCost();
        updateTotal();
    });
    
    // Menu popup functionality
    document.getElementById('view-menu-btn').addEventListener('click', function() {
        document.getElementById('menu-popup').classList.remove('hidden');
        loadMenuPage(1);
        createPageDots();
    });
    
    document.getElementById('close-menu').addEventListener('click', function() {
        document.getElementById('menu-popup').classList.add('hidden');
    });
    
    document.getElementById('prev-page').addEventListener('click', function() {
        if(currentPage > 1) {
            currentPage--;
            loadMenuPage(currentPage);
            updatePageDots();
            // Scroll to top when changing pages
            document.getElementById('menu-book').scrollTop = 0;
        }
    });
    
    document.getElementById('next-page').addEventListener('click', function() {
        if(currentPage < totalPages) {
            currentPage++;
            loadMenuPage(currentPage);
            updatePageDots();
            // Scroll to top when changing pages
            document.getElementById('menu-book').scrollTop = 0;
        }
    });
    
    // Close popup when clicking outside
    document.getElementById('menu-popup').addEventListener('click', function(e) {
        if(e.target === this) {
            this.classList.add('hidden');
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if(document.getElementById('menu-popup').classList.contains('hidden')) return;
        
        if(e.key === 'ArrowLeft' && currentPage > 1) {
            currentPage--;
            loadMenuPage(currentPage);
            updatePageDots();
            document.getElementById('menu-book').scrollTop = 0;
        } else if(e.key === 'ArrowRight' && currentPage < totalPages) {
            currentPage++;
            loadMenuPage(currentPage);
            updatePageDots();
            document.getElementById('menu-book').scrollTop = 0;
        } else if(e.key === 'Escape') {
            document.getElementById('menu-popup').classList.add('hidden');
        }
    });
    
    function createPageDots() {
        const pageDotsContainer = document.getElementById('page-dots');
        pageDotsContainer.innerHTML = '';
        
        for(let i = 1; i <= totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = `w-3 h-3 rounded-full transition-all duration-300 ${i === currentPage ? 'bg-gold scale-125' : 'bg-gray-600 hover:bg-gold/50'}`;
            dot.addEventListener('click', function() {
                currentPage = i;
                loadMenuPage(currentPage);
                updatePageDots();
                document.getElementById('menu-book').scrollTop = 0;
            });
            pageDotsContainer.appendChild(dot);
        }
    }
    
    function updatePageDots() {
        const dots = document.getElementById('page-dots').children;
        for(let i = 0; i < dots.length; i++) {
            if(i + 1 === currentPage) {
                dots[i].className = 'w-3 h-3 rounded-full transition-all duration-300 bg-gold scale-125';
            } else {
                dots[i].className = 'w-3 h-3 rounded-full transition-all duration-300 bg-gray-600 hover:bg-gold/50';
            }
        }
    }
    
    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        // Update Previous button
        if(currentPage <= 1) {
            prevBtn.style.opacity = '0.5';
            prevBtn.style.cursor = 'not-allowed';
            prevBtn.disabled = true;
        } else {
            prevBtn.style.opacity = '1';
            prevBtn.style.cursor = 'pointer';
            prevBtn.disabled = false;
        }
        
        // Update Next button
        if(currentPage >= totalPages) {
            nextBtn.style.opacity = '0.5';
            nextBtn.style.cursor = 'not-allowed';
            nextBtn.disabled = true;
        } else {
            nextBtn.style.opacity = '1';
            nextBtn.style.cursor = 'pointer';
            nextBtn.disabled = false;
        }
        
        // Hide buttons if only one page
        if(totalPages <= 1) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
            nextBtn.style.display = 'flex';
        }
    }
    
    function loadMenuPage(page) {
        const category = categories[page - 1];
        
        const categoryContent = document.getElementById('category-content');
        const categoryTitle = document.getElementById('category-title');
        
        categoryContent.innerHTML = '';
        
        // Load category
        if (category) {
            categoryTitle.textContent = category.title;
            category.items.forEach(item => {
                const menuItemHTML = createMenuItemHTML(item);
                categoryContent.innerHTML += menuItemHTML;
            });
        }
        
        // Add event listeners to add buttons
        document.querySelectorAll('.add-to-order').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                const itemName = this.dataset.name;
                const itemPrice = parseFloat(this.dataset.price);
                
                addToOrder(itemId, itemName, itemPrice);
                
                // Visual feedback
                this.textContent = '✓ Added';
                this.classList.add('bg-green-500');
                setTimeout(() => {
                    this.textContent = '+ Add';
                    this.classList.remove('bg-green-500');
                }, 1000);
            });
        });
        
        // Update page info and navigation
        document.getElementById('page-info').textContent = `Page ${page} of ${totalPages}`;
        
        // Update current category display
        document.getElementById('current-category').textContent = category.title;
        
        // Update navigation buttons
        updateNavigationButtons();
    }
    
    function createMenuItemHTML(item) {
        const imageUrl = item.image_path || '../../assets/images/default-food.jpg';
        return `
            <div class="border-2 border-gold hover:border-yellow-400 transition duration-300 transform hover:scale-105 mb-6 bg-white rounded-lg shadow-lg overflow-hidden">
                <img src="${imageUrl}" alt="${item.name}" class="w-full h-48 object-cover">
                <div class="p-4">
                    <h4 class="text-lg font-serif font-bold mb-2 text-black">${item.name}</h4>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">${item.description}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-gold text-xl font-bold">$${parseFloat(item.price).toFixed(2)}</span>
                        <button class="add-to-order bg-gold text-black px-4 py-2 rounded hover:bg-yellow-400 transition duration-300 text-sm font-semibold" 
                                data-id="${item.id}" 
                                data-name="${item.name}" 
                                data-price="${item.price}">
                            + Add
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    function addToOrder(itemId, itemName, itemPrice) {
        // Get button element
        const button = document.querySelector(`[data-id="${itemId}"]`);
        
        // Add visual feedback with scale animation
        button.classList.add('scale-110', 'bg-green-500');
        button.innerHTML = '<span class="text-black">✓ Added</span>';
        
        // Add ripple effect
        const ripple = document.createElement('span');
        ripple.className = 'absolute inset-0 bg-white/30 rounded animate-ripple';
        button.appendChild(ripple);
        
        setTimeout(() => {
            button.classList.remove('scale-110', 'bg-green-500');
            button.textContent = '+ Add';
            ripple.remove();
        }, 500);

        // Update order data
        const existingItem = orderedItems.find(item => item.id === itemId);
        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            orderedItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                quantity: 1
            });
        }

        // Update display and totals with animation
        updateOrderedItemsDisplay();
        animateUpdatedTotal();
    }

    function animateUpdatedTotal() {
        const foodCost = orderedItems.reduce((total, item) => {
            return total + (parseFloat(item.price) * parseInt(item.quantity));
        }, 0);

        // Update food cost with animation
        if (foodCost > 0) {
            const foodCostRow = document.getElementById('food-cost-row');
            const foodCostElem = document.getElementById('food-cost');
            
            foodCostRow.style.display = 'flex';
            foodCostElem.classList.add('scale-110', 'text-white');
            foodCostElem.textContent = `$${foodCost.toFixed(2)}`;
            
            setTimeout(() => {
                foodCostElem.classList.remove('scale-110', 'text-white');
            }, 300);
        }

        // Calculate final totals
        const baseReservationFee = 50.00;
        let subtotal = baseReservationFee + foodCost;
        
        if (document.getElementById('pickup-yes').checked) {
            subtotal += parseFloat(document.getElementById('pickup_cost').value) || 0;
        }

        const tax = subtotal * 0.10;
        const total = subtotal + tax;

        // Animate tax and total updates
        const taxElem = document.getElementById('tax-amount');
        const totalElem = document.getElementById('summary-total');
        
        taxElem.classList.add('scale-110', 'text-white');
        totalElem.classList.add('scale-110', 'text-white');
        
        taxElem.textContent = `$${tax.toFixed(2)}`;
        totalElem.textContent = `$${total.toLocaleString()}`;
        
        setTimeout(() => {
            taxElem.classList.remove('scale-110', 'text-white');
            totalElem.classList.remove('scale-110', 'text-white');
        }, 300);
    }

    // Add style for ripple animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(4); opacity: 0; }
        }
        .animate-ripple {
            animation: ripple 0.5s linear;
        }
    `;
    document.head.appendChild(style);
    
    function updateOrderedItemsDisplay() {
        const orderedItemsContainer = document.getElementById('ordered-items');
        const orderedItemsSection = document.getElementById('ordered-items-section');
        
        if(orderedItems.length === 0) {
            orderedItemsSection.classList.add('hidden');
            return;
        }
        
        orderedItemsSection.classList.remove('hidden');
        
        orderedItemsContainer.innerHTML = orderedItems.map(item => `
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-300">${item.name} (x${item.quantity})</span>
                <div class="flex items-center gap-2">
                    <span class="text-gold">$${(item.price * item.quantity).toFixed(2)}</span>
                    <button class="remove-item text-red-400 hover:text-red-300 text-xs" data-id="${item.id}">✕</button>
                </div>
            </div>
        `).join('');
        
        // Add remove functionality
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.id;
                orderedItems = orderedItems.filter(item => item.id !== itemId);
                updateOrderedItemsDisplay();
                updateTotal();
            });
        });
    }
    
    
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
        
        // Pickup time
        const pickupTime = document.getElementById('pickup-time');
        const selectedTime = pickupTime.options[pickupTime.selectedIndex].text;
        document.getElementById('summary-pickup-time').textContent = selectedTime;
    }
    
    // Fungsi untuk set pickup location ke USU
    function setPickupLocationToUSU() {
        document.getElementById('pickup-location').value = USU_LOCATION.address;
        // Trigger distance calculation
        calculateDistance(new google.maps.LatLng(USU_LOCATION.lat, USU_LOCATION.lng));
    }

    // Event listener tombol Lokasi USU
    document.addEventListener('DOMContentLoaded', function() {
        // ...existing code...
        document.getElementById('set-usu-location').addEventListener('click', setPickupLocationToUSU);
        // ...existing code...
    });
    
    // Initialize
    updateSummary();
    updatePickupCost();
});

// Update hidden fields before form submission
document.getElementById('reservation-form').addEventListener('submit', function(e) {
    document.getElementById('pickup_cost').value = parseFloat(document.getElementById('pickup-cost').textContent.replace(/[^0-9.-]+/g,""));
    document.getElementById('food_cost').value = parseFloat(document.getElementById('food-cost').textContent.replace(/[^0-9.-]+/g,""));
    document.getElementById('ordered_items').value = JSON.stringify(orderedItems);
});

// Add this calendar script
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('date');
    const calendarPopup = document.getElementById('calendar-popup');
    const prevMonth = document.getElementById('prev-month');
    const nextMonth = document.getElementById('next-month');
    const currentMonthDisplay = document.getElementById('current-month');
    const calendarDays = document.getElementById('calendar-days');

    let currentDate = new Date();
    let selectedDate = new Date(currentDate);
    selectedDate.setDate(selectedDate.getDate() + 1); // Set default to tomorrow

    // Show calendar when clicking input
    dateInput.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        calendarPopup.style.display = 'block';
        renderCalendar();
    });

    // Hide calendar when clicking outside
    document.addEventListener('click', function(e) {
        if (!calendarPopup.contains(e.target) && e.target !== dateInput) {
            calendarPopup.style.display = 'none';
        }
    });

    // Month navigation
    prevMonth.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonth.addEventListener('click', function(e) {
        e.stopPropagation();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    function renderCalendar() {
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const today = new Date();
        today.setHours(0,0,0,0);

        // Update month display
        currentMonthDisplay.textContent = firstDay.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });

        calendarDays.innerHTML = '';
        
        // Add empty cells for days before first day of month
        for (let i = 0; i < firstDay.getDay(); i++) {
            const emptyCell = document.createElement('div');
            calendarDays.appendChild(emptyCell);
        }

        // Add calendar days
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
            const dayCell = document.createElement('div');
            const isDisabled = date < today;
            const isSelected = date.toDateString() === selectedDate.toDateString();

            dayCell.className = `text-center p-2 rounded-full text-sm
                ${isDisabled ? 'text-gray-600 cursor-not-allowed' : 'cursor-pointer hover:bg-gold/20 text-gold'}
                ${isSelected ? 'bg-gold text-black font-bold' : ''}`;
            dayCell.textContent = day;

            if (!isDisabled) {
                dayCell.addEventListener('click', () => selectDate(date));
            }

            calendarDays.appendChild(dayCell);
        }
    }

    function selectDate(date) {
        selectedDate = date;
        dateInput.value = formatDate(date);
        calendarPopup.style.display = 'none';
        dateInput.dispatchEvent(new Event('change')); // Trigger change event
    }

    function formatDate(date) {
        return date.toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    // Set initial date
    dateInput.value = formatDate(selectedDate);
});
</script>

<style>
    /* Updated Calendar Styling */
    .calendar-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        background: #1a1a1a;
        border: 2px solid #FFD700;
        border-radius: 8px;
        padding: 1rem;
        width: 300px;
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
        z-index: 50;
        display: none;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        color: #FFD700;
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 2px;
        text-align: center;
    }

    .calendar-grid > div {
        padding: 0.5rem;
        color: #FFD700;
    }

    .calendar-day {
        cursor: pointer;
        border-radius: 4px;
        padding: 0.5rem;
        transition: all 0.2s;
    }

    .calendar-day:hover:not(.disabled) {
        background: rgba(255, 215, 0, 0.2);
    }

    .calendar-day.selected {
        background: #FFD700;
        color: #000;
        font-weight: bold;
    }

    .calendar-day.disabled {
        color: #666;
        cursor: not-allowed;
    }

    .calendar-day.today {
        border: 1px solid #FFD700;
    }

    /* Custom Calendar Styling */
    .calendar-day {
        transition: all 0.2s ease;
    }

    .calendar-day:not(.disabled):hover {
        background-color: rgba(255, 215, 0, 0.2);
    }

    #calendar-popup {
    width: 300px;
    background: #1a1a1a;
    border: 2px solid #FFD700;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
    z-index: 1000;
}

#calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

#current-month {
    font-size: 1.1rem;
    font-weight: bold;
}

#prev-month, #next-month {
    font-size: 1.5rem;
    padding: 0 8px;
    cursor: pointer;
    transition: color 0.2s;
}

#prev-month:hover, #next-month:hover {
    color: white;
}
</style>