<?php

require_once '../settings/core.php';
require_once '../classes/product_classes.php';

function update_product_controller($product_id, $product_name, $category_id, $brand_id, $store_id){
    $product = new Products();
    return $product->update_product($product_id, $product_name, $category_id, $brand_id, $store_id);
}
