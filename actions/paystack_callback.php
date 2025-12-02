<?php
/**
 * Paystack Payment Callback Handler
 * This file handles the redirect from Paystack after payment
 */

require_once '../settings/core.php';
require_once '../classes/paystack_classes.php';

// Get the reference from the URL
$reference = $_GET['reference'] ?? null;

if (!$reference) {
    // No reference provided, redirect to orders with error
    header('Location: ../view/BlueCollar/orders.php?payment=error&message=' . urlencode('No payment reference provided'));
    exit;
}

// Verify the transaction with Paystack
$paystack = new Paystack();
$result = $paystack->verify_transaction($reference);

if ($result['status'] === 'success') {
    // Payment successful
    $order_id = $result['order_id'];
    $amount = $result['amount'];
    
    // Clear pending payment from session
    if (isset($_SESSION['pending_payment'])) {
        unset($_SESSION['pending_payment']);
    }
    
    // Redirect to orders page with success message
    header('Location: ../view/BlueCollar/orders.php?payment=success&order_id=' . $order_id . '&amount=' . $amount);
    exit;
} else {
    // Payment failed
    $message = $result['message'] ?? 'Payment verification failed';
    $gateway_response = $result['gateway_response'] ?? '';
    
    header('Location: ../view/BlueCollar/orders.php?payment=failed&message=' . urlencode($message . ($gateway_response ? ': ' . $gateway_response : '')));
    exit;
}

