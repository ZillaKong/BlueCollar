<?php

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/product_classes.php';

function add_product_controller($product_code, $product_name, $category_id, $brand_name, $description = '', $stock_quantity = 0, $price = 0.00){
    $store_id = get_logged_in_user_store_id();
    if (!$store_id) {
        return ['status' => 'error', 'message' => 'Unable to determine store ID. Please ensure you have a storefront set up.'];
    }

    $product = new Products();
    return $product->add_product($product_code, $product_name, $category_id, $brand_name, $store_id, $description, $stock_quantity, $price);
}
