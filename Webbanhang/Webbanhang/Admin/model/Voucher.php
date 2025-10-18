<?php 
// File: Admin/model/Voucher.php
require_once __DIR__ . '/../../Database/Database.php';

class Voucher {
    // 1. THUỘC TÍNH (Properties) - Private/Public tương ứng với CSDL
    public $voucher_id; 
    private $makhuyenmai;
    private $giam;
    private $ngayhethan;
    private $soluong;
    private $luotsudung; // Thêm cột luotsudung nếu có trong CSDL

    private $conn;
    private $table_name = "makhuyenmai"; // Tên bảng

    // 2. CONSTRUCTOR
    public function __construct($db) {
        // $db là đối tượng kết nối mysqli được truyền vào
        $this->conn = $db;
    }

    // --- GETTERS (Đọc dữ liệu) ---
    public function getId() { return $this->voucher_id; }
    public function getMaKhuyenMai() { return $this->makhuyenmai; }
    public function getGiam() { return $this->giam; }
    public function getNgayHetHan() { return $this->ngayhethan; }
    public function getSoLuong() { return $this->soluong; }
    public function getLuotSuDung() { return $this->luotsudung; }


    // --- SETTERS (Ghi dữ liệu) ---
    private function setId($id) { 
        $this->voucher_id = (int)$id; 
    } 

    public function setMaKhuyenMai($ma) {
        if (empty(trim($ma))) {
            throw new Exception("Mã khuyến mãi không được để trống.");
        }
        $this->makhuyenmai = htmlspecialchars(strip_tags(trim($ma)));
    }
    
    public function setGiam($giam) {
        if (!is_numeric($giam) || $giam < 0) {
            throw new Exception("Giá trị giảm không hợp lệ.");
        }
        // Ép kiểu float/decimal
        $this->giam = (float)$giam;
    }

    public function setNgayHetHan($ngayhethan) {
        if (empty(trim($ngayhethan))) {
             throw new Exception("Ngày hết hạn không được để trống.");
        }
        // Giả định định dạng ngày tháng là chuẩn SQL (YYYY-MM-DD)
        $this->ngayhethan = trim($ngayhethan);
    }

    public function setSoLuong($soluong) {
        // Cho phép NULL hoặc rỗng nếu CSDL cho phép
        $this->soluong = (empty($soluong) || !is_numeric($soluong)) ? null : (int)$soluong;
    }
    
    public function setLuotSuDung($luotsudung) {
        $this->luotsudung = (int)$luotsudung;
    }


    // --- ACTIVE RECORD METHODS ---

    // 1. TÌM KIẾM VÀ TẢI DỮ LIỆU VÀO ĐỐI TƯỢNG
    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE voucher_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Tải dữ liệu vào thuộc tính của đối tượng hiện tại
            $this->setId($row['voucher_id']);
            $this->setMaKhuyenMai($row['makhuyenmai']);
            $this->setGiam($row['giam']);
            $this->setNgayHetHan($row['ngayhethan']);
            $this->setSoLuong($row['soluong']);
            $this->setLuotSuDung($row['luotsudung'] ?? 0); // Giả định cột này tồn tại
            return true;
        }
        return false;
    }
    
    // 2. LƯU (SAVE) - Xử lý INSERT và UPDATE
    public function save() {
        if (empty($this->voucher_id)) {
            // THỰC HIỆN CREATE (INSERT)
            $sql = "INSERT INTO " . $this->table_name . " 
                     (makhuyenmai, giam, ngayhethan, soluong) 
                     VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            
            // Xử lý giá trị NULL/0 cho cột soluong
            $soluong_db = is_null($this->soluong) ? 0 : $this->soluong;
            
            $stmt->bind_param(
                "sssi", // string, string(float), string(date), integer
                $this->makhuyenmai, 
                $this->giam, 
                $this->ngayhethan, 
                $soluong_db 
            );

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            error_log("Insert failed: " . $stmt->error);
            return false;

        } else {
            // THỰC HIỆN UPDATE (Nếu voucher_id đã tồn tại)
            $sql = "UPDATE " . $this->table_name . " 
                      SET makhuyenmai = ?, giam = ?, ngayhethan = ?, soluong = ?, luotsudung = ?
                      WHERE voucher_id = ?";
            
            $stmt = $this->conn->prepare($sql);
            
            $soluong_db = is_null($this->soluong) ? 0 : $this->soluong;
            
            $stmt->bind_param(
                "sssiii", // string, string(float), string(date), integer, integer, integer
                $this->makhuyenmai, 
                $this->giam, 
                $this->ngayhethan, 
                $soluong_db, 
                $this->luotsudung,
                $this->voucher_id
            );

            if ($stmt === false) {
                error_log("Prepare failed (UPDATE): " . $this->conn->error);
                return false; 
            }
            
            return $stmt->execute();
        }
    }

    // 3. XÓA (DELETE) 
    public function delete() {
        if (empty($this->voucher_id)) {
            return false; 
        }

        $sql = "DELETE FROM " . $this->table_name . " WHERE voucher_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (DELETE): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $this->voucher_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Execute failed (DELETE): " . $stmt->error);
            return false;
        }
    }
    
    // 4. ĐỌC TẤT CẢ (READ ALL) - Trả về kết quả thô cho Controller xử lý
    // Phương thức này là Static để gọi không cần khởi tạo đối tượng Entity
    public static function readAllStatic($db) {
        $instance = new self($db);
        $sql = "SELECT * FROM " . $instance->table_name . " ORDER BY ngayhethan DESC";
        $result = $instance->conn->query($sql);
        return $result;
    }
    
    // 5. Kiểm tra trùng mã (Sử dụng cho logic tạo/cập nhật)
    public function checkDuplicateCode($makhuyenmai, $ignore_id = null) {
        $query = "SELECT voucher_id FROM " . $this->table_name . " WHERE makhuyenmai = ?";
        $types = "s";
        $params = [htmlspecialchars(strip_tags($makhuyenmai))];

        if ($ignore_id) {
            $query .= " AND voucher_id != ?";
            $types .= "i";
            $params[] = $ignore_id;
        }
        
        $stmt = $this->conn->prepare($query);
        
        // Sử dụng call_user_func_array để bind_param linh hoạt với mảng
        $bind_names = [$types];
        foreach ($params as &$param) {
            $bind_names[] = &$param;
        }
        call_user_func_array([$stmt, 'bind_param'], $bind_names);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->num_rows;
        $stmt->close();
        
        return $count > 0;
    }
}