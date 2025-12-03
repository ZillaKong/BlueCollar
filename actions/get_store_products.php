<?php
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/get_store_products_controller.php';

header('Content-Type: application/json');
echo json_encode(get_store_products_controller());
