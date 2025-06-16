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

// Query untuk mengambil data reservasi
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
        <?php include 'komponen/profile.php'; ?>
        
        <?php include 'komponen/navigasi.php'; ?>
        
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Special occasion </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-black divide-y divide-gray-800">
                        <?php while ($reservation = mysqli_fetch_assoc($reservations)): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('F d, Y', strtotime($reservation['reservation_date'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($reservation['guests']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($reservation['table_number']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php 
                                $status_colors = ['pending' => 'bg-yellow-900 text-yellow-300', 'confirmed' => 'bg-blue-900 text-blue-300', 'completed' => 'bg-green-900 text-green-300', 'cancelled' => 'bg-red-900 text-red-300'];
                                $status_color = $status_colors[$reservation['status']] ?? 'bg-gray-900 text-gray-300';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?php echo $status_color; ?>"><?php echo ucfirst(htmlspecialchars($reservation['status'])); ?></span>
                            </td>
                            <td class="px-6 py-4"><?php echo !empty($reservation['special_occasion']) ? htmlspecialchars($reservation['special_occasion']) : '<span class="text-gray-500">-</span>'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="#" 
                                   class="text-gold hover:text-gold-dark view-reservation"
                                   data-id="<?php echo $reservation['id']; ?>"
                                   data-guests="<?php echo htmlspecialchars($reservation['guests']); ?>"
                                   data-date="<?php echo date('F j, Y', strtotime($reservation['reservation_date'])); ?>"
                                   data-time="<?php echo date('g:i A', strtotime($reservation['reservation_time'])); ?>"
                                   data-occasion="<?php echo htmlspecialchars(!empty($reservation['special_occasion']) ? $reservation['special_occasion'] : 'None'); ?>"
                                   data-table="<?php echo htmlspecialchars($reservation['table_number']); ?>"
                                   
                                   data-table-fee="<?php echo htmlspecialchars($reservation['table_fee'] ?? 50.00); ?>"
                                   data-food-cost="<?php echo htmlspecialchars($reservation['food_cost'] ?? 0.00); ?>"
                                   data-coupon-discount="<?php echo htmlspecialchars($reservation['coupon_discount'] ?? 0.00); ?>"
                                   data-tax-amount="<?php echo htmlspecialchars($reservation['tax_amount'] ?? 0.00); ?>"
                                   data-total-amount="<?php echo htmlspecialchars($reservation['total_amount'] ?? 0.00); ?>"
                                   >View</a>
                                </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($total_pages > 1): ?>
            <div class="mt-8 flex justify-between items-center">
                <div class="text-sm text-gray-400">Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $total_reservations); ?> of <?php echo $total_reservations; ?> reservations</div>
                <div class="flex space-x-2">
                    <?php if ($page > 1): ?><a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1 border border-gray-800 rounded text-gray-400 hover:border-gold hover:text-gold">Previous</a><?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?><a href="?page=<?php echo $i; ?>" class="px-3 py-1 border <?php echo $i === $page ? 'border-gold bg-gold text-black' : 'border-gray-800 text-gray-400 hover:border-gold hover:text-gold'; ?> rounded"><?php echo $i; ?></a><?php endfor; ?>
                    <?php if ($page < $total_pages): ?><a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1 border border-gray-800 rounded text-gray-400 hover:border-gold hover:text-gold">Next</a><?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="text-center py-8 text-gray-400"><p>Belum ada pesanan</p></div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div id="reservationModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center hidden z-50">
    <div class="bg-black border border-gray-800 rounded-lg shadow-xl p-8 max-w-lg w-full text-white">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-serif font-bold">Order Summary</h2>
            <button id="closeModal" class="text-gray-400 hover:text-white">&times;</button>
        </div>
        <div id="modal-body">
            </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('reservationModal');
    const closeModal = document.getElementById('closeModal');
    const modalBody = document.getElementById('modal-body');

    document.querySelectorAll('.view-reservation').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            // Mengambil data dari atribut data-*
            const reservationId = this.dataset.id;
            const guests = this.dataset.guests;
            const date = this.dataset.date;
            const time = this.dataset.time;
            const occasion = this.dataset.occasion;
            const table = this.dataset.table;
            const totalAmount = parseFloat(this.dataset.totalAmount);
            
            // Konten modal disederhanakan sesuai permintaan
            modalBody.innerHTML = `
                <div class="pb-4 mb-6 border-b border-gold">
                     <div class="flex justify-between items-baseline">
                         <span class="text-xl font-serif text-gray-300">Total Payable</span>
                         <span class="text-3xl font-bold text-gold">$${totalAmount.toFixed(2)}</span>
                     </div>
                </div>

                <div class="border border-gray-800 p-4">
                    <h3 class="text-xl font-serif font-bold mb-4">Reservation Details</h3>
                    <div class="space-y-2 text-gray-300">
                        <p><strong>Reservation ID:</strong> ${reservationId}</p>
                        <p><strong>Guests:</strong> ${guests} people</p>
                        <p><strong>Date:</strong> ${date}</p>
                        <p><strong>Time:</strong> ${time}</p>
                        <p><strong>Occasion:</strong> ${occasion}</p>
                        <p><strong>Table Number:</strong> ${table}</p>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        });
    });

    closeModal.addEventListener('click', function () {
        modal.classList.add('hidden');
    });

    window.addEventListener('click', function (e) {
        if (e.target == modal) {
            modal.classList.add('hidden');
        }
    });
});
</script>