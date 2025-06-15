<?php
require_once __DIR__ . '/../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Château Restaurant</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .about-hero {
            margin-top: 80px;
            height: 60vh;
            background: linear-gradient(to bottom, rgba(0,0,0,0.7), rgba(0,0,0,0.5));
        }

        .section-divider {
            width: 150px;
            height: 2px;
            background: linear-gradient(to right, transparent, #FFD700, transparent);
            margin: 2rem auto;
        }

        .feature-card {
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid #FFD700;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(255, 215, 0, 0.2);
        }

        .timeline-item {
            border-left: 2px solid #FFD700;
            padding-left: 2rem;
            position: relative;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -6px;
            top: 0;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #FFD700;
        }
    </style>
</head>
<body class="bg-black text-white">
    <?php include '../includes/header.php'; ?>

    <main class="min-h-screen">
        <!-- Hero Section -->
        <section class="about-hero relative overflow-hidden">
            <div class="absolute inset-0 bg-black/50 z-10"></div>
            <img src="../assets/images/reservasi.jpg" class="w-full h-full object-cover" alt="Restaurant Interior">
            <div class="absolute inset-0 flex flex-col items-center justify-center z-20">
                <h1 class="text-5xl font-serif font-bold text-gold mb-4">About Château Restaurant</h1>
                <p class="text-lg text-white/90 max-w-2xl text-center">
                    A Journey Through Culinary Excellence
                </p>
            </div>
        </section>

        <!-- Our Story -->
        <section class="py-16 px-4">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-serif text-gold text-center mb-8">Our Story</h2>
                <div class="section-divider"></div>
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div>
                        <p class="text-gray-300 leading-relaxed mb-6">
                            Founded in 1975, Château Restaurant Web Platform revolutionizes the dining experience by seamlessly connecting food enthusiasts with exceptional culinary experiences. Our platform combines traditional hospitality with modern technology, offering an intuitive reservation system and comprehensive menu exploration.
                        </p>
                        <p class="text-gray-300 leading-relaxed">
                            What began as a simple booking system has evolved into a complete digital dining companion, featuring real-time table availability, personalized dining preferences, and exclusive member benefits.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <img src="../assets/videos/Video_Restoran_Mewah_Selesai.gif" alt="Restaurant Interior" class="rounded-lg shadow-lg">
                        <img src="../assets/videos/Video_Pengalaman_Makan_Mewah.gif" alt="Dining Experience" class="rounded-lg shadow-lg mt-8">
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16 px-4 bg-black/50">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-3xl font-serif text-gold text-center mb-8">Platform Features</h2>
                <div class="section-divider"></div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="feature-card p-6 rounded-lg">
                        <h3 class="text-xl font-serif text-gold mb-4">Smart Reservations</h3>
                        <p class="text-gray-300">Real-time table booking system with instant confirmation and special requests handling.</p>
                    </div>
                    <div class="feature-card p-6 rounded-lg">
                        <h3 class="text-xl font-serif text-gold mb-4">Digital Menu</h3>
                        <p class="text-gray-300">Interactive menu with detailed descriptions, images, and dietary information.</p>
                    </div>
                    <div class="feature-card p-6 rounded-lg">
                        <h3 class="text-xl font-serif text-gold mb-4">VIP Services</h3>
                        <p class="text-gray-300">Luxury transportation options and personalized dining experiences.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Timeline -->
        <section class="py-16 px-4">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl font-serif text-gold text-center mb-8">Our Journey</h2>
                <div class="section-divider"></div>
                <div class="space-y-12">
                    <div class="timeline-item">
                        <h3 class="text-xl font-serif text-gold mb-2">2025 - Platform Launch</h3>
                        <p class="text-gray-300">Initial release with basic reservation capabilities.</p>
                    </div>
                    <div class="timeline-item">
                        <h3 class="text-xl font-serif text-gold mb-2">2025 - Digital Menu Integration</h3>
                        <p class="text-gray-300">Introduction of interactive menu system with real-time updates.</p>
                    </div>
                    <div class="timeline-item">
                        <h3 class="text-xl font-serif text-gold mb-2">2025 - VIP Services</h3>
                        <p class="text-gray-300">Launch of premium features including luxury transportation.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
