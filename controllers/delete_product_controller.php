<?php

require_once '../settings/core.php';
require_once '../classes/product_classes.php';

function delete_product_controller($product_id){
    $product = new Products();
    return $product->delete_product($product_id);
}
