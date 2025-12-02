<?php
// Suppress warnings to ensure clean JSON output
error_reporting(E_ERROR | E_PARSE);

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/register_user_controller.php';

header('Content-type: application/json');

if (is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; 
    $phone = trim($_POST['phone_number']);
        $company_name = isset($_POST['company_name']) ? trim($_POST['company_name']) : null;
        $store_name = isset($_POST['store_name']) ? trim($_POST['store_name']) : null;
        $store_description = isset($_POST['store_description']) ? trim($_POST['store_description']) : null;
        $trade_type = isset($_POST['trade-type']) ? trim($_POST['trade-type']) : null;

    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
        exit;
    }

    if ($role === 'buyer'){
    $registration_result = register_user_controller(
        $first_name,
        $last_name,
        $email,
        $password,
        $role,
        $phone,
        $company_name,
        null, null,
        $trade_type
    );}
    else if ($role === 'supplier'){
    $registration_result = register_user_controller(
        $first_name,
        $last_name,
        $email,
        $password,
        $role,
        $phone,
        $company_name,
        $store_name,
        $store_description
    );}

    if ($registration_result['status'] === 'success') {
        echo json_encode(['status' => 'success', 'message' => 'Registration successful. You can now log in.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $registration_result['message']]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}