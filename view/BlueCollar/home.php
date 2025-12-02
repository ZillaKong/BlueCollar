<?php
require_once '../../settings/core.php';
require_login();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BlueCollar</title>
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
        <a href="../BlueCollar/home.php"><h1 id="logo-text">BlueCollar</h1></a>
        <div id="login-links">
        <a href="orders.php">My Orders</a>
        <a href="../BlueCollar.supply/register.php">For Suppliers</a>
        <a href="./profile.php"> Profile </a>
        <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>
    <div id="body-container">
        <!-- <div id="home-box">
            <div id="text-container">
                <h1 id="landing-text">Welcome Back to BlueCollar</h1>
                <h3 id="sub-landing-text"> Your Trusted Marketplace for Trade Supplies & Services</h3>
            </div>
        </div> -->
        <div id="display-container">
            <h3 id ="display-heading"> Explore Categories </h3>
            <div class="card-container" id="category-container">
                <!-- Categories will be loaded dynamically by JavaScript -->
            </div>
        </div>
        <div id="spacer"></div>
        <div id="display-container">
            <h3 id="display-heading"> Featured Products </h3>
            <div class="card-container" id="product-container">
                <!-- Products will be loaded dynamically by JavaScript -->
            </div>
        </div>
        <div id="spacer"></div>
        <div id="display-container">
            <h3 id="display-heading"> Browse Stores </h3>
            <div class="card-container" id="store-container">
                <!-- Stores will be loaded dynamically by JavaScript -->
            </div>
        </div>
        <div id="spacer"></div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/home.js"></script>

</body>
</html>