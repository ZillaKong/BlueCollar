<!-- Admin Access Bar - Hidden by default, shown via JavaScript when admin is logged in -->
<?php $base = defined('BASE_URL') ? BASE_URL : ''; ?>
<div id="admin-access-bar" style="display: none;">
    <span class="admin-badge">👑 Admin Access</span>
    <nav class="admin-quick-links">
        <a href="<?php echo $base; ?>/view/admin.php">Admin Dashboard</a>
        <a href="<?php echo $base; ?>/view/BlueCollar/home.php">Buyer View</a>
        <a href="<?php echo $base; ?>/view/BlueCollar.supply/home.php">Supplier View</a>
    </nav>
</div>
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
            console.error('Error checking user role:', error);
        });
})();
</script>
