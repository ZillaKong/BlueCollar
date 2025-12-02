<?php
require_once '../../settings/core.php';
require_login();

// Ensure user is a supplier or admin
$user_role = get_logged_in_user_role();
if ($user_role !== 'supplier' && $user_role !== 'admin') {
    header("Location: ../BlueCollar/home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders - BlueCollar Supply</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar.supply/general.css">
    <link rel="stylesheet" href="../../css/BlueCollar/orders.css">
    <link rel="stylesheet" href="../../css/font.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include_once '../includes/admin_bar.php'; ?>
    <div id="top-bar">
        <a href="home.php"><h1 id="logo-text">BlueCollar<span style="color: #454B1B;">.supply</span></h1></a>
        <div id="login-links">
            <a href="order.php">Orders</a>
            <a href="inventory.php">Inventory</a>
            <a href="./profile.php">Profile</a>
            <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>

    <div id="body-container">
        <div id="orders-header">
            <a href="home.php" class="back-link">&larr; Back to Dashboard</a>
            <div id="orders-title-row">
                <h1 id="orders-title" style="color: #454B1B;">Customer Orders</h1>
            </div>
            <p id="orders-subtitle">View and manage orders from your customers</p>
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
                <p>Loading orders...</p>
            </div>
            <div id="orders-list">
                <!-- Orders will be loaded dynamically -->
            </div>
            <div id="no-orders" style="display: none;">
                <div class="empty-state">
                    <div class="empty-icon">📦</div>
                    <h3>No orders yet</h3>
                    <p>Orders from customers will appear here.</p>
                </div>
            </div>
        </div>

        <div id="spacer"></div>
    </div>

    <!-- Order Details Modal -->
    <div id="order-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #454B1B 0%, #5a6323 100%);">
                <h2 id="modal-title">Order Details</h2>
                <button class="modal-close" id="close-modal">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Order details will be loaded here -->
            </div>
            <div class="modal-footer" id="modal-footer">
                <!-- Action buttons -->
            </div>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar.supply. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar.supply/orders.js"></script>
</body>
</html>

