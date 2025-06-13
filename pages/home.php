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
            <h2 class="text-5xl font-serif font-bold text-gold" data-aos="zoom-in">Reservation</h2>
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
                
                <form id="reservation-form" action="booking.php" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label for="guests" class="block text-sm font-medium mb-2 text-gold">Number of Guests</label>
                            <select id="guests" name="guests" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white" required>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo $i; ?>" <?php echo $i == 2 ? 'selected' : ''; ?>>
                                        <?php echo $i . ($i == 1 ? ' Person' : ' People'); ?>
                                    </option>
                                <?php endfor; ?>
                                <option value="13">13+ People (Contact Us)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="date" class="block text-sm font-medium mb-2 text-gold">Date</label>
                            <input type="date" id="date" name="date" min="<?php echo date('Y-m-d'); ?>" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white" required>
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
                                    <input
                                        type="text"
                                        id="pickup-time"
                                        name="pickup-time"
                                        class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white"
                                        placeholder="e.g. 18:30 or 6:30 PM"
                                        pattern="^([01]?[0-9]|2[0-3]):[0-5][0-9]( ?[APap][Mm])?$"
                                        autocomplete="off"
                                    >
                                </div>
                                
                                <div>
                                    <label for="vehicle-type" class="block text-sm font-medium mb-2 text-gold">Vehicle Preference</label>
                                    <select id="vehicle-type" name="vehicle-type" class="w-full bg-black border border-gold py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold text-white">
                                        <option value="mercedes-s-class" data-price="25000">Mercedes S-Class ($25,000/km)</option>
                                        <option value="rolls-royce-phantom" data-price="50000">Rolls-Royce Phantom ($50,000/km)</option>
                                        <option value="bentley-mulsanne" data-price="40000">Bentley Mulsanne ($40,000/km)</option>
                                        <option value="maybach" data-price="35000">Maybach ($35,000/km)</option>
                                        <option value="limousine" data-price="75000">Limousine ($75,000/km)</option>
                                    </select>
                                </div>
                                
                                <div class="md:col-span-2">
                                    <p class="text-sm text-gray-400">Distance: <span id="estimated-distance" class="text-gold">0 km</span></p>
                                    <p class="text-sm text-gray-400">Pickup cost: <span id="pickup-cost" class="text-gold">$0</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
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
    if (!document.getElementById('pickup-yes').checked) {
        document.getElementById('pickup-cost').textContent = '$0';
        document.getElementById('pickup-cost-summary').textContent = '$0';
        return;
    }

    const vehicleSelect = document.getElementById('vehicle-type');
    const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
    const pricePerKm = parseInt(selectedOption.dataset.price);
    
    // Always calculate based on 5km
    const pickupCost = Math.round(pricePerKm * 5);
    
    // Update cost displays
    const formattedCost = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(pickupCost);
    
    document.getElementById('pickup-cost').textContent = formattedCost;
    document.getElementById('pickup-cost-row').classList.remove('hidden');
    document.getElementById('pickup-cost-summary').textContent = formattedCost;
    
    updateTotal();
}

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
        const existingItem = orderedItems.find(item => item.id === itemId);
        
        if(existingItem) {
            existingItem.quantity += 1;
        } else {
            orderedItems.push({
                id: itemId,
                name: itemName,
                price: itemPrice,
                quantity: 1
            });
        }
        
        updateOrderedItemsDisplay();
        updateTotal();
    }
    
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
    
    function updatePickupCost() {
        if (!document.getElementById('pickup-yes').checked) {
            document.getElementById('pickup-cost').textContent = '$0';
            document.getElementById('pickup-cost-summary').textContent = '$0';
            return;
        }

        const vehicleSelect = document.getElementById('vehicle-type');
        const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
        const pricePerKm = parseInt(selectedOption.dataset.price);
        
        // Always calculate based on 5km
        const pickupCost = Math.round(pricePerKm * 5);
        
        // Update cost displays
        const formattedCost = new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD',
            maximumFractionDigits: 0
        }).format(pickupCost);
        
        document.getElementById('pickup-cost').textContent = formattedCost;
        document.getElementById('pickup-cost-row').classList.remove('hidden');
        document.getElementById('pickup-cost-summary').textContent = formattedCost;
        
        updateTotal();
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
        const pickupTimeInput = document.getElementById('pickup-time');
        let pickupTime = pickupTimeInput.value.trim();
        let formatted = '-';
        if (pickupTime) {
            // Try to parse 24h or 12h format
            let match = pickupTime.match(/^([01]?[0-9]|2[0-3]):([0-5][0-9]( ?[APap][Mm])?)$/);
            if (match) {
                let hour = parseInt(match[1], 10);
                let minute = match[2];
                let ampm = match[4] ? match[4].toUpperCase() : '';
                if (!ampm) {
                    // Convert 24h to 12h
                    ampm = hour >= 12 ? 'PM' : 'AM';
                    hour = hour % 12 || 12;
                }
                formatted = `${hour}:${minute} ${ampm}`;
            } else {
                formatted = pickupTime; // fallback, show as typed
            }
        }
        document.getElementById('summary-pickup-time').textContent = formatted;
    }
    
    function updateTotal() {
        let subtotal = 50.00; // Base reservation fee

        // Add food cost
        const foodCost = orderedItems.reduce((total, item) => total + (item.price * item.quantity), 0);
        if(foodCost > 0) {
            document.getElementById('food-cost-row').classList.remove('hidden');
            document.getElementById('food-cost').textContent = `$${foodCost.toFixed(2)}`;
            subtotal += foodCost;
        } else {
            document.getElementById('food-cost-row').classList.add('hidden');
        }

        // Add pickup cost
        if(document.getElementById('pickup-yes').checked) {
            const vehicleSelect = document.getElementById('vehicle-type');
            const selectedOption = vehicleSelect.options[vehicleSelect.selectedIndex];
            const pricePerKm = parseInt(selectedOption.dataset.price);
            const pickupCost = pricePerKm * currentDistance;
            
            document.getElementById('pickup-cost-row').classList.remove('hidden');
            document.getElementById('pickup-cost-summary').textContent = `$${pickupCost.toLocaleString()}`;
            subtotal += pickupCost;
        } else {
            document.getElementById('pickup-cost-row').classList.add('hidden');
        }
        
        // Calculate tax and total
        const tax = subtotal * 0.10;
        const total = subtotal + tax;
        
        // Update display
        document.getElementById('tax-amount').textContent = `$${tax.toFixed(2)}`;
        document.getElementById('summary-total').textContent = `$${total.toLocaleString()}`;
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
</script>