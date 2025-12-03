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
    <title>My Storefront - BlueCollar.Supply</title>
    <link rel="stylesheet" href="../../css/index.css">
    <link rel="stylesheet" href="../../css/BlueCollar.supply/general.css">
    <link rel="stylesheet" href="../../css/BlueCollar.supply/profile.css">
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
        <!-- Storefront Preview Header -->
        <div id="storefront-preview-header">
            <div class="header-content">
                <h2>📍 Your Storefront Preview</h2>
                <p>This is how buyers see your store</p>
                <button id="edit-profile-btn" class="btn-edit-profile">
                    ⚙️ Edit Store Settings
                </button>
            </div>
        </div>

        <!-- Store Info Preview (mimics buyer view) -->
        <div id="store-preview-container">
            <div id="store-info-preview">
                <div class="store-header">
                    <h1 id="preview-store-name">Loading...</h1>
                    <span class="store-badge">Your Store</span>
                </div>
                
                <div class="store-details-grid">
                    <div class="detail-card">
                        <div class="detail-icon">🏷️</div>
                        <h4>Primary Category</h4>
                        <p id="preview-category">Loading...</p>
                    </div>
                    
                    <div class="detail-card">
                        <div class="detail-icon">🏢</div>
                        <h4>Company</h4>
                        <p id="preview-company">Loading...</p>
                    </div>
                    
                    <div class="detail-card">
                        <div class="detail-icon">📞</div>
                        <h4>Contact</h4>
                        <p id="preview-phone">Loading...</p>
                    </div>
                    
                    <div class="detail-card wide">
                        <div class="detail-icon">📝</div>
                        <h4>About This Store</h4>
                        <p id="preview-description">Loading...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Preview Section -->
        <div id="products-preview-section">
            <div class="section-header">
                <h3>📦 Your Products</h3>
                <div class="product-stats">
                    <span id="products-count">0 products</span>
                    <a href="inventory.php" class="link-to-inventory">Manage Inventory →</a>
                </div>
            </div>
            <div class="products-preview-grid" id="products-preview-container">
                <!-- Products will be loaded dynamically -->
            </div>
        </div>

        <!-- Store Statistics -->
        <div id="store-stats-section">
            <h3>📊 Store Overview</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value" id="stat-products">0</div>
                    <div class="stat-label">Total Products</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-categories">0</div>
                    <div class="stat-label">Categories</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-instock">0</div>
                    <div class="stat-label">In Stock</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="stat-lowstock">0</div>
                    <div class="stat-label">Low Stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Modal -->
    <div id="settings-modal" class="profile-modal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-header">
                <h2>⚙️ Store Settings</h2>
                <button class="modal-close" id="close-settings-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="profile-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="store_name">Store Name *</label>
                            <input type="text" id="store_name" name="store_name" placeholder="Your Store Name" required>
                            <small>This is displayed to buyers</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" id="company_name" name="company_name" placeholder="Company Name">
                            <small>Optional - Your registered business name</small>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="store_description">Store Description</label>
                        <textarea id="store_description" name="store_description" rows="4" placeholder="Describe your store and the products you offer..."></textarea>
                        <small>Tell buyers what makes your store special</small>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="primary_category">Primary Category</label>
                            <select id="primary_category" name="primary_category">
                                <option value="">Select a Category</option>
                                <!-- Categories loaded by JavaScript -->
                            </select>
                            <small>Main type of products you sell</small>
                        </div>

                        <div class="form-group">
                            <label for="phone">Contact Phone</label>
                            <input type="tel" id="phone" name="phone" placeholder="e.g. +233 XX XXX XXXX">
                            <small>For business inquiries</small>
                        </div>
                    </div>

                    <div id="profile-message" class="profile-message" style="display: none;"></div>

                    <div class="modal-actions">
                        <button type="button" class="btn-cancel" id="cancel-settings">Cancel</button>
                        <button type="submit" class="btn-save">💾 Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="footer">
        <p>&copy; 2025 BlueCollar.supply. All rights reserved.</p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../js/BlueCollar.supply/profile.js"></script>
</body>
</html>
