document.addEventListener('DOMContentLoaded', () => {
    // Countdown Timer
    const eventDate = new Date('2026-02-14T05:00:00').getTime();

    const updateCountdown = () => {
        const now = new Date().getTime();
        const distance = eventDate - now;

        if (distance < 0) {
            document.getElementById('countdown').innerHTML = "<div class='text-4xl font-bold gradient-text'>EVENT STARTED</div>";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('days').innerText = String(days).padStart(2, '0');
        document.getElementById('hours').innerText = String(hours).padStart(2, '0');
        document.getElementById('minutes').innerText = String(minutes).padStart(2, '0');
        document.getElementById('seconds').innerText = String(seconds).padStart(2, '0');
    };

    setInterval(updateCountdown, 1000);
    updateCountdown();

    // Scroll Animation Observer
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('visible');
                }, index * 100);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up').forEach(el => {
        observer.observe(el);
    });

    // Navbar Scroll Effect
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-white', 'shadow-lg', 'text-gray-800', 'scrolled');
            navbar.classList.remove('bg-transparent', 'text-white');
        } else {
            navbar.classList.remove('bg-white', 'shadow-lg', 'text-gray-800', 'scrolled');
            navbar.classList.add('bg-transparent', 'text-white');
        }
    });

    // Mobile Menu Toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
            }
        });

        // Close menu when clicking on menu items
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
    }

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Parallax Effect for Hero Section
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const hero = document.querySelector('.hero-bg');
        if (hero) {
            hero.style.transform = `translateY(${scrolled * 0.5}px)`;
        }
    });

    // Add Running Icon Animation
    const addRunningIcon = () => {
        const icon = document.createElement('div');
        icon.className = 'runner-animation';
        icon.innerHTML = '🏃';
        document.body.appendChild(icon);

        setTimeout(() => {
            icon.remove();
        }, 15000);
    };

    // Trigger running icon every 20 seconds
    setInterval(addRunningIcon, 20000);
    addRunningIcon();

    // Category Cards Hover Effect
    const categoryCards = document.querySelectorAll('#categories .bg-white');
    categoryCards.forEach(card => {
        card.classList.add('category-card');
        
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.05)';
            this.style.boxShadow = '0 20px 40px rgba(227, 6, 19, 0.3)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
        });
    });

    // Add Confetti Effect on CTA buttons
    const ctaButtons = document.querySelectorAll('.sparkle-btn');
    ctaButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            createConfetti(e.pageX, e.pageY);
        });
    });

    function createConfetti(x, y) {
        const colors = ['#E30613', '#FFD700', '#8B0000', '#FF6B00'];
        for (let i = 0; i < 20; i++) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                width: 8px;
                height: 8px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${x}px;
                top: ${y}px;
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                animation: confettiFall ${Math.random() * 2 + 1}s ease-out forwards;
            `;
            document.body.appendChild(confetti);

            const angle = Math.random() * Math.PI * 2;
            const velocity = Math.random() * 200 + 100;
            const vx = Math.cos(angle) * velocity;
            const vy = Math.sin(angle) * velocity - 200;

            confetti.style.setProperty('--vx', `${vx}px`);
            confetti.style.setProperty('--vy', `${vy}px`);

            setTimeout(() => confetti.remove(), 2000);
        }
    }

    // Add CSS for confetti animation
    if (!document.getElementById('confetti-style')) {
        const style = document.createElement('style');
        style.id = 'confetti-style';
        style.textContent = `
            @keyframes confettiFall {
                0% {
                    transform: translate(0, 0) rotate(0deg);
                    opacity: 1;
                }
                100% {
                    transform: translate(var(--vx), calc(var(--vy) + 500px)) rotate(720deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }

    // Add Number Counter Animation
    const animateCounter = (element, target, duration = 2000) => {
        let start = 0;
        const increment = target / (duration / 16);
        const timer = setInterval(() => {
            start += increment;
            if (start >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(start);
            }
        }, 16);
    };

    // Add Energy Pulse to Register Section
    const registerSection = document.getElementById('register');
    if (registerSection) {
        registerSection.classList.add('energy-wave');
    }

    // Add Medal Shine to Categories
    document.querySelectorAll('#categories .bg-white').forEach(card => {
        const icon = card.querySelector('.rounded-full');
        if (icon) {
            icon.classList.add('medal-shine', 'glow');
        }
    });

    // Countdown boxes floating effect
    document.querySelectorAll('#countdown > div').forEach((box, index) => {
        box.style.animation = `floating 3s ease-in-out ${index * 0.2}s infinite`;
    });

    // Add Progress Line to Schedule
    const scheduleLine = document.querySelector('#schedule .border-l-4');
    if (scheduleLine) {
        scheduleLine.classList.add('progress-line');
    }

    // Track cursor for special effects (optional)
    let lastX = 0, lastY = 0;
    document.addEventListener('mousemove', (e) => {
        lastX = e.pageX;
        lastY = e.pageY;
    });

    // Add sparkle trail to logo
    const logo = document.querySelector('nav img');
    if (logo) {
        logo.classList.add('glow');
    }

    console.log('✨ Phichai Run 2026 - All effects loaded!');
});
