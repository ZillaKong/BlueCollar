<?php

require_once '../settings/core.php';
require_once '../controllers/add_category_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name'] ?? '');

    if (empty($category_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Category name is required.']);
        exit;
    }

    $result = add_category_controller($category_name);

    if ($result['status'] === 'success') {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
