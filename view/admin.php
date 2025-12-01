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
                <form id="addCategoryForm" style="display: none; margin-bottom: 20px;">
                    <label for="categoryName">Category Name:</label>
                    <input type="text" id="categoryName" name="categoryName" required>
                    <button type="submit">Add Category</button>
                </form>
                <table id="categoryTable">
                    <thead>
                        <tr>
                        <th>Category ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div id="display-container">
                <h3 id="cat-heading">Storefronts</h3>
                <table>
                    <tr>
                    <th>Storefront ID</th>
                    <th>Store Name</th>
                    <th>Seller ID</th>
                    <th>Description</th>
                    <th>Actions</th>
                    </tr>
                </table>
            </div>
        </div>
        <div id="footer">
            <p> &copy; 2025 BlueCollar. All rights reserved. </p>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="/../BlueCollar/js/category.js"></script>
        <script src="/../BlueCollar/js/product.js"></script>
        <script src="/../BlueCollar/js/brands.js"></script>
    </body>
</html>