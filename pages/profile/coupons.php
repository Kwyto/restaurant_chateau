<?php 
include '../../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Get user data with membership info
$user_id = $_SESSION['user_id'];

// Ambil data lengkap pengguna, termasuk membership_level dari database
$user_query = "SELECT * FROM users WHERE id = ?"; // PENTING: Ganti 'users' jika nama tabel pengguna Anda berbeda
$user_stmt = mysqli_prepare($conn, $user_query);
mysqli_stmt_bind_param($user_stmt, "i", $user_id);
mysqli_stmt_execute($user_stmt);
$user_result = mysqli_stmt_get_result($user_stmt);
$user = mysqli_fetch_assoc($user_result);


if (!$user) {
   
    session_destroy();
    header("Location: ../auth/login.php");
    exit();
}


// Modify the available coupons query to check membership level
$available_coupons_query = "
    SELECT c.* FROM coupons c
    WHERE c.expiration_date >= CURRENT_DATE
    AND NOT EXISTS (
        SELECT 1 
        FROM user_coupons uc 
        WHERE uc.coupon_id = c.id 
        AND uc.user_id = ?
    )
    AND (
        c.membership_required IS NULL 
        OR (
            c.membership_required = 'gold' AND ? IN ('gold', 'platinum')
            OR (c.membership_required = 'platinum' AND ? = 'platinum')
        )
    )
    ORDER BY c.membership_required DESC, c.discount_value DESC"; // Mengurutkan berdasarkan membership dan diskon;
$stmt = mysqli_prepare($conn, $available_coupons_query);
mysqli_stmt_bind_param($stmt, "iss", $user_id, $user['membership_level'], $user['membership_level']);
mysqli_stmt_execute($stmt);
$available_coupons = mysqli_stmt_get_result($stmt);

// Handle coupon claim
if (isset($_POST['claim_coupon'])) {
    $coupon_id = (int)$_POST['coupon_id'];
    
    // Check coupon membership requirement
    $check_query = "SELECT c.*, 
                           EXISTS(SELECT 1 FROM user_coupons uc WHERE uc.user_id = ? AND uc.coupon_id = ?) as already_claimed
                           FROM coupons c 
                           WHERE c.id = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "iii", $user_id, $coupon_id, $coupon_id);
    mysqli_stmt_execute($check_stmt);
    $coupon_check = mysqli_fetch_assoc(mysqli_stmt_get_result($check_stmt));
    
    if ($coupon_check['already_claimed']) {
        $_SESSION['error'] = "You already have this coupon!";
    } 
    elseif ($coupon_check['membership_required'] && 
            (($coupon_check['membership_required'] === 'platinum' && $user['membership_level'] !== 'platinum') ||
             ($coupon_check['membership_required'] === 'gold' && !in_array($user['membership_level'], ['gold', 'platinum'])))) {
        $_SESSION['error'] = "This coupon requires " . ucfirst($coupon_check['membership_required']) . " membership!";
    }
    else {
        $insert_query = "INSERT INTO user_coupons (user_id, coupon_id, used) VALUES (?, ?, 0)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ii", $user_id, $coupon_id);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $_SESSION['success'] = "Coupon claimed successfully!";
        } else {
            $_SESSION['error'] = "Error claiming coupon.";
        }
    }
    
    header("Location: coupons.php");
    exit();
}

// Get user's ACTIVE coupons (coupons that have not been used)
$user_coupons_query = "
    SELECT c.*, uc.used, uc.id as user_coupon_id 
    FROM coupons c
    INNER JOIN user_coupons uc ON c.id = uc.coupon_id
    WHERE uc.user_id = ? AND uc.used = 0
    ORDER BY c.expiration_date DESC";
