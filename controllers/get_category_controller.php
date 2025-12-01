<?php

require_once '../settings/core.php';
require_once '../classes/category_classes.php';

function get_category_controller(){
    $veiw_cat = new Category();
    return $veiw_cat->get_category();
}
