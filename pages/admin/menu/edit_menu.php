<?php
session_start();
include '../includes/config.php';

// Validasi dan sanitasi ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['default' => 0, 'min_range' => 0]]);

// Fetch menu item data
$menuItem = [];
if ($id > 0) {
    $query = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menuItem = mysqli_fetch_assoc($result) ?: [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    $category = in_array($_POST['category'] ?? '', ['appetizer', 'main', 'dessert', 'beverage']) ? $_POST['category'] : '';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Validasi dasar
    $errors = [];
    if (empty($name)) $errors[] = 'Name is required';
    if ($price === false) $errors[] = 'Valid price is required';
    if (empty($category)) $errors[] = 'Category is required';
    
    // Handle image upload jika tidak ada error
    $imagePath = $menuItem['image_path'] ?? '';
    if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../assets/images/menu/';
        
        // Pastikan direktori ada
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validasi file gambar
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['image']['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Only JPG, PNG, GIF, or WebP images are allowed';
        } elseif ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Image size must be less than 2MB';
        } else {
            // Generate nama file yang unik dan aman
            $safeName = preg_replace('/[^a-zA-Z0-9-_]/', '-', strtolower($name));
            $safeName = str_replace(' ', '-', $safeName); // Ganti spasi dengan dash
            $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = $safeName . '.' . strtolower($fileExt); // Format: nama-menu-uniqueid.ext
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                // Hapus gambar lama jika ada
                if (!empty($menuItem['image_path']) && file_exists($uploadDir . $menuItem['image_path'])) {
                    @unlink($uploadDir . $menuItem['image_path']);
                }
                $imagePath = 'items/' . $fileName;
            } else {
                $errors[] = 'Failed to upload image';
            }
        }
        finfo_close($fileInfo);
    }
    
    // Jika tidak ada error, proses ke database
    if (empty($errors)) {
        if ($id > 0) {
            // Update existing menu item
            $query = "UPDATE menu_items SET 
                name = ?, description = ?, price = ?, 
                category = ?, is_featured = ?, image_path = ?
                WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssdsssi', 
                $name, $description, $price, 
                $category, $isFeatured, $imagePath, $id);
        } else {
            // Create new menu item
            $query = "INSERT INTO menu_items 
                     (name, description, price, category, is_featured, image_path)
                     VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ssdsss', 
                $name, $description, $price, 
                $category, $isFeatured, $imagePath);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success_message'] = $id > 0 ? 'Menu item updated successfully' : 'Menu item created successfully';
            header('Location: ../menu');
            exit;
        } else {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id > 0 ? 'Edit' : 'Add' ?> Menu Item</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .image-preview-container {
            transition: all 0.3s ease;
        }
        .image-preview {
            max-height: 200px;
            object-fit: contain;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <?php include '../components/sidebar.php' ?>
        <div class="flex flex-col flex-1 overflow-hidden">
            <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-800">
                    <?= $id > 0 ? 'Edit Menu Item' : 'Add New Menu Item' ?>
                </h1>
            </div>
            
            <div class="flex-1 overflow-auto p-4">
                <?php if (!empty($errors)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        <h3 class="font-bold">Error!</h3>
                        <ul class="list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($menuItem['name'] ?? '') ?>" 
                                       required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($menuItem['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" name="price" step="0.01" min="0" 
                                           value="<?= htmlspecialchars($menuItem['price'] ?? '') ?>" 
                                           required class="pl-7 w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select name="category" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select Category</option>
                                    <option value="appetizer" <?= ($menuItem['category'] ?? '') === 'appetizer' ? 'selected' : '' ?>>Appetizer</option>
                                    <option value="main" <?= ($menuItem['category'] ?? '') === 'main' ? 'selected' : '' ?>>Main Course</option>
                                    <option value="dessert" <?= ($menuItem['category'] ?? '') === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                                    <option value="beverage" <?= ($menuItem['category'] ?? '') === 'beverage' ? 'selected' : '' ?>>Beverage</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Featured Item</label>
                                <div class="mt-1">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_featured" value="1" 
                                               <?= ($menuItem['is_featured'] ?? 0) ? 'checked' : '' ?>
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2">Mark as featured item</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                                <div class="image-preview-container">
                                    <?php if (!empty($menuItem['image_path'])): ?>
                                        <div class="mb-2">
                                            <img src="<?= '../../../assets/images/menu/' . htmlspecialchars($menuItem['image_path']) ?>" 
                                                 alt="Current menu item" 
                                                 class="image-preview rounded-md border border-gray-200">
                                            <p class="text-xs text-gray-500 mt-1">Current image</p>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="image" accept="image/*" class="w-full border border-gray-300 rounded-md p-2" id="imageUpload">
                                    <p class="text-xs text-gray-500 mt-1">Max 2MB (JPEG, PNG, GIF, WebP)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="../menu/" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                            Cancel
                        </a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Image preview functionality
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const previewContainer = document.querySelector('.image-preview-container');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    let preview = previewContainer.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview rounded-md border border-gray-200 mb-2';
                        previewContainer.insertBefore(preview, previewContainer.firstChild);
                    }
                    preview.src = e.target.result;
                    
                    // Add current image label if not exists
                    if (!previewContainer.querySelector('.text-xs.text-gray-500')) {
                        const label = document.createElement('p');
                        label.className = 'text-xs text-gray-500 mt-1';
                        label.textContent = 'New selected image';
                        preview.parentNode.insertBefore(label, preview.nextSibling);
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>