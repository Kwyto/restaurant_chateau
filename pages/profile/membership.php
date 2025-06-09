<?php 
include '../../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user data and reservation count
$user_id = $_SESSION['user_id'];
$query = "SELECT u.*, COUNT(r.id) as total_reservations,
          CASE 
            WHEN COUNT(r.id) >= 50 THEN 'platinum'
            WHEN COUNT(r.id) >= 10 THEN 'gold'
            ELSE 'silver'
          END as calculated_level
          FROM users u
          LEFT JOIN reservations r ON u.id = r.user_id
          WHERE u.id = ?
          GROUP BY u.id";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Update membership level if needed
if ($user['membership_level'] !== $user['calculated_level']) {
    $update_query = "UPDATE users SET membership_level = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "si", $user['calculated_level'], $user_id);
    mysqli_stmt_execute($stmt);
    $user['membership_level'] = $user['calculated_level'];
}

// Calculate progress to next level
$current_reservations = $user['total_reservations'];
$next_level_target = ($user['membership_level'] === 'silver') ? 10 : 
                    (($user['membership_level'] === 'gold') ? 50 : 50);
$progress_percentage = min(($current_reservations / $next_level_target) * 100, 100);

// Fetch membership benefits
$query = "SELECT * FROM memberships ORDER BY FIELD(level, 'silver', 'gold', 'platinum')";
$memberships = mysqli_query($conn, $query);

include '../../includes/header.php';
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <!-- Profile Header -->
        <?php include 'komponen/profile.php'; ?>
        <!-- Profile Navigation -->
        <?php include 'komponen/navigasi.php'; ?>
        
        <!-- Membership Content -->
        <div class="border border-gray-800 p-8">
            <h2 class="text-2xl font-serif font-bold mb-6">Your Membership</h2>
            
            <div class="grid md:grid-cols-3 gap-8 mb-12">
                <?php 
                $membership_data = [
                    'silver' => [
                        'title' => 'Silver',
                        'description' => 'Basic membership with standard benefits',
                        'benefits' => ['Priority reservations', 'Monthly newsletter'],
                        'required_reservations' => 0
                    ],
                    'gold' => [
                        'title' => 'Gold',
                        'description' => 'Enhanced membership with exclusive benefits',
                        'benefits' => [
                            'Priority reservations',
                            'Monthly newsletter',
                            'Exclusive events',
                            '10% discount on pickup service'
                        ],
                        'required_reservations' => 10
                    ],
                    'platinum' => [
                        'title' => 'Platinum',
                        'description' => 'Premium membership with VIP benefits',
                        'benefits' => [
                            'VIP reservations',
                            'Weekly newsletter',
                            'VIP events',
                            '20% discount on pickup service',
                            'Complimentary champagne'
                        ],
                        'required_reservations' => 50
                    ]
                ];

                foreach ($membership_data as $level => $data): 
                    $is_current = $user['membership_level'] === $level;
                    $is_available = $current_reservations >= $data['required_reservations'];
                ?>
                <div class="border <?php echo $is_current ? 'border-gold' : 'border-gray-700'; ?> p-6 relative">
                    <?php if ($is_current): ?>
                    <div class="absolute top-0 right-0 bg-gold text-black px-3 py-1 text-sm font-medium">CURRENT</div>
                    <?php endif; ?>
                    
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-serif font-bold"><?php echo $data['title']; ?></h3>
                        <?php if ($is_current): ?>
                        <span class="px-3 py-1 bg-gold/10 text-gold text-sm rounded-full">Your Tier</span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-gray-400 mb-6"><?php echo $data['description']; ?></p>
                    
                    <ul class="space-y-3 mb-8">
                        <?php foreach ($data['benefits'] as $benefit): ?>
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-gold mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <?php echo $benefit; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <?php if ($is_current): ?>
                        <button class="w-full py-2 bg-transparent border border-gray-700 text-white">Current Plan</button>
                    <?php elseif (!$is_available): ?>
                        <div class="text-sm text-gray-400 text-center">
                            Need <?php echo $data['required_reservations'] - $current_reservations; ?> more reservations
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="border-t border-gray-800 pt-8">
                <h3 class="text-xl font-serif font-bold mb-4">Membership Progress</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-medium mb-3">Current Benefits</h4>
                        <ul class="space-y-3">
                            <?php 
                            $current_benefits = $membership_data[$user['membership_level']]['benefits'];
                            foreach ($current_benefits as $benefit): 
                            ?>
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-gold mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span><?php echo $benefit; ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-medium mb-3">Your Progress</h4>
                        <div class="mb-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium">
                                    <?php 
                                    $next_level = $user['membership_level'] === 'silver' ? 'Gold' : 
                                                ($user['membership_level'] === 'gold' ? 'Platinum' : 'Platinum');
                                    echo $next_level . " Status";
                                    ?>
                                </span>
                                <span class="text-sm text-gray-400">
                                    <?php echo $current_reservations . "/" . $next_level_target; ?> reservations
                                </span>
                            </div>
                            <div class="w-full bg-gray-800 rounded-full h-2.5">
                                <div class="bg-gold h-2.5 rounded-full" style="width: <?php echo $progress_percentage; ?>%"></div>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm">
                            <?php if ($user['membership_level'] !== 'platinum'): ?>
                                Need <?php echo $next_level_target - $current_reservations; ?> more reservations to reach <?php echo $next_level; ?> status.
                            <?php else: ?>
                                You've reached our highest membership tier!
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>