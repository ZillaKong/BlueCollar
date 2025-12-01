<?php

require_once '../settings/core.php';
require_once '../classes/brand_classes.php';

function update_brand_controller($brand_id, $brand_name){
    $brand = new Brands();
    return $brand->update_brand($brand_id, $brand_name);
}
