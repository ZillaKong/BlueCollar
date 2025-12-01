<?php
require_once '../settings/db_class.php';

class Brands extends db_connection {

    public function get_brand(){
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT b.brand_id AS brand_id, b.name AS brand_name, COUNT(p.id) AS total_products
        FROM final_brands b
        LEFT JOIN final_products p ON b.brand_id = p.brand_id
        GROUP BY b.brand_id, b.name
        ORDER BY b.name";
        // Since this query has NO user input, we can use a direct query.
        $result = $this->db->query($sql);

        $data = []; // Initialize data array

        if ($result) {
            // Fetch all rows into an array
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            $result->free(); // Free the result set memory
        } else {
            // Log or handle the SQL error
            error_log("MySQLi Query Error: " . $this->db->error);
        }

        return $data;
    }

    public function update_brand($brand_id, $brand_name){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }
        $sql = "UPDATE final_brands SET name = ? WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("si", $brand_name, $brand_id);
            if ($stmt->execute()) {
                $stmt->close();
                return ['status' => 'success'];
            } else {
                $error = $this->db->error;
                $stmt->close();
                return ['status' => 'error', 'message' => 'Failed to update brand: ' . $error];
            }
        } else {
            error_log("MySQLi Prepare Error: " . $this->db->error);
            return ['status' => 'error', 'message' => 'Prepare statement failed.'];
        }
    }

    public function delete_brand($brand_id){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }
        $sql = "DELETE FROM final_brands WHERE brand_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $brand_id);
            if ($stmt->execute()) {
                $stmt->close();
                return ['status' => 'success'];
            } else {
                $error = $this->db->error;
                $stmt->close();
                return ['status' => 'error', 'message' => 'Failed to delete brand: ' . $error];
            }
        } else {
            error_log("MySQLi Prepare Error: " . $this->db->error);
            return ['status' => 'error', 'message' => 'Prepare statement failed.'];
        }
    }
}
