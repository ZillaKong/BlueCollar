<?php
require_once '../settings/core.php';

// Clear session
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

session_destroy();

// Redirect to index - use multiple fallback methods
$redirect_url = BASE_URL . '/index.php';

// If BASE_URL is empty, try to compute from current script
if (empty(BASE_URL)) {
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    // Go up one level from /actions to get to root
    $base = dirname($script_dir);
    $redirect_url = ($base === '/' || $base === '\\') ? '/index.php' : $base . '/index.php';
}

header("Location: " . $redirect_url);
exit;
