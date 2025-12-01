<?php

define( 'PROJECT_ROOT', __DIR__ . '/' );


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ob_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login(){
    if (!is_logged_in()){
        header("Location: /BlueCollar/index.php");
        exit;
    }
}

function get_logged_in_user_id() {

    return $_SESSION['user_id'] ?? null;
}

function get_logged_in_user_role() {
    return $_SESSION['user_role'] ?? null;
}

function admin_only() {
    if (!is_logged_in() || get_logged_in_user_role() !== 'admin') {
        header("Location: /BlueCollar/index.php");
        exit;
    }
}

function logout_user() {
    $_SESSION = array();
    session_destroy();
    header("Location: /BlueCollar/index.php");
    exit;
}

function get_logged_in_user_store_id(){
    if (get_logged_in_user_role() === 'supplier'){
        return get_logged_in_user_id();
    }
}
