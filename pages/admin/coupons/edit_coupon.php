<?php
include '../includes/config.php';
// Check if coupon ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?error=Invalid coupon ID');
    exit;
}

$coupon_id = intval($_GET['id']);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $description = $_POST['description'] ?? '';
    $discount_value = $_POST['discount_value'] ?? '';
    $expiration_date = $_POST['expiration_date'] ?? '';
    $membership_required = $_POST['membership_required'] !== 'none' ? $_POST['membership_required'] : null;

    // Validate form data
    $errors = [];
    
    if (empty($code)) {
        $errors[] = 'Coupon code is required';
    }
    
    if (empty($discount_value) || !is_numeric($discount_value)) {
        $errors[] = 'Valid discount value is required';
    }
    
    if (empty($expiration_date)) {
        $errors[] = 'Expiration date is required';
    }

    if (empty($errors)) {
        // Update coupon in database
        $query = "UPDATE coupons SET 
                  code = ?, 
                  description = ?, 
                  discount_value = ?, 
                  expiration_date = ?, 
                  membership_required = ?
                  WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssdssi', $code, $description, $discount_value, $expiration_date, $membership_required, $coupon_id);

        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?success=Coupon updated successfully');
            exit;
        } else {
            $errors[] = 'Failed to update coupon: ' . mysqli_error($conn);
        }
    }
}

// Get current coupon data
$query = "SELECT * FROM coupons WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $coupon_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$coupon = mysqli_fetch_assoc($result);

if (!$coupon) {
    header('Location: index.php?error=Coupon not found');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Restaurant - Edit Coupon</title>
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
        <?php include '../components/sidebar.php' ?>
        <div class="flex flex-col flex-1 overflow-hidden">
            <div class="flex-1 overflow-auto p-4">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Edit Coupon: <?= htmlspecialchars($coupon['code']) ?></h1>
                    <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Coupons
                    </a>
                </div>
                
                <!-- Success/Error Messages -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <!-- Coupon Form -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <form method="POST" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Coupon Code -->
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Coupon Code *</label>
                                <input type="text" id="code" name="code" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?= htmlspecialchars($coupon['code']) ?>">
                            </div>
                            
                            <!-- Discount Value -->
                            <div>
                                <label for="discount_value" class="block text-sm font-medium text-gray-700 mb-1">Discount Value *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" id="discount_value" name="discount_value" step="0.01" min="0" required
                                           class="pl-7 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           value="<?= htmlspecialchars($coupon['discount_value']) ?>">
                                </div>
                            </div>
                            
                            <!-- Expiration Date -->
                            <div>
                                <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date *</label>
                                <input type="date" id="expiration_date" name="expiration_date" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?= htmlspecialchars(date('Y-m-d', strtotime($coupon['expiration_date']))) ?>"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <!-- Membership Required -->
                            <div>
                                <label for="membership_required" class="block text-sm font-medium text-gray-700 mb-1">Membership Required</label>
                                <select id="membership_required" name="membership_required"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value='none' <?= $coupon['membership_required'] === 'none' ? 'selected' : '' ?>>No Membership</option>
                                    <option value="gold" <?= $coupon['membership_required'] === 'gold' ? 'selected' : '' ?>>Gold</option>
                                    <option value="platinum" <?= $coupon['membership_required'] === 'platinum' ? 'selected' : '' ?>>Platinum</option>
                                </select>
                            </div>

                            <div class="flex items-center">
                                <label for="status" class="ml-2 mr-3 block text-sm text-gray-700">
                                    Active Coupon
                                </label>
                                <label for="toggleExample" class="block items-center cursor-pointer">
                                    <div class="relative">
                                        <input id="toggleExample" type="checkbox" <?= isset($_POST['status']) ? 'checked' : 'checked' ?> class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-300 rounded-full peer-checked:bg-blue-600 transition duration-300"></div>
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow-md transform peer-checked:translate-x-full transition duration-300"></div>
                                    </div>
                                </label>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($coupon['description']) ?></textarea>
                            </div>
                            
                            <!-- Created/Updated Info -->
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="mt-8 flex justify-end gap-3">
                            <a href="index.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Update Coupon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set minimum date for expiration date (today)
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('expiration_date').min = new Date().toISOString().split('T')[0];
        });
    </script>
</body>
</html>