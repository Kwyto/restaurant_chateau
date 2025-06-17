<?php 
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
}

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../../access-denied.php");
    exit();
}

include_once '../includes/config.php';

// Get all necessary data
$users = getCustomers($conn);
$menu = getMenuItems($conn, limit:5);
$reservations = getReservations($conn, limit:6);
$coupons = getCoupon($conn, limit:3);

// Calculate statistics
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));
$current_month = date('m');
$current_year = date('Y');
$last_month = date('m', strtotime('-1 month'));

// Total reservations
$total_reservations_query = "SELECT COUNT(*) as total FROM reservations";
$total_reservations_result = mysqli_query($conn, $total_reservations_query);
$total_reservations = mysqli_fetch_assoc($total_reservations_result)['total'];

// Today's reservations
$today_reservations_query = "SELECT COUNT(*) as total FROM reservations WHERE reservation_date = '$today'";
$today_reservations_result = mysqli_query($conn, $today_reservations_query);
$today_reservations = mysqli_fetch_assoc($today_reservations_result)['total'];

// Yesterday's reservations
$yesterday_reservations_query = "SELECT COUNT(*) as total FROM reservations WHERE reservation_date = '$yesterday'";
$yesterday_reservations_result = mysqli_query($conn, $yesterday_reservations_query);
$yesterday_reservations = mysqli_fetch_assoc($yesterday_reservations_result)['total'];

// This month's reservations
$month_reservations_query = "SELECT COUNT(*) as total FROM reservations 
                            WHERE MONTH(reservation_date) = '$current_month' 
                            AND YEAR(reservation_date) = '$current_year'";
$month_reservations_result = mysqli_query($conn, $month_reservations_query);
$month_reservations = mysqli_fetch_assoc($month_reservations_result)['total'];

// Last month's reservations
$last_month_reservations_query = "SELECT COUNT(*) as total FROM reservations 
                                WHERE MONTH(reservation_date) = '$last_month' 
                                AND YEAR(reservation_date) = '$current_year'";
$last_month_reservations_result = mysqli_query($conn, $last_month_reservations_query);
$last_month_reservations = mysqli_fetch_assoc($last_month_reservations_result)['total'];

// Calculate percentage changes
$reservations_percentage_change = $last_month_reservations > 0 ? 
    round((($month_reservations - $last_month_reservations) / $last_month_reservations) * 100, 1) : 0;

$today_vs_yesterday_percentage = $yesterday_reservations > 0 ? 
    round((($today_reservations - $yesterday_reservations) / $yesterday_reservations) * 100, 1) : 0;

// Revenue calculations
// Monthly revenue
$month_revenue_query = "SELECT SUM(total_amount) as revenue FROM reservations 
                       WHERE MONTH(reservation_date) = '$current_month' 
                       AND YEAR(reservation_date) = '$current_year' 
                       AND status = 'confirmed'";
$month_revenue_result = mysqli_query($conn, $month_revenue_query);
$month_revenue = mysqli_fetch_assoc($month_revenue_result)['revenue'] ?? 0;

// Last month revenue
$last_month_revenue_query = "SELECT SUM(total_amount) as revenue FROM reservations 
                           WHERE MONTH(reservation_date) = '$last_month' 
                           AND YEAR(reservation_date) = '$current_year' 
                           AND status = 'confirmed'";
$last_month_revenue_result = mysqli_query($conn, $last_month_revenue_query);
$last_month_revenue = mysqli_fetch_assoc($last_month_revenue_result)['revenue'] ?? 0;

// Revenue percentage change
$revenue_percentage_change = $last_month_revenue > 0 ? 
    round((($month_revenue - $last_month_revenue) / $last_month_revenue) * 100, 1) : 0;

// New customers this week
$new_customers_query = "SELECT COUNT(*) as total FROM users 
                       WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
