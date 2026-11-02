// /patchwebsite/assets/js/custom.js

document.addEventListener('DOMContentLoaded', function() {
    
    // ---------- Auto-dismiss alerts after 5 seconds ----------
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.classList.add('fade');
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // ---------- Mobile nav close on link click ----------
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');

    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                navbarToggler.click();
            }
        });
    });

    // ---------- Smooth scroll for anchor links ----------
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 80;
                const topBarHeight = document.querySelector('.top-bar')?.offsetHeight || 0;
                const totalOffset = navbarHeight + topBarHeight;
                
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - totalOffset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ---------- Pricing tabs smooth scroll on mobile ----------
    const pricingTabs = document.querySelectorAll('.pricing-tabs .nav-link');
    pricingTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            // Small delay to allow tab content to render
            setTimeout(function() {
                const tabContent = document.querySelector(this.getAttribute('data-bs-target'));
                if (tabContent && window.innerWidth < 768) {
                    const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 80;
                    const topBarHeight = document.querySelector('.top-bar')?.offsetHeight || 0;
                    const totalOffset = navbarHeight + topBarHeight;
                    
                    const targetPosition = tabContent.getBoundingClientRect().top + window.pageYOffset - totalOffset - 20;
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }.bind(this), 300);
        });
    });

    // ---------- Sticky navbar shadow on scroll ----------
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 4px 30px rgba(0, 0, 0, 0.5)';
            } else {
                navbar.style.boxShadow = 'none';
            }
        });
    }

    // ---------- Back to top button (optional) ----------
    // Create back to top button if it doesn't exist
    if (!document.querySelector('.back-to-top')) {
        const backToTop = document.createElement('button');
        backToTop.className = 'back-to-top';
        backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
        backToTop.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--cyan, #00F0FF);
            color: var(--bg-dark, #0D0F12);
            border: none;
            font-size: 20px;
            cursor: pointer;
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 240, 255, 0.3);
        `;
        document.body.appendChild(backToTop);

        window.addEventListener('scroll', function() {
            if (window.scrollY > 500) {
                backToTop.style.opacity = '1';
                backToTop.style.visibility = 'visible';
            } else {
                backToTop.style.opacity = '0';
                backToTop.style.visibility = 'hidden';
            }
        });

        backToTop.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ---------- Active nav link highlight on scroll ----------
    const sections = document.querySelectorAll('section[id]');
    const navLinksAll = document.querySelectorAll('.navbar-nav .nav-link');

    if (sections.length > 0 && navLinksAll.length > 0) {
        window.addEventListener('scroll', function() {
            let current = '';
            const navbarHeight = document.querySelector('.navbar')?.offsetHeight || 80;
            const topBarHeight = document.querySelector('.top-bar')?.offsetHeight || 0;
            const totalOffset = navbarHeight + topBarHeight;

            sections.forEach(function(section) {
                const sectionTop = section.offsetTop - totalOffset - 100;
                if (window.scrollY >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinksAll.forEach(function(link) {
                link.classList.remove('active');
                const href = link.getAttribute('href');
                if (href && href === '#' + current) {
                    link.classList.add('active');
                }
                // For home page
                if (current === '' && link.getAttribute('href') === '/patchwebsite/') {
                    link.classList.add('active');
                }
            });
        });
    }

    // ---------- Form validation enhancement ----------
    const forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(function(field) {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                // Scroll to first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });

        // Remove invalid class on input
        form.querySelectorAll('[required]').forEach(function(field) {
            field.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });
        });
    });

    // ---------- Lazy load images (optional) ----------
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('data-src');
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    // ---------- Console branding (optional) ----------
    console.log('%c 🎨 PunchyArts ', 'background: #00F0FF; color: #0D0F12; font-size: 20px; font-weight: bold; padding: 10px 20px; border-radius: 4px;');
    console.log('%c Premium Embroidery & Vector Tracing Services', 'color: #A0A5B5; font-size: 14px;');
    console.log('%c Based in USA 🇺🇸', 'color: #FF0055; font-size: 12px;');
});