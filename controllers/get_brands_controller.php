<?php

require_once '../settings/core.php';
require_once '../classes/brand_classes.php';

function get_brands_controller(){
    $veiw_cat = new Brands();
    return $veiw_cat->get_brand();
}