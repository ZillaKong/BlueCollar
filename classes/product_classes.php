<?php
require_once '../settings/db_class.php';

class Products extends db_connection {

    public function get_product(){
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT p.id AS product_id, p.product_name, p.category_id, p.brand_id, p.storefront_id AS store_id
        FROM final_products p
        ORDER BY p.product_name";
        $result = $this->db->query($sql);

        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        } else {
            error_log("MySQLi Query Error: " . $this->db->error);
        }

        return $data;
    }

    public function update_product($product_id, $product_name, $category_id, $brand_id, $store_id){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "UPDATE final_products SET product_name = ?, category_id = ?, brand_id = ?, storefront_id = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("siiii", $product_name, $category_id, $brand_id, $store_id, $product_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to update product: ' . $error];
        }
    }

    public function delete_product($product_id){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "DELETE FROM final_products WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to delete product: ' . $error];
        }
    }

    public function get_products_by_store($store_id){
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT p.id AS product_id, p.product_code, p.product_name, c.name AS category_name, b.name AS brand_name, p.product_description AS description, p.stock_quantity, p.price
        FROM final_products p
        LEFT JOIN final_categories c ON p.category_id = c.cat_id
        LEFT JOIN final_brands b ON p.brand_id = b.brand_id
        WHERE p.storefront_id = ?
        ORDER BY p.product_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        } else {
            error_log("MySQLi Query Error: " . $this->db->error);
        }
        $stmt->close();
        return $data;
    }

    public function add_product($product_code, $product_name, $category_id, $brand_name, $store_id, $description = '', $stock_quantity = 0, $price = 0.00){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        // Check if product code already exists
        if ($this->product_code_exists($product_code)) {
            return ['status' => 'error', 'message' => 'Product code already exists. Please use a unique code.'];
        }

        // First, check if brand exists, if not, insert it
        $brand_id = $this->get_or_create_brand($brand_name);

        if (!$brand_id) {
            return ['status' => 'error', 'message' => 'Failed to create or find brand.'];
        }

        // Get seller_id from storefront
        $seller_id = $this->get_seller_id_from_storefront($store_id);
        if (!$seller_id) {
            return ['status' => 'error', 'message' => 'Invalid storefront.'];
        }

        $sql = "INSERT INTO final_products (product_code, product_name, category_id, brand_id, seller_id, storefront_id, product_description, stock_quantity, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssiiiisid", $product_code, $product_name, $category_id, $brand_id, $seller_id, $store_id, $description, $stock_quantity, $price);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success', 'product_code' => $product_code];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to add product: ' . $error];
        }
    }

    private function product_code_exists($product_code){
        $sql = "SELECT id FROM final_products WHERE product_code = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $product_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    private function get_seller_id_from_storefront($store_id){
        $sql = "SELECT seller_id FROM final_seller_storefront WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $store_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['seller_id'];
        }
        
        $stmt->close();
        return null;
    }

    public function get_products_by_category($category_id){
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT p.id AS product_id, p.product_name, p.product_description AS description, 
                p.stock_quantity, p.price, p.storefront_id AS store_id, b.name AS brand_name, u.store_name
                FROM final_products p
                LEFT JOIN final_brands b ON p.brand_id = b.brand_id
                LEFT JOIN final_seller_storefront s ON p.storefront_id = s.id
                LEFT JOIN final_users u ON s.seller_id = u.user_id
                WHERE p.category_id = ?
                ORDER BY p.product_name";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free();
        } else {
            error_log("MySQLi Query Error: " . $this->db->error);
        }
        $stmt->close();
        return $data;
    }

    private function get_or_create_brand($brand_name){
        $sql = "SELECT brand_id FROM final_brands WHERE name = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $brand_name);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['brand_id'];
        } else {
            $stmt->close();
            $sql = "INSERT INTO final_brands (name) VALUES (?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $brand_name);
            if ($stmt->execute()) {
                $brand_id = $this->db->insert_id;
                $stmt->close();
                return $brand_id;
            } else {
                $stmt->close();
                return false;
            }
        }
    }
}
