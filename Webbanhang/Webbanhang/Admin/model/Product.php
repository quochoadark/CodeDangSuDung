<?php
// File: Admin/model/Product.php

require_once __DIR__ . '/../../Database/Database.php'; // Đảm bảo lớp kết nối Database tồn tại

class Product {
    // 1. THUỘC TÍNH (Properties)
    public $product_id; 
    private $tensanpham;
    private $category_id;
    private $gia;
    private $tonkho;
    private $mota;
    private $img;
    private $ngaytao; // Giữ lại nhưng không dùng setter
    private $is_deleted; 

    private $conn;
    private $table_name = "sanpham";

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS (Đọc dữ liệu) ---
    public function getId() { return $this->product_id; }
    public function getImg() { return $this->img; }
    // ... các getter khác

    // --- SETTERS (Gán dữ liệu) ---
    private function setId($id) { $this->product_id = (int)$id; } 

    public function setTenSanPham($tensanpham) {
        if (empty(trim($tensanpham))) { throw new Exception("Tên sản phẩm không được để trống."); }
        $this->tensanpham = trim($tensanpham);
    }
    public function setCategoryId($category_id) { $this->category_id = (int)$category_id; }
    public function setGia($gia) {
        if ($gia < 0) { throw new Exception("Giá phải lớn hơn hoặc bằng 0."); }
        $this->gia = (float)$gia;
    }
    public function setTonKho($tonkho) {
        if ($tonkho < 0) { throw new Exception("Tồn kho không thể là số âm."); }
        $this->tonkho = (int)$tonkho;
    }
    public function setMoTa($mota) { $this->mota = $mota; }
    public function setImg($img) { $this->img = $img; }
    public function setIsDeleted($is_deleted) { $this->is_deleted = (int)$is_deleted; }


    // --- ACTIVE RECORD METHODS ---

    // 3. TÌM KIẾM THEO ID (Find)
    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null; 
    }

    // 4. LƯU (SAVE) - INSERT / UPDATE
    public function save() {
        if (empty($this->product_id)) {
            // THỰC HIỆN CREATE (INSERT)
            $sql = "INSERT INTO " . $this->table_name . " 
                        (tensanpham, category_id, gia, tonkho, mota, img, is_deleted) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)"; // Đã thêm is_deleted
            
            $stmt = $this->conn->prepare($sql);
            if ($stmt === false) {
                error_log("Prepare failed (INSERT): " . $this->conn->error);
                return false;
            }

            $default_is_deleted = 0; // Giá trị mặc định là 0
            
            $stmt->bind_param(
                "sdiissi", // Thêm 'i' cho is_deleted
                $this->tensanpham, 
                $this->category_id, 
                $this->gia, 
                $this->tonkho, 
                $this->mota, 
                $this->img,
                $default_is_deleted // Gán giá trị 0
            );

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            error_log("Execute failed (INSERT): " . $stmt->error);
            return false;

        } else {
            // THỰC HIỆN UPDATE
            return $this->update();
        }
    }

    // 5. CẬP NHẬT (UPDATE)
    public function update() {
        if (empty($this->product_id)) {
            error_log("Cannot update: Product ID is missing.");
            return false;
        }
        
        $sql = "UPDATE " . $this->table_name . " 
                    SET tensanpham = ?, category_id = ?, gia = ?, tonkho = ?, mota = ?, img = ? 
                    WHERE product_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             error_log("Prepare failed (UPDATE): " . $this->conn->error);
             return false; 
        }

        $stmt->bind_param(
            "sdiissi", 
            $this->tensanpham, 
            $this->category_id, 
            $this->gia, 
            $this->tonkho, 
            $this->mota, 
            $this->img, 
            $this->product_id
        );
        
        if ($stmt->execute()) {
             return true; 
        } else {
             error_log("Execute failed (UPDATE): " . $stmt->error);
             return false;
        }
    }


    // 6. XÓA MỀM (SOFT DELETE) - Đặt is_deleted = 1
    public function delete($product_id) {
        if (empty($product_id)) { return false; }

        $sql = "UPDATE " . $this->table_name . " SET is_deleted = 1 WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             error_log("Prepare failed (SOFT DELETE): " . $this->conn->error);
             return false;
        }
        
        $stmt->bind_param("i", $product_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
             error_log("Execute failed (SOFT DELETE): " . $stmt->error);
             return false;
        }
    }
    
    // 7. XÓA CỨNG (HARD DELETE) - Xóa vĩnh viễn khỏi DB
    public function hardDelete($product_id) {
        if (empty($product_id)) { return false; }

        $sql = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) { 
            error_log("Prepare failed (HARD DELETE): " . $this->conn->error);
            return false; 
        }
        
        $stmt->bind_param("i", $product_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Execute failed (HARD DELETE): " . $stmt->error);
            return false;
        }
    }
    
    // 8. KIỂM TRA LIÊN KẾT ĐƠN HÀNG
    public function hasRelatedOrders($product_id) {
        if (empty($product_id)) { return true; } 

        $sql = "SELECT 1 FROM chitietdonhang WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) { 
            error_log("Prepare failed (hasRelatedOrders): " . $this->conn->error);
            return true; // Coi như có lỗi DB, tránh xóa cứng
        } 

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->num_rows > 0; 
    }


    // 9. Lấy tổng số bản ghi (Count) - CHỈ ĐẾM SẢN PHẨM CHƯA XÓA MỀM (is_deleted = 0)
    public function getTotalRecords() {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE is_deleted = 0";
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    // 10. Đọc tất cả sản phẩm (Read All) - CHỈ LẤY SẢN PHẨM CHƯA XÓA MỀM (is_deleted = 0)
    public function readAll($limit, $offset) {
        $sql = "SELECT p.*, c.tendanhmuc 
                FROM " . $this->table_name . " p
                JOIN danhmucsanpham c ON p.category_id = c.category_id
                WHERE p.is_deleted = 0
                ORDER BY p.product_id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
             return null;
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result(); 
    }

}