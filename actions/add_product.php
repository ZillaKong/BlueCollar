<?php

require_once '../settings/core.php';
require_once '../controllers/add_product_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $brand_id = trim($_POST['brand_id'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($product_name) || empty($category_id) || empty($brand_id)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $add_result = add_product_controller($product_name, $category_id, $brand_id, $description);

        if ($add_result['status'] === 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Product added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $add_result['message'] ?? 'Failed to add product.']);
        }
    } catch (Exception $e) {
        error_log("Add Product Exception: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'A critical server error occurred during product addition.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
