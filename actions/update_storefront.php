<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/get_storefront_controller.php';

header('Content-Type: application/json');

// Require login and supplier role
if (!is_logged_in() || get_logged_in_user_role() !== 'supplier') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

$data = [
    'store_name' => trim($_POST['store_name'] ?? ''),
    'company_name' => trim($_POST['company_name'] ?? ''),
    'store_description' => trim($_POST['store_description'] ?? ''),
    'primary_category' => intval($_POST['primary_category'] ?? 0),
    'phone' => trim($_POST['phone'] ?? '')
];

if (empty($data['store_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Store name is required.']);
    exit;
}

echo json_encode(update_storefront_controller($data));

