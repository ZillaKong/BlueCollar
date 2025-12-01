<?php

require_once '../settings/core.php';
require_once '../controllers/get_category_controller.php';

header('Content-Type: application/json');
echo json_encode(get_category_controller());

