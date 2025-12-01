<?php

require_once '../settings/core.php';
require_once '../controllers/delete_brand_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = trim($_POST['brand_id'] ?? '');

    if (empty($brand_id)) {
        echo json_encode(['status' => 'error', 'message' => 'Brand ID is required.']);
        exit;
    }

    try {
        $result = delete_brand_controller($brand_id);

        if ($result['status'] === 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Brand deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $result['message'] ?? 'Failed to delete brand.']);
        }
    } catch (Exception $e) {
        error_log("Delete Brand Exception: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'A critical server error occurred during brand deletion.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
