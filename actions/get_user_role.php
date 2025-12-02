<?php

require_once '../settings/core.php';

header('Content-Type: application/json');

if (is_logged_in()) {
    echo json_encode([
        'status' => 'success',
        'role' => get_logged_in_user_role(),
        'user_id' => get_logged_in_user_id()
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Not logged in'
    ]);
}

