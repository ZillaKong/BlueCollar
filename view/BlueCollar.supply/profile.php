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
    <title>StoreFront Profile - BlueCollar.Supply</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar.supply/general.css">
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
            <a href="./profile.php" class="active">Profile</a>
            <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>

    <div id="body-container">
        <div id="profile-container">
            <h2>StoreFront Settings</h2>
            <p>Manage your store profile and settings</p>

            <form id="profile-form">
                <div class="form-group">
                    <label for="store_name">Store Name</label>
                    <input type="text" id="store_name" name="store_name" placeholder="Your Store Name" required>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" placeholder="Company Name">
                </div>

                <div class="form-group">
                    <label for="store_description">Store Description</label>
                    <textarea id="store_description" name="store_description" rows="4" placeholder="Describe your store and products..."></textarea>
                </div>

                <div class="form-group">
                    <label for="primary_category">Primary Category</label>
                    <select id="primary_category" name="primary_category">
                        <option value="">Select a Category</option>
                        <!-- Categories loaded by JavaScript -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="Contact Phone">
                </div>

                <button type="submit" class="btn-save">Save Changes</button>
            </form>

            <div id="profile-message" style="display: none;"></div>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar.supply. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar.supply/profile.js"></script>
</body>
</html>

