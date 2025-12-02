<?php
/**
 * Paystack Payment Integration Class
 * Handles payment initialization, verification, and recording
 */

require_once __DIR__ . '/../settings/paystack_config.php';
require_once __DIR__ . '/../settings/db_class.php';

class Paystack extends db_connection {

    /**
     * Initialize a Paystack transaction
     * @param string $email Customer email
     * @param float $amount Amount in main currency (will be converted to kobo)
     * @param int $order_id Order reference
     * @param array $metadata Additional transaction data
     * @return array
     */
    public function initialize_transaction($email, $amount, $order_id, $metadata = []) {
        // Convert amount to kobo (Paystack uses smallest currency unit)
        $amount_kobo = $amount * 100;
        
        // Generate unique reference
        $reference = 'BC_' . $order_id . '_' . time() . '_' . bin2hex(random_bytes(4));
        
        $fields = [
            'email' => $email,
            'amount' => $amount_kobo,
            'reference' => $reference,
            'callback_url' => PAYSTACK_CALLBACK_URL,
            'metadata' => array_merge([
                'order_id' => $order_id,
                'custom_fields' => [
                    [
                        'display_name' => 'Order ID',
                        'variable_name' => 'order_id',
                        'value' => $order_id
                    ]
                ]
            ], $metadata)
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, PAYSTACK_INIT_ENDPOINT);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'status' => 'error',
                'message' => 'Connection error: ' . $error
            ];
        }
        
        $result = json_decode($response, true);
        
        if ($result && $result['status'] === true) {
            // Store pending payment reference
            $this->store_pending_payment($order_id, $reference, $amount);
            
            return [
                'status' => 'success',
                'authorization_url' => $result['data']['authorization_url'],
                'access_code' => $result['data']['access_code'],
                'reference' => $result['data']['reference']
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $result['message'] ?? 'Payment initialization failed'
        ];
    }

    /**
     * Verify a Paystack transaction
     * @param string $reference Transaction reference
     * @return array
     */
    public function verify_transaction($reference) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, PAYSTACK_VERIFY_ENDPOINT . rawurlencode($reference));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Content-Type: application/json',
            'Cache-Control: no-cache'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return [
                'status' => 'error',
                'message' => 'Verification failed: ' . $error
            ];
        }
        
        $result = json_decode($response, true);
        
        if ($result && $result['status'] === true && $result['data']['status'] === 'success') {
            $data = $result['data'];
            
            // Extract order_id from metadata
            $order_id = $data['metadata']['order_id'] ?? null;
            
            if ($order_id) {
                // Record the successful payment
                $payment_result = $this->record_payment($order_id, $data);
                
                if ($payment_result['status'] === 'success') {
                    return [
                        'status' => 'success',
                        'message' => 'Payment verified successfully',
                        'order_id' => $order_id,
                        'amount' => $data['amount'] / 100, // Convert back from kobo
                        'reference' => $reference,
                        'payment_id' => $payment_result['payment_id']
                    ];
                }
            }
            
            return [
                'status' => 'error',
                'message' => 'Payment verified but order processing failed'
            ];
        }
        
        return [
            'status' => 'error',
            'message' => $result['message'] ?? 'Payment verification failed',
            'gateway_response' => $result['data']['gateway_response'] ?? null
        ];
    }

    /**
     * Store pending payment reference for tracking
     */
    private function store_pending_payment($order_id, $reference, $amount) {
        if (!$this->db_connect()) {
            return false;
        }
        
        // Check if we have a pending_payments table, if not use a simpler approach
        // Store reference in session or temporary storage
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['pending_payment'] = [
            'order_id' => $order_id,
            'reference' => $reference,
            'amount' => $amount,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return true;
    }

    /**
     * Record successful payment in database
     * @param int $order_id
     * @param array $paystack_data Payment data from Paystack
     * @return array
     */
    public function record_payment($order_id, $paystack_data) {
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
        
        // First, get or create the invoice for this order
        $invoice_sql = "SELECT id FROM final_invoices WHERE order_id = ?";
        $invoice_stmt = $this->db->prepare($invoice_sql);
        $invoice_stmt->bind_param("i", $order_id);
        $invoice_stmt->execute();
        $invoice_result = $invoice_stmt->get_result();
        
        if ($invoice_result->num_rows === 0) {
            // Create invoice first
            $total_sql = "SELECT SUM(quantity * price_at_order) AS total FROM final_order_items WHERE order_id = ?";
            $total_stmt = $this->db->prepare($total_sql);
            $total_stmt->bind_param("i", $order_id);
            $total_stmt->execute();
            $total_result = $total_stmt->get_result();
            $total_amount = $total_result->fetch_assoc()['total'] ?? 0;
            $total_stmt->close();
            
            $create_invoice_sql = "INSERT INTO final_invoices (order_id, total_amount) VALUES (?, ?)";
            $create_invoice_stmt = $this->db->prepare($create_invoice_sql);
            $create_invoice_stmt->bind_param("id", $order_id, $total_amount);
            $create_invoice_stmt->execute();
            $invoice_id = $this->db->insert_id;
            $create_invoice_stmt->close();
        } else {
            $invoice_id = $invoice_result->fetch_assoc()['id'];
        }
        $invoice_stmt->close();
        
        // Convert amount from kobo to main currency
        $amount = $paystack_data['amount'] / 100;
        
        // Record payment
        $payment_sql = "INSERT INTO final_payments (invoice_id, amount, payment_method, payment_reference, payment_status, payment_date) 
                        VALUES (?, ?, 'paystack', ?, 'completed', CURRENT_TIMESTAMP)";
        $payment_stmt = $this->db->prepare($payment_sql);
        $reference = $paystack_data['reference'];
        $payment_stmt->bind_param("ids", $invoice_id, $amount, $reference);
        
        if (!$payment_stmt->execute()) {
            $error = $this->db->error;
            $payment_stmt->close();
            return ['status' => 'error', 'message' => 'Failed to record payment: ' . $error];
        }
        
        $payment_id = $this->db->insert_id;
        $payment_stmt->close();
        
        // Update order status to completed
        $update_order_sql = "UPDATE final_orders SET status = 'completed' WHERE id = ?";
        $update_order_stmt = $this->db->prepare($update_order_sql);
        $update_order_stmt->bind_param("i", $order_id);
        $update_order_stmt->execute();
        $update_order_stmt->close();
        
        return [
            'status' => 'success',
            'payment_id' => $payment_id,
            'invoice_id' => $invoice_id
        ];
    }

    /**
     * Get payment details for an order
     * @param int $order_id
     * @return array|null
     */
    public function get_order_payment($order_id) {
        if (!$this->db_connect()) {
            return null;
        }
        
        $sql = "SELECT p.*, i.total_amount, i.order_id 
                FROM final_payments p
                JOIN final_invoices i ON p.invoice_id = i.id
                WHERE i.order_id = ?
                ORDER BY p.payment_date DESC
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $payment = $result->fetch_assoc();
            $stmt->close();
            return $payment;
        }
        
        $stmt->close();
        return null;
    }
}

