<?php

require_once '../settings/core.php';
require_once '../classes/register_classes.php';   

function login_user_controller($email, $password)
{
    $login_user = new RegisterUser();
    return $login_user->loginUser($email, $password);
} 