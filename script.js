document.addEventListener('DOMContentLoaded', () => {
    const body = document.body;
    const menuToggle = document.getElementById('menuToggle');
    const navWrap = document.getElementById('navWrap');
    const themeToggle = document.getElementById('themeToggle');
    const scrollTopBtn = document.getElementById('scrollTopBtn');

    if (menuToggle && navWrap) {
        menuToggle.addEventListener('click', () => {
            navWrap.classList.toggle('open');
        });
    }

    const savedTheme = localStorage.getItem('cornerstone-theme');
    if (savedTheme === 'dark') {
        body.classList.add('dark-theme');
        if (themeToggle) themeToggle.innerHTML = '<i class="fa-solid fa-sun"></i>';
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const isDark = body.classList.contains('dark-theme');
            localStorage.setItem('cornerstone-theme', isDark ? 'dark' : 'light');
            themeToggle.innerHTML = isDark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
        });
    }

    window.addEventListener('scroll', () => {
        if (!scrollTopBtn) return;
        scrollTopBtn.classList.toggle('show', window.scrollY > 300);
    });

    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    const filterButtons = document.querySelectorAll('[data-filter]');
    const searchInput = document.getElementById('menuSearch');
    const foodCards = document.querySelectorAll('.menu-filter-card');

    const applyFilters = () => {
        const activeCategory = document.querySelector('[data-filter].active')?.dataset.filter || 'all';
        const searchValue = (searchInput?.value || '').trim().toLowerCase();

        foodCards.forEach((card) => {
            const category = card.dataset.category.toLowerCase();
            const text = card.dataset.search.toLowerCase();
            const categoryMatch = activeCategory === 'all' || category === activeCategory;
            const searchMatch = searchValue === '' || text.includes(searchValue);
            card.classList.toggle('hidden', !(categoryMatch && searchMatch));
        });
    };

    filterButtons.forEach((button) => {
        button.addEventListener('click', () => {
            filterButtons.forEach((btn) => btn.classList.remove('active'));
            button.classList.add('active');
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    const track = document.querySelector('.testimonial-track');
    if (track) {
        const cards = Array.from(track.children);
        let index = 0;

        const moveSlider = () => {
            const firstCard = cards[0];
            if (!firstCard) return;
            const gap = 16;
            const step = firstCard.getBoundingClientRect().width + gap;
            const visibleCount = window.innerWidth <= 820 ? 1 : (window.innerWidth <= 1080 ? 2 : 3);
            const maxIndex = Math.max(cards.length - visibleCount, 0);
            index = index >= maxIndex ? 0 : index + 1;
            track.style.transform = `translateX(-${index * step}px)`;
        };

        if (cards.length > 1) {
            setInterval(moveSlider, 3500);
        }
    }

    const qrBox = document.getElementById('menuQrCode');
    if (qrBox && typeof QRCode !== 'undefined') {
        const menuUrl = qrBox.dataset.url;
        qrBox.innerHTML = '';
        new QRCode(qrBox, {
            text: menuUrl,
            width: 180,
            height: 180,
            colorDark: '#111827',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H,
        });
    }

    const orderItemSelect = document.getElementById('food_item');
    const orderItemLabel = document.getElementById('selectedFoodLabel');
    if (orderItemSelect && orderItemLabel) {
        const syncFoodLabel = () => {
            orderItemLabel.textContent = orderItemSelect.options[orderItemSelect.selectedIndex]?.text || 'Choose meal';
        };
        orderItemSelect.addEventListener('change', syncFoodLabel);
        syncFoodLabel();
    }
});
