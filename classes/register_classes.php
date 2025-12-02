<?php

require_once __DIR__ . '/../settings/db_class.php';

class RegisterUser extends db_connection
{
    private $user_id;
    private $first_name;
    private $last_name;
    private $email;
    private $password;
    private $role;
    private $phone;
    private $store_name;
    private $store_description;
    private $company_name;
    private $trade_type;

    public function __construct($user_id = null, $role = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->role = $role;
            $this->load_user_data();
        }
    }

    private function load_user_data($user_id = null, $role = null)
{
    // Ensure the connection and user_id are set
    if (!$this->db || !$this->user_id || !$this->role) {
        
        return; 
    }

    // 1. Prepare the statement using a placeholder (?)
    $sql = "SELECT first_name, last_name, email, role, phone, store_name, 
                   store_description, company_name, trade_type 
            FROM final_users 
            WHERE user_id = ?";
            
    $stmt = $this->db->prepare($sql);

    // Check for statement preparation error
    if (!$stmt) {
        error_log("Failed to prepare user data query: " . $this->db->error);
        return;
    }

    // 2. Bind the user_id parameter ('i' stands for integer)
    $stmt->bind_param("i", $this->user_id);

    // 3. Execute the statement
    $stmt->execute();
    
    // 4. Get the result
    $result = $stmt->get_result();

    // 5. Fetch the data and assign properties
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        
        $this->first_name = $row['first_name'];
        $this->last_name = $row['last_name'];
        $this->email = $row['email'];
        $this->role = $row['role'];
        $this->phone = $row['phone'];

        if ($this->role === 'supplier') {
        $this->store_name = $row['store_name'];
        $this->store_description = $row['store_description'];
        $this->company_name = $row['company_name'];}
        
        else  if ($this->role === 'buyer') {
        $this->trade_type = $row['trade_type'];
        $this->company_name = $row['company_name'];}
    }

    $stmt->close();
}

    public function register($first_name, $last_name, $email, $password, $role, $company_name, $phone, $store_name = null, $store_description = null, $trade_type = null)
    {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $username = 'user_' . time() . rand(1000, 9999); // Always generate unique username
        
        if ($role === 'supplier') {
            $sql = "INSERT INTO final_users (first_name, last_name, company_name, username, email, password, phone, role, store_name, store_description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ssssssssss", $first_name, $last_name, $company_name, $username, $email, $hashed_password, $phone, $role, $store_name, $store_description);
        }
        else if ($role === 'buyer') {
            $sql = "INSERT INTO final_users (first_name, last_name, company_name, username, email, password, phone, role, buyer_type)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("sssssssss", $first_name, $last_name, $company_name, $username, $email, $hashed_password, $phone, $role, $trade_type);
        }
        
        if ($stmt->execute()) {
            $new_user_id = $this->db->insert_id;
            $stmt->close();
            
            // If supplier, create storefront automatically
            if ($role === 'supplier') {
                $storefront_result = $this->createStorefront($new_user_id);
                if ($storefront_result['status'] === 'error') {
                    // Log error but don't fail registration
                    error_log("Failed to create storefront for user $new_user_id: " . $storefront_result['message']);
                }
                return ['status' => 'success', 'user_id' => $new_user_id, 'store_id' => $storefront_result['store_id'] ?? null];
            }
            
            return ['status' => 'success', 'user_id' => $new_user_id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['status' => 'error', 'message' => $error];
        }
    }

    private function createStorefront($seller_id)
    {
        $sql = "INSERT INTO final_seller_storefront (seller_id) VALUES (?)";
        $stmt = $this->db->prepare($sql);
        
        if (!$stmt) {
            return ['status' => 'error', 'message' => 'Failed to prepare storefront query: ' . $this->db->error];
        }
        
        $stmt->bind_param("i", $seller_id);
        
        if ($stmt->execute()) {
            $store_id = $this->db->insert_id;
            $stmt->close();
            return ['status' => 'success', 'store_id' => $store_id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['status' => 'error', 'message' => $error];
        }
    }

    public function loginUser($email, $password){
        $sql = "SELECT user_id, password, role FROM final_users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                return ['status' => 'success', 'user_id' => $row['user_id'], 'user_role' => $row['role'], 'message' => 'Login successful.'];
            } else {
                return ['status' => 'error', 'message' => 'Invalid email or password.'];
            }
        } else {
            return ['status' => 'error', 'message' => 'Account doesnt exists. Please register first.'];
        }
    }
}
