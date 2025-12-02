<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>BlueCollar | Login</title>
    <link rel="stylesheet" href="../../css/BlueCollar/general.css">
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/font.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <div id="top-bar">
        <a href="../../index.php"><h1 id="logo-text">BlueCollar</h1></a>
        <div id="login-links">
        <a href="../BlueCollar.supply/register.php">For Suppliers</a>
        <a href="../login.php">Login</a>
        </div>
    </div>
    <div id="body-container">
        <div id="info-container">
            <h1 id="landing-text">Register as a Buyer</h1>
        </div>
            <div id="divider">
                <h3 id="sub-landing-text"> Join BlueCollar Today!</h3>
                <p><b> Create your buyer account to access a world of verified suppliers, competitive prices, and reliable trade services. Start connecting with the best in the industry now!</b></p>
            </div>
            <div class="info-box" id="register-box">
                <h2>Buyer Registration</h2>
                <form action="" method="POST" id="register-form" name="buyer-registration-form">
                    <span class="form">
                        <input type="text" name="first_name" placeholder="First Name" required>
                        <input type="text" name="last_name" placeholder="Last Name" required>
                        <input type="email" name="email" placeholder="Email Address" required>
                        <input type="password" name="password" placeholder="Password" required>
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                        <input type="text" name="company_name" placeholder="Company Name" required>
                        <input type="hidden" name="role" value="buyer"> 
                        <input type="text" name="phone_number" placeholder="Phone Number" required>
                        <select name="trade-type" id="">
                            <option value="" disabled selected>Select Trade Type</option>
                            <option value="mechanic">Mechanic</option>
                            <option value="electrician">Electrician</option>
                            <option value="plumber">Plumber</option>
                            <option value="carpenter">Carpenter</option>
                            <option value="painter">Painter</option>
                            <option value="other">Other</option>
                        </select>
                        
                    </span>
                    <span class="form-button">
                        <button type="submit" id="register-button">Register</button>
                    </span>
                </form>
            </div>
        <div id= "account-management">
            <p>Already have an account? <a href="../login.php">Login here</a></p>
            <p> Want to register as a Supplier? <a href="../BlueCollar.supply/register.php" id="supply-register-link">Register here</a></p>
        </div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/register.js"></script>

</body>

</html>