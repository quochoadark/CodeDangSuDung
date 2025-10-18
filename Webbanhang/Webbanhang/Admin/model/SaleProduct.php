<?php 
// File: Admin/Model/SaleProduct.php

class SaleProduct {
    // 1. THUỘC TÍNH (Properties)
    public $promo_id; 
    private $product_id;
    private $mota;
    private $giam; // Giá trị giảm (tiền hoặc %)
    private $ngaybatdau;
    private $ngayketthuc;
    
    // Thuộc tính CSDL
    private $conn;
    private $table_name = "khuyenmai_sanpham"; 

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS ---
    public function getId() { return $this->promo_id; }
    public function getProductId() { return $this->product_id; }
    public function getMoTa() { return $this->mota; }
    public function getGiam() { return $this->giam; }
    public function getNgayBatDau() { return $this->ngaybatdau; }
    public function getNgayKetThuc() { return $this->ngayketthuc; }


    // --- SETTERS ---
    private function setId($id) { $this->promo_id = (int)$id; } 

    public function setProductId($id) {
        if ((int)$id <= 0) { throw new Exception("Vui lòng chọn sản phẩm áp dụng."); }
        $this->product_id = (int)$id;
    }
    
    public function setMoTa($mota) {
        if (empty(trim($mota))) { throw new Exception("Mô tả khuyến mãi không được để trống."); }
        $this->mota = htmlspecialchars(strip_tags(trim($mota)));
    }
    
    public function setGiam($giam) {
        // Chấp nhận số tiền (>= 1) hoặc tỷ lệ (0 < % < 1)
        if (!is_numeric($giam) || $giam < 0) { throw new Exception("Giá trị giảm không hợp lệ."); }
        $this->giam = (float)$giam;
    }

    public function setNgayBatDau($ngay) {
        if (empty(trim($ngay))) { throw new Exception("Ngày bắt đầu không được để trống."); }
        $this->ngaybatdau = trim($ngay);
    }
    
    public function setNgayKetThuc($ngay) {
        if (empty(trim($ngay))) { throw new Exception("Ngày kết thúc không được để trống."); }
        $this->ngayketthuc = trim($ngay);
    }

    // --- ACTIVE RECORD METHODS ---

    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE promo_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $this->setId($row['promo_id']);
            // Sử dụng thuộc tính private để gán giá trị
            $this->product_id = (int)$row['product_id']; 
            $this->mota = $row['mota'];
            $this->giam = (float)$row['giam'];
            $this->ngaybatdau = $row['ngaybatdau'];
            $this->ngayketthuc = $row['ngayketthuc'];
            return true;
        }
        return false;
    }
    
    /**
     * Lưu (INSERT/UPDATE) dữ liệu vào CSDL
     * @return bool
     */
    public function save() {
        if (empty($this->promo_id)) {
            // INSERT
            $sql = "INSERT INTO " . $this->table_name . " 
                     (product_id, mota, giam, ngaybatdau, ngayketthuc) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bind_param(
                "isdss", // integer, string, double, string, string
                $this->product_id, 
                $this->mota, 
                $this->giam, 
                $this->ngaybatdau, 
                $this->ngayketthuc
            );

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            error_log("Insert failed: " . $stmt->error);
            return false;

        } else {
            // UPDATE
            $sql = "UPDATE " . $this->table_name . " 
                     SET product_id = ?, mota = ?, giam = ?, ngaybatdau = ?, ngayketthuc = ? 
                     WHERE promo_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            $stmt->bind_param(
                "isdssi", // integer, string, double, string, string, integer
                $this->product_id, 
                $this->mota, 
                $this->giam, 
                $this->ngaybatdau, 
                $this->ngayketthuc,
                $this->promo_id
            );
            
            if ($stmt === false) { error_log("Prepare failed (UPDATE): " . $this->conn->error); return false; }
            return $stmt->execute();
        }
    }

    /**
     * XÓA (DELETE) khuyến mãi hiện tại
     * @return bool
     */
    public function delete() {
        if (empty($this->promo_id)) { return false; }
        $sql = "DELETE FROM " . $this->table_name . " WHERE promo_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $this->promo_id); 
        return $stmt->execute();
    }
    
    /**
     * ĐỌC TẤT CẢ (READ ALL) - Kết hợp với bảng sanpham để lấy tên và giá gốc
     * @param object $db Kết nối CSDL
     * @return mixed Đối tượng resultset hoặc false
     */
    public static function readAllStatic($db) {
        $instance = new self($db);
        $sql = "SELECT sp.*, p.tensanpham, p.gia
                FROM " . $instance->table_name . " sp
                INNER JOIN sanpham p ON sp.product_id = p.product_id 
                ORDER BY sp.promo_id DESC";
        $result = $instance->conn->query($sql);
        return $result;
    }
    
    /**
     * Lấy danh sách sản phẩm cho form (chỉ cần ID và Tên)
     * @return array Danh sách sản phẩm
     */
    public function getProducts() {
        $sql = "SELECT product_id, tensanpham FROM sanpham ORDER BY tensanpham ASC";
        $result = $this->conn->query($sql);
        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free();
        }
        return $products;
    }
}