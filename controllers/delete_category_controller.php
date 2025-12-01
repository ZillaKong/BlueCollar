<?php

require_once '../settings/core.php';
require_once '../classes/category_classes.php';

function delete_category_controller($category_id){
    $category = new Category();
    return $category->delete_category($category_id);
}
