<?php
require_once '../settings/core.php';

require_login();
admin_only();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>BlueCollar | Admin Access</title>
        <link rel="stylesheet" href="../css/index.css">
        <link rel="stylesheet" href="../css/font.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Monoton&display=swap" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Audiowide&family=Quicksand:wght@300..700&display=swap" rel="stylesheet">
    </head>
    <body>
        <?php include_once 'includes/admin_bar.php'; ?>
        <div id="top-bar">
            <a href="../index.php"><h1 id="logo-text">BlueCollar</h1></a>
            <div><p>Admin Access</p></div>
            <div id="dev-links">
                <a href="">Category</a>
                <a href="">Brands</a>
                <a href="">Products</a>
                <a href="">Storefronts</a>
                <a href="../actions/logout.php">Logout</a>
            </div>
        </div>
        <div id="body-container">
            <div id="display-container">
                <h3 id="cat-heading">Products</h3>
                <table id="productTable">
                    <thead>
                    <tr>
                    <th>Products ID</th>
                    <th>Product Name</th>
                    <th>Category ID</th>
                    <th>Brand ID</th>
                    <th>Store ID</th>
                    <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="display-container">
                <h3 id="cat-heading">Brands</h3>
                <table id="brandsTable">
                    <thead>
                        <tr>
                        <th>Brands ID</th>
                        <th>Name</th>
                        <th>Number of products</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="display-container">
                <div id="table-heading">
                    <h3 id="cat-heading">Category</h3>
                    <button id="addCategoryBtn">Add category</button>
                </div>
                <table id="categoryTable">
                    <thead>
                        <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Number of Products</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <!-- Category Modal Popup -->
            <div id="categoryModal" class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Add New Category</h3>
                        <button type="button" class="modal-close" id="closeCategoryModal">&times;</button>
                    </div>
                    <form id="addCategoryForm">
                        <div class="form-group">
                            <label for="categoryName">Category Name:</label>
                            <input type="text" id="categoryName" name="categoryName" placeholder="Enter category name" required>
                        </div>
                        <div class="modal-actions">
                            <button type="button" class="btn-cancel" id="cancelCategoryBtn">Cancel</button>
                            <button type="submit" class="btn-submit">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="display-container">
                <h3 id="cat-heading">Storefronts</h3>
                <table id="storefrontTable">
                    <thead>
                        <tr>
                            <th>Store ID</th>
                            <th>Store Name</th>
                            <th>Seller ID</th>
                            <th>Products</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Admin Quick Navigation -->
        <div id="admin-nav-container">
            <h3>Quick Navigation</h3>
            <div id="admin-nav-sections">
                <div class="admin-nav-section">
                    <h4>🛒 Buyer Pages (BlueCollar)</h4>
                    <ul>
                        <li><a href="BlueCollar/home.php">Buyer Home</a></li>
                        <li><a href="BlueCollar/category.php">Browse Categories</a></li>
                        <li><a href="BlueCollar/storefront.php">View Storefront</a></li>
                        <li><a href="BlueCollar/orders.php">Buyer Orders</a></li>
                        <li><a href="BlueCollar/profile.php">Buyer Profile</a></li>
                        <li><a href="BlueCollar/register.php">Buyer Registration</a></li>
                    </ul>
                </div>
                <div class="admin-nav-section">
                    <h4>🏪 Supplier Pages (BlueCollar.Supply)</h4>
                    <ul>
                        <li><a href="BlueCollar.supply/home.php">Supplier Home</a></li>
                        <li><a href="BlueCollar.supply/inventory.php">Manage Inventory</a></li>
                        <li><a href="BlueCollar.supply/order.php">Supplier Orders</a></li>
                        <li><a href="BlueCollar.supply/profile.php">Supplier Profile</a></li>
                        <li><a href="BlueCollar.supply/register.php">Supplier Registration</a></li>
                    </ul>
                </div>
                <div class="admin-nav-section">
                    <h4>⚙️ System Pages</h4>
                    <ul>
                        <li><a href="../index.php">Landing Page</a></li>
                        <li><a href="login.php">Login Page</a></li>
                        <li><a href="admin.php">Admin Dashboard (Current)</a></li>
                        <li><a href="../actions/logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="footer">
            <p> &copy; 2025 BlueCollar. All rights reserved. </p>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="../js/category.js"></script>
        <script src="../js/product.js"></script>
        <script src="../js/brands.js"></script>
        <script src="../js/storefronts.js"></script>
    </body>
</html>