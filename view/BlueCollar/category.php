<?php
require_once '../../settings/core.php';
require_login();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Category - BlueCollar</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar/general.css">
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
            <a href="../BlueCollar.supply/register.php">For Suppliers</a>
            <a href="./profile.php">Profile</a>
            <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>

    <div id="body-container">
        <div id="category-header">
            <a href="home.php" class="back-link">&larr; Back to Home</a>
            <h2 id="category-title">Loading...</h2>
            <p id="product-count">Loading products...</p>
        </div>

        <div id="display-container">
            <div class="card-container" id="category-products-container">
                <!-- Products will be loaded dynamically by JavaScript -->
            </div>
        </div>
        <div id="spacer"></div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar/category.js"></script>
</body>
</html>
