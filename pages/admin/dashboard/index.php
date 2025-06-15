<?php 

include '../includes/config.php';
$users = getCustomers($conn);
$menu = getMenuItems($conn, limit:5);
$reservations = getReservations($conn, limit:6);
$coupons = getCoupon($conn, limit:3);
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
        <?php include '../components/sidebar.php' ?>

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
                <div class="flex items-center">
                    <div class="relative">
                        <button class="p-1 text-gray-500 rounded-full hover:text-gray-600 focus:outline-none">
                            <i class="fas fa-bell"></i>
                        </button>
                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                    </div>
                    <div class="ml-4">
                        <div class="flex items-center">
                            <img class="w-8 h-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin profile">
                            <span class="ml-2 text-sm font-medium text-gray-700">Admin</span>
                        </div>
                    </div>
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
                                <div class="mt-1 text-3xl font-semibold text-gray-900">31</div>
                            </div>
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-calendar-alt text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-600 text-sm font-semibold">+12%</span>
                            <span class="text-gray-500 text-sm ml-2">from last month</span>
                        </div>
                    </div>

                    <!-- Today's Reservations -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Today's Reservations</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900">8</div>
                            </div>
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-clock text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-600 text-sm font-semibold">+3%</span>
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
                            <span class="text-green-600 text-sm font-semibold">+1</span>
                            <span class="text-gray-500 text-sm ml-2">new this week</span>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-500 truncate">Monthly Revenue</div>
                                <div class="mt-1 text-3xl font-semibold text-gray-900">$12,345</div>
                            </div>
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-dollar-sign text-xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <span class="text-green-600 text-sm font-semibold">+8.2%</span>
                            <span class="text-gray-500 text-sm ml-2">from last month</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Reservations and Menu Items -->
                <div class="grid grid-cols-1 gap-5 mt-6 lg:grid-cols-2">
                    <!-- Recent Reservations -->
                    <div class="p-5 bg-white rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Recent Reservations</h2>
                            <a href="#" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $reservation['reservation_number'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $reservation['first_name'] . ' ' . $reservation['last_name']; ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $reservation['reservation_date'] . ', ' . $reservation['reservation_time'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"><?php echo $reservation['status'] ?></span>
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
                            <a href="#" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
                        </div>
                        <div class="space-y-4">
                            <?php 
                            while($item = mysqli_fetch_assoc($menu)) :
                            ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden bg-gray-200">
                                        <img src=<?php echo $item['image_path'] ?> alt="Black Truffle Pasta" class="h-full w-full object-cover">
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
                            <a href="#" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
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
                            <a href="#" class="text-sm font-medium text-primary hover:text-secondary">View All</a>
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