<?php
include 'includes/config.php';
include 'includes/testimonials.php';

// Get featured menu items
$featuredItems = getMenuItems($conn, 'main');

// Get testimonials
$testimonials = getTestimonials($conn);

// Check if user is logged in and get their data if needed
$userData = null;
if (isset($_SESSION['user_id'])) {
    $query = "SELECT * FROM users WHERE id = " . $_SESSION['user_id'];
    $result = mysqli_query($conn, $query);
    $userData = mysqli_fetch_assoc($result);
}

// Handle testimonial submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login to submit a testimonial";
    } else {
        $rating = $_POST['rating'];
        $comment = $_POST['comment'];
        
        if (addTestimonial($conn, $_SESSION['user_id'], $rating, $comment)) {
            $_SESSION['success'] = "Thank you for your testimonial!";
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $_SESSION['error'] = "Failed to submit testimonial";
        }
    }
}

?>

<?php include 'includes/header.php'; ?>

<style>
/* Ultimate Luxury Restaurant Styles */
:root {
    --gold: #D4AF37;
    --gold-dark: #B8941F;
    --gold-light: #E6C547;
    --platinum: #E5E4E2;
    --diamond: rgba(255, 255, 255, 0.8);
    --black-velvet: #0a0a0a;
    --black-satin: #1a1a1a;
    --champagne: #F7E7CE;
}

* {
    font-family: 'Inter', sans-serif;
    scroll-behavior: smooth;
}

.font-serif {
    font-family: 'Playfair Display', serif;
    letter-spacing: 1px;
}

/* Luxury Background Effects */
.hero-bg {
    background: linear-gradient(135deg, var(--black-velvet), var(--black-satin));
    position: relative;
    overflow: hidden;
}

.hero-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.08) 0%, transparent 25%),
        radial-gradient(circle at 80% 70%, rgba(212, 175, 55, 0.08) 0%, transparent 25%);
    z-index: 0;
    animation: gradientPulse 15s ease infinite alternate;
}

@keyframes gradientPulse {
    0% { opacity: 0.8; }
    100% { opacity: 1; }
}

/* Diamond Dust Overlay */
.diamond-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 20% 30%, var(--diamond) 0.5px, transparent 1px),
        radial-gradient(circle at 80% 70%, var(--diamond) 0.5px, transparent 1px);
    background-size: 30px 30px;
    opacity: 0.15;
    pointer-events: none;
    z-index: 1;
}

/* Luxury Particles */
.particles {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    top: 0;
    left: 0;
    z-index: 2;
}

.particle {
    position: absolute;
    display: block;
    width: 6px;
    height: 6px;
    background: var(--gold);
    border-radius: 50%;
    box-shadow: 0 0 10px 2px var(--gold);
    animation: float 15s infinite linear;
    opacity: 0;
}

@keyframes float {
    0% {
        opacity: 0;
        transform: translateY(100vh) scale(0.5);
    }
    20% {
        opacity: 0.8;
        transform: translateY(80vh) scale(1);
    }
    80% {
        opacity: 0.8;
        transform: translateY(-10vh) scale(1);
    }
    100% {
        opacity: 0;
        transform: translateY(-20vh) scale(0.5);
    }
}

/* Gold Emboss Text */
.gold-emboss {
    text-shadow: 
        1px 1px 1px var(--black-satin),
        2px 2px 1px var(--black-satin),
        3px 3px 3px rgba(212, 175, 55, 0.3),
        4px 4px 6px rgba(212, 175, 55, 0.2);
}

/* Luxury Glow Text */
.glow-text {
    text-shadow: 0 0 10px var(--gold), 0 0 20px rgba(212, 175, 55, 0.5);
    animation: pulse 3s ease infinite alternate;
}

@keyframes pulse {
    0% { text-shadow: 0 0 10px var(--gold), 0 0 20px rgba(212, 175, 55, 0.5); }
    100% { text-shadow: 0 0 15px var(--gold), 0 0 30px rgba(212, 175, 55, 0.8); }
}

