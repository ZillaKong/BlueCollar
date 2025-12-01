<?php

require_once '../settings/core.php';
require_once '../controllers/update_product_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $product_name = trim($_POST['product_name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $brand_id = $_POST['brand_id'] ?? '';
    $store_id = $_POST['store_id'] ?? '';

    if (empty($product_id) || empty($product_name) || empty($category_id) || empty($brand_id) || empty($store_id)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $result = update_product_controller($product_id, $product_name, $category_id, $brand_id, $store_id);

    if ($result['status'] === 'success') {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
