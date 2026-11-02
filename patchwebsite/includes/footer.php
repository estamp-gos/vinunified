<?php
// /patchwebsite/includes/footer.php

// Include security for CSRF function
require_once __DIR__ . '/security.php';
?>

<!-- ============================================
     PRE-FOOTER: Contact + CTA Section
     ============================================ -->
<section class="pre-footer">
    <div class="container">
        <div class="row g-0">
            <!-- Left Side: Contact Form (70%) -->
            <div class="col-lg-8 pre-footer-form">
                <div class="pre-footer-content">
                    <h2>Get In <span>Touch</span></h2>
                    <p class="text-muted">Have a project in mind? Let's talk about your design needs.</p>
                    
                    <form method="POST" action="<?php echo SITE_URL; ?>contact" enctype="multipart/form-data">
                        <?php echo csrfField(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Your name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Your email" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (800) 123-4567">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="4" placeholder="Your message..." required></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Attach File (optional)</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar">
                                <small class="text-muted">Max 50MB. Supported: JPG, PNG, PDF, DOC, ZIP, RAR</small>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-hero-primary w-100">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">* Required fields. We'll respond within 24 hours.</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Right Side: CTA Box (30%) -->
            <div class="col-lg-4 pre-footer-cta">
                <div class="pre-footer-cta-content">
                    <div class="cta-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3>Ready to <span>Start?</span></h3>
                    <p>Get your embroidery digitized, vector traced, or custom patches designed today!</p>
                    <a href="<?php echo SITE_URL; ?>register" class="btn-cta-primary">
                        <i class="fas fa-arrow-right"></i> Order Now
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<br>
<br>

<!-- ============================================
     FOOTER
     ============================================ -->
<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <!-- Brand -->
            <div class="col-lg-4 col-md-6">
                <img src="<?php echo SITE_URL; ?>assets/images/logo-vertical.png" alt="<?php echo SITE_NAME; ?>" height="50">
                <p class="mt-3"><?php echo SITE_TAGLINE; ?></p>
                <p class="text-muted small"><i class="fas fa-map-marker-alt text-cyan"></i> <?php echo SITE_ADDRESS; ?></p>
                <div class="social">
                    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank"><i class="fab fa-facebook"></i></a>
                    <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank"><i class="fab fa-youtube"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-linkedin"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h5>Quick Links</h5>
                <a href="<?php echo SITE_URL; ?>services">Services</a>
                <a href="<?php echo SITE_URL; ?>portfolio">Portfolio</a>
                <a href="<?php echo SITE_URL; ?>pricing">Pricing</a>
                <a href="<?php echo SITE_URL; ?>faq">FAQ</a>
                <a href="<?php echo SITE_URL; ?>blog">Blog</a>
                <a href="<?php echo SITE_URL; ?>contact">Contact</a>
            </div>
            
            <!-- Contact -->
            <div class="col-lg-3 col-md-6">
                <h5>Contact Us</h5>
                <p><i class="fas fa-envelope text-cyan"></i> <?php echo SITE_EMAIL; ?></p>
                <p><i class="fas fa-phone text-cyan"></i> <?php echo SITE_PHONE; ?></p>
                <p><i class="fas fa-phone text-cyan"></i> <?php echo SITE_PHONE_USA_ALT; ?></p>
            </div>
            
            <!-- File Formats & Payment Methods -->
            <div class="col-lg-3 col-md-6">
                <h5>File Formats We Support</h5>
                <div class="file-formats">
                    <span class="badge-format">JPG</span>
                    <span class="badge-format">PNG</span>
                    <span class="badge-format">PDF</span>
                    <span class="badge-format">AI</span>
                    <span class="badge-format">EPS</span>
                    <span class="badge-format">PSD</span>
                    <span class="badge-format">DST</span>
                    <span class="badge-format">PES</span>
                    <span class="badge-format">EMB</span>
                    <span class="badge-format">CND</span>
                    <span class="badge-format">ZIP</span>
                    <span class="badge-format">RAR</span>
                </div>
                <h5 class="mt-3">Payment Methods</h5>
                <div class="payment-methods">
                    <span class="badge-payment"><i class="fas fa-credit-card"></i> Credit Card</span>
                    <span class="badge-payment"><i class="fab fa-cc-paypal"></i> PayPal</span>
                    <span class="badge-payment"><i class="fas fa-university"></i> Wire Transfer</span>
                    <span class="badge-payment"><i class="fab fa-cc-stripe"></i> Stripe</span>
                </div>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom text-center">
            &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo SITE_URL; ?>assets/js/custom.js"></script>
<?php if (isset($includeSlider) && $includeSlider): ?>
<script src="<?php echo SITE_URL; ?>assets/js/slider.js"></script>
<?php endif; ?>
</body>
</html>