/* Luxury Button Styles */
.btn-luxury {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, transparent, rgba(212, 175, 55, 0.3), transparent);
    background-size: 200% 200%;
    border: 1px solid var(--gold);
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    z-index: 1;
}

.btn-luxury::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.6s ease;
    z-index: -1;
}

.btn-luxury:hover::before {
    left: 100%;
}

.btn-luxury:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(212, 175, 55, 0.4);
}

/* Luxury Card Design */
.card-luxury {
    background: linear-gradient(145deg, #121212, #1e1e1e);
    border: 1px solid rgba(212, 175, 55, 0.1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    overflow: hidden;
    position: relative;
}

.card-luxury::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), transparent);
    z-index: 1;
    pointer-events: none;
}

.card-luxury:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 20px 40px rgba(212, 175, 55, 0.3);
    border-color: rgba(212, 175, 55, 0.3);
}

/* Luxury Section Background */
.section-luxury {
    background: linear-gradient(135deg, rgba(10, 10, 10, 0.95), rgba(20, 20, 20, 0.98));
    backdrop-filter: blur(10px);
    border: 1px solid rgba(212, 175, 55, 0.1);
    box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.5);
}

/* Luxury Image Hover Effect */
.luxury-image {
    position: relative;
    overflow: hidden;
}

.luxury-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), transparent);
    transform: translateX(-100%);
    transition: transform 0.8s cubic-bezier(0.23, 1, 0.32, 1);
    z-index: 1;
}

.luxury-image:hover::before {
    transform: translateX(100%);
}

/* Luxury Counter Animation */
.luxury-counter {
    font-size: 3.5rem;
    font-weight: 300;
    font-family: 'Playfair Display', serif;
    color: var(--gold);
    position: relative;
    display: inline-block;
}

.luxury-counter::after {
    content: '+';
    position: absolute;
    right: -20px;
    color: var(--gold-light);
}

/* Luxury Loading Animation */
.luxury-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: var(--black-velvet);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    animation: fadeOut 1.5s ease-out 2.5s forwards;
}

.luxury-spinner {
    width: 80px;
    height: 80px;
    border: 3px solid transparent;
    border-top-color: var(--gold);
    border-radius: 50%;
    position: relative;
    animation: spin 1.5s linear infinite;
}

.luxury-spinner::before,
.luxury-spinner::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    border: 3px solid transparent;
}

.luxury-spinner::before {
    top: 5px;
    left: 5px;
    right: 5px;
    bottom: 5px;
    border-top-color: var(--gold-light);
    animation: spin 2s linear infinite reverse;
}

.luxury-spinner::after {
    top: 15px;
    left: 15px;
    right: 15px;
    bottom: 15px;
    border-top-color: var(--champagne);
    animation: spin 3s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes fadeOut {
    to { opacity: 0; visibility: hidden; }
}

/* Luxury Scroll Indicator */
.luxury-scroll-bar {
    position: fixed;
    top: 0;
    left: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--gold), var(--gold-light));
    z-index: 9998;
    transition: width 0.3s ease;
    box-shadow: 0 0 10px var(--gold);
}

/* Luxury Floating Action Button */
.luxury-fab {
    position: fixed;
    bottom: 40px;
    right: 40px;
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light));
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5);
    z-index: 1000;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    animation: float 3s ease-in-out infinite;
}

.luxury-fab:hover {
    transform: scale(1.1) translateY(-5px);
    box-shadow: 0 15px 40px rgba(212, 175, 55, 0.7);
}

/* Luxury Testimonial Cards */
.testimonial-luxury {
    position: relative;
    overflow: hidden;
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.testimonial-luxury::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        to bottom right,
        transparent 45%,
        rgba(212, 175, 55, 0.1) 50%,
        transparent 55%
    );
    transform: rotate(45deg);
    transition: all 0.8s ease;
    opacity: 0;
}

.testimonial-luxury:hover::before {
    animation: shine 1.5s ease;
}

@keyframes shine {
    0% { left: -50%; opacity: 0; }
    50% { opacity: 0.8; }
    100% { left: 150%; opacity: 0; }
}

