<?php

define('PROJECT_ROOT', __DIR__ . '/../');
define('BASE_URL', '/BlueCollar');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ob_start();

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login(){
    if (!is_logged_in()){
        header("Location: " . BASE_URL . "/index.php");
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
        header("Location: " . BASE_URL . "/index.php");
        exit;
    }
}

function logout_user() {
    // Unset all session variables
    $_SESSION = array();
    session_unset();
    
    // Delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

function get_logged_in_user_store_id(){
    if (get_logged_in_user_role() !== 'supplier'){
        return null;
    }
    
    // Return cached store_id if already fetched
    if (isset($_SESSION['store_id'])) {
        return $_SESSION['store_id'];
    }
    
    // Fetch store_id from database
    require_once PROJECT_ROOT . 'settings/db_class.php';
    $db = new db_connection();
    if (!$db->db_connect()) {
        return null;
    }
    
    $user_id = get_logged_in_user_id();
    $sql = "SELECT id FROM final_seller_storefront WHERE seller_id = ?";
    $stmt = $db->db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $_SESSION['store_id'] = $row['id']; // Cache it in session
        $stmt->close();
        return $row['id'];
    }
    
    $stmt->close();
    return null;
}
