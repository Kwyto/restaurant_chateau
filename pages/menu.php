<?php
require_once __DIR__ . '/../includes/config.php';

// Get menu items grouped by category using existing mysqli connection
try {
    $menuItems = getMenuItems($conn);
    if ($menuItems) {
        // Group menu items by category
        $menuByCategory = [];
        while ($item = mysqli_fetch_assoc($menuItems)) {
            $menuByCategory[$item['category']][] = $item;
        }
    } else {
        throw new Exception("No menu items found");
    }
} catch (Exception $e) {
    error_log("Error in menu.php: " . $e->getMessage());
    $error = "Unable to load menu items. Please try again later.";
    $menuItems = [];
    $menuByCategory = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Château Restaurant</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .menu-hero {
            margin-top: 120px; /* Ubah dari 80px ke 120px untuk memberikan ruang pada category nav */
            position: relative;
            height: 60vh;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.5));
        }
        
        .category-nav {
            position: fixed; /* Ubah dari sticky ke fixed */
            top: 64px; /* Sesuaikan dengan tinggi navbar */
            left: 0;
            right: 0;
            background: #000000; /* Ubah ke solid black */
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
            z-index: 30;
            transition: all 0.3s ease;
        }

        .category-btn {
            position: relative;
            overflow: hidden;
            border: 1px solid #FFD700; /* Brighter gold color */
            transition: all 0.3s ease;
            color: #FFD700;
        }

        .category-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background-color: #FFD700;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 0;
        }

        .category-btn span {
            position: relative;
            z-index: 1;
        }

        .category-btn:hover::before,
        .category-btn.active::before {
            opacity: 0.2;
        }

        .category-btn.active {
            border-color: #FFD700;
            background-color: rgba(255, 215, 0, 0.15); /* Brighter semi-transparent gold */
            color: #FFD700;
            font-weight: 600;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5); /* Gold glow effect */
        }

        .category-btn:hover {
            color: #FFD700;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.3); /* Gold glow on hover */
        }

        .text-gold {
            color: #FFD700 !important; /* Brighter gold text */
        }

        .border-gold {
            border-color: #FFD700 !important;
        }

        .menu-card:hover {
            border-color: #FFD700 !important;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.2) !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 1s ease forwards;
        }

        .animation-delay-200 {
            animation-delay: 0.2s;
        }
        
        .menu-item-image {
            height: 200px;
            width: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .menu-card:hover .menu-item-image {
            transform: scale(1.05);
        }
        
        .image-container {
            overflow: hidden;
            position: relative;
        }
        
        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 100%);
        }

        /* Add these new styles */
        .scroll-margin {
            scroll-margin-top: 120px; /* Increased margin to account for sticky header + navigation */
        }
        
        .scroll-padding {
            scroll-padding-top: 120px;
        }
        
        html {
            scroll-behavior: smooth;
            scroll-padding-top: 120px; /* Add padding to html for better scroll positioning */
        }

        /* Add these new styles for enhanced visibility */
        .category-btn.active span {
            background: linear-gradient(45deg, #FFD700, #FFF7DE);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            filter: drop-shadow(0 0 2px rgba(255, 215, 0, 0.5));
        }

        /* Add modal styles */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 50;
            backdrop-filter: blur(5px);
        }

        .modal.active {
            display: flex;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: linear-gradient(to bottom, #1a1a1a, #000);
            border: 2px solid #FFD700;
            max-width: 90%;
            width: 800px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .menu-grid-item {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .menu-grid-item:hover {
            transform: translateY(-5px);
        }

        .menu-description {
            display: none;
        }
    </style>
</head>
<body class="bg-black text-white">
    <?php include '../includes/header.php'; ?>

    <main class="min-h-screen">
        <!-- Hero Section -->
        <section class="menu-hero overflow-hidden">
            <div class="absolute inset-0 bg-black/50 z-10"></div>
            <img src="../assets/images/reservasi.jpg" class="w-full h-full object-cover" alt="Menu Banner">
            <div class="absolute inset-0 flex flex-col items-center justify-center z-20">
                <h1 class="text-5xl font-serif font-bold text-gold mb-4 opacity-0 transform translate-y-4 animate-fadeIn">
                    Our Menu
                </h1>
                <p class="text-lg text-white/90 max-w-2xl text-center opacity-0 transform translate-y-4 animate-fadeIn animation-delay-200">
                    Experience culinary excellence with our carefully curated selection of dishes
                </p>
            </div>
        </section>

        <!-- Category Navigation -->
        <nav class="category-nav py-4 px-4">
            <div class="max-w-7xl mx-auto flex flex-wrap justify-center gap-2">
                <?php foreach(array_keys($menuByCategory) as $category): ?>
                <a href="#<?php echo $category; ?>" 
                   class="category-btn px-4 py-2 text-sm font-medium text-gold transition rounded">
                    <span><?php echo ucwords(str_replace('_', ' ', $category)); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- Menu Categories -->
        <div class="max-w-7xl mx-auto px-4 py-12">
            <?php foreach($menuByCategory as $category => $items): ?>
            <section id="<?php echo $category; ?>" class="mb-16 scroll-margin">
                <h2 class="text-3xl font-serif text-gold mb-8 text-center">
                    <?php echo ucwords(str_replace('_', ' ', $category)); ?>
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($items as $item): 
                        // Convert backslashes to forward slashes in image path
                        $imagePath = str_replace('\\', '/', $item['image_path']);
                    ?>
                    <div class="menu-grid-item bg-black border-2 border-gold/30 hover:border-gold rounded-lg overflow-hidden transition-all duration-300 hover:shadow-lg hover:shadow-gold/20">
                        <div class="image-container">
                            <img src="../assets/images/menu/<?php echo $imagePath; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="menu-item-image"
                                 onerror="this.src='../assets/images/menu/default.jpg'">
                            <div class="image-overlay"></div>
                        </div>
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-serif font-bold text-gold"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <?php if(isset($item['is_featured']) && $item['is_featured']): ?>
                                <span class="bg-gold/20 text-gold px-2 py-1 rounded text-xs">Chef's Choice</span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-400 text-sm mb-3"><?php echo htmlspecialchars($item['description'] ?? ''); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-2xl font-bold text-gold">$<?php echo number_format($item['price'], 2); ?></span>
                                <button onclick="showMenuItem(<?php echo htmlspecialchars(json_encode($item)); ?>)" 
                                        class="px-4 py-2 bg-transparent border border-gold text-gold hover:bg-gold hover:text-black transition-all duration-300 rounded-full text-sm">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endforeach; ?>
        </div>
    </main>

    <!-- Modal Template -->
    <div id="menuModal" class="modal">
        <div class="modal-content m-auto rounded-lg overflow-hidden">
            <div class="relative">
                <img id="modalImage" src="" alt="" class="w-full h-64 object-cover">
                <button onclick="closeModal()" class="absolute top-4 right-4 bg-black/50 text-white w-10 h-10 rounded-full flex items-center justify-center hover:bg-gold hover:text-black transition">
                    ×
                </button>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-start gap-4 mb-4">
                    <h3 id="modalTitle" class="text-2xl font-serif font-bold text-gold"></h3>
                    <span id="modalFeatured" class="bg-gold/20 text-gold px-2 py-1 rounded text-xs whitespace-nowrap hidden">
                        Chef's Choice
                    </span>
                </div>
                <p id="modalDescription" class="text-gray-300 text-lg mb-6"></p>
                <div class="flex justify-between items-center">
                    <span id="modalPrice" class="text-3xl font-bold text-gold"></span>
                    <span id="modalCategory" class="text-sm text-gray-400"></span>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update smooth scroll function with precise offset
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                const headerOffset = 120;
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });
            });
        });

        // Create Intersection Observer for better accuracy
        const observerOptions = {
            root: null,
            rootMargin: '-120px 0px -80% 0px', // Adjust these values to control when the highlight triggers
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const id = entry.target.getAttribute('id');
                const menuBtn = document.querySelector(`.category-btn[href="#${id}"]`);
                
                if (entry.isIntersecting) {
                    // Remove active class from all buttons
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    // Add active class to current button
                    menuBtn?.classList.add('active');
                }
            });
        }, observerOptions);

        // Observe all category sections
        document.querySelectorAll('section[id]').forEach((section) => {
            observer.observe(section);
        });
    });

    function showMenuItem(item) {
        const modal = document.getElementById('menuModal');
        const image = document.getElementById('modalImage');
        const title = document.getElementById('modalTitle');
        const description = document.getElementById('modalDescription');
        const price = document.getElementById('modalPrice');
        const featured = document.getElementById('modalFeatured');
        const category = document.getElementById('modalCategory');

        // Set content
        image.src = item.image_path ? 
            `../assets/images/menu/${item.image_path}` : 
            `../assets/images/menu/defaults/${item.category}.jpg`;
        image.onerror = () => image.src = '../assets/images/menu/default.jpg';
        
        title.textContent = item.name;
        description.textContent = item.description;
        price.textContent = `$${parseFloat(item.price).toFixed(2)}`;
        category.textContent = item.category.charAt(0).toUpperCase() + item.category.slice(1);
        
        featured.style.display = item.is_featured ? 'block' : 'none';

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('menuModal');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on outside click
    document.getElementById('menuModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
    </script>
</body>
</html>
</html>
</body>
</html>
</html>
