<?php
/**
 * Paystack Configuration
 * Secure payment gateway settings
 */

require_once 'db_cred.php';

// Paystack API Keys
define('PAYSTACK_SECRET_KEY', 'sk_live_8ed7716e72881edd9740c0b3ecb6aa9e7bdbe257');
define('PAYSTACK_PUBLIC_KEY', 'pk_live_7991ad74f2f66102221e9bc29f86d833549125f4');

// Paystack URLs
define('PAYSTACK_API_URL', 'https://api.paystack.co');
define('PAYSTACK_INIT_ENDPOINT', PAYSTACK_API_URL . '/transaction/initialize');
define('PAYSTACK_VERIFY_ENDPOINT', PAYSTACK_API_URL . '/transaction/verify/');

// App Settings
define('APP_ENVIRONMENT', 'test');
define('APP_BASE_URL', 'http://localhost/BlueCollar');
define('PAYSTACK_CALLBACK_URL', APP_BASE_URL . '/actions/paystack_callback.php');

