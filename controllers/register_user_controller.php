<?php

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/register_classes.php';

function register_user_controller($first_name, $last_name, $email, $password, $role, $phone, $company_name, $store_name = null, $store_description = null, $trade_type = null)
{
    $register_user = new RegisterUser();
    if ($role === 'buyer') {
        return $register_user->register($first_name, $last_name, $email, $password, $role, $company_name, $phone, null, null, $trade_type);
    }else if ($role === 'supplier'){
        return $register_user->register($first_name, $last_name, $email, $password, $role, $company_name, $phone, $store_name, $store_description, $trade_type);
    }
    else {
        return ['status' => 'error', 'message' => 'Invalid role specified.'];
    }
}

    