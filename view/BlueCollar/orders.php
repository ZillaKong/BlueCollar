<?php
require_once '../../settings/core.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders - BlueCollar</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar/general.css">
    <link rel="stylesheet" href="../../css/BlueCollar/orders.css">
    <link rel="stylesheet" href="../../css/font.css">
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
        <div id="orders-header">
            <a href="home.php" class="back-link">&larr; Back to Home</a>
            <div id="orders-title-row">
                <h1 id="orders-title">My Orders</h1>
                <a href="new_order.php" class="btn-primary" id="new-order-btn">
                    <span class="btn-icon">+</span> Start a New Order
                </a>
            </div>
            <p id="orders-subtitle">View and manage your orders from suppliers</p>
        </div>

        <!-- Filter Tabs -->
        <div id="orders-filters">
            <button class="filter-btn active" data-filter="all">All Orders</button>
            <button class="filter-btn" data-filter="pending">Pending</button>
            <button class="filter-btn" data-filter="completed">Completed</button>
            <button class="filter-btn" data-filter="canceled">Canceled</button>
        </div>

        <!-- Orders List -->
        <div id="orders-container">
            <div id="orders-loading">
                <div class="loader"></div>
                <p>Loading your orders...</p>
            </div>
            <div id="orders-list">
                <!-- Orders will be loaded dynamically -->
            </div>
            <div id="no-orders" style="display: none;">
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>No orders yet</h3>
                    <p>Start ordering from your favorite suppliers!</p>
                    <a href="new_order.php" class="btn-primary">Start Your First Order</a>
                </div>
            </div>
        </div>

        <div id="spacer"></div>
    </div>

    <!-- Order Details Modal -->
    <div id="order-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Order Details</h2>
                <button class="modal-close" id="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer" id="modal-footer">
                <!-- Action buttons will be added based on order status -->
            </div>
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
                        <span id="payment-total">GH₵0.00</span>
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

    <!-- Payment Success Alert -->
    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'success'): ?>
    <div id="payment-success-alert" class="alert alert-success">
        <div class="alert-content">
            <span class="alert-icon">✓</span>
            <div class="alert-text">
                <strong>Payment Successful!</strong>
                <p>Your order has been completed. Amount: GH₵<?php echo number_format(floatval($_GET['amount'] ?? 0), 2); ?></p>
            </div>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['payment']) && $_GET['payment'] === 'failed'): ?>
    <div id="payment-failed-alert" class="alert alert-error">
        <div class="alert-content">
            <span class="alert-icon">✕</span>
            <div class="alert-text">
                <strong>Payment Failed</strong>
                <p><?php echo htmlspecialchars($_GET['message'] ?? 'An error occurred during payment.'); ?></p>
            </div>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <div id="footer">
        <p>&copy; 2025 BlueCollar. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const PAYSTACK_PUBLIC_KEY = '<?php require_once "../../settings/paystack_config.php"; echo PAYSTACK_PUBLIC_KEY; ?>';
    </script>
    <script src="../../js/BlueCollar/orders.js"></script>
</body>
</html>

