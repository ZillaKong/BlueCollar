<?php

require_once '../settings/core.php';
require_once '../controllers/add_product_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_code = trim($_POST['product_code'] ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $brand_name = trim($_POST['brand_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
    $price = floatval($_POST['price'] ?? 0.00);

    if (empty($product_code) || empty($product_name) || empty($category_id) || empty($brand_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Product code, name, category, and brand are required.']);
        exit;
    }

    try {
        $add_result = add_product_controller($product_code, $product_name, $category_id, $brand_name, $description, $stock_quantity, $price);

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
