<?php

require_once '../settings/core.php';
require_once '../controllers/get_products_by_category_controller.php';

header('Content-Type: application/json');

if (isset($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']);
    echo json_encode(get_products_by_category_controller($category_id));
} else {
    echo json_encode(['status' => 'error', 'message' => 'Category ID is required.']);
}

