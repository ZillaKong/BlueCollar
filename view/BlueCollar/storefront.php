<?php
require_once '../../settings/core.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Storefront - BlueCollar</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar/general.css">
    <link rel="stylesheet" href="../../css/BlueCollar/orders.css">
    <link rel="stylesheet" href="../../css/font.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/admin_bar.php'; ?>
    <div id="top-bar">
        <a href="home.php"><h1 id="logo-text">BlueCollar</h1></a>
        <div id="login-links">
            <a href="orders.php">My Orders</a>
            <a href="../BlueCollar.supply/register.php">For Suppliers</a>
            <a href="./profile.php">Profile</a>
            <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>

    <div id="body-container">
        <div id="storefront-header">
            <a href="home.php" class="back-link">&larr; Back to Home</a>
        </div>

        <!-- Store Info Section -->
        <div id="store-info-container">
            <div id="store-details">
                <h1 id="store-name">Loading...</h1>
                <div id="store-category">
                    <h4>Primary Category</h4>
                    <p id="primary-category">Loading...</p>
                </div>
                <div id="store-description-box">
                    <h4>About This Store</h4>
                    <p id="store-description">Loading...</p>
                </div>
                <div id="store-contact">
                    <p id="store-company"></p>
                    <p id="store-phone"></p>
                </div>
                <div id="store-order-action">
                    <button id="start-order-btn" class="btn-start-order">
                        🛒 Start a New Order
                    </button>
                    <p id="order-status-text"></p>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div id="products-section">
            <div id="products-header">
                <h3>Products</h3>
                <div id="sort-buttons">
                    <span>Sort by:</span>
                    <button class="sort-btn" data-sort="name" data-order="asc">Name</button>
                    <button class="sort-btn" data-sort="brand" data-order="asc">Brand</button>
                    <button class="sort-btn" data-sort="price-low" data-order="asc">Price: Low to High</button>
                    <button class="sort-btn" data-sort="price-high" data-order="desc">Price: High to Low</button>
                </div>
            </div>
            <p id="products-count">Loading products...</p>
            <div class="card-container" id="storefront-products-container">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>
        <div id="spacer"></div>
    </div>

    <!-- Order Floating Bar -->
    <div id="order-floating-bar">
        <div class="order-bar-info">
            <span class="order-bar-invoice">Order: <span id="bar-invoice-number">-</span></span>
            <span class="order-bar-items"><span id="bar-item-count">0</span> item(s)</span>
            <span class="order-bar-total">Total: $<span id="bar-total">0.00</span></span>
        </div>
        <div class="order-bar-actions">
            <a href="orders.php" class="btn-secondary">View Order</a>
            <button id="bar-checkout-btn" class="btn-primary">Pay & Checkout</button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="modal" style="display: none;">
        <div class="modal-content payment-modal-content">
            <div class="modal-header">
                <h2>Complete Payment</h2>
                <button class="modal-close" id="close-payment-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="payment-summary">
                    <div class="payment-row">
                        <span>Order:</span>
                        <span id="payment-invoice-number">-</span>
                    </div>
                    <div class="payment-row">
                        <span>Items:</span>
                        <span id="payment-item-count">0</span>
                    </div>
                    <div class="payment-row total-row">
                        <span>Total Amount:</span>
                        <span id="payment-total">$0.00</span>
                    </div>
                </div>
                
                <div class="payment-form">
                    <div class="form-group">
                        <label for="payment-email">Email Address</label>
                        <input type="email" id="payment-email" placeholder="Enter your email for receipt" required>
                        <small>Payment receipt will be sent to this email</small>
                    </div>
                </div>

                <div class="payment-methods">
                    <h4>Pay securely with Paystack</h4>
                    <div class="payment-icons">
                        <span class="payment-icon">💳</span>
                        <span class="payment-icon">🏦</span>
                        <span class="payment-icon">📱</span>
                    </div>
                    <p class="payment-note">Supports Cards, Bank Transfer, USSD & Mobile Money</p>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancel-payment-btn" class="btn-secondary">Cancel</button>
                <button id="proceed-payment-btn" class="btn-primary">
                    <span class="btn-text">Proceed to Pay</span>
                    <span class="btn-loader" style="display: none;">Processing...</span>
                </button>
            </div>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        // Paystack public key from PHP config
        const PAYSTACK_PUBLIC_KEY = '<?php require_once "../../settings/paystack_config.php"; echo PAYSTACK_PUBLIC_KEY; ?>';
    </script>
    <script src="../../js/BlueCollar/storefront.js"></script>
</body>
</html>

