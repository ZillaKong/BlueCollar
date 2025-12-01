<?php

require_once '../settings/core.php';
require_once '../classes/category_classes.php';

function add_category_controller($category_name){
    $category = new Category();
    return $category->add_category($category_name);
}
