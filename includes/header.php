<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the base path based on current location
$current_dir = dirname($_SERVER['SCRIPT_NAME']);
$base_path = '';

// Check if we're in a subdirectory
if (strpos($current_dir, '/pages/auth') !== false || strpos($current_dir, '/pages/profile') !== false) {
    $base_path = '../../';
} elseif (strpos($current_dir, '/pages') !== false) {
    $base_path = '../';
}

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);

// Jika project ada di subfolder
$basePath = $protocol . '://' . $host . $scriptPath . '/';

// Jika project di root
// $basePath = $protocol . '://' . $host . '/';

$adminPath = $basePath . 'pages/admin/dashboard/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Château Lumière | Luxury Fine Dining</title>
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/main.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Montserrat', sans-serif; }
        .text-gold { color: #D4AF37; }
        .text-gold-dark { color: #B8941F; }
        .bg-gold { background-color: #D4AF37; }
        .border-gold { border-color: #D4AF37; }
        .hover\:text-gold:hover { color: #D4AF37; }
        .hover\:text-gold-dark:hover { color: #B8941F; }
        .hover\:bg-gold:hover { background-color: #D4AF37; }
        .hover\:border-gold:hover { border-color: #D4AF37; }
        .focus\:ring-gold:focus { --tw-ring-color: #D4AF37; }
        .focus\:border-gold:focus { border-color: #D4AF37; }
        .hover\:shadow-gold\/20:hover { box-shadow: 0 25px 50px -12px rgba(212, 175, 55, 0.2); }
        
        /* Custom dropdown animation */
        .dropdown-menu {
            opacity: 0;
            visibility: hidden;
            transform: translateY(10px);
            transition: all 0.3s ease;
        }
        
        .dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .header-solid {
            background-color: black;
            border-bottom: 1px solid #1f2937;
        }

        /* Style untuk link yang dinonaktifkan */
        .disabled-link {
            opacity: 0.5;
            cursor: not-allowed;
            position: relative;
        }
    </style>
</head>
<body class="bg-black text-white font-sans">
    <nav class="fixed w-full z-50 bg-black border-b border-gray-800 transition-all duration-500 ease-in-out transform hover:shadow-lg hover:shadow-gold/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?php echo $base_path; ?>index.php" class="text-2xl font-serif font-bold text-gold hover:text-gold-dark transition duration-500">Château Lumière</a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?php echo $base_path; ?>index.php" class="text-white hover:text-gold transition duration-300 relative group">
                        Home
                        <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_role'] == 'admin') : ?>
                            <a href="<?php echo $base_path; ?>pages/admin/dashboard/" class="text-white hover:text-gold transition duration-300 relative group">
                                Dashboard
                                <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                            </a>
                        <?php endif ?>
                        <a href="<?php echo $base_path; ?>pages/reservation.php" class="text-white hover:text-gold transition duration-300 relative group">
                            Reservations
                            <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                        </a>
                    <?php else: ?>
                        <a href="#" class="text-white transition duration-300 relative group disabled-link" title="Please login to make a reservation">
                            Reservations
                            <span class="absolute bottom-0 left-0 w-full h-px bg-transparent"></span> </a>
                    <?php endif; ?>
                    <a href="<?php echo $base_path; ?>pages/menu.php" class="text-white hover:text-gold transition duration-300 relative group">
                        Menu
                        <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="<?php echo $base_path; ?>pages/about.php" class="text-white hover:text-gold transition duration-300 relative group">
                        About
                        <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="<?php echo $base_path; ?>pages/contact.php" class="text-white hover:text-gold transition duration-300 relative group">
                        Contact
                        <span class="absolute bottom-0 left-0 w-0 h-px bg-gold transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="relative dropdown">
                            <a href="<?php echo $base_path; ?>pages/profile" class="flex items-center gap-2 text-gold hover:text-gold-dark transition duration-300">
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <i class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </a>
                            <div class="dropdown-menu absolute right-0 mt-2 w-48 bg-black border border-gray-800 rounded-md shadow-lg py-1 z-50">
                                <a href="<?php echo $base_path; ?>pages/profile" class="block px-4 py-2 text-white hover:bg-gray-900 hover:text-gold transition duration-300">Profile</a>
                                <a href="<?php echo $base_path; ?>includes/logout.php" class="block px-4 py-2 text-white hover:bg-gray-900 hover:text-gold transition duration-300">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>pages/auth/login.php" class="px-4 py-2 border border-gold text-gold hover:bg-gold hover:text-black transition duration-500 transform hover:-translate-y-1">
                            Login
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="md:hidden flex items-center">
                    <button type="button" class="mobile-menu-button p-2 rounded-md text-white hover:text-gold focus:outline-none transition duration-300">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="mobile-menu hidden md:hidden bg-black border-t border-gray-800 transition-all duration-300 ease-in-out">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?php echo $base_path; ?>index.php" class="block px-3 py-2 text-white hover:text-gold transition duration-300">Home</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_path; ?>pages/reservation.php" class="block px-3 py-2 text-white hover:text-gold transition duration-300">Reservations</a>
                <?php else: ?>
                    <a href="#" class="block px-3 py-2 text-white disabled-link" onclick="alert('Please login to make a reservation'); return false;">Reservations</a>
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>pages/menu.php" class="block px-3 py-2 text-white hover:text-gold transition duration-300">Menu</a>
                <a href="<?php echo $base_path; ?>pages/about.php" class="block px-3 py-2 text-white hover:text-gold transition duration-300">About</a>
                <a href="<?php echo $base_path; ?>pages/contact.php" class="block px-3 py-2 text-white hover:text-gold transition duration-300">Contact</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_path; ?>pages/profile" class="block px-3 py-2 text-gold">My Account</a>
                    <a href="<?php echo $base_path; ?>includes/logout.php" class="block px-3 py-2 text-white hover:text-gold">Logout</a>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>pages/auth/login.php" class="block px-3 py-2 text-white hover:text-gold">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="pt-16">
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.querySelector('.mobile-menu-button');
            const mobileMenu = document.querySelector('.mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            // Note: The original code had a 'header' selector which doesn't exist.
            // I'm assuming you meant the 'nav' element.
            const nav = document.querySelector('nav'); 
            
            if (nav) { // Check if nav element exists
                window.addEventListener('scroll', function() {
                    if (window.scrollY > 0) {
                        nav.classList.add('header-solid');
                    } else {
                        nav.classList.remove('header-solid');
                    }
                });
            }
        });
    </script>