$new_customers_result = mysqli_query($conn, $new_customers_query);
$new_customers = mysqli_fetch_assoc($new_customers_result)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1a365d',
                        secondary: '#2c5282',
                        accent: '#ecc94b',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php include_once '../components/sidebar.php' ?>

        <!-- Main content -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Top navigation -->
            <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                <div class="flex items-center">
                    <button class="text-gray-500 focus:outline-none md:hidden">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="ml-4 text-xl font-semibold text-gray-800">Dashboard</h1>
                </div>
            </div>

            <!-- Content -->
            <div class="flex-1 overflow-auto p-4">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 gap-5 mt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Reservations -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Total Reservations</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900"><?= $total_reservations ?></div>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="<?= $reservations_percentage_change >= 0 ? 'text-green-600' : 'text-red-600' ?> text-sm font-semibold">
                                <?= $reservations_percentage_change >= 0 ? '+' : '' ?><?= $reservations_percentage_change ?>%
                            </span>
                            <span class="text-gray-500 text-sm ml-2">from last month</span>
                        </div>
                    </div>

                    <!-- Today's Reservations -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Today's Reservations</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900"><?= $today_reservations ?></div>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="<?= $today_vs_yesterday_percentage >= 0 ? 'text-green-600' : 'text-red-600' ?> text-sm font-semibold">
                                <?= $today_vs_yesterday_percentage >= 0 ? '+' : '' ?><?= $today_vs_yesterday_percentage ?>%
                            </span>
                            <span class="text-gray-500 text-sm ml-2">from yesterday</span>
                        </div>
                    </div>

                    <!-- Total Customers -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Total Customers</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900"><?= mysqli_num_rows($users) ?></div>
                            </div>
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-600 text-sm font-semibold">+<?= $new_customers ?></span>
                            <span class="text-gray-500 text-sm ml-2">new this week</span>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900">$<?= number_format($month_revenue, 2) ?></div>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="<?= $revenue_percentage_change >= 0 ? 'text-green-600' : 'text-red-600' ?> text-sm font-semibold">
                                <?= $revenue_percentage_change >= 0 ? '+' : '' ?><?= $revenue_percentage_change ?>%
                            </span>
                            <span class="text-gray-500 text-sm ml-2">from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Rest of your existing content remains the same -->
                <!-- Recent Reservations and Menu Items -->
                <div class="grid grid-cols-1 gap-5 mt-6 lg:grid-cols-2">
                    <!-- Recent Reservations -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Recent Reservations</h2>
                            <a href="../reservation/" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while($reservation = mysqli_fetch_assoc($reservations)) : ?> 
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $reservation['reservation_id'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $reservation['first_name'] . ' ' . $reservation['last_name']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $reservation['reservation_date'] . ', ' . $reservation['reservation_time'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php 
                                                    $statusColor = [
                                                        'confirmed' => 'green',
                                                        'pending' => 'yellow',
                                                        'cancelled' => 'red'
                                                    ][$reservation['status']] ?? 'gray';
                                                ?>
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                                                    <?= ucfirst($reservation['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Featured Menu Items -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Featured Menu Items</h2>
                            <a href="../menu" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
                        </div>
                        <div class="space-y-4">
                            <?php 
                            while($item = mysqli_fetch_assoc($menu)) :
                            ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden bg-gray-200">
                                        <img src=<?= '../../../assets/images/menu/' . $item['image_path'] ?> alt="" class="h-full w-full object-cover">
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo $item['name'] ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo $item['description'] ?></p>
                                        <div class="mt-1 flex items-center">
                                            <span class="text-sm font-medium text-gray-900">$<?php echo $item['price'] ?></span>
                                            <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800"><?php echo $item['is_featured'] ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>
                </div>

                <!-- Active Coupons and Recent Customers -->
                <div class="grid grid-cols-1 gap-5 mt-6 lg:grid-cols-2">
                    <!-- Active Coupons -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Active Coupons</h2>
                            <a href="../coupons/" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
                        </div>
                        <div class="space-y-4">
                            <?php while($item = mysqli_fetch_assoc($coupons)) : ?>
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                                    <div>
                                        <h3 class="text-sm font-medium text-gray-900"><?php echo $item['code'] ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo $item['description'] ?></p>
                                        <p class="mt-1 text-xs text-gray-500">Expires: <?php echo $item['expiration_date'] ?></p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-primary"><?php echo ($item['discount_value'] / 100) * 100 . '%' ?></span>
                                        <div class="mt-1 text-xs text-gray-500">
                                            <?php 
                                                if( $item['membership_required'] === null ) {
                                                    echo 'no membership';
                                                } else {
                                                    echo $item['membership_required'];
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>

                    <!-- Recent Customers -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Recent Customers</h2>
                            <a href="../customers/" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
                        </div>
                        <div class="space-y-4">
                            <?php while($customer = mysqli_fetch_assoc($users)) : ?>
                                <div class="flex items-center">
                                    <img class="w-10 h-10 rounded-full" src=<?php echo "https://ui-avatars.com/api/?name=" . $customer['first_name'] . ' ' . $customer['last_name'] . "&background=random "?> alt=<?php echo $customer['first_name'] . ' ' . $customer['last_name'] ?> >
                                    <div class="ml-4 flex justify-between w-full">
                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900"><?php echo $customer['first_name'] . ' ' . $customer['last_name'] ?></h3>
                                            <p class="text-sm text-gray-500"><?php echo $customer['email'] ?></p>
                                            <div class="mt-1 flex items-center">
                                                <span class="ml-2 text-xs text-gray-500">Joined: <?php echo $customer['created_at'] ?></span>
                                            </div>
                                        </div>
                                        <div class="h-full flex justify-center items-center">
                                            <?php $membershipColor = [
                                                'platinum' => 'zinc',
                                                'gold' => 'amber'
                                            ] ?>
                                            <?php if ($customer['membership_level'] !== null) : ?>
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800"><?php echo $customer['membership_level'] ?></span>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>