/* Luxury Menu Item Hover */
.menu-item-hover {
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
}

.menu-item-hover:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(212, 175, 55, 0.3);
}

.menu-item-hover:hover .menu-item-overlay {
    opacity: 1;
}

.menu-item-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
    opacity: 0;
    transition: opacity 0.6s ease;
}

/* Luxury Text Reveal */
.luxury-reveal {
    position: relative;
    overflow: hidden;
}

.luxury-reveal::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, var(--gold), var(--gold-light));
    animation: reveal 1.8s cubic-bezier(0.23, 1, 0.32, 1) forwards;
    transform-origin: left;
}

@keyframes reveal {
    0% { transform: scaleX(0); }
    40% { transform: scaleX(1); transform-origin: left; }
    60% { transform: scaleX(1); transform-origin: right; }
    100% { transform: scaleX(0); transform-origin: right; }
}

/* Luxury Background Patterns */
.luxury-pattern {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 10% 20%, rgba(212, 175, 55, 0.03) 0%, transparent 20%),
        radial-gradient(circle at 90% 80%, rgba(212, 175, 55, 0.03) 0%, transparent 20%);
    pointer-events: none;
    z-index: 0;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .hero-title {
        font-size: 4rem !important;
    }
    
    .luxury-counter {
        font-size: 2.5rem;
    }
}

/* Testimonial Button Style */
#openTestimonialModal {
  background: linear-gradient(135deg, #D4AF37 0%, #F9D423 100%);
  color: #000;
  font-weight: 600;
  border: none;
  border-radius: 9999px;
  box-shadow: 0 4px 6px rgba(212, 175, 55, 0.3);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

#openTestimonialModal:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(212, 175, 55, 0.4);
}

#openTestimonialModal::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

#openTestimonialModal:hover::before {
  left: 100%;
}

/* Testimonial Modal Style */
#testimonialModal {
  backdrop-filter: blur(8px);
}

#testimonialModal > div {
  background: linear-gradient(145deg, #111 0%, #000 100%);
  border: 1px solid #D4AF37;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
}

#testimonialModal h3 {
  text-shadow: 0 2px 4px rgba(212, 175, 55, 0.3);
  position: relative;
  padding-bottom: 10px;
}

#testimonialModal h3::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 2px;
  background: linear-gradient(90deg, transparent, #D4AF37, transparent);
}

/* Star Rating Style */
input[name="rating"]:checked + label svg {
  fill: #D4AF37;
  stroke: #D4AF37;
}

label[for^="star"]:hover svg {
  fill: #D4AF37;
  stroke: #D4AF37;
  transform: scale(1.1);
  transition: all 0.2s ease;
}

/* Textarea Style */
#comment {
  background: rgba(20, 20, 20, 0.8);
  transition: all 0.3s ease;
}

#comment:focus {
  border-color: #D4AF37;
  box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
}

/* Submit Button Style */
button[name="submit_testimonial"] {
  position: relative;
  overflow: hidden;
  transition: all 0.3s ease;
}

button[name="submit_testimonial"]::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

button[name="submit_testimonial"]:hover::after {
  left: 100%;
}

/* Close Button Style */
#closeTestimonialModal {
  transition: all 0.3s ease;
}

#closeTestimonialModal:hover {
  transform: rotate(90deg);
}

/* Login Button Style in Modal */
#testimonialModal a {
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

#testimonialModal a:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(212, 175, 55, 0.4);
}

#testimonialModal a::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

#testimonialModal a:hover::before {
  left: 100%;
}

/* Success/Error Messages */
.bg-red-900 {
  background: rgba(127, 29, 29, 0.8) !important;
  border-left: 4px solid #FECACA;
}

.bg-green-900 {
  background: rgba(6, 78, 59, 0.8) !important;
  border-left: 4px solid #A7F3D0;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

#testimonialModal > div {
  animation: modalFadeIn 0.3s ease-out forwards;
}

