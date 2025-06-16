<?php
// payment.php
include '../includes/config.php'; // Pastikan path ini benar dan sudah memanggil session_start()

// --- START: PENANGAN AJAX UNTUK PEMBATALAN RESERVASI ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_timed_out_reservation') {
    header('Content-Type: application/json');
    if (isset($_POST['reservation_id']) && isset($_SESSION['user_id'])) {
        $reservation_id = $_POST['reservation_id'];
        $user_id = $_SESSION['user_id'];

        if (isset($_SESSION['reservation_data']) && $_SESSION['reservation_data']['id'] == $reservation_id) {
            $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ? AND status = 'pending'");
            $stmt->bind_param("ii", $reservation_id, $user_id);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                unset($_SESSION['reservation_data'], $_SESSION['booking_data'], $_SESSION['applied_coupon'], $_SESSION['payment_start_time']);
                echo json_encode(['status' => 'success', 'message' => 'Reservasi berhasil dibatalkan.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Reservasi tidak dapat dibatalkan.']);
            }
            $stmt->close();
        } else {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Otorisasi gagal.']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Permintaan tidak valid.']);
    }
    exit();
}
// --- END: PENANGAN AJAX ---

// Redirect jika tidak login atau tidak ada data reservasi
// --- MODIFICATION START: Izinkan halaman untuk memuat bahkan jika data reservasi tidak ada, untuk menampilkan popup sukses ---
if(!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
// Cek jika tidak ada data reservasi DAN tidak ada proses pembayaran yang baru saja berhasil
if (!isset($_SESSION['reservation_data']) && !isset($_SESSION['payment_just_completed'])) {
    header("Location: ../pages/home.php");
    exit();
}
// --- MODIFICATION END ---


// --- START: LOGIKA COUNTDOWN PEMBAYARAN (SAAT PAGE LOAD) ---
if (isset($_SESSION['reservation_data'])) {
    $time_limit = 300; // 5 menit dalam detik
    if (!isset($_SESSION['payment_start_time'])) {
        $_SESSION['payment_start_time'] = time();
    }
    $time_elapsed = time() - $_SESSION['payment_start_time'];
    $time_remaining = $time_limit - $time_elapsed;

    if ($time_remaining <= 0) {
        $reservation_id = $_SESSION['reservation_data']['id'];
        $stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $stmt->close();
        unset($_SESSION['reservation_data'], $_SESSION['booking_data'], $_SESSION['applied_coupon'], $_SESSION['payment_start_time']);
        $_SESSION['error_message'] = "Waktu pembayaran telah habis. Reservasi Anda telah dibatalkan.";
        header("Location: ../pages/home.php");
        exit();
    }
}
// --- END: LOGIKA COUNTDOWN ---

$userData = getUserData($conn, $_SESSION['user_id']);
// --- MODIFICATION START: Inisialisasi variabel bahkan jika sesi tidak ada, untuk mencegah error pada tampilan setelah pembayaran ---
$reservation = $_SESSION['reservation_data'] ?? [];
// --- MODIFICATION END ---

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['selected-table']) && !isset($_POST['apply_coupon']) && !isset($_POST['remove_coupon'])) {
    $_SESSION['booking_data'] = $_POST;
    $_SESSION['booking_data']['selected_table_number'] = $_POST['selected-table'];
    header("Location: payment.php");
    exit();
}

$table_number = $_POST['selected-table'];
$booking = $_SESSION['booking_data'] ?? [];
$pickup_service_status = $reservation['pickup_service'] ?? 0;
$pickupCost = $reservation['pickup_cost'] ?? 0.00;
$foodCost = $reservation['food_cost'] ?? 0.00;
$orderedItems = $reservation['ordered_items'] ?? [];
$selectedTableNumber = $booking['selected_table_number'] ?? 'Not Selected';
$reservationFee = 50.00;

$subtotal = $reservationFee + $foodCost;
if ($pickup_service_status == 1) {
    $subtotal += $pickupCost;
}

