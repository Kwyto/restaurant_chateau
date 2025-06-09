<?php 
include '../../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Fetch reservations with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$count_query = "SELECT COUNT(*) as total FROM reservations WHERE user_id = ?";
$count_stmt = mysqli_prepare($conn, $count_query);
mysqli_stmt_bind_param($count_stmt, "i", $user_id);
mysqli_stmt_execute($count_stmt);
$total_reservations = mysqli_fetch_assoc(mysqli_stmt_get_result($count_stmt))['total'];

$total_pages = ceil($total_reservations / $limit);

// Fetch reservations
$query = "SELECT * FROM reservations 
          WHERE user_id = ? 
          ORDER BY reservation_date DESC, reservation_time DESC 
          LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "iii", $user_id, $limit, $offset);
mysqli_stmt_execute($stmt);
$reservations = mysqli_stmt_get_result($stmt);

include '../../includes/header.php';
?>

<main class="min-h-screen bg-black text-white">
    <div class="max-w-7xl mx-auto py-12 px-4">
        <!-- Profile Header -->
        <?php include 'komponen/profile.php'; ?>
        
        <!-- Profile Navigation -->
        <?php include 'komponen/navigasi.php'; ?>
        
        <!-- History Content -->
        <div class="border border-gray-800 p-8">
            <h2 class="text-2xl font-serif font-bold mb-6">Reservation History</h2>
            
            <?php if (mysqli_num_rows($reservations) > 0): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-800">
                    <thead class="bg-gray-900">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Time</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Guests</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Table</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Special Request</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-black divide-y divide-gray-800">
                        <?php while ($reservation = mysqli_fetch_assoc($reservations)): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($reservation['guests']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php echo htmlspecialchars($reservation['table_number']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $status_colors = [
                                    'pending' => 'bg-yellow-900 text-yellow-300',
                                    'confirmed' => 'bg-blue-900 text-blue-300',
                                    'completed' => 'bg-green-900 text-green-300',
                                    'cancelled' => 'bg-red-900 text-red-300'
                                ];
                                $status_color = $status_colors[$reservation['status']] ?? 'bg-gray-900 text-gray-300';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $status_color; ?>">
                                    <?php echo ucfirst(htmlspecialchars($reservation['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <?php echo !empty($reservation['special_request']) ? 
                                    htmlspecialchars(substr($reservation['special_request'], 0, 30)) . '...' : 
                                    '<span class="text-gray-500">-</span>'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="view_reservation.php?id=<?php echo $reservation['id']; ?>" 
                                   class="text-gold hover:text-gold-dark">View</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="mt-8 flex justify-between items-center">
                <div class="text-sm text-gray-400">
                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_reservations); ?> 
                    of <?php echo $total_reservations; ?> reservations
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" 
                       class="px-3 py-1 border border-gray-800 rounded text-gray-400 hover:border-gold hover:text-gold">
                        Previous
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" 
                       class="px-3 py-1 border <?php echo $i === $page ? 'border-gold bg-gold text-black' : 'border-gray-800 text-gray-400 hover:border-gold hover:text-gold'; ?> rounded">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" 
                       class="px-3 py-1 border border-gray-800 rounded text-gray-400 hover:border-gold hover:text-gold">
                        Next
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-8 text-gray-400">
                <p>Belum ada pesanan</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>