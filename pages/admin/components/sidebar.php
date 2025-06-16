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
                <a href="../membership" id="membership-link" class="flex items-center px-2 py-3 text-sm font-medium text-gray-300 hover:text-white hover:bg-secondary rounded-md group">
                    <i class="fas fa-money-bill-wave mr-3"></i>
                    Membership
                </a>
            </div>
        </div>
        <div class="p-4 border-t border-gray-700">
            <div class="flex items-center">
                <img class="w-10 h-10 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin profile">
                <div class="ml-3">
                    <p class="text-sm font-medium text-white">Admin User</p>
                    <p class="text-xs font-medium text-gray-300">admin@luxuryrestaurant.com</p>
                </div>
            </div>
            <button class="mt-3 w-full flex items-center justify-center px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
                <i class="fas fa-sign-out-alt mr-2"></i>
                Logout
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dapatkan path URL saat ini
    const currentPath = window.location.pathname;
    
    // Hapus semua kelas aktif terlebih dahulu
    document.querySelectorAll('a[id$="-link"]').forEach(link => {
        link.classList.remove('bg-secondary', 'text-white');
        link.classList.add('text-gray-300');
    });
    
    // Tentukan link mana yang harus aktif berdasarkan URL
    if (currentPath.includes('/dashboard')) {
        document.getElementById('dashboard-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('dashboard-link').classList.remove('text-gray-300');
    } else if (currentPath.includes('/customers')) {
        document.getElementById('customers-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('reservation-link').classList.remove('text-gray-300');
    } else if (currentPath.includes('/reservation')) {
        document.getElementById('reservation-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('reservation-link').classList.remove('text-gray-300');
    } else if (currentPath.includes('/menu')) {
        document.getElementById('menu-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('menu-link').classList.remove('text-gray-300');
    } else if (currentPath.includes('/coupons')) {
        document.getElementById('coupons-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('coupons-link').classList.remove('text-gray-300');
    } else if (currentPath.includes('/membership')) {
        document.getElementById('membership-link').classList.add('bg-secondary', 'text-white');
        document.getElementById('membership-link').classList.remove('text-gray-300');
    }
});
</script>