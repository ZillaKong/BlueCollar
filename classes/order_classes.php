<?php
require_once __DIR__ . '/../settings/db_class.php';

class Orders extends db_connection {

    /**
     * Generate the next invoice number
     * Format: i001, i002, i003, etc.
     */
    private function generate_invoice_number() {
        if (!$this->db_connect()) {
            return null;
        }
        
        $sql = "SELECT invoice_number FROM final_orders ORDER BY id DESC LIMIT 1";
        $result = $this->db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_invoice = $row['invoice_number'];
            // Extract the number part (e.g., "i001" -> 1)
            $number = intval(substr($last_invoice, 1));
            $next_number = $number + 1;
        } else {
            $next_number = 1;
        }
        
        // Format: i001, i002, ..., i999, i1000, etc.
        return 'i' . str_pad($next_number, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new order
     * @param int $buyer_id
     * @param int $supplier_id
     * @param int $storefront_id
     * @return array
     */
    public function create_order($buyer_id, $supplier_id, $storefront_id) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $invoice_number = $this->generate_invoice_number();
        if (!$invoice_number) {
            return ['status' => 'error', 'message' => 'Failed to generate invoice number.'];
        }

        $sql = "INSERT INTO final_orders (buyer_id, supplier_id, storefront_id, invoice_number, status) VALUES (?, ?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiis", $buyer_id, $supplier_id, $storefront_id, $invoice_number);

