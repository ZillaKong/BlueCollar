<?php

require_once '../settings/core.php';
require_once '../controllers/login_user_controller.php';
// get_logged_in_user_role();

// Start the session at the very beginning

if (isset($_SESSION['user_id'])) {
    header('Content-Type: application/json'); 
    echo json_encode(['status' => 'error', 'message' => 'You are already logged in.']);
    // if (get_logged_in_user_role() === 'supplier'){
    //     header("Location: /../BlueCollar/view/BlueCoallr.supply/home.php"); 
    // } else if (get_logged_in_user_role() === 'buyer'){
    //     header("Location: /../BlueCollar/view/BlueCollar/home.php"); 
    // }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); 
    
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) && empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
        exit;
    }
    elseif (empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Password is required.']);
        exit;
    }

    try {
        // This is the only part that should be in 'try'
        $login_result = login_user_controller($email, $password);

        // Success Output (Move ALL result handling inside the POST block)
        if ($login_result['status'] === 'success') {
            $_SESSION['user_id'] = $login_result['user_id'];
            $_SESSION['user_role'] = $login_result['user_role'];

            // 2. Output the success message
            echo json_encode(['status' => 'success', 'message' => 'Login successful. Redirecting...', 'role' => $login_result['user_role']]);
            exit;
        } else {
            $errorMessage = $login_result['message'] ?? 'Login failed. Check credentials.';
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        }
        
    } catch (\Exception $e) {
        // Catch any formal Exceptions thrown by the controller or dependencies
        error_log("Login Exception: " . $e->getMessage()); // Log the error on the server
        echo json_encode(['status' => 'error', 'message' => 'A critical server error occurred during login.']);
    }

    exit; // STOP SCRIPT HERE after handling the POST request
}

// If the script reaches here, it's a GET request, and no JSON is outputted.
?>


