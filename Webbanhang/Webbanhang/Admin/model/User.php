<?php 
// File: Admin/model/User.php
require_once __DIR__ . '/../../Database/Database.php';

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

    // --- SETTERS ---
    private function setId($id) { $this->user_id = (int)$id; } 
    public function setHoTen($hoten) { $this->hoten = trim($hoten); }
    public function setEmail($email) { $this->email = trim($email); }
    public function setMatKhau($matkhau) { $this->matkhau = $matkhau; }
    public function setDienThoai($dienthoai) { $this->dienthoai = trim($dienthoai); }
    public function setDiaChi($diachi) { $this->diachi = $diachi; }
    public function setTierId($tier_id) { $this->tier_id = (int)$tier_id; }
    public function setTrangThai($trangthai) { $this->trangthai = (int)$trangthai; }


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

    // 2. LƯU (SAVE) - BỊ VÔ HIỆU HÓA
    public function save($is_update_password = false) {
        error_log("Đã cố gắng gọi phương thức LƯU/TẠO/CẬP NHẬT Người dùng đã bị vô hiệu hóa.");
        return false;
    }

    // 3. Phương thức update riêng biệt - BỊ VÔ HIỆU HÓA
    public function update($is_update_password = false) {
        return false;
    }


    // 4. XÓA (DELETE) - BỊ VÔ HIỆU HÓA
    public function delete($user_id) {
        error_log("Đã cố gắng gọi phương thức XÓA Người dùng đã bị vô hiệu hóa.");
        return false; 
    }
    
    // 5. Đọc tất cả người dùng (kèm phân trang và lọc theo Tier)
    public function readAll($limit, $offset, $tier_id = null) {
        $sql = "SELECT u.*, h.tenhang 
                FROM " . $this->table_name . " u
                LEFT JOIN hangkhachhang h ON u.tier_id = h.tier_id ";
        
        $where_params = [];
        $param_types = "";
        
        if ($tier_id !== null && $tier_id > 0) {
            $sql .= " WHERE u.tier_id = ?";
            $param_types .= "i";
            $where_params[] = $tier_id;
        }

        $sql .= " ORDER BY u.user_id DESC LIMIT ? OFFSET ?";
        $param_types .= "ii";
        $where_params[] = $limit;
        $where_params[] = $offset;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("Chuẩn bị thất bại: (" . $this->conn->errno . ") " . $this->conn->error);
             return null;
        }

        if (!empty($where_params)) {
            $params = array_merge([$param_types], $where_params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
        }

        $stmt->execute();
        return $stmt->get_result(); 
    }

    // 6. Lấy tổng số bản ghi (Count)
    public function getTotalRecords($tier_id = null) {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $param_types = "";
        $where_params = [];
        
        if ($tier_id !== null && $tier_id > 0) {
            $sql .= " WHERE tier_id = ?";
            $param_types .= "i";
            $where_params[] = $tier_id;
        }
        
        if (!empty($where_params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return 0;
            
            $params = array_merge([$param_types], $where_params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }
        
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }
    
    // Phương thức hỗ trợ để truyền tham chiếu cho bind_param
    private function refValues($arr) {
        if (strnatcmp(phpversion(),'5.3') >= 0) { 
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
    
    // 7. Cập nhật Trạng thái người dùng (Khóa/Mở khóa)
    public function updateStatus($user_id, $trangthai_moi) {
        if (empty($user_id)) {
            return false; 
        }
        
        $trangthai_moi = ($trangthai_moi == 1) ? 1 : 0;

        $sql = "UPDATE " . $this->table_name . " SET trangthai = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Chuẩn bị thất bại (updateStatus): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $trangthai_moi, $user_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Thực thi thất bại (updateStatus): " . $stmt->error);
            return false;
        }
    }
    
    // 8. KIỂM TRA CHỨC VỤ ADMIN (Được giữ lại ở đây để tiện sử dụng cho Controller này)
    public function isAdmin($staff_id) {
        $sql = "SELECT id_chucvu FROM nhanvien WHERE staff_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Chuẩn bị thất bại (isAdmin): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Giả định id_chucvu = 2 là Admin (hoặc chức vụ được phép gửi mail)
            // Dựa trên dữ liệu bạn cung cấp, Admin là id_chucvu = 2
            return (int)$row['id_chucvu'] === 2; 
        }
        return false;
    }
    
    // ⚠️ Phương thức createMessage đã được chuyển sang Message.php.
}