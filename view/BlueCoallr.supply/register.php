<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>BlueCollar.Supply | Login</title>
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
        <a href="/../BlueCollar/index.php"><h1 id="logo-text">BlueCollar.Supply</h1></a>
        <div id="login-links">
        <a href="../BlueCollar/register.php">For Buyers</a>
        <a href="../login.php">Login</a>
        </div>
    </div>
    <div id="body-container">
        <div id="info-container">
            <h1 id="landing-text">Register as a Supplier</h1>
            </div>
            <div id="divider">
                <h3 id="sub-landing-text"> Join BlueCollar.Supply Today!</h3>
                <p><b> Create your supplier account to connect with a vast network of artisans and tradespeople. Expand your reach, showcase your products, and grow your business with BlueCollar.Supply!</b></p>
            </div>
            <div id="register-box">
                <h2>Supplier Registration</h2>
                <form action="" method="POST" id="register-form" name="supplier-registration-form">
                    <span class="form">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="email" name="email" placeholder="Email Address" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <input type="text" name="company_name" placeholder="Company Name" required>
                        <input type="text" name="store_name" placeholder="Store Name" required>
                        <input type="text" name="store_description" placeholder="Store Description">
                        <input type="text" name="phone_number" placeholder="Phone Number" required>
                        <input type="hidden" name="role" value="supplier">
                    </span>
                    <span class="form-button">
                        <button type="submit" id="register-button">Register</button>
                    </span>
                </form>
            </div>
        </div>
        <div id= "account-management">
            <p>Already have an account? <a href="../login.php">Login here</a></p>
            <p> Want to register as a Buyer? <a href="../BlueCollar/register.php">Register here</a></p>
        </div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="/../BlueCollar/js/register.js"></script>
</body>
</html>
