<?php

require_once '../settings/core.php';
require_once '../controllers/get_product_controller.php';

header('Content-Type: application/json');
echo json_encode(get_product_controller());
