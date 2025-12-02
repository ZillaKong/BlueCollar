<?php

require_once '../settings/core.php';
require_once '../classes/order_classes.php';

header('Content-Type: application/json');

// Require login and supplier role
if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to continue.']);
    exit;
}

if (get_logged_in_user_role() !== 'supplier') {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

$supplier_id = get_logged_in_user_id();
$action = $_REQUEST['action'] ?? '';

// Get supplier's storefront ID
require_once '../settings/db_class.php';
$db = new db_connection();
if (!$db->db_connect()) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}

$stmt = $db->db->prepare("SELECT id FROM final_seller_storefront WHERE seller_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([]);
    exit;
}

$storefront = $result->fetch_assoc();
$storefront_id = $storefront['id'];
$stmt->close();

switch ($action) {
    case 'get_orders':
        // Get all orders for this supplier's store
        $sql = "SELECT o.id AS order_id, o.invoice_number, o.status, o.created_at,
                CONCAT(u.first_name, ' ', u.last_name) AS buyer_name,
                u.company_name AS buyer_company,
                COALESCE(i.total_amount, 0) AS total_amount,
                (SELECT COUNT(*) FROM final_order_items WHERE order_id = o.id) AS item_count
                FROM final_orders o
                LEFT JOIN final_users u ON o.buyer_id = u.user_id
                LEFT JOIN final_invoices i ON o.id = i.order_id
                WHERE o.storefront_id = ?
                ORDER BY o.created_at DESC";
        
        $stmt = $db->db->prepare($sql);
        $stmt->bind_param("i", $storefront_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
        
        echo json_encode($orders);
        break;

    case 'get_order':
        // Get specific order details
        $order_id = intval($_GET['order_id'] ?? 0);
        
        if (!$order_id) {
            echo json_encode(['status' => 'error', 'message' => 'Order ID is required.']);
            exit;
        }
        
        // Get order info
        $sql = "SELECT o.id AS order_id, o.invoice_number, o.status, o.created_at,
                o.buyer_id, o.supplier_id, o.storefront_id,
                CONCAT(u.first_name, ' ', u.last_name) AS buyer_name,
                u.company_name AS buyer_company,
                u.phone AS buyer_phone,
                u.email AS buyer_email,
                COALESCE(i.total_amount, 0) AS total_amount,
                i.invoice_date
                FROM final_orders o
                LEFT JOIN final_users u ON o.buyer_id = u.user_id
                LEFT JOIN final_invoices i ON o.id = i.order_id
                WHERE o.id = ? AND o.storefront_id = ?";
        
        $stmt = $db->db->prepare($sql);
        $stmt->bind_param("ii", $order_id, $storefront_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            echo json_encode(['status' => 'error', 'message' => 'Order not found.']);
            exit;
        }
        
        $order = $result->fetch_assoc();
        $stmt->close();
        
        // Get order items
        $items_sql = "SELECT oi.id AS item_id, oi.product_id, oi.quantity, oi.price_at_order,
                      p.product_name, p.product_code, c.name AS category_name, b.name AS brand_name
                      FROM final_order_items oi
                      LEFT JOIN final_products p ON oi.product_id = p.id
                      LEFT JOIN final_categories c ON p.category_id = c.cat_id
                      LEFT JOIN final_brands b ON p.brand_id = b.brand_id
                      WHERE oi.order_id = ?
                      ORDER BY p.product_name";
        
        $items_stmt = $db->db->prepare($items_sql);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        
        $items = [];
        $calculated_total = 0;
        while ($item = $items_result->fetch_assoc()) {
            $item['line_total'] = $item['quantity'] * $item['price_at_order'];
            $calculated_total += $item['line_total'];
            $items[] = $item;
        }
        $items_stmt->close();
        
        echo json_encode([
            'status' => 'success',
            'order' => $order,
            'items' => $items,
            'calculated_total' => $calculated_total
        ]);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
        break;
}

