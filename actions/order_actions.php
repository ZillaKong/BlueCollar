<?php
// Suppress PHP warnings/notices from appearing before JSON output
error_reporting(E_ERROR | E_PARSE);

require_once '../settings/core.php';
require_once '../controllers/order_controller.php';
require_once '../classes/paystack_classes.php';

header('Content-Type: application/json');

// Require login for all order actions
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to continue.']);
    exit;
}

$buyer_id = get_logged_in_user_id();
$action = $_REQUEST['action'] ?? '';

switch ($action) {
    case 'create':
        // Create a new order
        $storefront_id = intval($_POST['storefront_id'] ?? 0);
        
        if (!$storefront_id) {
            echo json_encode(['status' => 'error', 'message' => 'Store is required.']);
            exit;
        }
        
        echo json_encode(create_order_controller($buyer_id, $storefront_id));
        break;

    case 'add_item':
        // Add item to order
        $order_id = intval($_POST['order_id'] ?? 0);
        $product_id = intval($_POST['product_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        $price = floatval($_POST['price'] ?? 0);
        
        if (!$order_id || !$product_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order and product are required.']);
            exit;
        }
        
        if ($quantity < 1) {
            echo json_encode(['status' => 'error', 'message' => 'Quantity must be at least 1.']);
            exit;
        }
        
        echo json_encode(add_order_item_controller($order_id, $product_id, $quantity, $price, $buyer_id));
        break;

    case 'update_item':
        // Update item quantity
        $item_id = intval($_POST['item_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 0);
        
        if (!$item_id) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID is required.']);
            exit;
        }
        
        echo json_encode(update_order_item_controller($item_id, $quantity, $buyer_id));
        break;

    case 'remove_item':
        // Remove item from order
        $item_id = intval($_POST['item_id'] ?? 0);
        
        if (!$item_id) {
            echo json_encode(['status' => 'error', 'message' => 'Item ID is required.']);
            exit;
        }
        
        echo json_encode(remove_order_item_controller($item_id, $buyer_id));
        break;

    case 'get_orders':
        // Get all buyer orders
        echo json_encode(get_buyer_orders_controller($buyer_id));
        break;

    case 'get_order':
        // Get specific order details
        $order_id = intval($_GET['order_id'] ?? 0);
        
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order ID is required.']);
            exit;
        }
        
        echo json_encode(get_order_details_controller($order_id, $buyer_id));
        break;

    case 'check_active':
        // Check for active order with a store
        $storefront_id = intval($_GET['storefront_id'] ?? 0);
        
        if (!$storefront_id) {
            echo json_encode(['status' => 'error', 'message' => 'Store ID is required.']);
            exit;
        }
        
        echo json_encode(get_active_order_controller($buyer_id, $storefront_id));
        break;

    case 'checkout':
        // Checkout order (without payment - for manual/cash orders)
        $order_id = intval($_POST['order_id'] ?? 0);
        
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order ID is required.']);
            exit;
        }
        
        echo json_encode(checkout_order_controller($order_id, $buyer_id));
        break;

    case 'init_payment':
        // Initialize Paystack payment
        $order_id = intval($_POST['order_id'] ?? 0);
        $email = trim($_POST['email'] ?? '');
        
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order ID is required.']);
            exit;
        }
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'error', 'message' => 'Valid email is required for payment.']);
            exit;
        }
        
        // Get order details to verify ownership and get total
        $order_details = get_order_details_controller($order_id, $buyer_id);
        
        if ($order_details['status'] === 'error') {
            echo json_encode(['status' => 'error', 'message' => 'Order not found or access denied.']);
            exit;
        }
        
        if ($order_details['order']['status'] !== 'pending') {
            echo json_encode(['status' => 'error', 'message' => 'Order has already been processed.']);
            exit;
        }
        
        if (count($order_details['items']) === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Cannot checkout an empty order.']);
            exit;
        }
        
        $total_amount = $order_details['calculated_total'];
        
        // Initialize Paystack transaction
        $paystack = new Paystack();
        $result = $paystack->initialize_transaction($email, $total_amount, $order_id, [
            'invoice_number' => $order_details['order']['invoice_number'],
            'store_name' => $order_details['order']['store_name'] ?? 'BlueCollar Store'
        ]);
        
        echo json_encode($result);
        break;

    case 'verify_payment':
        // Verify payment status
        $reference = trim($_GET['reference'] ?? '');
        
        if (!$reference) {
            echo json_encode(['status' => 'error', 'message' => 'Payment reference is required.']);
            exit;
        }
        
        $paystack = new Paystack();
        $result = $paystack->verify_transaction($reference);
        echo json_encode($result);
        break;

    case 'cancel':
        // Cancel order
        $order_id = intval($_POST['order_id'] ?? 0);
        
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order ID is required.']);
            exit;
        }
        
        echo json_encode(cancel_order_controller($order_id, $buyer_id));
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        break;
}