document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('testimonialCarousel');
    const dotsContainer = document.getElementById('testimonialDots');
    const cards = document.querySelectorAll('.testimonial-card');
    const cardCount = cards.length / 2; // Karena ada duplikat
    let currentIndex = 0;
    let autoScrollInterval;

    // Create dots
    for (let i = 0; i < cardCount; i++) {
        const dot = document.createElement('div');
        dot.classList.add('carousel-dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }

    // Auto scroll function
    function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % cardCount;
            updateCarousel();
        }, 4000);
    }

    function updateCarousel() {
        const scrollPosition = currentIndex * cards[0].offsetWidth;
        carousel.scrollTo({
            left: scrollPosition,
            behavior: 'smooth'
        });

        // Update active dot
        document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
        resetAutoScroll();
    }

    function resetAutoScroll() {
        clearInterval(autoScrollInterval);
        startAutoScroll();
    }

    // Start auto scroll
    startAutoScroll();

    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoScrollInterval);
    });

    carousel.addEventListener('mouseleave', startAutoScroll);
});

</style>

<!-- Luxury Loading Animation -->
<div class="luxury-loader">
    <div class="luxury-spinner"></div>
</div>

<!-- Luxury Scroll Indicator -->
<div class="luxury-scroll-bar"></div>

<!-- Luxury Background Elements -->
<div class="luxury-pattern"></div>

<!-- Luxury Floating Action Button -->
<div class="luxury-fab" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
    </svg>
</div>

