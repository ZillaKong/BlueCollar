<?php

require_once '../settings/core.php';
require_once '../controllers/delete_product_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';

    if (empty($product_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required.']);
        exit;
    }

    $result = delete_product_controller($product_id);

    if ($result['status'] === 'success') {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
