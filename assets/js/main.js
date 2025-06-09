// Mobile menu toggle
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuButton = document.querySelector('.mobile-menu-button');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // Initialize AOS animation
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            offset: 120
        });
    }
    
    // Carousel functionality
    const carousels = document.querySelectorAll('.carousel');
    carousels.forEach(carousel => {
        const inner = carousel.querySelector('.carousel-inner');
        const items = carousel.querySelectorAll('.carousel-item');
        let currentIndex = 0;
        const intervalTime = 5000; // 5 seconds
    
        function showItem(index) {
            items.forEach(item => item.classList.remove('active'));
            items[index].classList.add('active');
            inner.style.transform = `translateX(-${index * 100}%)`;
        }
    
        function nextItem() {
            currentIndex = (currentIndex + 1) % items.length;
            showItem(currentIndex);
        }
    
        // Auto-rotate
        let interval = setInterval(nextItem, intervalTime);
    
        // Pause on hover
        carousel.addEventListener('mouseenter', () => clearInterval(interval));
        carousel.addEventListener('mouseleave', () => {
            interval = setInterval(nextItem, intervalTime);
        });
    
        // Initial show
        showItem(currentIndex);
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                
                // Add shake animation to invalid fields
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('animate-shake');
                        setTimeout(() => {
                            field.classList.remove('animate-shake');
                        }, 500);
                    }
                });
                
                // Show error message
                const errorMessage = document.createElement('div');
                errorMessage.className = 'bg-red-900 text-white p-4 rounded mb-4';
                errorMessage.textContent = 'Please fill in all required fields.';
                
                const firstInvalidField = this.querySelector('[required]:invalid');
                if (firstInvalidField) {
                    firstInvalidField.parentNode.insertBefore(errorMessage, firstInvalidField.nextSibling);
                    
                    // Remove message after 5 seconds
                    setTimeout(() => {
                        errorMessage.remove();
                    }, 5000);
                }
            }
        });
    });
    
    // Add shake animation to CSS
    const style = document.createElement('style');
    style.textContent = `
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        .animate-shake {
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        }
    `;
    document.head.appendChild(style);
    
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add floating animation to elements with .float class
    const floatElements = document.querySelectorAll('.float');
    floatElements.forEach(el => {
        el.style.animationDelay = `${Math.random() * 2}s`;
    });
    
    // Add pulse animation to elements with .pulse-gold class
    const pulseElements = document.querySelectorAll('.pulse-gold');
    pulseElements.forEach(el => {
        el.style.animationDelay = `${Math.random() * 2}s`;
    });
    
    // Dynamic year in footer
    const yearElement = document.querySelector('#current-year');
    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }
});