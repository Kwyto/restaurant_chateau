<?php
include '../includes/config.php';
// Fungsi untuk menghitung total reservasi
function countReservations($conn, $status = '', $search = '', $date = '') {
    $query = "SELECT COUNT(*) as total FROM reservations WHERE 1=1";
    $params = array();
    
    if (!empty($status)) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $query .= " AND (reservation_number LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, array_fill(0, 4, $searchTerm));
    }
    
    if (!empty($date)) {
        $query .= " AND reservation_date = ?";
        $params[] = $date;
    }
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['total'];
}

// Ambil parameter dari URL
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';
$date = $_GET['date'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;

// Dapatkan data reservasi
$reservations = getReservations($conn, $status, $search, $date, $page, $perPage);
$totalReservations = countReservations($conn, $status, $search, $date);
$totalPages = ceil($totalReservations / $perPage);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations Dashboard</title>
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
                    <h1 class="ml-4 text-xl font-semibold text-gray-800">Reservations Management</h1>
                </div>
            </div>
            <div class="flex-1 overflow-auto p-4">                
                <!-- Filter Section -->
                <div class="bg-white p-4 rounded-lg shadow mb-6">
                    <form id="filter-form" method="GET" class="flex flex-wrap items-center gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                                <option value="">All Status</option>
                                <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                            <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>" class="border border-gray-300 rounded-md px-3 py-2">
                        </div>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md mt-6 hover:bg-blue-700">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <button type="button" id="reset-filters" class="bg-gray-500 text-white px-4 py-2 rounded-md mt-6 hover:bg-gray-600">
                            <i class="fas fa-sync-alt mr-2"></i>Reset
                        </button>
                    </form>
                </div>
                
                <!-- Reservations Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reservation ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Special Occasion</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Service</th>

                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while($reservation = mysqli_fetch_assoc($reservations)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= $reservation['reservation_id'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <?= strtoupper(substr($reservation['first_name'], 0, 1)) . strtoupper(substr($reservation['last_name'], 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']) ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?= htmlspecialchars($reservation['email']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?= date('M j, Y', strtotime($reservation['reservation_date'])) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= date('H:i', strtotime($reservation['reservation_time'])) ?>
                                        </div>
                                    </td>
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
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= $reservation['guests'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?= $reservation['special_occasion'] ?>
                                    </td>
                                    <?php 
                                        $statusColor = [
                                            '1' => 'green',
                                            '0' => 'red',
                                        ][$reservation['pickup_service']] ?? 'gray';
                                        ?>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-<?= $statusColor ?>-100 text-<?= $statusColor ?>-800">
                                            <?= $reservation['pickup_service'] == 1 ? 'yes' : 'no' ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewReservation(<?= $reservation['id'] ?>)" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="deleteReservation(<?= $reservation['id'] ?>)" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                     <?php if ($totalPages > 1 ) : ?>
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
                                    Showing <span class="font-medium"><?= ($page - 1) * $perPage + 1 ?></span> to <span class="font-medium"><?= min($page * $perPage, $totalReservations) ?></span> of <span class="font-medium"><?= $totalReservations ?></span> results
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
                    <?php endif ?>
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
        // Variabel global untuk menyimpan ID reservasi yang akan dihapus
        let reservationToDelete = null;
        
        // Fungsi untuk menampilkan modal
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }
        
        // Fungsi untuk menutup modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }
        
        // Fungsi untuk melihat detail reservasi
        function viewReservation(id) {
            fetch(`get_reservation.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    const resData = data.data
                    const details = document.getElementById('reservation-details');
                    details.innerHTML = `
                        <div>
                            <p class="text-sm font-medium text-gray-500">Reservation ID</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.id}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Customer Name</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.first_name} ${resData.last_name}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.email}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.phone}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Date & Time</p>
                            <p class="mt-1 text-sm text-gray-900">${new Date(resData.reservation_date).toLocaleDateString()} at ${resData.reservation_time}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Table Number</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.table_number}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Number of Guests</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.guests}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Occasion</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.special_occasion}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Special Requests</p>
                            <p class="mt-1 text-sm text-gray-900">${resData.special_request || 'None'}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    ${resData.status === 'confirmed' ? 'bg-green-100 text-green-800' : 
                                        resData.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        'bg-red-100 text-red-800'}">
                                    ${resData.status.charAt(0).toUpperCase() + resData.status.slice(1)}
                                </span>
                            </p>
                        </div>
                    `;
                    openModal('view-modal');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load reservation details');
                });
        }
        
        function editReservation(id) {
            window.location.href = `edit_reservation.php?id=${id}`;
        }
        
        // Fungsi untuk mempersiapkan penghapusan reservasi
        function deleteReservation(id) {
            reservationToDelete = id;
            openModal('delete-modal');
        }
        
        // Fungsi untuk mengkonfirmasi penghapusan
        function confirmDelete() {
            if (!reservationToDelete) return;
            
            fetch(`delete_reservation.php?id=${reservationToDelete}`, {
                method: 'DELETE'
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    throw new Error('Failed to delete reservation');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to delete reservation');
                closeModal('delete-modal');
            });
        }
        
        // Fungsi untuk mereset filter
        document.getElementById('reset-filters').addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
    </script>
</body>
</html>

<?php
// Tutup koneksi database
mysqli_close($conn);
?>