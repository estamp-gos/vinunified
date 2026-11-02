// assets/js/script.js

document.addEventListener('DOMContentLoaded', function() {
    
    // ========== MULTI-STEP FORM ==========
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const form = document.getElementById('multiStepForm');
    
    if (nextBtn && prevBtn && step1 && step2) {
        nextBtn.addEventListener('click', function() {
            // Validate Step 1 fields
            const fullName = document.querySelector('input[name="full_name"]').value;
            const email = document.querySelector('input[name="email"]').value;
            const phone = document.querySelector('input[name="phone"]').value;
            const vin = document.querySelector('input[name="vin"]').value;
            const plate = document.querySelector('input[name="license_plate"]').value;
            const vehicleType = document.querySelector('select[name="vehicle_type"]').value;
            
            if (!fullName) { alert('Please enter your full name'); return; }
            if (!email || !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) { alert('Please enter a valid email'); return; }
            if (!phone || phone.replace(/\D/g, '').length < 8) { alert('Please enter a valid phone number'); return; }
            if (!vin && !plate) { alert('Please enter either VIN or License Plate'); return; }
            if (!vehicleType) { alert('Please select vehicle type'); return; }
            
            step1.classList.remove('active-step');
            step1.style.display = 'none';
            step2.classList.add('active-step');
            step2.style.display = 'block';
        });
        
        prevBtn.addEventListener('click', function() {
            step2.classList.remove('active-step');
            step2.style.display = 'none';
            step1.classList.add('active-step');
            step1.style.display = 'block';
        });
        
        step2.style.display = 'none';
    }
    
    // ========== SCROLL TO FORM WHEN PRICING BUTTON CLICKED ==========
    document.querySelectorAll('.btn-price, .btn-cta').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const formSection = document.querySelector('.form-card');
            if (formSection) {
                formSection.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // ========== FAQ TOGGLE ==========
    document.querySelectorAll('.faq-card').forEach(card => {
        const question = card.querySelector('.faq-question');
        question.addEventListener('click', () => {
            card.classList.toggle('active');
        });
    });
    
    // ========== PACKAGE CARD SELECTION ==========
    document.querySelectorAll('.package-card').forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        });
    });
    
    // ========== SWIPER JS FOR TESTIMONIALS ==========
    if (typeof Swiper !== 'undefined') {
        new Swiper('.testimonials-slider', {
            loop: true,
            autoplay: { delay: 4000 },
            pagination: { el: '.swiper-pagination', clickable: true },
            slidesPerView: 1,
            breakpoints: {
                768: { slidesPerView: 2 },
                1024: { slidesPerView: 3 }
            }
        });
    }
    
    // ========== NAVBAR SCROLL EFFECT ==========
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.background = 'white';
            navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.05)';
            navbar.style.padding = '15px 0';
        } else {
            navbar.style.background = 'transparent';
            navbar.style.boxShadow = 'none';
            navbar.style.padding = '20px 0';
        }
    });
    
    // ========== FORM VALIDATION ON SUBMIT ==========
    form?.addEventListener('submit', function(e) {
        const packageSelected = document.querySelector('input[name="package"]:checked');
        const agreement = document.querySelector('input[name="agreement"]');
        
        if (!packageSelected) {
            e.preventDefault();
            alert('Please select a package');
            return false;
        }
        
        if (!agreement?.checked) {
            e.preventDefault();
            alert('Please agree to the terms and conditions');
            return false;
        }
        
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
    });
});