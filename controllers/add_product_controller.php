<?php

require_once '../settings/core.php';
require_once '../classes/product_classes.php';

function add_product_controller($product_name, $category_id, $brand_id, $description = ''){
    $store_id = get_logged_in_user_store_id();
    if (!$store_id) {
        return ['status' => 'error', 'message' => 'Unable to determine store ID.'];
    }

    $product = new Products();
    return $product->add_product($product_name, $category_id, $brand_id, $store_id, $description);
}