$stmt = $conn->prepare("
    SELECT c.id, c.code, c.description, c.discount_value, c.expiration_date, uc.id as user_coupon_id
    FROM coupons c
    INNER JOIN user_coupons uc ON c.id = uc.coupon_id
    WHERE uc.user_id = ? AND uc.used = 0 AND c.expiration_date >= CURRENT_DATE()
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$availableCoupons = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (isset($_POST['apply_coupon'])) {
    $selectedUserCouponId = $_POST['coupon_id'];
    if (!empty($selectedUserCouponId)) {
        $stmt = $conn->prepare("
            SELECT c.id, c.code, c.description, c.discount_value, c.expiration_date
            FROM coupons c
            INNER JOIN user_coupons uc ON c.id = uc.coupon_id
            WHERE uc.id = ? AND uc.user_id = ? AND uc.used = 0 AND c.expiration_date >= CURRENT_DATE()
        ");
        $stmt->bind_param("ii", $selectedUserCouponId, $_SESSION['user_id']);
        $stmt->execute();
        $couponData = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($couponData) {
            $_SESSION['applied_coupon'] = [
                'user_coupon_id' => $selectedUserCouponId,
                'coupon_code' => $couponData['code'],
                'discount_value' => (float) $couponData['discount_value']
            ];
        } else {
            unset($_SESSION['applied_coupon']);
            $error_message = "Kupon tidak valid, sudah kedaluwarsa, atau sudah digunakan.";
        }
    } else {
        unset($_SESSION['applied_coupon']);
    }
    header("Location: payment.php");
    exit();
}

if (isset($_POST['remove_coupon'])) {
    unset($_SESSION['applied_coupon']);
    header("Location: payment.php");
    exit();
}

$discount_amount = 0.00;
if (isset($_SESSION['applied_coupon'])) {
    $discount_amount = $_SESSION['applied_coupon']['discount_value'];
    if ($discount_amount > $subtotal) {
        $discount_amount = $subtotal;
    }
}
$subtotalAfterDiscount = $subtotal - $discount_amount;

$tax = $subtotalAfterDiscount * 0.10;
$grandTotal = $subtotalAfterDiscount + $tax;

// --- MODIFICATION START: Inisialisasi variabel untuk popup ---
$show_success_popup = false;
// --- MODIFICATION END ---

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['payment-method']) && isset($_POST['complete_payment_submit'])) {
    $payment_method = $_POST['payment-method'];
    $reservation_id = $reservation['id'];
    $user_id = $_SESSION['user_id'];
    $final_amount_paid = $grandTotal;

    $conn->begin_transaction();
    try {
        $stmt_payment = $conn->prepare("INSERT INTO payments (user_id, reservation_id, amount, payment_method) VALUES (?, ?, ?, ?)");
        $stmt_payment->bind_param("iids", $user_id, $reservation_id, $final_amount_paid, $payment_method);
        $stmt_payment->execute();

        if ($stmt_payment->affected_rows > 0) {
            // Update both status, total_amount, AND table_number
            $stmt_update_reservation = $conn->prepare("UPDATE reservations SET status = 'completed', total_amount = ?, table_number = ? WHERE id = ?");
            $selectedTableNumber = $_SESSION['booking_data']['selected_table_number'] ?? null;
            $stmt_update_reservation->bind_param("dii", $final_amount_paid, $selectedTableNumber, $reservation_id);
            $stmt_update_reservation->execute();

            if ($stmt_update_reservation->affected_rows > 0) {
                if (isset($_SESSION['applied_coupon'])) {
                    $userCouponIdToMark = $_SESSION['applied_coupon']['user_coupon_id'];
                    $stmt_mark_coupon_used = $conn->prepare("UPDATE user_coupons SET used = 1 WHERE id = ? AND user_id = ?");
                    $stmt_mark_coupon_used->bind_param("ii", $userCouponIdToMark, $user_id);
                    $stmt_mark_coupon_used->execute();
                    $stmt_mark_coupon_used->close();
                }
                $conn->commit();
                
                // --- MODIFICATION START: Atur flag untuk memicu popup, alih-alih redirect ---
                $show_success_popup = true;
                $_SESSION['payment_just_completed'] = true; // Flag untuk mencegah redirect di awal
                // --- MODIFICATION END ---

            } else {
                throw new Exception("Gagal memperbarui status reservasi.");
            }
        } else {
            throw new Exception("Gagal mencatat pembayaran.");
        }
    } catch (Exception $e) {
        $conn->rollback();
        $error_message = "Pembayaran gagal: " . $e->getMessage();
    } finally {
        if (isset($stmt_payment)) $stmt_payment->close();
        if (isset($stmt_update_reservation)) $stmt_update_reservation->close();
    }
}
?>