<main class="min-h-screen bg-black text-white overflow-x-hidden">
    <!-- Luxury Hero Section -->
    <section class="relative h-screen flex items-center justify-center hero-bg overflow-hidden">
        <!-- Diamond Dust Overlay -->
        <div class="diamond-overlay"></div>
        
        <!-- Luxury Particles -->
        <div class="particles">
            <span class="particle" style="left: 10%; animation-delay: 0s;"></span>
            <span class="particle" style="left: 20%; animation-delay: 2s;"></span>
            <span class="particle" style="left: 30%; animation-delay: 4s;"></span>
            <span class="particle" style="left: 40%; animation-delay: 1s;"></span>
            <span class="particle" style="left: 50%; animation-delay: 3s;"></span>
            <span class="particle" style="left: 60%; animation-delay: 5s;"></span>
            <span class="particle" style="left: 70%; animation-delay: 2.5s;"></span>
            <span class="particle" style="left: 80%; animation-delay: 4.5s;"></span>
            <span class="particle" style="left: 90%; animation-delay: 1.5s;"></span>
        </div>
        
        <!-- Luxury Content -->
        <div class="relative z-20 text-center px-4 max-w-4xl mx-auto">
            <h1 class="hero-title text-6xl md:text-8xl font-serif font-black mb-8 tracking-tight glow-text luxury-reveal">
                Château Lumière
            </h1>
            <p class="text-xl md:text-2xl font-light mb-12 leading-relaxed opacity-0" style="animation: fadeIn 1.5s ease-out 1s forwards;">
                Where culinary artistry meets timeless elegance. Our Michelin-starred restaurant offers an unparalleled dining experience for the discerning palate.
            </p>
            
            <div class="flex flex-col sm:flex-row justify-center gap-6 opacity-0" style="animation: fadeIn 1.5s ease-out 1.5s forwards;">
                <?php if(isset($_SESSION['user_id'])): 
                    $userData = getUserData($conn, $_SESSION['user_id']);
                    ?>
                    <a href="pages/profile" class="px-10 py-5 btn-luxury text-gold hover:text-black transition-all duration-700 font-medium tracking-wide">
                        <?php echo getGreeting() . ', ' . ($userData['gender'] == 'male' ? 'Mr.' : 'Mrs.') . ' ' . $userData['first_name']; ?>
                    </a>
                <?php else: ?>
                    <a href="pages/auth/login.php" class="px-10 py-5 btn-luxury text-gold hover:text-black transition-all duration-700 font-medium tracking-wide">
                        Login
                    </a>
                <?php endif; ?>
                <a href="pages/home.php" class="px-10 py-5 bg-gradient-to-r from-gold to-gold-light text-black hover:from-gold-light hover:to-gold transition-all duration-700 font-medium tracking-wide transform hover:-translate-y-1 hover:shadow-2xl hover:shadow-gold/40">
                    Make a Reservation
                </a>
            </div>
        </div>
        
        <!-- Luxury Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-20">
            <div class="animate-bounce">
                <svg class="w-8 h-8 text-gold animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
        </div>
    </section>

    <!-- Luxury About Section -->
    <section class="py-32 px-4 max-w-7xl mx-auto section-luxury rounded-3xl my-16 relative overflow-hidden">
        <div class="luxury-pattern"></div>
        <div class="grid md:grid-cols-2 gap-20 items-center relative z-10">
            <div data-aos="fade-right" data-aos-duration="1200">
                <h2 class="text-5xl font-serif font-black mb-10 glow-text gold-emboss">Our Story</h2>
                <p class="text-lg leading-relaxed mb-8 opacity-80">
                    Founded in 1995, Château Lumière has consistently ranked among the world's finest dining establishments. 
                    Our chef, with over 30 years of experience in Michelin-starred restaurants across Europe and Asia, 
                    brings a unique fusion of traditional techniques and innovative flavors.
                </p>
                <p class="text-lg leading-relaxed opacity-80">
                    Every dish is a masterpiece, crafted with the finest seasonal ingredients sourced from local artisans 
                    and international purveyors who share our commitment to excellence.
                </p>
                
                <!-- Luxury Counter Stats -->
                <div class="grid grid-cols-3 gap-6 mt-12">
                    <div class="text-center">
                        <div class="luxury-counter" data-target="30">0</div>
                        <p class="text-gold font-serif">Years Experience</p>
                    </div>
                    <div class="text-center">
                        <div class="luxury-counter" data-target="5">0</div>
                        <p class="text-gold font-serif">Michelin Stars</p>
                    </div>
                    <div class="text-center">
                        <div class="luxury-counter" data-target="1000">0</div>
                        <p class="text-gold font-serif">Happy Guests</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-6" data-aos="fade-left" data-aos-duration="1200">
                <div class="luxury-image card-luxury">
                    <img src="assets/images/restaurant-1.jpg" alt="Restaurant Interior" class="w-full h-72 object-cover">
                    <div class="menu-item-overlay"></div>
                </div>
                <div class="luxury-image card-luxury" style="transform: translateY(2rem);">
                    <img src="assets/images/restaurant-2.jpg" alt="Chef Preparing Food" class="w-full h-72 object-cover">
                    <div class="menu-item-overlay"></div>
                </div>
                <div class="luxury-image card-luxury" style="transform: translateY(-2rem);">
                    <img src="assets/images/restaurant-3.jpg" alt="Wine Cellar" class="w-full h-72 object-cover">
                    <div class="menu-item-overlay"></div>
                </div>
                <div class="luxury-image card-luxury">
                    <img src="assets/images/restaurant-4.jpg" alt="Dining Experience" class="w-full h-72 object-cover">
                    <div class="menu-item-overlay"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Luxury Signature Dishes Section -->
    <section class="py-24 px-4 max-w-7xl mx-auto relative">
        <div class="luxury-pattern"></div>
        <h2 class="text-5xl font-serif font-black mb-16 text-center glow-text gold-emboss" data-aos="fade-up">Signature Dishes</h2>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-10 relative z-10">
            <?php while($item = mysqli_fetch_assoc($featuredItems)): ?>
                <div class="card-luxury menu-item-hover" data-aos="zoom-in" data-aos-duration="800">
                    <div class="luxury-image h-72 overflow-hidden">
                        <img src="<?php echo $item['image_path']; ?>" alt="<?php echo $item['name']; ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="menu-item-overlay"></div>
                    </div>
                    <div class="p-8">
                        <h3 class="text-2xl font-serif font-bold mb-3 group-hover:text-gold transition-colors duration-300"><?php echo $item['name']; ?></h3>
                        <p class="text-gray-400 mb-6 leading-relaxed"><?php echo $item['description']; ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-gold text-2xl font-serif">$<?php echo number_format($item['price'], 2); ?></span>
                            <button class="px-6 py-3 border border-gold text-gold hover:bg-gold hover:text-black transition-all duration-500 rounded-full font-medium group-hover:shadow-lg group-hover:shadow-gold/30">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Luxury Testimonials Section -->
