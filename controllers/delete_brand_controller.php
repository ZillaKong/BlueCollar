<?php

require_once '../settings/core.php';
require_once '../classes/brand_classes.php';

function delete_brand_controller($brand_id){
    $brand = new Brands();
    return $brand->delete_brand($brand_id);
}
