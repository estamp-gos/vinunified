// /patchwebsite/assets/js/slider.js

document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('beforeAfterSlider');
    if (!slider) return;

    const handle = document.getElementById('sliderHandle');
    const afterImage = slider.querySelector('.after-image');
    const beforeImage = slider.querySelector('img');

    if (!handle || !afterImage) return;

    let isDragging = false;
    let isTouchDevice = false;

    // ---------- Update slider position ----------
    function updateSlider(x) {
        const rect = slider.getBoundingClientRect();
        let position = ((x - rect.left) / rect.width) * 100;
        position = Math.max(0, Math.min(100, position));
        
        afterImage.style.clipPath = `inset(0 ${100 - position}% 0 0)`;
        handle.style.left = position + '%';
        
        // Update handle aria-valuenow for accessibility
        handle.setAttribute('aria-valuenow', Math.round(position));
    }

    // ---------- Mouse events ----------
    handle.addEventListener('mousedown', function(e) {
        isDragging = true;
        e.preventDefault();
        handle.style.cursor = 'grabbing';
        slider.classList.add('dragging');
    });

    document.addEventListener('mousemove', function(e) {
        if (isDragging) {
            updateSlider(e.clientX);
        }
    });

    document.addEventListener('mouseup', function() {
        if (isDragging) {
            isDragging = false;
            handle.style.cursor = 'ew-resize';
            slider.classList.remove('dragging');
        }
    });

    // ---------- Touch events ----------
    handle.addEventListener('touchstart', function(e) {
        isDragging = true;
        isTouchDevice = true;
        e.preventDefault();
        slider.classList.add('dragging');
    });

    document.addEventListener('touchmove', function(e) {
        if (isDragging) {
            const touch = e.touches[0];
            if (touch) {
                updateSlider(touch.clientX);
            }
        }
    }, { passive: true });

    document.addEventListener('touchend', function() {
        if (isDragging) {
            isDragging = false;
            slider.classList.remove('dragging');
        }
    });

    // ---------- Click on slider to move ----------
    slider.addEventListener('click', function(e) {
        // Don't trigger if clicked on handle
        if (e.target === handle || e.target.closest('.slider-handle')) return;
        // Don't trigger if dragging
        if (isDragging) return;
        updateSlider(e.clientX);
    });

    // ---------- Keyboard accessibility ----------
    handle.setAttribute('role', 'slider');
    handle.setAttribute('aria-valuemin', '0');
    handle.setAttribute('aria-valuemax', '100');
    handle.setAttribute('aria-valuenow', '50');
    handle.setAttribute('aria-label', 'Before and after comparison slider');

    handle.addEventListener('keydown', function(e) {
        const rect = slider.getBoundingClientRect();
        let currentPosition = parseFloat(handle.style.left) || 50;
        let step = 2;

        if (e.key === 'ArrowRight' || e.key === 'ArrowUp') {
            e.preventDefault();
            currentPosition = Math.min(100, currentPosition + step);
            const x = rect.left + (currentPosition / 100) * rect.width;
            updateSlider(x);
        } else if (e.key === 'ArrowLeft' || e.key === 'ArrowDown') {
            e.preventDefault();
            currentPosition = Math.max(0, currentPosition - step);
            const x = rect.left + (currentPosition / 100) * rect.width;
            updateSlider(x);
        } else if (e.key === 'Home') {
            e.preventDefault();
            const x = rect.left;
            updateSlider(x);
        } else if (e.key === 'End') {
            e.preventDefault();
            const x = rect.right;
            updateSlider(x);
        }
    });

    // ---------- Responsive: Recalculate on resize ----------
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Reset position to center on resize
            const rect = slider.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            updateSlider(centerX);
        }, 200);
    });

    // ---------- Initialize slider to center ----------
    setTimeout(function() {
        const rect = slider.getBoundingClientRect();
        if (rect.width > 0) {
            const centerX = rect.left + rect.width / 2;
            updateSlider(centerX);
        }
    }, 100);

    // ---------- Double click to reset to center ----------
    slider.addEventListener('dblclick', function(e) {
        const rect = slider.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        updateSlider(centerX);
    });

    // ---------- Touch device detection for better UX ----------
    if ('ontouchstart' in window || navigator.maxTouchPoints > 0) {
        isTouchDevice = true;
        // Make handle larger for touch devices
        handle.style.width = '40px';
        handle.style.height = '40px';
        handle.style.background = 'var(--cyan, #00F0FF)';
        handle.style.borderRadius = '50%';
        handle.style.transform = 'translateX(-50%)';
        handle.style.boxShadow = '0 0 30px rgba(0, 240, 255, 0.5)';
        
        // Add touch instruction
        const instruction = document.createElement('p');
        instruction.className = 'text-center text-muted mt-2 small';
        instruction.innerHTML = '👆 Drag or tap to compare before & after';
        instruction.style.fontSize = '13px';
        slider.parentNode.insertBefore(instruction, slider.nextSibling);
    }

    console.log('✅ Before/After Slider initialized');
});