<?php include '../includes/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* ... CSS Anda yang sudah ada ... */
    .payment-option {
        border: 2px solid #374151; border-radius: 0.5rem; padding: 1.5rem;
        cursor: pointer; transition: all 0.3s ease-in-out; position: relative; overflow: hidden;
        display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;
    }
    .payment-option:hover {
        border-color: #D4AF37; transform: translateY(-5px); box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2);
    }
    .payment-option.selected {
        border-color: #D4AF37; box-shadow: 0 0 15px rgba(212, 175, 55, 0.5);
    }
    .payment-option .payment-logo {
        height: 2.5rem; max-width: 100px; object-fit: contain; margin-bottom: 0.5rem;
    }
    .payment-option input[type="radio"] { display: none; }
    .payment-option .check-icon {
        position: absolute; top: 0.5rem; right: 0.5rem; width: 1.5rem; height: 1.5rem;
        background-color: #D4AF37; color: black; border-radius: 50%;
        display: none; align-items: center; justify-content: center;
    }
    .payment-option.selected .check-icon { display: flex; }
    .payment-details-container {
        max-height: 0; overflow: hidden;
        transition: max-height 0.7s ease-in-out, margin-top 0.7s ease-in-out;
    }
    .payment-details-container.open {
        max-height: 500px; margin-top: 1.5rem;
    }
    #countdown-container {
        background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 5px 5px 15px #111, -5px -5px 15px #333;
    }
    #timer {
        font-size: 3rem;
        font-weight: bold;
        color: #D4AF37;
        text-shadow: 0 0 10px #D4AF37;
    }
</style>

