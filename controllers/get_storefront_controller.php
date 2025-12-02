<?php

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/storefront_classes.php';

function get_storefront_controller($store_id){
    $storefront = new Storefront();
    return $storefront->get_full_storefront_data($store_id);
}

function get_all_storefronts_controller(){
    $storefront = new Storefront();
    return $storefront->get_all_storefronts();
}

function get_all_storefronts_admin_controller(){
    $storefront = new Storefront();
    return $storefront->get_all_storefronts_admin();
}

function get_current_storefront_controller(){
    $seller_id = get_logged_in_user_id();
    if (!$seller_id) {
        return ['status' => 'error', 'message' => 'Not logged in.'];
    }
    
    $storefront = new Storefront();
    $data = $storefront->get_current_storefront($seller_id);
    
    if ($data) {
        return ['status' => 'success', 'data' => $data];
    }
    
    return ['status' => 'error', 'message' => 'Storefront not found.'];
}

function update_storefront_controller($data){
    $seller_id = get_logged_in_user_id();
    if (!$seller_id) {
        return ['status' => 'error', 'message' => 'Not logged in.'];
    }
    
    $storefront = new Storefront();
    return $storefront->update_storefront($seller_id, $data);
}

