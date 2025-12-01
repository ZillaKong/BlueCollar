<?php

require_once '../settings/core.php';
require_once '../controllers/update_brand_controller.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = trim($_POST['brand_id'] ?? '');
    $brand_name = trim($_POST['brand_name'] ?? '');

    if (empty($brand_id) || empty($brand_name)) {
        echo json_encode(['status' => 'error', 'message' => 'Brand ID and name are required.']);
        exit;
    }

    try {
        $update_result = update_brand_controller($brand_id, $brand_name);

        if ($update_result['status'] === 'success') {
            echo json_encode(['status' => 'success', 'message' => 'Brand updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $update_result['message'] ?? 'Failed to update brand.']);
        }
    } catch (Exception $e) {
        error_log("Update Brand Exception: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'A critical server error occurred during brand update.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
