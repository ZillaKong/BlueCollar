<?php

require_once '../settings/core.php';
require_once '../controllers/get_brands_controller.php';

header('Content-Type: application/json');
echo json_encode(get_brands_controller());