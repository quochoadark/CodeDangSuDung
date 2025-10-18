<?php 
// File: Admin/Model/Login.php (ĐÃ BỔ SUNG id_chucvu VÀ GETTER)

class Login
{
    private $conn;
    private $table_name = "nhanvien"; 

    // Các thuộc tính nhân viên
    private $staff_id; 
    private $hoten;
    private $email;
    private $matkhau;
    private $trangthai;
    private $id_chucvu; // ⭐ BỔ SUNG THUỘC TÍNH ID CHỨC VỤ

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    // ======= GETTERS =======
    public function getStaffId() 
    {
        return $this->staff_id;
    }

    public function getHoTen()
    {
        return $this->hoten;
    }

    public function getMatKhau()
    {
        return $this->matkhau;
    }
    
    public function getIdChucVu() // ⭐ GETTER MỚI
    {
        return $this->id_chucvu;
    }
    
    // ======= SETTERS =======
    public function setStaffId($staff_id) 
    {
        $this->staff_id = $staff_id;
    }
    
    public function setEmail($email)
    {
        $this->email = trim($email);
    }

    public function checkStatusById(int $staff_id)
    {
        $sql = "SELECT trangthai FROM {$this->table_name} WHERE staff_id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi prepare trong checkStatusById(): " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("i", $staff_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return (int)$row['trangthai']; // Trả về 1 hoặc 0
        }

        return false; // Không tìm thấy ID
    }
    // =========================================================
    
    /**
     * Cập nhật token hash vào DB và thời gian hết hạn (Dùng cho Remember Me)
     */
    public function setRememberToken($token_hash) 
    {
        $expiry = $token_hash ? date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)) : NULL; 
        
        $sql = "UPDATE {$this->table_name} 
                 SET remember_token_hash = ?, token_expiry = ? 
                 WHERE staff_id = ?";
                 
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ssi", $token_hash, $expiry, $this->staff_id); 
        $stmt->execute();
        
        if ($stmt->error) {
            error_log("Lỗi DB khi cập nhật token admin: " . $stmt->error);
        }
        return $stmt->affected_rows > 0;
    }

    /**
     * Tạo token gốc, mã hóa, lưu hash vào DB và trả về token gốc (Dùng cho LoginController)
     */
    public function createRememberToken($staff_id)
    {
        $token = bin2hex(random_bytes(32)); 
        $token_hash = hash('sha256', $token);
        
        $this->staff_id = $staff_id; 
        
        if ($this->setRememberToken($token_hash)) {
            return $token; 
        }
        return false;
    }

    /**
     * Xóa token khỏi DB (Dùng cho LoginController)
     */
    public function clearRememberToken($staff_id)
    {
        $this->staff_id = $staff_id;
        return $this->setRememberToken(NULL); 
    }
    
    /**
     * Tìm người dùng đang hoạt động theo email (trangthai = 1)
     */
    public function findActiveByEmail()
    {
        // ⭐ Cập nhật: Lấy thêm id_chucvu
        $sql = "SELECT staff_id, hoten, email, matkhau, trangthai, id_chucvu
                 FROM {$this->table_name}
                 WHERE email = ? AND trangthai = 1 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
             error_log("Lỗi prepare trong findActiveByEmail(): " . $this->conn->error);
             return false;
        }

        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $this->staff_id = $row['staff_id'];
            $this->hoten = $row['hoten'];
            $this->email = $row['email'];
            $this->matkhau = $row['matkhau'];
            $this->trangthai = $row['trangthai'];
            $this->id_chucvu = $row['id_chucvu']; // ⭐ LƯU id_chucvu
            return true;
        }

        return false;
    }
    
    /**
     * Tìm người dùng theo Remember Token Hash và kiểm tra trạng thái (Dùng cho LoginController)
     */
    public function findByRememberToken(string $token_hash)
    {
        // ⭐ Cập nhật: Lấy thêm id_chucvu
        $sql = "SELECT staff_id, hoten, trangthai, token_expiry, id_chucvu
                 FROM {$this->table_name}
                 WHERE remember_token_hash = ? AND trangthai = 1
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (strtotime($row['token_expiry']) < time()) {
                 return false; 
            }
            
            $this->staff_id = $row['staff_id'];
            $this->hoten = $row['hoten'];
            $this->trangthai = $row['trangthai'];
            $this->id_chucvu = $row['id_chucvu']; // ⭐ LƯU id_chucvu
            
            return true; 
        }

        return false; 
    }
}
// Kết thúc file Login.php