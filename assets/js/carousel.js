document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    if (!carousel) return;

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