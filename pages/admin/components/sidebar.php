<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['user_role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit();
}

include_once '../includes/config.php';

// Menggunakan prepared statement
$query = 'SELECT * FROM users WHERE id = ?';
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-64 bg-primary">
        <div class="flex items-center justify-center h-16 px-4 bg-secondary">
            <span class="text-white text-xl font-semibold">Château Restaurant</span>
        </div>
        <div class="flex flex-col flex-grow px-4 py-4 overflow-y-hidden">
            <div class="space-y-1">
                <a href="../dashboard/" id="dashboard-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="../customers/" id="customers-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-users mr-3"></i>
                    Customers
                </a>
                <a href="../reservation/" id="reservation-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-calendar-alt mr-3"></i>
                    Reservations
                </a>
                <a href="../menu" id="menu-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-utensils mr-3"></i>
                    Menu Items
                </a>
                <a href="../coupons" id="coupons-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-tags mr-3"></i>
                    Coupons
                </a>
            </div>
        </div>
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center">
                <img class="w-10 h-10 rounded-full" src=<?= $user['profile_phote'] ?? "https://ui-avatars.com/api/?name=" . $user['first_name'] . ' ' . $user['last_name'] . "&background=random " ?> alt="Admin profile">
                <div class="ml-3">
                    <p class="text-sm font-medium text-white"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] ?? 'Admin User') ?></p>
                    <p class="text-xs font-medium text-gray-300"><?= htmlspecialchars($user['email'] ?? 'admin@luxuryrestaurant.com') ?></p>
                </div>
            </div>
            <a href="../../../includes/logout.php" class="mt-3 w-full flex items-center justify-center px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const links = {
        'dashboard': 'dashboard-link',
        'customers': 'customers-link',
        'reservation': 'reservation-link',
        'menu': 'menu-link',
        'coupons': 'coupons-link',
    };
    
    // Reset semua link
    Object.values(links).forEach(id => {
        const link = document.getElementById(id);
        link.classList.remove('bg-secondary', 'text-white');
        link.classList.add('text-gray-300');
    });
    
    // Aktifkan link yang sesuai
    for (const [key, id] of Object.entries(links)) {
        if (currentPath.includes('/'+key)) {
            const activeLink = document.getElementById(id);
            activeLink.classList.add('bg-secondary', 'text-white');
            activeLink.classList.remove('text-gray-300');
            break;
        }
    }
});
</script>