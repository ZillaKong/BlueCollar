<?php
require_once '../../settings/core.php';
require_login();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BlueCollar.Supply | Inventory Control</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar.supply/general.css">
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
        <a href="../index.php">
            <h1 id="logo-text">BlueCollar</h1>
        </a>
        <div id="login-links">
            <a href="../BlueCollar/register.php">For Buyers</a>
            <a href="./profile.php"> StoreFront </a>
            <a href="../../actions/logout.php">Logout</a>
        </div>
    </div>
    <div id="body-container">
        <div id="display-container">
            <h2 id="cat-heading">Inventory</h2>
            <button id="addProductBtn">Add to Inventory</button>
            <div id="addProductForm" style="display: none;">
                <h3>Add New Product</h3>
                <form>
                    <label for="productName">Product Name:</label>
                    <input type="text" id="productName" required><br><br>

                    <label for="productCategory">Category:</label>
                    <select id="productCategory" required>
                        <option value="">Select Category</option>
                    </select><br><br>

                    <label for="productBrand">Brand:</label>
                    <input type="text" id="productBrand" required><br><br>

                    <label for="productDescription">Description:</label>
                    <textarea id="productDescription"></textarea><br><br>

                    <label for="productStock">Stock Quantity:</label>
                    <input type="number" id="productStock" min="0" required><br><br>

                    <label for="productPrice">Price:</label>
                    <input type="number" id="productPrice" step="0.01" min="0" required><br><br>

                    <button type="submit">Add Product</button>
                    <button type="button" id="cancelAddProduct">Cancel</button>
                </form>
            </div>
            <table id="inventoryTable">
                <thead>
                    <tr>
                        <th>Products ID</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Brand</th>
                        <th>Description</th>
                        <th>Amount in Stock</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <div id="footer">
        <p> &copy; 2025 BlueCollar. All rights reserved. </p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar.supply/inventory.js"></script>
</body>