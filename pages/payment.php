<?php 
include '../includes/config.php';

// Redirect if not logged in or no reservation data
if(!isset($_SESSION['user_id']) || !isset($_SESSION['reservation_data']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../auth/login.php");
    exit();
}

$reservation = $_SESSION['reservation_data'];
$booking = $_SESSION['booking_data'];
$userData = getUserData($conn, $_SESSION['user_id']);

// Calculate total
$total = 50.00; // Base reservation fee
if($reservation['pickup'] == 'yes') {
    $total += 150.00; // Pickup service fee
}
$tax = $total * 0.10;
$grandTotal = $total + $tax;
?>

<?php include '../includes/header.php'; ?>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-4xl font-serif font-bold mb-12 text-center" data-aos="fade-up">Payment Information</h1>
        
        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-right">Payment Method</h2>
                
                <form id="payment-form" action="process-payment.php" method="POST">
                    <div class="space-y-4 mb-8">
                        <div class="border border-gray-800 p-4 flex items-center gap-4 cursor-pointer hover:border-gold transition duration-300" onclick="selectPayment('credit-card')">
                            <input type="radio" id="credit-card" name="payment-method" value="credit-card" checked class="h-4 w-4 text-gold focus:ring-gold">
                            <label for="credit-card" class="flex-1 cursor-pointer">
                                <span class="block font-medium">Credit Card</span>
                                <span class="block text-sm text-gray-400">Visa, Mastercard, American Express</span>
                            </label>
                            <div class="flex gap-2">
                                <img src="../../assets/images/payment/visa.png" alt="Visa" class="h-8">
                                <img src="../../assets/images/payment/mastercard.png" alt="Mastercard" class="h-8">
                                <img src="../../assets/images/payment/amex.png" alt="American Express" class="h-8">
                            </div>
                        </div>
                        
                        <div class="border border-gray-800 p-4 flex items-center gap-4 cursor-pointer hover:border-gold transition duration-300" onclick="selectPayment('paypal')">
                            <input type="radio" id="paypal" name="payment-method" value="paypal" class="h-4 w-4 text-gold focus:ring-gold">
                            <label for="paypal" class="flex-1 cursor-pointer">
                                <span class="block font-medium">PayPal</span>
                                <span class="block text-sm text-gray-400">Pay with your PayPal account</span>
                            </label>
                            <img src="../../assets/images/payment/paypal.png" alt="PayPal" class="h-8">
                        </div>
                        
                        <div class="border border-gray-800 p-4 flex items-center gap-4 cursor-pointer hover:border-gold transition duration-300" onclick="selectPayment('bank-transfer')">
                            <input type="radio" id="bank-transfer" name="payment-method" value="bank-transfer" class="h-4 w-4 text-gold focus:ring-gold">
                            <label for="bank-transfer" class="flex-1 cursor-pointer">
                                <span class="block font-medium">Bank Transfer</span>
                                <span class="block text-sm text-gray-400">Direct bank transfer</span>
                            </label>
                        </div>
                    </div>
                    
                    <div id="credit-card-form" class="mb-8">
                        <div class="space-y-4">
                            <div>
                                <label for="card-number" class="block text-sm font-medium mb-2">Card Number</label>
                                <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                            </div>
                            
                            <div>
                                <label for="card-name" class="block text-sm font-medium mb-2">Name on Card</label>
                                <input type="text" id="card-name" name="card-name" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold" value="<?php echo $userData['first_name'] . ' ' . $userData['last_name']; ?>">
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="card-expiry" class="block text-sm font-medium mb-2">Expiry Date</label>
                                    <input type="text" id="card-expiry" name="card-expiry" placeholder="MM/YY" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                </div>
                                <div>
                                    <label for="card-cvv" class="block text-sm font-medium mb-2">CVV</label>
                                    <input type="text" id="card-cvv" name="card-cvv" placeholder="123" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center mb-6">
                        <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded">
                        <label for="terms" class="ml-2 text-sm">I agree to the <a href="#" class="text-gold hover:underline">terms and conditions</a></label>
                    </div>
                    
                    <button type="submit" id="complete-reservation" class="w-full py-4 bg-gold text-black hover:bg-transparent hover:text-gold hover:border hover:border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30">
                        Complete Reservation
                    </button>
                </form>
            </div>
            
            <div>
                <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-left">Order Summary</h2>
                
                <div class="border border-gray-800 p-6 mb-6" data-aos="zoom-in">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Table Reservation</span>
                            <span>$50.00</span>
                        </div>
                        
                        <?php if($reservation['pickup'] == 'yes'): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Pickup Service</span>
                            <span>$150.00</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="pt-4 border-t border-gray-800">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal</span>
                                <span>$<?php echo number_format($total, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tax (10%)</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                        </div>
                        
                        <div class="pt-4 border-t border-gray-800 font-medium">
                            <div class="flex justify-between">
                                <span>Total</span>
                                <span>$<?php echo number_format($grandTotal, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border border-gray-800 p-6" data-aos="fade-up">
                    <h3 class="text-lg font-medium mb-4">Reservation Details</h3>
                    <div class="space-y-3 text-gray-400">
                        <p><strong>Name:</strong> <?php echo $booking['full-name']; ?></p>
                        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($reservation['date'])); ?></p>
                        <p><strong>Time:</strong> <?php echo date('g:i A', strtotime($reservation['time'])); ?></p>
                        <p><strong>Guests:</strong> <?php echo $reservation['guests'] . ($reservation['guests'] == 1 ? ' person' : ' people'); ?></p>
                        <?php if($reservation['pickup'] == 'yes'): ?>
                            <p><strong>Pickup:</strong> <?php echo $reservation['pickup-location'] . ' at ' . date('g:i A', strtotime($reservation['pickup-time'])); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

<script>
function selectPayment(method) {
    document.getElementById(method).checked = true;
    
    if(method === 'credit-card') {
        document.getElementById('credit-card-form').classList.remove('hidden');
    } else {
        document.getElementById('credit-card-form').classList.add('hidden');
    }
}
</script>