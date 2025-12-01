<?php

require_once '../settings/core.php';
require_once '../classes/product_classes.php';

function get_product_controller(){
    $product = new Products();
    return $product->get_product();
}
