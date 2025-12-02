<!-- Admin Access Bar - Hidden by default, shown via JavaScript when admin is logged in -->
<?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
<div id="admin-access-bar" style="display: none;">
    <span class="admin-badge">👑 Admin</span>
    <nav class="admin-quick-links">
        <a href="<?php echo $base; ?>/view/admin.php">📊 Dashboard</a>
        <div class="admin-dropdown">
            <span class="dropdown-trigger">🛒 Buyer Pages ▾</span>
            <div class="dropdown-content">
                <a href="<?php echo $base; ?>/view/BlueCollar/home.php">Home</a>
                <a href="<?php echo $base; ?>/view/BlueCollar/orders.php">Orders</a>
                <a href="<?php echo $base; ?>/view/BlueCollar/new_order.php">New Order</a>
            </div>
        </div>
        <div class="admin-dropdown">
            <span class="dropdown-trigger">🏭 Supplier Pages ▾</span>
            <div class="dropdown-content">
                <a href="<?php echo $base; ?>/view/BlueCollar.supply/home.php">Home</a>
                <a href="<?php echo $base; ?>/view/BlueCollar.supply/inventory.php">Inventory</a>
                <a href="<?php echo $base; ?>/view/BlueCollar.supply/order.php">Orders</a>
                <a href="<?php echo $base; ?>/view/BlueCollar.supply/profile.php">Profile</a>
            </div>
        </div>
        <a href="<?php echo $base; ?>/index.php">🏠 Landing</a>
        <a href="<?php echo $base; ?>/actions/logout.php" class="admin-logout">🚪 Logout</a>
    </nav>
</div>
<style>
/* Admin Bar Dropdown Styles */
.admin-dropdown {
    position: relative;
    display: inline-block;
}
.admin-dropdown .dropdown-trigger {
    cursor: pointer;
    padding: 5px 10px;
    color: white;
}
.admin-dropdown .dropdown-trigger:hover {
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
}
.admin-dropdown .dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #1a1a2e;
    min-width: 150px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    border-radius: 4px;
    z-index: 1001;
    padding: 5px 0;
}
.admin-dropdown .dropdown-content a {
    display: block;
    padding: 8px 15px;
    color: white;
    text-decoration: none;
    font-size: 13px;
}
.admin-dropdown .dropdown-content a:hover {
    background: rgba(255,255,255,0.1);
}
.admin-dropdown:hover .dropdown-content {
    display: block;
}
.admin-logout {
    color: #ff6b6b !important;
}
.admin-logout:hover {
    background: rgba(255,107,107,0.2) !important;
}
</style>
<script>
// Check if user is admin and show admin bar
(function() {
    const baseUrl = '<?php echo $base; ?>';
    fetch(baseUrl + '/actions/get_user_role.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.role === 'admin') {
                document.getElementById('admin-access-bar').style.display = 'flex';
            }
        })
        .catch(error => {
            // Silently fail - user might not be logged in
        });
})();
</script>
