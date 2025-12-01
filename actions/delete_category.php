<?php

require_once '../settings/core.php';
require_once '../controllers/delete_category_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'] ?? '';

    if (empty($category_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Category ID is required.']);
        exit;
    }

    $result = delete_category_controller($category_id);

    if ($result['status'] === 'success') {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
