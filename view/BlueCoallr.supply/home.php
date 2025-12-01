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
    <link rel="stylesheet" href="/../BlueCollar/css/index.css">
    <link rel="stylesheet" href="/../BlueCollar/css/BlueCollar.supply/general.css">
    <link rel="stylesheet" href="/../BlueCollar/css/font.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="top-bar">
        <a href="/../BlueCollar/index.php"><h1 id="logo-text">BlueCollar</h1></a>
        <div id="login-links">
        <a href="../BlueCollar/register.php">For Buyers</a>
        <a href="./profile.php"> StoreFront </a>
        <a href="/../BlueCollar/actions/logout.php">Logout</a>
        </div>
    </div>
    <div id="body-container">
        <div id="action-cards">
            <div class="action">
                <h2>Stock Management</h2>
                <button><a href="inventory.php">Manage Your Inventory</a></button>
            </div>
            <div class="action">
                <h2>Order Management</h2>
                <button><a href="orders.php">View and Process Orders</a></button>
            </div>
            <div class="action">
                <h2>StoreFront Settings</h2>
                <button><a href="profile.php">Update Your Store Information</a></button>
            </div>
        </div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>
</body>