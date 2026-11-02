<?php
// thankyou.php - VinUnified Thank You Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Purchase | VinUnified</title>
    <meta name="description" content="Thank You for Your Purchase! Your order has been successfully received. Your Vehicle History Report will be delivered to your registered email address within 25–45 minutes.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon.png">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        /* ============================================ */
        /* THANK YOU PAGE THEME */
        /* ============================================ */
        .thankyou-section {
            background: linear-gradient(135deg, #F8F9FF 0%, #F0E6FF 100%);
            padding: 30px 0 65px;
            position: relative;
            overflow: hidden;
        }

        .ambient-shape-1 {
            position: absolute;
            top: -100px;
            right: -100px;
            width: 450px;
            height: 450px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(108, 99, 255, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
            z-index: 1;
        }

        .ambient-shape-2 {
            position: absolute;
            bottom: 40px;
            left: -120px;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 180, 216, 0.12) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
            z-index: 1;
        }

        .thankyou-content {
            position: relative;
            z-index: 2;
            max-width: 760px;
            margin: 30px auto 0;
            width: 100%;
        }

        .thankyou-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 24px;
            padding: 45px 40px;
            box-shadow: 0 20px 45px rgba(3, 4, 94, 0.08), 0 8px 20px rgba(108, 99, 255, 0.04);
            border: 1px solid rgba(108, 99, 255, 0.12);
            text-align: center;
        }

        /* Celebratory Checkmark Icon */
        .success-icon-badge {
            width: 80px;
            height: 80px;
            margin: 0 auto 18px;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 28px rgba(16, 185, 129, 0.35);
            position: relative;
        }

        .success-icon-badge::after {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2px dashed rgba(16, 185, 129, 0.35);
            animation: spinRing 20s linear infinite;
        }

        @keyframes spinRing {
            to { transform: rotate(360deg); }
        }

        .success-icon-badge i {
            font-size: 38px;
            color: #FFFFFF;
        }

        .order-confirmed-pill {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #E8F8F0;
            color: #059669;
            font-weight: 600;
            font-size: 13px;
            padding: 5px 16px;
            border-radius: 50px;
            margin-bottom: 14px;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .thankyou-card h1 {
            font-size: 34px;
            font-weight: 800;
            color: var(--blue-dark);
            line-height: 1.25;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .thankyou-subtitle {
            font-size: 18px;
            color: #4B5563;
            font-weight: 500;
            margin-bottom: 28px;
        }

        /* Order Progress Tracker */
        .order-progress-box {
            background: #F8F9FE;
            border: 1px solid #ECECF8;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 26px;
        }

        .order-progress-steps {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .prog-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
            z-index: 2;
        }

        .prog-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            background: white;
            border: 2px solid #D1D5DB;
            color: #9CA3AF;
            transition: all 0.3s;
        }

        .prog-step.done .prog-circle {
            background: #10B981;
            border-color: #10B981;
            color: white;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
        }

        .prog-step.processing .prog-circle {
            background: var(--purple-primary);
            border-color: var(--purple-primary);
            color: white;
            box-shadow: 0 4px 12px rgba(108, 99, 255, 0.3);
        }

        .prog-title {
            font-size: 12px;
            font-weight: 600;
            color: #6B7280;
            text-align: center;
            line-height: 1.3;
        }

        .prog-step.done .prog-title {
            color: #059669;
        }

        .prog-step.processing .prog-title {
            color: var(--purple-primary);
        }

        .prog-connector {
            height: 3px;
            flex-grow: 1;
            background: #E5E7EB;
            margin: 0 -8px 24px;
            z-index: 1;
        }

        .prog-connector.active {
            background: linear-gradient(90deg, #10B981, var(--purple-primary));
        }

        /* Delivery Time Box */
        .delivery-notice-box {
            display: flex;
            align-items: center;
            text-align: left;
            gap: 18px;
            background: linear-gradient(135deg, rgba(108, 99, 255, 0.05), rgba(0, 180, 216, 0.07));
            border: 1.5px solid rgba(108, 99, 255, 0.2);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 20px;
        }

        .delivery-notice-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            background: linear-gradient(135deg, var(--purple-primary), var(--blue-primary));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 8px 18px rgba(108, 99, 255, 0.25);
        }

        .delivery-notice-text h4 {
            font-size: 16px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 4px;
        }

        .delivery-notice-text p {
            font-size: 15px;
            color: #4B5563;
            line-height: 1.5;
            margin: 0;
        }

        .delivery-highlight-tag {
            background: var(--purple-light);
            color: var(--purple-primary);
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 14px;
        }

        /* Spam / Junk Alert Callout */
        .spam-alert-box {
            display: flex;
            align-items: center;
            text-align: left;
            gap: 15px;
            background: #FFFBEB;
            border: 1.5px solid #FCD34D;
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .spam-alert-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            background: #F59E0B;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .spam-alert-text {
            font-size: 14px;
            color: #92400E;
            line-height: 1.5;
        }

        .spam-alert-text strong {
            color: #78350F;
        }

        /* Need Help Support Card */
        .help-support-box {
            background: white;
            border: 1.5px solid #E5E7EB;
            border-radius: 18px;
            padding: 24px 22px;
            margin-bottom: 22px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        }

        .help-support-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--purple-light);
            color: var(--purple-primary);
            font-size: 12px;
            font-weight: 700;
            padding: 3px 12px;
            border-radius: 50px;
            margin-bottom: 10px;
            letter-spacing: 0.4px;
        }

        .help-support-box h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--blue-dark);
            margin-bottom: 6px;
        }

        .help-support-box p {
            font-size: 14.5px;
            color: var(--gray);
            margin-bottom: 16px;
        }

        .support-email-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--blue-dark);
            color: white !important;
            font-size: 15.5px;
            font-weight: 600;
            padding: 12px 26px;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 6px 16px rgba(3, 4, 94, 0.18);
        }

        .support-email-pill:hover {
            background: var(--purple-primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 22px rgba(108, 99, 255, 0.3);
        }

        .support-email-pill i {
            color: #00B4D8;
            font-size: 17px;
        }

        /* Thank you closing note */
        .store-closing-text {
            font-size: 16px;
            font-weight: 600;
            color: var(--blue-dark);
            margin-bottom: 25px;
        }

        .store-closing-text span {
            color: var(--purple-primary);
            font-weight: 700;
        }

        /* Buttons */
        .thankyou-btn-row {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-thankyou-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--purple-primary), var(--blue-primary));
            color: white !important;
            padding: 13px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(108, 99, 255, 0.28);
            transition: all 0.3s ease;
        }

        .btn-thankyou-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(108, 99, 255, 0.38);
        }

        .btn-thankyou-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--blue-dark) !important;
            border: 1.5px solid #E5E7EB;
            padding: 13px 28px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-thankyou-outline:hover {
            border-color: var(--purple-primary);
            color: var(--purple-primary) !important;
            background: var(--purple-light);
            transform: translateY(-2px);
        }

        /* Trust Stats Bar */
        .trust-stats-row {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }

        .trust-stat-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13.5px;
            color: #4B5563;
            font-weight: 500;
        }

        .trust-stat-item i {
            color: var(--purple-primary);
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .thankyou-card {
                padding: 35px 20px;
                border-radius: 20px;
            }

            .thankyou-card h1 {
                font-size: 26px;
            }

            .thankyou-subtitle {
                font-size: 15px;
                margin-bottom: 20px;
            }

            .order-progress-box {
                padding: 16px 12px;
            }

            .prog-circle {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }

            .prog-title {
                font-size: 10.5px;
            }

            .delivery-notice-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                padding: 16px 18px;
            }

            .delivery-notice-icon {
                width: 44px;
                height: 44px;
                min-width: 44px;
                font-size: 18px;
            }

            .spam-alert-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 14px 16px;
            }

            .support-email-pill {
                width: 100%;
                justify-content: center;
                font-size: 14px;
                padding: 12px 16px;
            }

            .thankyou-btn-row {
                flex-direction: column;
            }

            .btn-thankyou-primary, .btn-thankyou-outline {
                width: 100%;
                justify-content: center;
            }

            .trust-stats-row {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
        }
    </style>