<section class="py-32 px-4 max-w-7xl mx-auto section-luxury rounded-3xl my-16 relative overflow-hidden">
    <div class="luxury-pattern"></div>
<h2 class="text-5xl font-serif font-black mb-16 text-center glow-text gold-emboss">What Our Guests Say</h2>

<!-- Testimonial Carousel Container -->
<div class="relative overflow-hidden py-10">
    <div class="testimonial-carousel flex gap-8 overflow-x-auto snap-x snap-mandatory scroll-smooth py-4" 
         id="testimonialCarousel">
        <!-- Testimonial Cards -->
        <?php foreach (array_merge($testimonials, $testimonials) as $testimonial): ?>
        <div class="testimonial-card flex-shrink-0 w-80 md:w-96 p-8 rounded-2xl bg-gradient-to-br from-gray-900 to-gray-800 border border-gray-700 hover:border-gold transition-all snap-center">
            <div class="flex items-center mb-6">
                <div class="w-14 h-14 rounded-full bg-gradient-to-r from-gold to-gold-light flex items-center justify-center text-black font-bold mr-4">
                    <?php echo substr($testimonial['first_name'], 0, 1) . substr($testimonial['last_name'], 0, 1); ?>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-white">
                        <?php echo htmlspecialchars($testimonial['first_name'] . ' ' . $testimonial['last_name']); ?>
                    </h4>
                    <div class="flex text-gold mt-1">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <svg class="w-5 h-5" fill="<?php echo $i < $testimonial['rating'] ? 'currentColor' : 'none'; ?>" 
                                 stroke="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <p class="text-gray-300 italic mb-4">
                "<?php echo htmlspecialchars($testimonial['comment']); ?>"
            </p>
            <p class="text-gray-500 text-sm">
                <?php echo date('F j, Y', strtotime($testimonial['created_at'])); ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation Dots -->
    <div class="flex justify-center mt-8 space-x-2" id="testimonialDots"></div>
</div>
    <!-- Add Testimonial Button -->
    <div class="text-center mt-16 relative z-10">
        <button id="openTestimonialModal" class="px-8 py-4 bg-gradient-to-r from-gold to-gold-light text-black font-medium rounded-full hover:shadow-lg hover:shadow-gold/40 transition-all duration-300">
            Share Your Experience
        </button>
    </div>
</section>

<!-- Testimonial Modal -->
<div id="testimonialModal" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-10 rounded-3xl max-w-2xl w-full relative border border-gold">
        <button id="closeTestimonialModal" class="absolute top-6 right-6 text-gold hover:text-gold-light">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <h3 class="text-3xl font-serif font-bold mb-8 text-gold text-center">Share Your Experience</h3>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-900 text-white p-4 mb-6 rounded-lg">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-900 text-white p-4 mb-6 rounded-lg">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <form method="POST" action="">
                <div class="mb-8">
                    <label class="block text-gold-light mb-4 font-medium">Your Rating</label>
                    <div class="flex justify-center space-x-2">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                                   class="hidden" <?php echo $i == 5 ? 'checked' : ''; ?>>
                            <label for="star<?php echo $i; ?>" class="text-3xl cursor-pointer text-gray-500 hover:text-gold transition-colors">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="mb-8">
                    <label for="comment" class="block text-gold-light mb-4 font-medium">Your Experience</label>
                    <textarea id="comment" name="comment" rows="5" 
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg p-4 text-white focus:border-gold focus:ring-gold" 
                              placeholder="Tell us about your experience..." required></textarea>
                </div>
                
                <button type="submit" name="submit_testimonial" 
                        class="w-full py-4 bg-gradient-to-r from-gold to-gold-light text-black font-bold rounded-lg hover:from-gold-light hover:to-gold transition-all duration-300">
                    Submit Testimonial
                </button>
            </form>
        <?php else: ?>
            <div class="text-center py-10">
                <p class="text-xl mb-8">Please login to share your experience with us.</p>
                <a href="pages/auth/login.php" 
                   class="px-8 py-4 bg-gradient-to-r from-gold to-gold-light text-black font-bold rounded-lg hover:from-gold-light hover:to-gold transition-all duration-300 inline-block">
                    Login Now
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

