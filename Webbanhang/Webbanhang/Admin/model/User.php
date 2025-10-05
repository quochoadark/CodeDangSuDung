<?php 
// File: Admin/model/User.php
require_once __DIR__ . '/../../Database/database.php';

class User {
    // 1. THUỘC TÍNH (Properties)
    public $user_id; 
    private $hoten;
    private $email;
    private $matkhau;
    private $dienthoai;
    private $diachi;
    private $tier_id;
    private $trangthai;

    private $conn;
    private $table_name = "nguoidung"; // Tên bảng

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS (Đọc dữ liệu) ---
    public function getId() { return $this->user_id; }
    public function getHoTen() { return $this->hoten; }
    public function getEmail() { return $this->email; }
    public function getDienThoai() { return $this->dienthoai; }
    public function getDiaChi() { return $this->diachi; }
    public function getTierId() { return $this->tier_id; }
    public function getTrangThai() { return $this->trangthai; }

    // --- SETTERS (Ghi dữ liệu) ---
    private function setId($id) { $this->user_id = (int)$id; } 

    public function setHoTen($hoten) {
        if (empty(trim($hoten))) {
            throw new Exception("Họ tên không được để trống.");
        }
        $this->hoten = trim($hoten);
    }
    
    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ.");
        }
        $this->email = trim($email);
    }

    public function setMatKhau($matkhau) {
        if (strlen($matkhau) < 6) {
             throw new Exception("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        $this->matkhau = $matkhau;
    }

    public function setDienThoai($dienthoai) {
        $this->dienthoai = trim($dienthoai);
    }

    public function setDiaChi($diachi) {
        $this->diachi = $diachi;
    }
    
    public function setTierId($tier_id) {
        $this->tier_id = (int)$tier_id;
    }
    
    public function setTrangThai($trangthai) {
        $this->trangthai = (int)$trangthai;
    }


    // --- ACTIVE RECORD METHODS ---

    // 1. TÌM KIẾM THEO ID (Find)
    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // 2. LƯU (SAVE) - Xử lý INSERT và UPDATE
    public function save($is_update_password = false) {
        if (empty($this->user_id)) {
            // THỰC HIỆN CREATE (INSERT)
            $sql = "INSERT INTO " . $this->table_name . " 
                     (hoten, email, matkhau, dienthoai, diachi, tier_id, trangthai) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            // Hashing mật khẩu
            $hashed_password = password_hash($this->matkhau, PASSWORD_DEFAULT);
            $stmt->bind_param(
                "sssssii", 
                $this->hoten, 
                $this->email, 
                $hashed_password, 
                $this->dienthoai, 
                $this->diachi, 
                $this->tier_id, 
                $this->trangthai
            );

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            error_log("Insert failed: " . $stmt->error);
            return false;

        } else {
            // THỰC HIỆN UPDATE (Nếu user_id đã tồn tại)
            if($is_update_password && !empty($this->matkhau)) {
                // Cập nhật CẢ MẬT KHẨU
                 $sql = "UPDATE " . $this->table_name . " 
                          SET hoten = ?, email = ?, matkhau = ?, dienthoai = ?, diachi = ?, tier_id = ?, trangthai = ?
                          WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $hashed_password = password_hash($this->matkhau, PASSWORD_DEFAULT);
                $stmt->bind_param(
                    "sssssiii", 
                    $this->hoten, $this->email, $hashed_password, $this->dienthoai, $this->diachi, $this->tier_id, $this->trangthai, $this->user_id
                );
            } else {
                 // Cập nhật KHÔNG BAO GỒM MẬT KHẨU
                 $sql = "UPDATE " . $this->table_name . " 
                          SET hoten = ?, email = ?, dienthoai = ?, diachi = ?, tier_id = ?, trangthai = ? 
                          WHERE user_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param(
                    "ssssiii", 
                    $this->hoten, $this->email, $this->dienthoai, $this->diachi, $this->tier_id, $this->trangthai, $this->user_id
                );
            }

            if ($stmt === false) {
                error_log("Prepare failed (UPDATE): " . $this->conn->error);
                return false; 
            }
            
            return $stmt->execute();
        }
    }

    // 3. Phương thức update riêng biệt để tương thích với Controller
    public function update($is_update_password = false) {
        return $this->save($is_update_password);
    }


    // 4. XÓA (DELETE) 
    public function delete($user_id) {
        if (empty($user_id)) {
            return false; 
        }

        // Tạm thời vô hiệu hóa kiểm tra khóa ngoại (giữ nguyên logic bạn đã dùng trước)
        // $this->conn->query("SET FOREIGN_KEY_CHECKS=0"); 

        $sql = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (DELETE): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $user_id); 
        
        if ($stmt->execute()) {
            // $this->conn->query("SET FOREIGN_KEY_CHECKS=1"); // Bật lại
            return $stmt->affected_rows > 0;
        } else {
            // $this->conn->query("SET FOREIGN_KEY_CHECKS=1"); // Bật lại
            error_log("Execute failed (DELETE): " . $stmt->error);
            return false;
        }
    }
    
    // 5. Đọc tất cả người dùng (kèm phân trang) - ĐÃ SỬA ĐỂ CÓ TÊN HẠNG
    public function readAll($limit, $offset) {
        // SỬA: Thêm LEFT JOIN với bảng hangkhachhang (h) để lấy tên hạng (tenhang)
        $sql = "SELECT u.*, h.tenhang 
                FROM " . $this->table_name . " u
                LEFT JOIN hangkhachhang h ON u.tier_id = h.tier_id  /* <-- DÒNG BỔ SUNG QUAN TRỌNG */
                ORDER BY u.user_id DESC
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

    // 6. Lấy tổng số bản ghi (Count)
    public function getTotalRecords() {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }
    
    // 7. Cập nhật Trạng thái người dùng (Khóa/Mở khóa)
    public function updateStatus($user_id, $trangthai_moi) {
        if (empty($user_id)) {
            return false; 
        }
        
        // Đảm bảo trạng thái mới là 0 hoặc 1
        $trangthai_moi = ($trangthai_moi == 1) ? 1 : 0;

        $sql = "UPDATE " . $this->table_name . " SET trangthai = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (updateStatus): " . $this->conn->error);
            return false;
        }
        
        // bind_param: i (integer) cho trangthai, i (integer) cho user_id
        $stmt->bind_param("ii", $trangthai_moi, $user_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Execute failed (updateStatus): " . $stmt->error);
            return false;
        }
    }
}