</head>

<body>
    <!-- Top Hero Section Containing Navigation and Thank You Content -->
    <section class="thankyou-section">
        <div class="ambient-shape-1"></div>
        <div class="ambient-shape-2"></div>

        <div class="container">
            <!-- Navigation (Exact Home Page Theme) -->
            <nav class="navbar">
                <div class="logo">
                    <a href="index.php" style="display: flex; align-items: center;">
                        <img src="assets/images/vinunified-nav.png" alt="VinUnified Logo" class="logo-img">
                    </a>
                </div>
                
                <button class="hamburger" id="hamburgerBtn" aria-label="Toggle navigation menu">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="nav-links" id="navLinks">
                    <a href="index.php">Home</a>
                    <a href="index.php#how-it-works">How It Works</a>
                    <a href="index.php#pricing">Pricing</a>
                    <a href="sample-report.php">Sample Report</a>
                    <a href="index.php#faq">FAQ</a>
                </div>
            </nav>
            <div class="menu-overlay" id="menuOverlay"></div>

            <!-- Main Thank You Card Content -->
            <div class="thankyou-content">
                <div class="thankyou-card">
                    
                    <!-- Checkmark Icon with Spinning Ring -->
                    <div class="success-icon-badge">
                        <i class="fas fa-check"></i>
                    </div>

                    <!-- Status Pill -->
                    <div>
                        <span class="order-confirmed-pill">
                            <i class="fas fa-circle-check"></i> Order Confirmed
                        </span>
                    </div>

                    <!-- Heading -->
                    <h1>Thank You for Your <span class="gradient-text">Purchase!</span></h1>
                    <p class="thankyou-subtitle">Your order has been successfully received.</p>

                    <!-- Order Progress Stepper -->
                    <div class="order-progress-box">
                        <div class="order-progress-steps">
                            <div class="prog-step done">
                                <div class="prog-circle"><i class="fas fa-check"></i></div>
                                <div class="prog-title">Order Received</div>
                            </div>
                            <div class="prog-connector active"></div>
                            <div class="prog-step processing">
                                <div class="prog-circle"><i class="fas fa-rotate fa-spin"></i></div>
                                <div class="prog-title">Generating Report</div>
                            </div>
                            <div class="prog-connector"></div>
                            <div class="prog-step">
                                <div class="prog-circle"><i class="fas fa-envelope"></i></div>
                                <div class="prog-title">Delivery to Email</div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Notice Box -->
                    <div class="delivery-notice-box">
                        <div class="delivery-notice-icon">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>
                        <div class="delivery-notice-text">
                            <h4>Delivery Window: <span class="delivery-highlight-tag">25–45 Minutes</span></h4>
                            <p>Your Vehicle History Report will be delivered to your registered email address within <strong>25–45 minutes</strong>.</p>
                        </div>
                    </div>

                    <!-- Spam / Junk Notice Box -->
                    <div class="spam-alert-box">
                        <div class="spam-alert-icon">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="spam-alert-text">
                            Please check your inbox and don't forget to check your <strong>Spam/Junk folder</strong>, as the email may occasionally land there.
                        </div>
                    </div>

                    <!-- Need Help Support Card -->
                    <div class="help-support-box">
                        <span class="help-support-badge"><i class="fas fa-headset"></i> 24/7 Support</span>
                        <h3>Need Help?</h3>
                        <p>If you haven't received your report within the expected time, please contact our support team:</p>
                        <a href="mailto:car.check.store@gmail.com" class="support-email-pill">
                            <i class="fas fa-envelope"></i>
                            <span>car.check.store@gmail.com</span>
                        </a>
                    </div>

                    <!-- Brand Closing -->
                    <div class="store-closing-text">
                        <i class="fas fa-shield-heart" style="color: #6C63FF; margin-right: 6px;"></i> Thank you for choosing <span>vinunified Store</span>!
                    </div>

                    <!-- Action Buttons -->
                    <div class="thankyou-btn-row">
                        <a href="index.php" class="btn-thankyou-primary">
                            <i class="fas fa-arrow-left"></i> Back to Home
                        </a>
                        <a href="sample-report.php" class="btn-thankyou-outline">
                            <i class="fas fa-file-invoice"></i> View Sample Report
                        </a>
                    </div>

                </div>

                <!-- Trust Stats Row -->
                <div class="trust-stats-row">
                    <div class="trust-stat-item">
                        <i class="fas fa-lock"></i>
                        <span>256-Bit SSL Encryption</span>
                    </div>
                    <div class="trust-stat-item">
                        <i class="fas fa-database"></i>
                        <span>70+ Data Sources</span>
                    </div>
                    <div class="trust-stat-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>30-Day Data Deletion</span>
                    </div>
                    <div class="trust-stat-item">
                        <i class="fas fa-envelope-circle-check"></i>
                        <span>Direct Email Delivery</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer (Matching Home Page) -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="assets/images/vinunified-footer.png" alt="VinUnified Logo" class="footer-logo-img">
                </div>
                <div class="footer-links">
                    <a href="index.php">Home</a>
                    <a href="sample-report.php">Sample Report</a>
                    <a href="legal.php">Privacy Policy</a>
                    <a href="legal.php">Terms</a>
                    <a href="legal.php">Refund</a>
                </div>
                <div class="footer-contact">
                    <i class="fas fa-envelope"></i> car.check.store@gmail.com
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 VinUnified.store | All Rights Reserved</p>
            </div>
        </div>
    </footer>

    <!-- Mobile Navigation Script -->
    <script>
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
    })();
    </script>
</body>
</html>
