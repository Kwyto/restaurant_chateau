<?php
include '../includes/config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../../uploads/menu/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = 'uploads/menu/' . $file_name;
            }
        }
    }
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Menu name is required';
    }
    
    if (empty($price) || !is_numeric($price)) {
        $errors[] = 'Valid price is required';
    }
    
    if (empty($category)) {
        $errors[] = 'Category is required';
    }

    if (empty($errors)) {
        // Insert into database
        $query = "INSERT INTO menu_items (name, description, price, category, is_featured, image_path) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssdsis', $name, $description, $price, $category, $is_featured, $image_path);
        
        if (mysqli_stmt_execute($stmt)) {
            header('Location: index.php?success=Menu item added successfully');
            exit;
        } else {
            $errors[] = 'Failed to add menu item: ' . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Restaurant - Add Menu Item</title>
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
                    <h1 class="text-2xl font-bold text-gray-800">Add New Menu Item</h1>
                    <a href="index.php" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Menu
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
                
                <!-- Menu Form -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <form method="POST" enctype="multipart/form-data" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Menu Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" id="name" name="name" required 
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                            </div>
                            
                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" id="price" name="price" step="0.01" min="0" required
                                           class="pl-7 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
                                </div>
                            </div>
                            
                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select id="category" name="category" required
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="appetizer" <?= ($_POST['category'] ?? '') === 'appetizer' ? 'selected' : '' ?>>Appetizer</option>
                                    <option value="main" <?= ($_POST['category'] ?? '') === 'main' ? 'selected' : '' ?>>Main Course</option>
                                    <option value="dessert" <?= ($_POST['category'] ?? '') === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                                    <option value="beverage" <?= ($_POST['category'] ?? '') === 'beverage' ? 'selected' : '' ?>>Beverage</option>
                                </select>
                            </div>
                            
                            <!-- Is Featured -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_featured" name="is_featured"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                       <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
                                <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                                    Featured Item
                                </label>
                            </div>
                            
                            <!-- Image Upload -->
                            <div class="md:col-span-2">
                                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                                <div class="mt-1 flex items-center">
                                    <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                        <img id="image-preview" src="#" alt="Preview" class="h-full w-full object-cover hidden">
                                        <svg id="image-placeholder" class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                    <label for="image" class="ml-5 cursor-pointer">
                                        <span class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Change
                                        </span>
                                        <input id="image" name="image" type="file" class="sr-only" accept="image/*">
                                    </label>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">JPEG, PNG, or JPG (Max 2MB)</p>
                            </div>
                            
                            <!-- Description -->
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="3"
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="mt-8 flex justify-end gap-3">
                            <button type="reset" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-400">
                                <i class="fas fa-undo mr-2"></i>Reset
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                <i class="fas fa-save mr-2"></i>Save Menu Item
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('image').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(file);
            } else {
                preview.src = '#';
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>