<?php

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../classes/order_classes.php';

/**
 * Create a new order
 */
function create_order_controller($buyer_id, $storefront_id) {
    $orders = new Orders();
    
    // Get supplier ID from storefront
    $supplier_id = $orders->get_supplier_from_storefront($storefront_id);
    if (!$supplier_id) {
        return ['status' => 'error', 'message' => 'Invalid store selected.'];
    }
    
    // Check for existing active order with this store
    $existing_order = $orders->get_active_order($buyer_id, $storefront_id);
    if ($existing_order) {
        return [
            'status' => 'success',
            'order_id' => $existing_order['order_id'],
            'invoice_number' => $existing_order['invoice_number'],
            'message' => 'Continuing existing order.'
        ];
    }
    
    return $orders->create_order($buyer_id, $supplier_id, $storefront_id);
}

/**
 * Add item to order
 */
function add_order_item_controller($order_id, $product_id, $quantity, $price_at_order, $buyer_id) {
    $orders = new Orders();
    
    // Verify order belongs to buyer
    $order_details = $orders->get_order_details($order_id, $buyer_id);
    if ($order_details['status'] === 'error') {
        return ['status' => 'error', 'message' => 'Order not found or access denied.'];
    }
    
    if ($order_details['order']['status'] !== 'pending') {
        return ['status' => 'error', 'message' => 'Cannot modify a completed order.'];
    }
    
    return $orders->add_order_item($order_id, $product_id, $quantity, $price_at_order);
}

/**
 * Update order item quantity
 */
function update_order_item_controller($item_id, $quantity, $buyer_id) {
    $orders = new Orders();
    
    // Verify item belongs to buyer's order
    if (!verify_item_ownership($item_id, $buyer_id)) {
        return ['status' => 'error', 'message' => 'Item not found or access denied.'];
    }
    
    return $orders->update_order_item($item_id, $quantity);
}

/**
 * Remove item from order
 */
function remove_order_item_controller($item_id, $buyer_id) {
    $orders = new Orders();
    
    // Verify item belongs to buyer's order
    if (!verify_item_ownership($item_id, $buyer_id)) {
        return ['status' => 'error', 'message' => 'Item not found or access denied.'];
    }
    
    return $orders->remove_order_item($item_id);
}

/**
 * Get all orders for buyer
 */
function get_buyer_orders_controller($buyer_id) {
    $orders = new Orders();
    return $orders->get_buyer_orders($buyer_id);
}

/**
 * Get order details
 */
function get_order_details_controller($order_id, $buyer_id) {
    $orders = new Orders();
    return $orders->get_order_details($order_id, $buyer_id);
}

/**
 * Check for active order with a store
 */
function get_active_order_controller($buyer_id, $storefront_id) {
    $orders = new Orders();
    $active_order = $orders->get_active_order($buyer_id, $storefront_id);
    
    if ($active_order) {
        return [
            'status' => 'success',
            'has_active_order' => true,
            'order_id' => $active_order['order_id'],
            'invoice_number' => $active_order['invoice_number']
        ];
    }
    
    return [
        'status' => 'success',
        'has_active_order' => false
    ];
}

/**
 * Checkout order
 */
function checkout_order_controller($order_id, $buyer_id) {
    $orders = new Orders();
    return $orders->checkout($order_id, $buyer_id);
}

/**
 * Cancel order
 */
function cancel_order_controller($order_id, $buyer_id) {
    $orders = new Orders();
    return $orders->cancel_order($order_id, $buyer_id);
}

/**
 * Helper: Verify item belongs to buyer's order
 */
function verify_item_ownership($item_id, $buyer_id) {
    require_once __DIR__ . '/../settings/db_class.php';
    
    $db = new db_connection();
    if (!$db->db_connect()) {
        return false;
    }
    
    $sql = "SELECT oi.id FROM final_order_items oi
            JOIN final_orders o ON oi.order_id = o.id
            WHERE oi.id = ? AND o.buyer_id = ? AND o.status = 'pending'";
    $stmt = $db->db->prepare($sql);
    $stmt->bind_param("ii", $item_id, $buyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    $stmt->close();
    
    return $exists;
}

