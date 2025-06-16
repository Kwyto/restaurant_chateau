<?php
include '../../../includes/config.php';

// Handle search and pagination
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

// Get membership data
$memberships = getMemberships($conn, $search, $page, $perPage);
$totalItems = countTotalMemberships($conn, $search);
$totalPages = ceil($totalItems / $perPage);

// Helper functions
function getMemberships($conn, $search = '', $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    
    $query = "SELECT * FROM memberships WHERE 
              level LIKE ? OR 
              description LIKE ?
              ORDER BY level ASC
              LIMIT ?, ?";
    
    $searchTerm = "%$search%";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssii', $searchTerm, $searchTerm, $offset, $perPage);
    
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt);
}

function countTotalMemberships($conn, $search = '') {
    $query = "SELECT COUNT(*) AS total FROM memberships WHERE 
              level LIKE ? OR 
              description LIKE ?";
    
    $searchTerm = "%$search%";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $searchTerm, $searchTerm);
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return (int)$row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Restaurant - Membership Management</title>
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
            <div class="flex-1 overflow-auto p-6">
                <!-- Header and Add Button -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Membership Management</h1>
                        <p class="text-sm text-gray-600">Manage membership levels and benefits</p>
                    </div>
                    <a href="add_membership.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors flex items-center justify-center w-full md:w-auto">
                        <i class="fas fa-plus-circle mr-2"></i>Add Membership Level
                    </a>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                        <h3 class="text-sm font-medium text-gray-500">Total Membership Levels</h3>
                        <p class="text-2xl font-semibold text-gray-800"><?= $totalItems ?></p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                        <h3 class="text-sm font-medium text-gray-500">Highest Discount</h3>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                                $highest = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MAX(discount_percent) AS max FROM memberships"));
                                echo $highest['max'] ? $highest['max'].'%' : 'N/A';
                            ?>
                        </p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow border-l-4 border-purple-500">
                        <h3 class="text-sm font-medium text-gray-500">Average Discount</h3>
                        <p class="text-2xl font-semibold text-gray-800">
                            <?php 
                                $avg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(discount_percent) AS avg FROM memberships"));
                                echo $avg['avg'] ? round($avg['avg'], 1).'%' : 'N/A';
                            ?>
                        </p>
                    </div>
                </div>

                <!-- Filter and Search -->
                <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                    <form method="GET" class="flex flex-col sm:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Memberships</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="search" name="search" placeholder="Search by level or description..." 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       class="pl-10 w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md h-[42px]">
                                <i class="fas fa-filter mr-2"></i>Search
                            </button>
                            <a href="?" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md h-[42px] flex items-center">
                                <i class="fas fa-sync-alt mr-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Memberships Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (mysqli_num_rows($memberships) > 0): ?>
                                    <?php while ($membership = mysqli_fetch_assoc($memberships)): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $membership['id'] ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <?= htmlspecialchars($membership['level']) ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="font-bold <?= $membership['discount_percent'] > 15 ? 'text-green-600' : 'text-gray-600' ?>">
                                                    <?= $membership['discount_percent'] ?>%
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs"><?= htmlspecialchars($membership['description']) ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="edit_membership.php?id=<?= $membership['id'] ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?= $membership['id'] ?>)" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No membership levels found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium"><?= ($page - 1) * $perPage + 1 ?></span> to 
                                    <span class="font-medium"><?= min($page * $perPage, $totalItems) ?></span> of 
                                    <span class="font-medium"><?= $totalItems ?></span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $page - 1)])) ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                           class="<?= $i == $page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            <?= $i ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($totalPages, $page + 1)])) ?>" 
                                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this membership level?\nThis action cannot be undone.')) {
                window.location.href = 'delete_membership.php?id=' + id;
            }
        }
    </script>
</body>
</html>