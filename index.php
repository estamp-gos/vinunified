<?php
// index.php - VinUnified.store (Complete with Multi-Step, IP Detection, All Sections)
session_start();
require_once 'inc/country-detection.php';
$userCountry = getUserCountry();
$pricing = getPricingByCountry($userCountry);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VinUnified | Global Vehicle History Reports</title>
    <meta name="description" content="Get complete vehicle history reports including accidents, title records, odometer fraud, recalls, and market value. Trusted worldwide.">
    <meta name="keywords" content="VIN check, vehicle history report, car history, VIN lookup, used car check, accident history, title check">
    <meta name="author" content="VinUnified">
    <meta name="robots" content="index, follow">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    
    <!-- Open Graph -->
    <meta property="og:title" content="VinUnified | Global Vehicle History Reports">
    <meta property="og:description" content="Get complete vehicle history reports worldwide. One VIN. Global History.">
    <meta property="og:image" content="https://vinunified.store/assets/images/og-image.jpg">
    <meta property="og:url" content="https://vinunified.store">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="VinUnified">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="VinUnified | Global Vehicle History Reports">
    <meta name="twitter:description" content="Get complete vehicle history reports worldwide.">
    <meta name="twitter:image" content="https://vinunified.store/assets/images/og-image.jpg">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    
    <!-- Swiper JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <!-- Navigation -->
            <nav class="navbar">
                <div class="logo">
                    <img src="assets/images/vinunified-nav.png" alt="VinUnified Logo" class="logo-img">
                </div>
                
                <button class="hamburger" id="hamburgerBtn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="nav-links" id="navLinks">
                    <a href="#how-it-works">How It Works</a>
                    <a href="#pricing">Pricing</a>
                    <a href="#sample-report">Sample Report</a>
                    <a href="#blog">Blog</a>
                    <a href="#faq">FAQ</a>
                </div>
            </nav>
            <div class="menu-overlay" id="menuOverlay"></div>

            <!-- Hero Grid -->
            <div class="hero-grid">
                <div class="hero-left">
                    <div class="hero-badge">
                        <i class="fas fa-globe-americas"></i> Worldwide Vehicle History
                    </div>
                    <h1>
                        Know Before You <span class="gradient-text">Drive</span>
                    </h1>
                    <p class="hero-desc">
                        Get complete vehicle history reports including accidents, title records, 
                        odometer fraud, recalls, and market value. Trusted worldwide.
                    </p>
                    <div class="hero-stats">
                        <div class="stat"><i class="fas fa-database"></i> 70+ Data Sources</div>
                        <div class="stat"><i class="fas fa-check-circle"></i> Comprehensive Records</div>
                        <div class="stat"><i class="fas fa-shield-alt"></i> 30-Day Data Deletion</div>
                    </div>
                </div>
                
                <div class="hero-right">
                    <div class="form-card glassmorphism">
                        <h2>Get Your Report</h2>
                        
                        <!-- Multi-Step Form -->
                        <form action="process.php" method="POST" id="multiStepForm">
                            <!-- Step 1 -->
                            <div id="step1" class="form-step active-step">
                                <div class="step-indicator">Step 1 of 2</div>
                                <div class="form-step-title">Vehicle & Contact Details</div>
                                
                                <div class="input-group">
                                    <i class="fas fa-user"></i>
                                    <input type="text" name="full_name" placeholder="Full Name" required>
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" name="email" placeholder="Email Address" required>
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" name="phone" placeholder="Phone Number" required>
                                </div>
                                <div class="input-row">
                                    <div class="input-group half">
                                        <i class="fas fa-qrcode"></i>
                                        <input type="text" name="vin" placeholder="VIN Number (17 digits)" maxlength="17">
                                    </div>
                                    <div class="input-group half">
                                        <i class="fas fa-search"></i>
                                        <input type="text" name="license_plate" placeholder="License Plate (Optional)">
                                    </div>
                                </div>
                                <div class="input-group">
                                    <i class="fas fa-car"></i>
                                    <select name="vehicle_type" required>
                                        <option value="">Select Vehicle Type</option>
                                        <option value="Car">🚗 Car</option>
                                        <option value="Bike">🏍️ Bike</option>
                                        <option value="Motorcycle">🏍️ Motorcycle</option>
                                        <option value="Truck">🚚 Truck</option>
                                        <option value="SUV">🚙 SUV</option>
                                        <option value="JEEP">🚙 Jeep</option>
                                        <option value="Van">🚐 Van</option>
                                        <option value="CamperVan">🚐 CamperVan</option>
                                        <option value="RV">🚐 RV</option>
                                        <option value="RV (Class A)">🚐 RV (Class A)</option>
                                        <option value="RV (Class B)">🚐 RV (Class B)</option>
                                        <option value="RV (Class C)">🚐 RV (Class C)</option>
                                        <option value="Motorhome">🚐 Motorhome</option>
                                        <option value="CARAVAN">🚐 Caravan</option>
                                        <option value="ATV">🏍️ ATV</option>
                                        <option value="Boat">⛵ Boat</option>
                                        <option value="JETSKI">🌊 Jet Ski</option>
                                        <option value="Trailer">🚛 Trailer</option>
                                        <option value="Fifth wheel">🚛 Fifth Wheel</option>
                                        <option value="Toy hauler">🚛 Toy Hauler</option>
                                    </select>
                                </div>
                                
                                <button type="button" class="btn-next" id="nextBtn">
                                    Continue <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                            
                            <!-- Step 2 -->
                            <div id="step2" class="form-step">
                                <div class="step-indicator">Step 2 of 2</div>
                                <div class="form-step-title">Select Package & Confirm</div>
                                
                                <div class="pricing-selector">
                                    <label>Choose Your Package</label>
                                    <div class="package-cards">
                                        <label class="package-card">
                                            <input type="radio" name="package" value="basic" data-price="£25">
                                            <div class="package-icon"><i class="fas fa-file-alt"></i></div>
                                            <div class="package-name">Basic</div>
                                            <div class="package-price">£25</div>
                                        </label>
                                        <label class="package-card popular">
                                            <div class="popular-tag">Most Popular</div>
                                            <input type="radio" name="package" value="premium" data-price="£55">
                                            <div class="package-icon"><i class="fas fa-crown"></i></div>
                                            <div class="package-name">Premium</div>
                                            <div class="package-price">£55</div>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="checkbox-group">
                                    <label class="checkbox-container">
                                        <input type="checkbox" name="agreement" required>
                                        <span class="checkmark"></span>
                                        <span class="checkbox-text">I confirm all information is accurate and I agree to the Terms of Service and Refund Policy.</span>
                                    </label>
                                </div>
                                
                                <div class="form-buttons">
                                    <button type="button" class="btn-prev" id="prevBtn">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </button>
                                    <button type="submit" class="btn-submit">
                                        <i class="fas fa-search"></i> Get My Report
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="form-footnote">
                            <i class="fas fa-lock"></i> Your data is secure. Deleted after 30 days.
                            <span style="display:block; font-size:11px; margin-top:4px; opacity:0.7;">🔒 Payments powered by Gumroad</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust Badges -->
    <section class="trust-section">
        <div class="container">
            <div class="trust-grid">
                <div class="trust-item"><i class="fas fa-check-circle"></i> High Accuracy Data</div>
                <div class="trust-item"><i class="fas fa-globe"></i> 150+ Countries</div>
                <div class="trust-item"><i class="fas fa-shield"></i> SSL Secure</div>
                <div class="trust-item"><i class="fas fa-trash-alt"></i> Auto Data Deletion</div>
                <div class="trust-item"><i class="fas fa-headset"></i> 24/7 Support</div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="howitworks" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Simple Process</span>
                <h2>Get Your Report in <span class="gradient-text">3 Steps</span></h2>
            </div>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">01</div>
                    <div class="step-icon"><i class="fas fa-pen-alt"></i></div>
                    <h3>Fill Details</h3>
                    <p>Enter VIN, your information</p>
                </div>
                <div class="step-card">
                    <div class="step-number">02</div>
                    <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                    <h3>Complete Payment</h3>
                    <p>Secure payment via Gumroad</p>
                </div>
                <div class="step-card">
                    <div class="step-number">03</div>
                    <div class="step-icon"><i class="fas fa-envelope-open-text"></i></div>
                    <h3>Receive Report</h3>
                    <p>Instant report in your email</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Pricing</span>
                <h2>Choose Your <span class="gradient-text">Plan</span></h2>
            </div>
            <div class="pricing-grid">
                
                <!-- Basic Plan -->
                <div class="price-card">
                    <div class="price-icon"><i class="fas fa-file"></i></div>
                    <h3>Basic</h3>
                    <div class="price-value">£25</div>
                    <ul class="price-features">
                        <li><i class="fas fa-check"></i> Accident History</li>
                        <li><i class="fas fa-check"></i> Title Information</li>
                        <li><i class="fas fa-check"></i> Odometer Verification</li>
                        <li><i class="fas fa-check"></i> Safety Recalls</li>
                        <li><i class="fas fa-check"></i> Vehicle Specifications</li>
                        <li><i class="fas fa-check"></i> Basic Market Value</li>
                        <li><i class="fas fa-check"></i> 24/7 Email Support</li>
                        <li><i class="fas fa-check"></i> Instant PDF Delivery</li>
                    </ul>
                    <a href="https://gumroad.com/checkout?_gl=1*1u1gg8z*_ga*NTQxMDEyMjQ5LjE3OTM2MjE5MzU.*_ga_6LJN6D94N6*czE3OTM2MjE5MzQkbzEkZzEkdDE3OTM2MjE5NzAkajI0JGwwJGgw" class="btn-price">Get Basic</a>
                </div>
                
                <!-- Premium Plan -->
                <div class="price-card popular">
                    <div class="popular-badge">Best Value</div>
                    <div class="price-icon"><i class="fas fa-crown"></i></div>
                    <h3>Premium</h3>
                    <div class="price-value">£55</div>
                    <ul class="price-features">
                        <li><i class="fas fa-check"></i> Everything in Basic</li>
                        <li><i class="fas fa-check"></i> Ownership History (3+ Owners)</li>
                        <li><i class="fas fa-check"></i> Detailed Market Value Analysis</li>
                        <li><i class="fas fa-check"></i> Service & Maintenance Records</li>
                        <li><i class="fas fa-check"></i> Accident Severity Report</li>
                        <li><i class="fas fa-check"></i> Previous Sale Photos</li>
                        <li><i class="fas fa-check"></i> Priority Email Support</li>
                        <li><i class="fas fa-check"></i> 14-Day Free Updates</li>
                    </ul>
                    <a href="https://gumroad.com/checkout?_gl=1*1tzxhkm*_ga*NTQxMDEyMjQ5LjE3OTM2MjE5MzU.*_ga_6LJN6D94N6*czE3OTM2MjE5MzQkbzEkZzEkdDE3OTM2MjE5NTEkajQzJGwwJGgw" class="btn-price">Get Premium</a>
                </div>
                
            </div>
        </div>
    </section>

    <!-- Sample Report Section -->
    <section class="sample-section" id="sample-report">
        <div class="container">
            <div class="sample-content">
                <div class="sample-left">
                    <i class="fas fa-file-pdf"></i>
                    <h2>See What You'll Get</h2>
                    <p>View a complete sample report before purchasing. No hidden surprises.</p>
                    <a href="sample-report.php" class="btn-sample" target="_blank">View Sample Report</a>
                </div>
                <div class="sample-right">
                    <div class="sample-preview">
                        <i class="fas fa-file-alt"></i>
                        <span>Sample Report Preview</span>
                        <small>PDF • 15-20 Pages</small>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Testimonials</span>
                <h2>What Our <span class="gradient-text">Customers Say</span></h2>
            </div>
            <div class="swiper testimonials-slider">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <i class="fas fa-quote-left"></i>
                            <p>"VinUnified saved me from buying a flooded car! The report was detailed and fast."</p>
                            <div class="testimonial-author">
                                <strong>Michael R.</strong>
                                <span>Verified Buyer</span>
                            </div>
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <i class="fas fa-quote-left"></i>
                            <p>"I got the report within minutes. Very professional service. Highly recommend!"</p>
                            <div class="testimonial-author">
                                <strong>Sarah K.</strong>
                                <span>Car Dealer</span>
                            </div>
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-card">
                            <i class="fas fa-quote-left"></i>
                            <p>"The odometer fraud detection saved me $5,000. Worth every penny."</p>
                            <div class="testimonial-author">
                                <strong>David L.</strong>
                                <span>First-time Buyer</span>
                            </div>
                            <div class="testimonial-stars">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="cta-overlay"></div>
        <div class="container">
            <div class="cta-content">
                <i class="fas fa-file-alt"></i>
                <h2>Ready to Know the Truth About Your Next Car?</h2>
                <p>Get a complete vehicle history report today. No hidden fees. Instant delivery.</p>
                <a href="#multiStepForm" class="btn-cta">Get Your Report Now</a>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section" id="blog">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">Latest Articles</span>
                <h2>Car Buying <span class="gradient-text">Tips & Insights</span></h2>
            </div>
            <div class="blog-grid">
                <div class="blog-card">
                    <div class="blog-icon"><i class="fas fa-file-alt"></i></div>
                    <h3>10 Red Flags When Buying a Used Car</h3>
                    <p>Learn the warning signs that could save you thousands of dollars.</p>
                    <a href="#" class="blog-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="blog-card">
                    <div class="blog-icon"><i class="fas fa-chart-line"></i></div>
                    <h3>How to Read a Vehicle History Report</h3>
                    <p>Understanding accident history, title brands, and odometer readings.</p>
                    <a href="#" class="blog-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="blog-card">
                    <div class="blog-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>5 Ways to Avoid Odometer Fraud</h3>
                    <p>Protect yourself from rolled-back mileage scams.</p>
                    <a href="#" class="blog-link">Read More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-content">
                <div class="mission-left">
                    <div class="mission-badge">
                        <i class="fas fa-rocket"></i> Our Mission
                    </div>
                    <h2>Driving <span class="gradient-text">Transparency</span> Worldwide</h2>
                    <p>At VinUnified, our mission is simple: <strong>no more hidden car histories.</strong> We believe that every car buyer, no matter where they are in the world, deserves access to accurate, complete, and affordable vehicle history reports.</p>
                    <p>Whether you're buying your first car or expanding your dealership fleet, we're here to give you the truth. One VIN. Global History.</p>
                </div>
                <div class="mission-right">
                    <div class="future-card">
                        <i class="fas fa-chart-line"></i>
                        <h3>Future Vision</h3>
                        <p>VinUnified is not just a report service. We're building a complete <strong>car classified platform</strong> where buying and selling used cars is safe, transparent, and hassle-free.</p>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> Verified Listings</li>
                            <li><i class="fas fa-check-circle"></i> Instant History Reports</li>
                            <li><i class="fas fa-check-circle"></i> Dealer Tools</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="contact-banner">
                <i class="fas fa-headset"></i>
                <span>Have questions or suggestions? We'd love to hear from you.</span>
                <a href="mailto:support@vinunified.store" class="contact-btn">
                    <i class="fas fa-envelope"></i> Contact Us
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section" id="faq">
        <div class="container">
            <div class="section-header">
                <span class="section-badge">FAQ</span>
                <h2>Frequently Asked <span class="gradient-text">Questions</span></h2>
            </div>
            <div class="faq-grid">
                <div class="faq-card">
                    <div class="faq-question"><i class="fas fa-question-circle"></i><span>What information do I need?</span><i class="fas fa-chevron-down"></i></div>
                    <div class="faq-answer">Name, email, phone, and VIN or license plate.</div>
                </div>
                <div class="faq-card">
                    <div class="faq-question"><i class="fas fa-clock"></i><span>How long does it take?</span><i class="fas fa-chevron-down"></i></div>
                    <div class="faq-answer">Within minutes after payment.</div>
                </div>
                <div class="faq-card">
                    <div class="faq-question"><i class="fas fa-undo"></i><span>What is your refund policy?</span><i class="fas fa-chevron-down"></i></div>
                    <div class="faq-answer">We offer a 14-day satisfaction guarantee. If your report has missing or inaccurate details, contact our support team for a free update or a full refund.</div>
                </div>
                <div class="faq-card">
                    <div class="faq-question"><i class="fas fa-database"></i><span>Is my data safe?</span><i class="fas fa-chevron-down"></i></div>
                    <div class="faq-answer">Yes, auto-deleted after 30 days.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="assets/images/vinunified-footer.png" alt="VinUnified Logo" class="footer-logo-img">
                </div>
                <div class="footer-links">
                    <a href="sample-report.php">Sample Report</a>
                    <a href="legal.php">Privacy Policy</a>
                    <a href="legal.php">Terms</a>
                    <a href="legal.php">Refund</a>
                </div>
                <div class="footer-contact">
                    <i class="fas fa-envelope"></i> support@vinunified.store
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 VinUnified.store | All Rights Reserved</p>
                <p style="margin-top: 8px; font-size: 12px; opacity: 0.8;">
                    <!--🔒 Payments powered by <strong>Gumroad</strong>-->
                </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
    // Mobile Menu
    (function() {
        const hamburger = document.getElementById('hamburgerBtn');
        const navLinks = document.getElementById('navLinks');
        const overlay = document.getElementById('menuOverlay');
        
        if (!hamburger || !navLinks || !overlay) return;
        
        function openMenu() {
            navLinks.classList.add('active');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            hamburger.innerHTML = '<i class="fas fa-times"></i>';
        }
        
        function closeMenu() {
            navLinks.classList.remove('active');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            hamburger.innerHTML = '<i class="fas fa-bars"></i>';
        }
        
        hamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            if (navLinks.classList.contains('active')) {
                closeMenu();
            } else {
                openMenu();
            }
        });
        
        overlay.addEventListener('click', closeMenu);
        
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });
        
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768 && navLinks.classList.contains('active')) {
                closeMenu();
            }
        });
        
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && navLinks.classList.contains('active')) {
                closeMenu();
            }
        });
    })();
    </script>

    <script src="assets/js/script.js"></script>
</body>
</html>