<?php
// /patchwebsite/customerpanel/new-order.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$pageTitle = 'New Order';
$currentPage = 'new-order.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="welcome-section">
        <div>
            <h1>Place New Order</h1>
            <p class="text-muted">Choose a service to get started</p>
        </div>
    </div>

    <div class="services-grid">
        <!-- Service 1: Embroidery Digitizing -->
        <a href="order-digitizing.php" class="service-card-link">
            <div class="service-card">
                <div class="service-icon cyan">
                    <i class="fas fa-vector-square"></i>
                </div>
                <h5>Embroidery Digitizing</h5>
                <p>Convert artwork to embroidery files</p>
                <span class="btn-service">Place Order →</span>
            </div>
        </a>

        <!-- Service 2: Vector Tracing -->
        <a href="order-vector.php" class="service-card-link">
            <div class="service-card">
                <div class="service-icon magenta">
                    <i class="fas fa-bezier-curve"></i>
                </div>
                <h5>Vector Tracing</h5>
                <p>Convert raster to vector files</p>
                <span class="btn-service">Place Order →</span>
            </div>
        </a>

        <!-- Service 3: Character Art -->
        <a href="order-character.php" class="service-card-link">
            <div class="service-card">
                <div class="service-icon cyan">
                    <i class="fas fa-palette"></i>
                </div>
                <h5>Character & Mascot Art</h5>
                <p>Custom character illustrations</p>
                <span class="btn-service">Place Order →</span>
            </div>
        </a>

        <!-- Service 4: Custom Patches -->
        <a href="order-patches.php" class="service-card-link">
            <div class="service-card">
                <div class="service-icon magenta">
                    <i class="fas fa-puzzle-piece"></i>
                </div>
                <h5>Custom Patches</h5>
                <p>Digital & Physical patches</p>
                <span class="btn-service">Place Order →</span>
            </div>
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>