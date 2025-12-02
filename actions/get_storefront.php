<?php

require_once '../settings/core.php';
require_once '../controllers/get_storefront_controller.php';

header('Content-Type: application/json');

if (isset($_GET['store_id'])) {
    $store_id = intval($_GET['store_id']);
    echo json_encode(get_storefront_controller($store_id));
} else if (isset($_GET['current'])) {
    // Get current logged-in user's storefront
    if (!is_logged_in() || get_logged_in_user_role() !== 'supplier') {
        echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
        exit;
    }
    echo json_encode(get_current_storefront_controller());
} else if (isset($_GET['admin'])) {
    // Admin view - shows all storefronts with full details
    echo json_encode(get_all_storefronts_admin_controller());
} else if (isset($_GET['all'])) {
    echo json_encode(get_all_storefronts_controller());
} else {
    echo json_encode(['status' => 'error', 'message' => 'Store ID is required.']);
}

