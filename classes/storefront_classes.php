<?php
require_once __DIR__ . '/../settings/db_class.php';

class Storefront extends db_connection {

    public function get_storefront_info($store_id){
        if (!$this->db_connect()) {
            return null;
        }
        
        $sql = "SELECT s.id AS store_id, u.store_name, u.store_description, 
                u.company_name, u.phone, u.first_name, u.last_name
                FROM final_seller_storefront s
                INNER JOIN final_users u ON s.seller_id = u.user_id
                WHERE s.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
        
        $stmt->close();
        return null;
    }

    public function get_storefront_categories($store_id){
        if (!$this->db_connect()) {
            return [];
        }
        
        // Try to get categories from the storefront_categories table
        // If the table doesn't exist, fall back to getting categories from products
        try {
            $sql = "SELECT c.cat_id AS category_id, c.name AS category_name
                    FROM final_storefront_categories sc
                    INNER JOIN final_categories c ON sc.category_id = c.cat_id
                    WHERE sc.storefront_id = ?
                    ORDER BY c.name";
            $stmt = $this->db->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("i", $store_id);
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
        } catch (Exception $e) {
            // Table might not exist, fall through to alternative query
        }
        
        // Fallback: Get unique categories from products in this store
        $seller_id = 0;
        $seller_sql = "SELECT seller_id FROM final_seller_storefront WHERE id = ?";
        $seller_stmt = $this->db->prepare($seller_sql);
        if ($seller_stmt) {
            $seller_stmt->bind_param("i", $store_id);
            $seller_stmt->execute();
            $seller_result = $seller_stmt->get_result();
            if ($seller_result && $seller_result->num_rows > 0) {
                $row = $seller_result->fetch_assoc();
                $seller_id = intval($row['seller_id']);
            }
            $seller_stmt->close();
        }
        
        $sql = "SELECT DISTINCT c.cat_id AS category_id, c.name AS category_name
                FROM final_products p
                INNER JOIN final_categories c ON p.category_id = c.cat_id
                WHERE p.storefront_id = ? OR (? > 0 AND p.seller_id = ?)
                ORDER BY c.name";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param("iii", $store_id, $seller_id, $seller_id);
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

    public function get_storefront_products($store_id){
        if (!$this->db_connect()) {
            return [];
        }
        
        // First, get the seller_id for this storefront
        $seller_sql = "SELECT seller_id FROM final_seller_storefront WHERE id = ?";
        $seller_stmt = $this->db->prepare($seller_sql);
        $seller_id = 0; // Use 0 instead of null for safe SQL comparison
        if ($seller_stmt) {
            $seller_stmt->bind_param("i", $store_id);
            $seller_stmt->execute();
            $seller_result = $seller_stmt->get_result();
            if ($seller_result && $seller_result->num_rows > 0) {
                $row = $seller_result->fetch_assoc();
                $seller_id = intval($row['seller_id']);
            }
            $seller_stmt->close();
        }
        
        // Query products by storefront_id OR seller_id (for backward compatibility)
        // Note: availability_status might not exist in all databases, using COALESCE for safety
        $sql = "SELECT p.id AS product_id, p.product_name, p.product_description AS description,
                p.price, p.stock_quantity, 
                COALESCE(p.availability_status, 'in stock') AS availability_status, 
                p.category_id,
                c.name AS category_name, b.name AS brand_name
                FROM final_products p
                LEFT JOIN final_categories c ON p.category_id = c.cat_id
                LEFT JOIN final_brands b ON p.brand_id = b.brand_id
                WHERE p.storefront_id = ? OR (? > 0 AND p.seller_id = ?)
                ORDER BY p.product_name";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed in get_storefront_products: " . $this->db->error);
            return [];
        }
        $stmt->bind_param("iii", $store_id, $seller_id, $seller_id);
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

    public function get_full_storefront_data($store_id){
        $info = $this->get_storefront_info($store_id);
        
        if (!$info) {
            return ['status' => 'error', 'message' => 'Storefront not found.'];
        }
        
        $categories = $this->get_storefront_categories($store_id);
        $products = $this->get_storefront_products($store_id);
        
        return [
            'status' => 'success',
            'store_info' => $info,
            'categories' => $categories,
            'products' => $products
        ];
    }