</main>

<?php include 'includes/footer.php'; ?>

<!-- Luxury Scripts -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    // Initialize AOS with luxury settings
    AOS.init({
        duration: 1200,
        easing: 'ease-out-quart',
        once: true,
        offset: 120
    });

    document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.getElementById('testimonialCarousel');
    const dotsContainer = document.getElementById('testimonialDots');
    const cards = document.querySelectorAll('.testimonial-card');
    const cardCount = cards.length / 2; // Karena ada duplikat
    let currentIndex = 0;
    let autoScrollInterval;

    // Create dots
    for (let i = 0; i < cardCount; i++) {
        const dot = document.createElement('div');
        dot.classList.add('carousel-dot');
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }

    // Auto scroll function
    function startAutoScroll() {
        autoScrollInterval = setInterval(() => {
            currentIndex = (currentIndex + 1) % cardCount;
            updateCarousel();
        }, 4000);
    }

    function updateCarousel() {
        const scrollPosition = currentIndex * cards[0].offsetWidth;
        carousel.scrollTo({
            left: scrollPosition,
            behavior: 'smooth'
        });

        // Update active dot
        document.querySelectorAll('.carousel-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
    }

    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
        resetAutoScroll();
    }

    function resetAutoScroll() {
        clearInterval(autoScrollInterval);
        startAutoScroll();
    }

    // Start auto scroll
    startAutoScroll();

    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoScrollInterval);
    });

    carousel.addEventListener('mouseleave', startAutoScroll);
});
    
    // Luxury Counter Animation
    function animateLuxuryCounters() {
        const counters = document.querySelectorAll('.luxury-counter');
        const speed = 200;
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(animateLuxuryCounters, 1);
            } else {
                counter.innerText = target;
            }
        });
    }
    
    // Intersection Observer for counter animation
    const counterSection = document.querySelector('.luxury-counter')?.closest('section');
    if (counterSection) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    animateLuxuryCounters();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(counterSection);
    }
    
    // Luxury Scroll Progress
    window.addEventListener('scroll', () => {
        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        document.querySelector('.luxury-scroll-bar').style.width = scrolled + '%';
    });
    
    // Luxury Particle Generation
    function createLuxuryParticle() {
        const particle = document.createElement('span');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 15 + 's';
        particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
        particle.style.width = (Math.random() * 6 + 4) + 'px';
        particle.style.height = particle.style.width;
        
        document.querySelector('.particles').appendChild(particle);
        
        setTimeout(() => {
            particle.remove();
        }, 15000);
    }
    
    // Generate luxury particles
    setInterval(createLuxuryParticle, 500);
    
    // Luxury Loading Animation
    window.addEventListener('load', () => {
        setTimeout(() => {
            document.querySelector('.luxury-loader').style.opacity = '0';
            setTimeout(() => {
                document.querySelector('.luxury-loader').style.display = 'none';
            }, 1500);
        }, 2000);
    });
    
    // Luxury Image Lazy Loading
    const luxuryImages = document.querySelectorAll('img');
    const luxuryImageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.style.transition = 'opacity 1.2s ease-out, transform 1.2s ease-out';
                img.style.opacity = '1';
                img.style.transform = 'scale(1)';
                luxuryImageObserver.unobserve(img);
            }
        });
    }, { threshold: 0.1 });
    
    luxuryImages.forEach(img => {
        img.style.opacity = '0';
        img.style.transform = 'scale(1.1)';
        luxuryImageObserver.observe(img);
    });
    
    // Luxury Card Tilt Effect
    document.querySelectorAll('.card-luxury').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const angleX = (y - centerY) / 20;
            const angleY = (centerX - x) / 20;
            
            card.style.transform = `perspective(1000px) rotateX(${angleX}deg) rotateY(${angleY}deg) scale(1.03)`;
            card.style.boxShadow = `${-angleY * 2}px ${angleX * 2}px 30px rgba(0, 0, 0, 0.3)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
            card.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.5)';
        });
    });
    
    // Luxury Button Hover Effects
    document.querySelectorAll('.btn-luxury').forEach(btn => {
        btn.addEventListener('mouseenter', () => {
            btn.style.backgroundPosition = '100% 50%';
        });
        
        btn.addEventListener('mouseleave', () => {
            btn.style.backgroundPosition = '0% 50%';
        });
    });
    
    // Luxury Testimonial Hover Effects
    document.querySelectorAll('.testimonial-luxury').forEach(testimonial => {
        testimonial.addEventListener('mouseenter', () => {
            testimonial.style.boxShadow = '0 20px 40px rgba(212, 175, 55, 0.2)';
            testimonial.querySelector('.rounded-full').style.transform = 'scale(1.1)';
            testimonial.querySelector('.rounded-full').style.boxShadow = '0 0 20px var(--gold)';
        });
        
        testimonial.addEventListener('mouseleave', () => {
            testimonial.style.boxShadow = 'none';
            testimonial.querySelector('.rounded-full').style.transform = 'scale(1)';
            testimonial.querySelector('.rounded-full').style.boxShadow = 'none';
        });
    });
    
    // Luxury Menu Item Add to Cart Animation
    document.querySelectorAll('.card-luxury button').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Animation
            this.innerHTML = '<svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Added!';
            this.style.background = 'var(--gold)';
            this.style.color = 'black';
            this.style.borderColor = 'var(--gold)';
            
            // Create floating notification
            const notification = document.createElement('div');
            notification.textContent = 'Item added to cart';
            notification.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                background: linear-gradient(135deg, var(--gold), var(--gold-light));
                color: black;
                padding: 15px 25px;
                border-radius: 50px;
                font-weight: 600;
                z-index: 10000;
                box-shadow: 0 10px 30px rgba(212, 175, 55, 0.5);
                animation: luxurySlideIn 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'luxurySlideOut 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards';
                setTimeout(() => notification.remove(), 600);
            }, 3000);
            
            setTimeout(() => {
                this.innerHTML = 'Add to Cart';
                this.style.background = 'transparent';
                this.style.color = 'var(--gold)';
                this.style.borderColor = 'var(--gold)';
            }, 2000);
        });
    });
    
    // Add luxury notification animations
    const luxuryStyle = document.createElement('style');
    luxuryStyle.textContent = `
        @keyframes luxurySlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes luxurySlideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;

