<?php
include '../includes/config.php';
// Get menu item ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch menu item data
$menuItem = null;
if ($id > 0) {
    $query = "SELECT * FROM menu_items WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $menuItem = mysqli_fetch_assoc($result);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Handle image upload
    $imagePath = $menuItem['image_path'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../uploads/menu/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExt = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExt;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/menu/' . $fileName;
            // Delete old image if exists
            if (!empty($menuItem['image_path'])) {
                @unlink('../../../' . $menuItem['image_path']);
            }
        }
    }
    
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
        $error = 'Failed to save menu item: ' . mysqli_error($conn);
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
            <div class="flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200">
                <h1 class="text-xl font-semibold text-gray-800">
                    <?= $id > 0 ? 'Edit Menu Item' : 'Add New Menu Item' ?>
                </h1>
            </div>
            
            <div class="flex-1 overflow-auto p-4">
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                                <input type="text" name="name" value="<?= htmlspecialchars($menuItem['name'] ?? '') ?>" 
                                       required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2"><?= htmlspecialchars($menuItem['description'] ?? '') ?></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                                <input type="number" name="price" step="0.01" min="0" 
                                       value="<?= htmlspecialchars($menuItem['price'] ?? '') ?>" 
                                       required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                                <select name="category" required class="w-full border border-gray-300 rounded-md px-3 py-2">
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
                                <?php if (!empty($menuItem['image_path'])): ?>
                                    <div class="mb-2">
                                        <img src="<?= htmlspecialchars($menuItem['image_path']) ?>" alt="Current image" class="h-24 w-24 object-cover rounded-md">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="image" accept="image/*" class="w-full">
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
</body>
</html>