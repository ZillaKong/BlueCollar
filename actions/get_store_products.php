<?php

require_once '../settings/core.php';
require_once '../controllers/get_store_products_controller.php';

header('Content-Type: application/json');
echo json_encode(get_store_products_controller());