        if ($stmt->execute()) {
            $order_id = $this->db->insert_id;
            $stmt->close();
            return [
                'status' => 'success',
                'order_id' => $order_id,
                'invoice_number' => $invoice_number
            ];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to create order: ' . $error];
        }
    }

    /**
     * Add item to order
     * @param int $order_id
     * @param int $product_id
     * @param int $quantity
     * @param float $price_at_order
     * @return array
     */
    public function add_order_item($order_id, $product_id, $quantity, $price_at_order) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        // Check if item already exists in order
        $check_sql = "SELECT id, quantity FROM final_order_items WHERE order_id = ? AND product_id = ?";
        $check_stmt = $this->db->prepare($check_sql);
        $check_stmt->bind_param("ii", $order_id, $product_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // Update existing item quantity
            $existing = $check_result->fetch_assoc();
            $new_quantity = $existing['quantity'] + $quantity;
            $check_stmt->close();

            $update_sql = "UPDATE final_order_items SET quantity = ?, price_at_order = ? WHERE id = ?";
            $update_stmt = $this->db->prepare($update_sql);
            $update_stmt->bind_param("idi", $new_quantity, $price_at_order, $existing['id']);

            if ($update_stmt->execute()) {
                $update_stmt->close();
                return ['status' => 'success', 'message' => 'Item quantity updated.', 'item_id' => $existing['id']];
            } else {
                $error = $this->db->error;
                $update_stmt->close();
                return ['status' => 'error', 'message' => 'Failed to update item: ' . $error];
            }
        }

        $check_stmt->close();

        // Insert new item
        $sql = "INSERT INTO final_order_items (order_id, product_id, quantity, price_at_order) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price_at_order);

        if ($stmt->execute()) {
            $item_id = $this->db->insert_id;
            $stmt->close();
            return ['status' => 'success', 'item_id' => $item_id];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to add item: ' . $error];
        }
    }

    /**
     * Update order item quantity
     * @param int $item_id
     * @param int $quantity
     * @return array
     */
    public function update_order_item($item_id, $quantity) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        if ($quantity <= 0) {
            return $this->remove_order_item($item_id);
        }

        $sql = "UPDATE final_order_items SET quantity = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $quantity, $item_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to update item: ' . $error];
        }
    }

    /**
     * Remove item from order
     * @param int $item_id
     * @return array
     */
    public function remove_order_item($item_id) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "DELETE FROM final_order_items WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $item_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to remove item: ' . $error];
        }
    }

    /**
     * Get all orders for a buyer
     * @param int $buyer_id
     * @return array
     */
    public function get_buyer_orders($buyer_id) {
        if (!$this->db_connect()) {
            return [];
        }

        $sql = "SELECT o.id AS order_id, o.invoice_number, o.status, o.created_at,
                u.store_name, u.company_name,
                COALESCE(i.total_amount, 0) AS total_amount,
                (SELECT COUNT(*) FROM final_order_items WHERE order_id = o.id) AS item_count
                FROM final_orders o
                LEFT JOIN final_users u ON o.supplier_id = u.user_id
                LEFT JOIN final_invoices i ON o.id = i.order_id
                WHERE o.buyer_id = ?
                ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $buyer_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }
        $stmt->close();
        return $data;
    }

    /**
     * Get order details with items
     * @param int $order_id
     * @param int $buyer_id (for verification)
     * @return array
     */
    public function get_order_details($order_id, $buyer_id = null) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        // Get order info
        $sql = "SELECT o.id AS order_id, o.invoice_number, o.status, o.created_at,
                o.buyer_id, o.supplier_id, o.storefront_id,
                u.store_name, u.company_name, u.phone AS supplier_phone,
                COALESCE(i.total_amount, 0) AS total_amount,
                i.invoice_date
                FROM final_orders o
                LEFT JOIN final_users u ON o.supplier_id = u.user_id
                LEFT JOIN final_invoices i ON o.id = i.order_id
                WHERE o.id = ?";
        
        if ($buyer_id) {
            $sql .= " AND o.buyer_id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($buyer_id) {
            $stmt->bind_param("ii", $order_id, $buyer_id);
        } else {
            $stmt->bind_param("i", $order_id);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return ['status' => 'error', 'message' => 'Order not found.'];
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
        
        $items_stmt = $this->db->prepare($items_sql);
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

        return [
            'status' => 'success',
            'order' => $order,
            'items' => $items,
            'calculated_total' => $calculated_total
        ];
    }

    /**
     * Check if buyer has an active (pending) order with a specific store
     * @param int $buyer_id
     * @param int $storefront_id
     * @return array|null
     */
    public function get_active_order($buyer_id, $storefront_id) {
        if (!$this->db_connect()) {
            return null;
        }

        $sql = "SELECT id AS order_id, invoice_number FROM final_orders 
                WHERE buyer_id = ? AND storefront_id = ? AND status = 'pending'
                ORDER BY created_at DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $buyer_id, $storefront_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $order = $result->fetch_assoc();
            $stmt->close();
            return $order;
        }
        
        $stmt->close();
        return null;
    }

    /**
     * Checkout - Create invoice for order
     * @param int $order_id
     * @param int $buyer_id (for verification)
     * @return array
     */
    public function checkout($order_id, $buyer_id) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        // Verify order belongs to buyer and is pending
        $verify_sql = "SELECT id, status FROM final_orders WHERE id = ? AND buyer_id = ?";
        $verify_stmt = $this->db->prepare($verify_sql);
        $verify_stmt->bind_param("ii", $order_id, $buyer_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();

        if ($verify_result->num_rows === 0) {
            $verify_stmt->close();
            return ['status' => 'error', 'message' => 'Order not found.'];
        }

        $order = $verify_result->fetch_assoc();
        $verify_stmt->close();

        if ($order['status'] !== 'pending') {
            return ['status' => 'error', 'message' => 'Order has already been processed.'];
        }

        // Check if order has items
        $items_sql = "SELECT COUNT(*) AS count FROM final_order_items WHERE order_id = ?";
        $items_stmt = $this->db->prepare($items_sql);
        $items_stmt->bind_param("i", $order_id);
        $items_stmt->execute();
        $items_result = $items_stmt->get_result();
        $items_count = $items_result->fetch_assoc()['count'];
        $items_stmt->close();

        if ($items_count === 0) {
            return ['status' => 'error', 'message' => 'Cannot checkout an empty order.'];
        }

        // Calculate total
        $total_sql = "SELECT SUM(quantity * price_at_order) AS total FROM final_order_items WHERE order_id = ?";
        $total_stmt = $this->db->prepare($total_sql);
        $total_stmt->bind_param("i", $order_id);
        $total_stmt->execute();
        $total_result = $total_stmt->get_result();
        $total_amount = $total_result->fetch_assoc()['total'] ?? 0;
        $total_stmt->close();

        // Check if invoice already exists
        $check_invoice_sql = "SELECT id FROM final_invoices WHERE order_id = ?";
        $check_invoice_stmt = $this->db->prepare($check_invoice_sql);
        $check_invoice_stmt->bind_param("i", $order_id);
        $check_invoice_stmt->execute();
        $check_invoice_result = $check_invoice_stmt->get_result();

        if ($check_invoice_result->num_rows > 0) {
            // Update existing invoice
            $invoice = $check_invoice_result->fetch_assoc();
            $check_invoice_stmt->close();

            $update_invoice_sql = "UPDATE final_invoices SET total_amount = ?, invoice_date = CURRENT_TIMESTAMP WHERE id = ?";
            $update_invoice_stmt = $this->db->prepare($update_invoice_sql);
            $update_invoice_stmt->bind_param("di", $total_amount, $invoice['id']);
            $update_invoice_stmt->execute();
            $invoice_id = $invoice['id'];
            $update_invoice_stmt->close();
        } else {
            $check_invoice_stmt->close();

            // Create invoice
            $invoice_sql = "INSERT INTO final_invoices (order_id, total_amount) VALUES (?, ?)";
            $invoice_stmt = $this->db->prepare($invoice_sql);
            $invoice_stmt->bind_param("id", $order_id, $total_amount);

            if (!$invoice_stmt->execute()) {
                $error = $this->db->error;
                $invoice_stmt->close();
                return ['status' => 'error', 'message' => 'Failed to create invoice: ' . $error];
            }
            $invoice_id = $this->db->insert_id;
            $invoice_stmt->close();
        }

        // Update order status to completed
        $update_sql = "UPDATE final_orders SET status = 'completed' WHERE id = ?";
        $update_stmt = $this->db->prepare($update_sql);
        $update_stmt->bind_param("i", $order_id);
        $update_stmt->execute();
        $update_stmt->close();

        return [
            'status' => 'success',
            'invoice_id' => $invoice_id,
            'total_amount' => $total_amount,
            'message' => 'Order completed successfully!'
        ];
    }

    /**
     * Cancel an order
     * @param int $order_id
     * @param int $buyer_id
     * @return array
     */
    public function cancel_order($order_id, $buyer_id) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "UPDATE final_orders SET status = 'canceled' WHERE id = ? AND buyer_id = ? AND status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $order_id, $buyer_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $stmt->close();
            return ['status' => 'success', 'message' => 'Order cancelled.'];
        } else {
            $stmt->close();
            return ['status' => 'error', 'message' => 'Unable to cancel order.'];
        }
    }

    /**
     * Get supplier ID from storefront ID
     * @param int $storefront_id
     * @return int|null
     */
    public function get_supplier_from_storefront($storefront_id) {
        if (!$this->db_connect()) {
            return null;
        }

        $sql = "SELECT seller_id FROM final_seller_storefront WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $storefront_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['seller_id'];
        }
        
        $stmt->close();
        return null;
    }
}

