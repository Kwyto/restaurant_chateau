<?php
include '../../../includes/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    $description = $_POST['description'] ?? '';
    $discount_value = $_POST['discount_value'] ?? '';
    $expiration_date = $_POST['expiration_date'] ?? '';
    $membership_required = isset($_POST['membership_required']) ? 1 : 0;

    // Validate and process the form
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
        // Insert into database
        $query = "INSERT INTO coupons (code, description, discount_value, expiration_date, membership_required) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssdsi', $code, $description, $discount_value, $expiration_date, $membership_required);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?success=Coupon added successfully');
            exit;
        } else {
            $errors[] = 'Failed to add coupon: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Restaurant - Add New Coupon</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Add New Coupon</h1>
                    <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Coupons
                    </a>
                </div>
                
                <!-- Success Message -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>
                
                <!-- Error Messages -->
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
                                       value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                                <p class="mt-1 text-sm text-gray-500">Unique code for the coupon (e.g., SUMMER20)</p>
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
                                           value="<?= htmlspecialchars($_POST['discount_value'] ?? '') ?>">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Amount or percentage discount</p>
                            </div>
                            
                            <!-- Expiration Date -->
                            <div>
                                <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-1">Expiration Date *</label>
                                <input type="date" id="expiration_date" name="expiration_date" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?= htmlspecialchars($_POST['expiration_date'] ?? '') ?>"
                                       min="<?= date('Y-m-d') ?>">
                            </div>
                            
                            <!-- Membership Required -->
                            <div class="">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Membership *</label>
                                <select name="Membership" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">Select Membership</option>
                                    <option value="no membership" === 'no membership' ? 'selected' : '' ?>No Membership</option>
                                    <option value="Platinum" === 'Platinum' ? 'selected' : '' ?>Platinum</option>
                                    <option value="Gold" === 'Gold' ? 'selected' : '' ?>Gold</option>
                                </select>
                            </div>
                            
                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" id="status" name="status"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       <?= isset($_POST['status']) ? 'checked' : 'checked' ?>>
                                <label for="status" class="ml-2 block text-sm text-gray-700">
                                    Active Coupon
                                </label>
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                                <p class="mt-1 text-sm text-gray-500">Optional description about the coupon</p>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="reset" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Save Coupon
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
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('expiration_date').min = today;
            
            // Generate a random coupon code if empty
            const codeField = document.getElementById('code');
            if (!codeField.value) {
                const randomCode = 'CODE' + Math.floor(1000 + Math.random() * 9000);
                codeField.value = randomCode;
            }
        });
    </script>
</body>
</html>