$stmt = mysqli_prepare($conn, $user_coupons_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_coupons = mysqli_stmt_get_result($stmt);

include '../../includes/header.php';
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <?php include 'komponen/profile.php'; ?>
        
        <?php include 'komponen/navigasi.php'; ?>
        
        <div class="border border-gray-800 p-8">
            <h2 class="text-2xl font-serif font-bold mb-6">Your Coupons</h2>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="grid md:grid-cols-2 gap-6 mb-12">
                <?php if (mysqli_num_rows($user_coupons) > 0): ?>
                    <?php while ($coupon = mysqli_fetch_assoc($user_coupons)): ?>
                        <?php
                        // Cek apakah kupon ini adalah kupon membership
                        $is_permanent_coupon = in_array($coupon['membership_required'], ['gold', 'platinum']);
                        ?>
                        <div class="border border-gold p-6 relative overflow-hidden">
                            <?php // Label 'ACTIVE' atau 'MEMBER' di pojok kanan atas ?>
                            <div class="absolute top-0 right-0 bg-gold text-black px-3 py-1 text-sm font-medium">
                                <?php echo $is_permanent_coupon ? 'MEMBER' : 'ACTIVE'; ?>
                            </div>
                            
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-xl font-serif font-bold"><?php echo htmlspecialchars($coupon['description']); ?></h3>
                                <span class="px-3 py-1 bg-gold/10 text-gold text-sm rounded-full">
                                    <?php 
                                    if (strtotime($coupon['expiration_date']) < time()) {
                                        echo 'Expired';
                                    } else {
                                        echo 'Active';
                                    }
                                    ?>
                                </span>
                            </div>
                            
                            <p class="text-gray-400 mb-6">
                                <?php echo $coupon['discount_value']; ?>% off
                            </p>
                            
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="block text-sm text-gray-400">Valid until</span>
                                    <span class="font-medium"><?php echo date('F d, Y', strtotime($coupon['expiration_date'])); ?></span>
                                </div>
                                <div class="text-right">
                                    <span class="block text-sm text-gray-400">Code</span>
                                    <span class="font-mono font-bold text-gold">
                                        <?php echo htmlspecialchars($coupon['code']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-gray-400 md:col-span-2">You don't have any active coupons right now. Claim one below!</p>
                <?php endif; ?>
            </div>
            
            <?php if (mysqli_num_rows($available_coupons) > 0): ?>
                <div class="border-t border-gray-800 pt-8 mb-12">
                    <h3 class="text-xl font-serif font-bold mb-6">Available Coupons</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <?php while ($coupon = mysqli_fetch_assoc($available_coupons)): ?>
                            <div class="border border-gray-700 p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-serif font-bold"><?php echo htmlspecialchars($coupon['description']); ?></h3>
                                    <?php if ($coupon['membership_required']): ?>
                                        <span class="px-3 py-1 bg-<?php echo $coupon['membership_required']; ?>-900/50 text-<?php echo $coupon['membership_required']; ?>-400 text-sm rounded-full">
                                            <?php echo ucfirst($coupon['membership_required']); ?> Only
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-800 text-sm rounded-full">Available</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-400 mb-6"><?php echo $coupon['discount_value']; ?>% off</p>
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="block text-sm text-gray-400">Valid until</span>
                                        <span class="font-medium"><?php echo date('F d, Y', strtotime($coupon['expiration_date'])); ?></span>
                                    </div>
                                    <form method="POST">
                                        <input type="hidden" name="coupon_id" value="<?php echo $coupon['id']; ?>">
                                        <button type="submit" name="claim_coupon" 
                                                class="px-4 py-2 bg-gold text-black hover:bg-transparent hover:text-gold border-gold border transition duration-300 text-sm">
                                            Claim Now
                                        </button>
                                        </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="border-t border-gray-800 pt-8">
                <h3 class="text-xl font-serif font-bold mb-4">How to Use Your Coupons</h3>
                <div class="grid md:grid-cols-3 gap-8">
                    <div>
                        <div class="flex items-center mb-3">
                            <div class="bg-gold/10 text-gold p-3 rounded-full mr-4">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium">Make a Reservation</h4>
                        </div>
                        <p class="text-gray-400 pl-14">Book your table through our website or mobile app as usual.</p>
                    </div>
                    <div>
                        <div class="flex items-center mb-3">
                            <div class="bg-gold/10 text-gold p-3 rounded-full mr-4">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium">Apply Coupon</h4>
                        </div>
                        <p class="text-gray-400 pl-14">Enter your coupon code during the reservation process.</p>
                    </div>
                    <div>
                        <div class="flex items-center mb-3">
                            <div class="bg-gold/10 text-gold p-3 rounded-full mr-4">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium">Enjoy Benefits</h4>
                        </div>
                        <p class="text-gray-400 pl-14">Your discount or special offer will be applied automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>