// Testimonial Modal
    const testimonialModal = document.getElementById('testimonialModal');
    const openModalBtn = document.getElementById('openTestimonialModal');
    const closeModalBtn = document.getElementById('closeTestimonialModal');
    
    openModalBtn.addEventListener('click', () => {
        testimonialModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    });
    
    closeModalBtn.addEventListener('click', () => {
        testimonialModal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    });
    
    // Star rating interaction
    const starInputs = document.querySelectorAll('input[name="rating"]');
    const starLabels = document.querySelectorAll('label[for^="star"]');
    
    starInputs.forEach(input => {
        input.addEventListener('change', () => {
            const rating = parseInt(input.value);
            
            starLabels.forEach((label, index) => {
                const svg = label.querySelector('svg');
                if (index < rating) {
                    svg.setAttribute('fill', 'currentColor');
                    svg.classList.add('text-gold');
                    svg.classList.remove('text-gray-500');
                } else {
                    svg.setAttribute('fill', 'none');
                    svg.classList.add('text-gray-500');
                    svg.classList.remove('text-gold');
                }
            });
        });
    });
    
    // Initialize stars to show the selected rating (5 by default)
    document.querySelector('input[name="rating"]:checked').dispatchEvent(new Event('change'));

    document.head.appendChild(luxuryStyle);
</script>