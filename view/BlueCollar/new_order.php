<?php
require_once '../../settings/core.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Start New Order - BlueCollar</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar/general.css">
    <link rel="stylesheet" href="../../css/BlueCollar/orders.css">
    <link rel="stylesheet" href="../../css/font.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
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
        <div id="new-order-header">
            <a href="orders.php" class="back-link">&larr; Back to Orders</a>
            <h1 id="page-title">Start a New Order</h1>
            <p id="page-subtitle">Select a store and category to begin ordering</p>
        </div>

        <!-- Step Indicator -->
        <div id="step-indicator">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Select Store</div>
            </div>
            <div class="step-connector"></div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Choose Category</div>
            </div>
            <div class="step-connector"></div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Add Products</div>
            </div>
        </div>

        <!-- Step 1: Select Store -->
        <div id="step-1" class="order-step">
            <h2 class="step-heading">Select a Store to Order From</h2>
            <div id="store-search-box">
                <input type="text" id="store-search" placeholder="Search stores...">
            </div>
            <div id="stores-loading">
                <div class="loader"></div>
                <p>Loading stores...</p>
            </div>
            <div class="card-container" id="store-selection-container">
                <!-- Stores will be loaded dynamically -->
            </div>
        </div>

        <!-- Step 2: Select Category -->
        <div id="step-2" class="order-step" style="display: none;">
            <div id="selected-store-info">
                <span id="selected-store-name"></span>
                <button id="change-store-btn" class="btn-secondary">Change Store</button>
            </div>
            <h2 class="step-heading">Choose a Category</h2>
            <div id="categories-loading" style="display: none;">
                <div class="loader"></div>
                <p>Loading categories...</p>
            </div>
            <div class="card-container" id="category-selection-container">
                <!-- Categories will be loaded dynamically -->
            </div>
        </div>

        <!-- Step 3: Add Products (redirect to storefront) -->
        <div id="step-3" class="order-step" style="display: none;">
            <div class="redirect-message">
                <div class="loader"></div>
                <p>Creating your order and redirecting to the store...</p>
            </div>
        </div>

        <div id="spacer"></div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar/new_order.js"></script>
</body>
</html>