    public function get_all_storefronts(){
        if (!$this->db_connect()) {
            return [];
        }
        
        // First, sync storefronts for any suppliers that don't have one
        $this->sync_supplier_storefronts();
        
        $sql = "SELECT s.id AS store_id, u.store_name, u.store_description, u.company_name,
                (SELECT COUNT(*) FROM final_products WHERE storefront_id = s.id) AS product_count
                FROM final_seller_storefront s
                INNER JOIN final_users u ON s.seller_id = u.user_id
                WHERE u.store_name IS NOT NULL
                ORDER BY u.store_name";
        $result = $this->db->query($sql);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }
        return $data;
    }

    public function get_all_storefronts_admin(){
        if (!$this->db_connect()) {
            return [];
        }
        
        // First, sync storefronts for any suppliers that don't have one
        $this->sync_supplier_storefronts();
        
        $sql = "SELECT s.id AS store_id, s.seller_id, u.store_name, u.store_description, 
                u.company_name, u.email, u.phone,
                (SELECT COUNT(*) FROM final_products WHERE storefront_id = s.id) AS product_count,
                s.created_at
                FROM final_seller_storefront s
                INNER JOIN final_users u ON s.seller_id = u.user_id
                ORDER BY s.id";
        $result = $this->db->query($sql);

        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        }
        return $data;
    }

    /**
     * Syncs storefronts for all suppliers who don't have one yet.
     * This ensures existing supplier accounts get storefronts.
     */
    public function sync_supplier_storefronts(){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }
        
        // Find all suppliers without a storefront
        $sql = "SELECT u.user_id 
                FROM final_users u
                LEFT JOIN final_seller_storefront s ON u.user_id = s.seller_id
                WHERE u.role = 'supplier' AND s.id IS NULL";
        $result = $this->db->query($sql);
        
        if (!$result) {
            error_log("Error finding suppliers without storefronts: " . $this->db->error);
            return ['status' => 'error', 'message' => $this->db->error];
        }
        
        $created_count = 0;
        $suppliers_without_storefront = [];
        
        while ($row = $result->fetch_assoc()) {
            $suppliers_without_storefront[] = $row['user_id'];
        }
        $result->free();
        
        // Create storefronts for each supplier without one
        foreach ($suppliers_without_storefront as $seller_id) {
            $insert_sql = "INSERT INTO final_seller_storefront (seller_id) VALUES (?)";
            $stmt = $this->db->prepare($insert_sql);
            
            if ($stmt) {
                $stmt->bind_param("i", $seller_id);
                if ($stmt->execute()) {
                    $created_count++;
                } else {
                    error_log("Failed to create storefront for seller $seller_id: " . $stmt->error);
                }
                $stmt->close();
            }
        }
        
        return ['status' => 'success', 'created' => $created_count];
    }

    /**
     * Get current user's storefront data for profile editing
     */
    public function get_current_storefront($seller_id){
        if (!$this->db_connect()) {
            return null;
        }
        
        $sql = "SELECT s.id AS store_id, s.primary_category, u.store_name, u.store_description, 
                u.company_name, u.phone, c.name AS category_name
                FROM final_seller_storefront s
                INNER JOIN final_users u ON s.seller_id = u.user_id
                LEFT JOIN final_categories c ON s.primary_category = c.cat_id
                WHERE s.seller_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return null;
        }
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $data = $result->fetch_assoc();
            $stmt->close();
            return $data;
        }
        
        $stmt->close();
        
        // If no storefront exists, create one and return basic data
        $this->get_storefront_by_seller($seller_id);
        
        // Try fetching again after creation
        $sql2 = "SELECT s.id AS store_id, s.primary_category, u.store_name, u.store_description, 
                u.company_name, u.phone
                FROM final_seller_storefront s
                INNER JOIN final_users u ON s.seller_id = u.user_id
                WHERE s.seller_id = ?";
        $stmt2 = $this->db->prepare($sql2);
        if ($stmt2) {
            $stmt2->bind_param("i", $seller_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2 && $result2->num_rows === 1) {
                $data = $result2->fetch_assoc();
                $stmt2->close();
                return $data;
            }
            $stmt2->close();
        }
        
        return null;
    }

    /**
     * Update storefront profile data
     */
    public function update_storefront($seller_id, $data){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }
        
        // Update user table (store_name, company_name, store_description, phone)
        $user_sql = "UPDATE final_users SET store_name = ?, company_name = ?, store_description = ?, phone = ? WHERE user_id = ?";
        $user_stmt = $this->db->prepare($user_sql);
        $user_stmt->bind_param("ssssi", $data['store_name'], $data['company_name'], $data['store_description'], $data['phone'], $seller_id);
        
        if (!$user_stmt->execute()) {
            $error = $this->db->error;
            $user_stmt->close();
            return ['status' => 'error', 'message' => 'Failed to update user data: ' . $error];
        }
        $user_stmt->close();
        
        // Update storefront table (primary_category)
        if (!empty($data['primary_category'])) {
            $sf_sql = "UPDATE final_seller_storefront SET primary_category = ? WHERE seller_id = ?";
            $sf_stmt = $this->db->prepare($sf_sql);
            $sf_stmt->bind_param("ii", $data['primary_category'], $seller_id);
            $sf_stmt->execute();
            $sf_stmt->close();
        }
        
        return ['status' => 'success', 'message' => 'Profile updated successfully.'];
    }

    /**
     * Get storefront ID by seller's user ID
     */
    public function get_storefront_by_seller($seller_id){
        if (!$this->db_connect()) {
            return null;
        }
        
        $sql = "SELECT id AS store_id FROM final_seller_storefront WHERE seller_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['store_id'];
        }
        
        $stmt->close();
        
        // If no storefront exists, create one
        $insert_sql = "INSERT INTO final_seller_storefront (seller_id) VALUES (?)";
        $insert_stmt = $this->db->prepare($insert_sql);
        $insert_stmt->bind_param("i", $seller_id);
        
        if ($insert_stmt->execute()) {
            $store_id = $this->db->insert_id;
            $insert_stmt->close();
            return $store_id;
        }
        
        $insert_stmt->close();
        return null;
    }
}

