<?php

require_once '../settings/core.php';
require_once '../classes/category_classes.php';

function update_category_controller($category_id, $category_name){
    $category = new Category();
    return $category->update_category($category_id, $category_name);
}
