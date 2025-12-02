<?php

require_once '../settings/core.php';
require_once '../classes/product_classes.php';

function get_products_by_category_controller($category_id){
    $product = new Products();
    return $product->get_products_by_category($category_id);
}

