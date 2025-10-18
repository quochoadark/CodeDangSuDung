<?php
// File: Admin/model/Category.php
require_once __DIR__ . '/../../Database/Database.php'; 

class Category{
    private $conn;
    private $table_name = "danhmucsanpham";

    // ✅ Giữ category_id để phục vụ lưu trữ và cập nhật (nếu có)
    public  $category_id;
    private $tendanhmuc;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getId() { return $this->category_id; }
    public function getTenDanhMuc() { return $this->tendanhmuc; }

    private function setId($id) { $this->category_id = (int)$id; } 

    public function setTenDanhMuc($tendanhmuc) {
        if (empty(trim($tendanhmuc))) {
            throw new Exception("Tên danh mục không được để trống.");
        }
        $this->tendanhmuc = trim($tendanhmuc);
    }
    
    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE category_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // LƯU (SAVE) - Xử lý INSERT và UPDATE
    public function save() {
        if (empty($this->category_id)) {
            // THỰC HIỆN CREATE (INSERT)
            $sql = "INSERT INTO " . $this->table_name . " (tendanhmuc) VALUES (?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $this->tendanhmuc);

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            return false;

        } else {
            // THỰC HIỆN UPDATE
            $sql = "UPDATE " . $this->table_name . " SET tendanhmuc = ? WHERE category_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $this->tendanhmuc, $this->category_id);
            
            if ($stmt === false) {
                 error_log("Prepare failed (UPDATE): " . $this->conn->error);
                 return false; 
            }
            return $stmt->execute();
        }
    }
    
    
    // READ ALL
    public function readAll() {
        $sql = "SELECT category_id, tendanhmuc FROM " . $this->table_name . " ORDER BY category_id DESC";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (READ ALL): " . $this->conn->error);
            return false;
        }
        
        $stmt->execute();
        return $stmt; 
    }
}
?>