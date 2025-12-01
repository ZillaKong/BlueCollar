<?php
require_once '../settings/db_class.php';

class Category extends db_connection {

    public function get_category(){
        if (!$this->db_connect()) {
            return [];
        }
        $sql = "SELECT cat_id AS category_id, name AS category_name, COUNT(*) AS total_products FROM final_categories
        GROUP BY cat_id, name
        ORDER BY name";
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

    public function update_category($category_id, $category_name){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "UPDATE final_categories SET name = ? WHERE cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $category_name, $category_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to update category: ' . $error];
        }
    }

    public function delete_category($category_id){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "DELETE FROM final_categories WHERE cat_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $category_id);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to delete category: ' . $error];
        }
    }

    public function add_category($category_name){
        if (!$this->db_connect()) {
            return ['status' => 'error', 'message' => 'Database connection failed.'];
        }

        $sql = "INSERT INTO final_categories (name) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $stmt->close();
            return ['status' => 'success'];
        } else {
            $error = $this->db->error;
            $stmt->close();
            return ['status' => 'error', 'message' => 'Failed to add category: ' . $error];
        }
    }
}
