<?php
require_once '../settings/core.php';
get_logged_in_user_role();
get_logged_in_user_id();

if (get_logged_in_user_id() !== null) {
    if (get_logged_in_user_role() === 'supplier'){
        header("Location: /../BlueCollar/view/BlueCollar.supply/home.php");
    } else if (get_logged_in_user_role() === 'buyer'){
        header("Location: /../BlueCollar/view/BlueCollar/home.php"); 
    }
    else {
        logout_user();
    }
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BlueCollar | Login</title>
    <link rel="stylesheet" href="/../BlueCollar/css/index.css">
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
        <a href="../view/BlueCollar/register.php">For Buyers</a>
        <a href="../view/BlueCollar.supply/register.php">For Suppliers</a>
        </div>
    </div>
    <div id="body-container">
        <div id="info-container">
            <h1 id="landing-text">Login to Your Account</h1>
            </div>
            <div id="login-divider">
                <h3 id="sub-landing-text"> Welcome Back to BlueCollar!</h3>
                <p><b> Access your account to connect with verified suppliers, manage your orders, and explore our wide range of trade supplies and services. We're glad to have you back!</b></p>
            </div>
            <div class="info-box" id="login-box">
                <h2>Login</h2>
                <form action="" method="POST" name="login-form" id="login-form">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" id="login-button">Login</button>
                </form>
            </div>
        </div>
        <div id= "account-management">
            <p>Don't have an account? <a href="../view/BlueCollar/register.php">Register as a Buyer</a> or <a href="../view/BlueCoallr.supply/register.php">Register as a Supplier</a></p>
        </div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/../BlueCollar/js/login.js"></script>

</body>
</html>