<main class="min-h-screen bg-black text-white py-16 px-4">
    <div class="max-w-4xl mx-auto">
        <?php if(isset($_SESSION['reservation_data'])): ?>
        <h1 class="text-4xl font-serif font-bold mb-6 text-center" data-aos="fade-up">Payment Information</h1>

        <div id="countdown-container" data-aos="fade-up" data-aos-delay="100">
            <p class="text-gray-300 mb-2 text-lg">Selesaikan pembayaran dalam</p>
            <div id="timer"></div>
        </div>

        <?php if (isset($error_message)): ?>
            <div class="bg-red-800 text-white p-4 rounded mb-4"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-12">
            <div>
                <form id="payment-form" action="payment.php" method="POST">
                    <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-right">Select Payment Method</h2>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                        <div class="payment-option selected" data-payment="credit_card">
                            <input type="radio" id="credit_card" name="payment-method" value="credit_card" checked>
                            <img src="https://img.icons8.com/color/96/card-in-use.png" alt="Credit Card" class="payment-logo">
                            <span class="font-medium">Credit Card</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>

                        <div class="payment-option" data-payment="paypal">
                            <input type="radio" id="paypal" name="payment-method" value="paypal">
                            <img src="https://img.icons8.com/?size=100&id=13611&format=png&color=000000" alt="PayPal" class="payment-logo">
                            <span class="font-medium">PayPal</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>

                        <div class="payment-option" data-payment="bank_transfer">
                            <input type="radio" id="bank_transfer" name="payment-method" value="bank_transfer">
                            <img src="https://img.icons8.com/color/96/000000/bank-building.png" alt="Bank Transfer" class="payment-logo">
                            <span class="font-medium">Bank Transfer</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>

                        <div class="payment-option" data-payment="apple_pay">
                            <input type="radio" id="apple_pay" name="payment-method" value="apple_pay">
                            <img src="https://img.icons8.com/?size=100&id=63492&format=png&color=000000" alt="Apple Pay" class="payment-logo">
                            <span class="font-medium">Apple Pay</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>
                        
                        <div class="payment-option" data-payment="crypto">
                            <input type="radio" id="crypto" name="payment-method" value="crypto">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/4/46/Bitcoin.svg" alt="Crypto" class="payment-logo">
                            <span class="font-medium">Crypto</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>
                        
                        <div class="payment-option" data-payment="google_pay">
                            <input type="radio" id="google_pay" name="payment-method" value="google_pay">
                            <img src="https://img.icons8.com/color/96/google-pay.png" alt="Google Pay" class="payment-logo">
                            <span class="font-medium">Google Pay</span>
                            <div class="check-icon"><svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg></div>
                        </div>
                    </div>
                    
                    <div id="payment-details-wrapper">
                        <div id="credit_card-details" class="payment-details-container">
                            <div class="space-y-4">
                                <div>
                                    <label for="card-number" class="block text-sm font-medium mb-2">Card Number</label>
                                    <input type="text" id="card-number" name="card-number" placeholder="1234 5678 9012 3456" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="card-expiry" class="block text-sm font-medium mb-2">Expiry Date</label>
                                        <input type="text" id="card-expiry" name="card-expiry" placeholder="MM/YY" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                                    </div>
                                    <div>
                                        <label for="card-cvv" class="block text-sm font-medium mb-2">CVV</label>
                                        <input type="text" id="card-cvv" name="card-cvv" placeholder="123" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="paypal-details" class="payment-details-container">
                            <div>
                                <label for="paypal-email" class="block text-sm font-medium mb-2">PayPal Account Email</label>
                                <input type="email" id="paypal-email" name="paypal-email" placeholder="you@example.com" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                            </div>
                        </div>
                        <div id="bank_transfer-details" class="payment-details-container">
                            <div>
                                <label for="bank-selection" class="block text-sm font-medium mb-2">Select Bank</label>
                                <select id="bank-selection" name="bank-selection" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md text-white">
                                    <option value="">-- Select Destination Bank --</option>
                                    <option value="jpmorgan">JPMorgan Chase</option>
                                    <option value="boa">Bank of America</option>
                                    <option value="hsbc">HSBC</option>
                                    <option value="citibank">Citibank</option>
                                    <option value="deutsche">Deutsche Bank</option>
                                </select>
                            </div>
                        </div>
                        <div id="crypto-details" class="payment-details-container">
                             <div class="space-y-4">
                                <div>
                                    <label for="crypto-coin" class="block text-sm font-medium mb-2">Select Coin</label>
                                    <select id="crypto-coin" name="crypto-coin" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md text-white">
                                        <option value="btc">Bitcoin (BTC)</option>
                                        <option value="eth">Ethereum (ETH)</option>
                                        <option value="usdt">Tether (USDT)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="crypto-wallet" class="block text-sm font-medium mb-2">Wallet Address</label>
                                    <input type="text" id="crypto-wallet" name="crypto-wallet" placeholder="Enter your wallet address" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                                </div>
                            </div>
                        </div>
                        <div id="google_pay-details" class="payment-details-container">
                             <div>
                                <label for="gpay-id" class="block text-sm font-medium mb-2">Google Pay ID / Phone Number</label>
                                <input type="text" id="gpay-id" name="gpay-id" placeholder="yourname@okhdfcbank" class="w-full bg-black border border-gray-800 py-3 px-4 focus:outline-none focus:ring-gold focus:border-gold rounded-md">
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center">
                        <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 text-gold focus:ring-gold border-gray-300 rounded">
                        <label for="terms" class="ml-2 text-sm">I agree to the <a href="#" class="text-gold hover:underline">terms and conditions</a></label>
                    </div>
                    
                    <input type="hidden" name="complete_payment_submit" value="1">
                    <button type="submit" id="complete-reservation" class="mt-6 w-full py-4 bg-gold text-black font-bold text-lg hover:bg-transparent hover:text-gold border-2 border-gold transition duration-500 transform hover:-translate-y-1 hover:shadow-lg hover:shadow-gold/30 rounded-lg">
                        Complete Reservation
                    </button>
                </form>
            </div>
            
            <div>
                 <h2 class="text-2xl font-serif font-bold mb-6" data-aos="fade-left">Order Summary</h2>

                <div class="border border-gray-800 p-6 mb-6" data-aos="zoom-in">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Table Reservation Fee</span>
                            <span>$<?php echo number_format($reservationFee, 2); ?></span>
                        </div>
                        <?php if($foodCost > 0): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Food Cost</span>
                            <span>$<?php echo number_format($foodCost, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if($pickup_service_status == 1): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Pickup Service</span>
                            <span>$<?php echo number_format($pickupCost, 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="pt-4 border-t border-gray-800">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal (Pre-Discount)</span>
                                <span>$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <?php if ($discount_amount > 0): ?>
                            <div class="flex justify-between text-green-400">
                                <span class="text-green-400">Coupon Discount</span>
                                <span>-$<?php echo number_format($discount_amount, 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Subtotal (Post-Discount)</span>
                                <span>$<?php echo number_format($subtotalAfterDiscount, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Tax (10%)</span>
                                <span>$<?php echo number_format($tax, 2); ?></span>
                            </div>
                        </div>
                        <div class="pt-4 border-t border-gray-800 font-medium">
                            <div class="flex justify-between text-gold text-lg">
                                <span>Total Payable</span>
                                <span>$<?php echo number_format($grandTotal, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border border-gray-800 p-6 mb-6" data-aos="fade-up">
                    <h3 class="text-lg font-medium mb-4">Apply Coupon</h3>
                    <form action="payment.php" method="POST" class="flex flex-col sm:flex-row gap-2">
                        <select name="coupon_id" class="w-full bg-black border border-gray-800 py-2 px-3 focus:outline-none focus:ring-gold focus:border-gold text-white rounded-md">
                            <option value="">Select a coupon</option>
                            <?php foreach ($availableCoupons as $coupon): ?>
                                <option value="<?php echo htmlspecialchars($coupon['user_coupon_id']); ?>"
                                    <?php echo (isset($_SESSION['applied_coupon']) && $_SESSION['applied_coupon']['user_coupon_id'] == $coupon['user_coupon_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($coupon['code'] . ' - $' . number_format($coupon['discount_value'], 2) . ' Off'); ?>
                                    <?php echo !empty($coupon['description']) ? ' (' . htmlspecialchars($coupon['description']) . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="apply_coupon" class="bg-gold text-black px-4 py-2 rounded-md hover:bg-yellow-400 transition text-sm font-semibold">Apply</button>
                    </form>
                    
                    <?php if (isset($_SESSION['applied_coupon'])): ?>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-green-400 text-sm">Coupon "<?php echo htmlspecialchars($_SESSION['applied_coupon']['coupon_code']); ?>" applied. Discount: $<?php echo number_format($_SESSION['applied_coupon']['discount_value'], 2); ?></p>
                            <form action="payment.php" method="POST" class="ml-2">
                                <button type="submit" name="remove_coupon" class="text-red-400 hover:underline text-sm">Remove</button>
                            </form>
                        </div>
                    <?php elseif (isset($_POST['apply_coupon']) && empty($couponData)): ?>
                        <p class="text-red-400 text-sm mt-2">Invalid, expired, or already used coupon.</p>
                    <?php endif; ?>
                    </div>

                 <div class="border border-gray-800 p-6" data-aos="fade-up">
                    <h3 class="text-lg font-medium mb-4">Reservation Details</h3>
                    <div class="space-y-3 text-gray-400">
                        <p><strong>Reservation ID:</strong> <?php echo htmlspecialchars($reservation['id']); ?></p>
                        <p><strong>Guests:</strong> <?php echo htmlspecialchars($reservation['guests'] . ($reservation['guests'] == 1 ? ' person' : ' people')); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars(date('F j, Y', strtotime($reservation['date']))); ?></p>
                        <p><strong>Time:</strong> <?php echo htmlspecialchars(date('g:i A', strtotime($reservation['time']))); ?></p>
                        <p><strong>Occasion:</strong> <?php echo htmlspecialchars(ucfirst($reservation['occasion'])); ?></p>
                        <p><strong>Table Number:</strong> <span class="text-gold"><?php echo htmlspecialchars($selectedTableNumber); ?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="text-center">
            <h1 class="text-4xl font-serif font-bold mb-6 text-gold">Terima Kasih</h1>
            <p class="text-lg text-gray-300">Pembayaran Anda sedang diproses dan konfirmasi akan segera muncul.</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php if ($show_success_popup): ?>
<?php
    // Hapus data sesi setelah digunakan untuk mencegah reservasi ganda
    unset($_SESSION['reservation_data'], $_SESSION['booking_data'], $_SESSION['applied_coupon'], $_SESSION['payment_start_time']);
    // Hapus flag khusus ini
    unset($_SESSION['payment_just_completed']);
?>
<style>
    @keyframes breathing {
        0% { transform: scale(1); }
        50% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }
    .swal2-icon.swal2-success .swal2-success-ring {
        border-color: #D4AF37 !important;
    }
    .swal2-icon.swal2-success [class^=swal2-success-line] {
        background-color: #D4AF37 !important;
    }
    .swal2-icon.swal2-success {
        animation: breathing 2s ease-in-out infinite;
        border-color: transparent !important;
    }
    .swal-custom-actions {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem; /* 12px */
        margin-top: 1.5rem; /* 24px */
    }
    .swal-custom-button {
        display: block;
        width: 80%;
        max-width: 280px;
        padding: 0.75rem; /* 12px */
        border-radius: 0.375rem; /* 6px */
        text-align: center;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .swal-custom-button.home {
        background-color: #D4AF37;
        color: #1a1d24; /* dark gray */
    }
    .swal-custom-button.home:hover {
        background-color: #c8a430;
        transform: translateY(-2px);
    }
    .swal-custom-button.profile {
        background-color: transparent;
        color: #D4AF37;
        border: 2px solid #D4AF37;
    }
     .swal-custom-button.profile:hover {
        background-color: #D4AF37;
        color: #1a1d24;
        transform: translateY(-2px);
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        title: 'Pembayaran Berhasil!',
        icon: 'success',
        background: '#1f2937',
        color: '#ffffff',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        html: `
            <p class="text-gray-300 mb-6 -mt-2">Reservasi Anda telah sukses terkonfirmasi.</p>
            <div class="swal-custom-actions">
                <a href="../pages/home.php" class="swal-custom-button home">Kembali ke Home</a>
                <a href="../pages/profile/history.php" class="swal-custom-button profile">Lihat Riwayat Reservasi</a>
            </div>
        `,
    });
});
</script>
<?php endif; ?>
<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentDetailsContainers = document.querySelectorAll('.payment-details-container');

    function setRequiredAttributes(containerId, required) {
        const container = document.getElementById(containerId);
        if (container) {
            const inputs = container.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (required) {
                    input.setAttribute('required', '');
                } else {
                    input.removeAttribute('required');
                }
            });
        }
    }

    function selectPaymentOption(option) {
        paymentOptions.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');
        
        const radio = option.querySelector('input[type="radio"]');
        if (radio) radio.checked = true;

        paymentDetailsContainers.forEach(container => {
            container.classList.remove('open');
            setRequiredAttributes(container.id, false);
        });

        const paymentType = option.getAttribute('data-payment');
        let targetContainerId = paymentType ? paymentType + '-details' : null;

        if (targetContainerId) {
            const targetContainer = document.getElementById(targetContainerId);
            if (targetContainer) {
                targetContainer.classList.add('open');
                setRequiredAttributes(targetContainerId, true);
            }
        }
    }
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', () => selectPaymentOption(option));
    });

    const initiallySelectedOption = document.querySelector('.payment-option.selected');
    if (initiallySelectedOption) {
        selectPaymentOption(initiallySelectedOption);
    }
    
    // --- START: SKRIP COUNTDOWN DAN NOTIFIKASI ---
    const timerElement = document.getElementById('timer');
    // Pastikan variabel PHP ada sebelum di-encode
    <?php if (isset($_SESSION['reservation_data']) && isset($time_remaining)): ?>
    const reservationId = <?php echo json_encode($_SESSION['reservation_data']['id']); ?>;
    let timeLeft = <?php echo $time_remaining; ?>;

    const timerInterval = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            timerElement.textContent = "00:00";
            const completeButton = document.getElementById('complete-reservation');
            if(completeButton) completeButton.disabled = true;

            Swal.fire({
                title: 'Waktu Habis!',
                text: "Waktu pembayaran Anda telah berakhir. Reservasi ini akan dibatalkan.",
                icon: 'warning',
                confirmButtonText: 'Kembali ke Home',
                background: '#1f2937',
                color: '#ffffff',
                confirmButtonColor: '#D4AF37',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "payment.php", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        // Redirect setelah request selesai
                        window.location.href = '../pages/home.php';
                    };
                    xhr.send("action=cancel_timed_out_reservation&reservation_id=" + reservationId);
                }
            });

        } else {
            const minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            if(timerElement) timerElement.textContent = `${minutes}:${seconds}`;
            timeLeft--;
        }
    }, 1000);
    <?php endif; ?>
    // --- END: SKRIP COUNTDOWN ---
});
</script>