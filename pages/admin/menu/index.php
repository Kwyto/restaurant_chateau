<?php

include '../includes/config.php';
// include 'edit_menu.php';

$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

// Get menu items with pagination
$menu = getMenuItems($conn, $category, $search, $page, $perPage);
$totalItems = countMenuItems($conn, $category, $search);
$totalPages = ceil($totalItems / $perPage);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Restaurant - Admin Dashboard</title>
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
                    <div class="flex items-center">
                        <button class="text-gray-500 focus:outline-none md:hidden">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="ml-4 text-xl font-semibold text-gray-800">Menu Management</h1>
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
                <div class="flex-1 overflow-auto p-4">                
                    <div class="bg-white p-4 rounded-lg shadow mb-6">
                        <form id="filter-form" method="GET" class="flex justify-between">
                            <div class="flex flex-wrap items-center gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                                    <select name="category" class="border border-gray-300 rounded-md px-3 py-2">
                                        <option value="">All Categories</option>
                                        <option value="appetizer" <?= $category === 'appetizer' ? 'selected' : '' ?>>Appetizer</option>
                                        <option value="main" <?= $category === 'main' ? 'selected' : '' ?>>Main Course</option>
                                        <option value="dessert" <?= $category === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                                        <option value="beverage" <?= $category === 'beverage' ? 'selected' : '' ?>>Beverage</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                    <input type="text" name="search" placeholder="Search menu..." value="<?= htmlspecialchars($search) ?>" class="border border-gray-300 rounded-md px-3 py-2">
                                </div>
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md mt-6 hover:bg-blue-700">
                                    <i class="fas fa-filter mr-2"></i>Filter
                                </button>
                                <button type="button" id="reset-filters" class="bg-gray-500 text-white px-4 py-2 rounded-md mt-6 hover:bg-gray-600">
                                    <i class="fas fa-sync-alt mr-2"></i>Reset
                                </button>
                            </div>
                            <a href="add_menu.php" class="bg-green-600 text-white mt-6 px-4 py-2 rounded-md hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i>Add Menu
                            </a>
                        </form>
                    </div>
                    
                    <!-- Menu Items Table -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Is Featured</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php while($item = mysqli_fetch_assoc($menu)) : ?> 
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <?php if ($item['image_path']): ?>
                                                    <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="h-10 w-10 rounded-full object-cover">
                                                <?php else: ?>
                                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-utensils text-gray-500"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?= htmlspecialchars($item['description']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                $<?= number_format($item['price'], 2) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?= ucfirst(htmlspecialchars($item['category'])) ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <?php if ($item['is_featured']): ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Yes
                                                    </span>
                                                <?php else: ?>
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="viewMenuItem(<?= $item['id'] ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button onclick="editMenuItem(<?= $item['id'] ?>)" class="text-green-600 hover:text-green-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteMenuItem(<?= $item['id'] ?>)" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)])) ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium"><?= ($page - 1) * $perPage + 1 ?></span> to <span class="font-medium"><?= min($page * $perPage, $totalItems) ?></span> of <span class="font-medium"><?= $totalItems ?></span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i == $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                <?= $i ?>
                                            </a>
                                        <?php endfor; ?>
                                        
                                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)])) ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Modal -->
        <div id="view-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Reservation Details</h3>
                        <div id="reservation-details" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Details will be loaded here via AJAX -->
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="closeModal('view-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Reservation</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to delete this reservation? This action cannot be undone.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="confirmDelete()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                        <button type="button" onclick="closeModal('delete-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Reset filters
            document.getElementById('reset-filters').addEventListener('click', function() {
                window.location.href = window.location.pathname;
            });

            let menuToDelete = null;

            function openModal(modalId) {
                document.getElementById(modalId).classList.remove('hidden');
            }  
            
            // Fungsi untuk menutup modal
            function closeModal(modalId) {
                document.getElementById(modalId).classList.add('hidden');
            }

            // Menu item functions
            function viewMenuItem(id) {
                fetch(`get_menu.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const resData = data.data
                    console.log(resData)
                    const details = document.getElementById('reservation-details');
                    details.innerHTML = `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Item Name</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.name}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Item Description</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.description}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Item Price</p>
                            <p class="mt-1 text-sm text-gray-900">$${resData.price}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Item Category</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.category}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Is Featured</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.category = 1 ? 'true' : 'false'}</p>
                        </div>
                        
                    `;
                    openModal('view-modal');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }

            function editMenuItem(id) {
                window.location.href = `edit_menu.php?id=${id}`;
            }

            function deleteMenuItem(id) {
                menuToDelete = id;
                openModal('delete-modal');
            }

            function confirmDelete() {
                if (!menuToDelete) return;
                
                fetch(`delete_menu.php?id=${menuToDelete}`, {
                    method: 'DELETE'
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        throw new Error('Failed to delete menu');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete menu');
                    closeModal('delete-modal');
                });
            }
        </script>
    </body>
</html>