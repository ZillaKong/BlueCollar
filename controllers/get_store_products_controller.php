<?php

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/product_classes.php';

function get_store_products_controller(){
    $store_id = get_logged_in_user_store_id();
    if (!$store_id) {
        return [];
    }

    $product = new Products();
    return $product->get_products_by_store($store_id);
}
