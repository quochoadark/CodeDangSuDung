<?php
// File: Admin/model/Staff.php
require_once __DIR__ . '/../../Database/database.php';

class Staff {
    // 1. THUỘC TÍNH (Properties)
    public $staff_id; 
    private $hoten;
    private $email;
    private $matkhau;
    private $dienthoai;
    private $trangthai;
    private $id_chucvu;

    private $conn;
    private $table_name = "nhanvien"; // Tên bảng

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS & SETTERS ---
    // (Tôi chỉ tập trung vào setters có validation cơ bản)
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
    
    public function setTrangThai($trangthai) {
        $this->trangthai = (int)$trangthai;
    }
    
    public function setIdChucVu($id_chucvu) {
        if ((int)$id_chucvu === 0) {
             throw new Exception("Vui lòng chọn chức vụ.");
        }
        $this->id_chucvu = (int)$id_chucvu;
    }

    // --- ACTIVE RECORD METHODS ---

    // 1. LƯU (SAVE) - INSERT
    public function save() {
        if (!empty($this->staff_id)) return $this->update(false); // Chuyển sang update nếu có ID

        $sql = "INSERT INTO " . $this->table_name . " 
                 (hoten, email, matkhau, dienthoai, trangthai, id_chucvu) 
                 VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             error_log("Prepare failed (Staff Insert): " . $this->conn->error);
             return false;
        }

        // Hashing mật khẩu
        $hashed_password = password_hash($this->matkhau, PASSWORD_DEFAULT);
        
        // sssssii: 5 strings, 2 integers
        $stmt->bind_param(
            "ssssii", 
            $this->hoten, 
            $this->email, 
            $hashed_password, 
            $this->dienthoai, 
            $this->trangthai, 
            $this->id_chucvu
        );

        if ($stmt->execute()) {
            $this->staff_id = $this->conn->insert_id; 
            return true;
        }
        error_log("Execute failed (Staff Insert): " . $stmt->error);
        return false;
    }

    // 2. CẬP NHẬT (UPDATE)
    public function update($is_update_password = false) {
        if (empty($this->staff_id)) return false; 

        if($is_update_password && !empty($this->matkhau)) {
            // Cập nhật CẢ MẬT KHẨU
             $sql = "UPDATE " . $this->table_name . " 
                      SET hoten = ?, email = ?, matkhau = ?, dienthoai = ?, trangthai = ?, id_chucvu = ?
                      WHERE staff_id = ?";
            $stmt = $this->conn->prepare($sql);
            $hashed_password = password_hash($this->matkhau, PASSWORD_DEFAULT);
            $stmt->bind_param(
                "ssssiii", // 4 strings, 3 integers
                $this->hoten, $this->email, $hashed_password, $this->dienthoai, $this->trangthai, $this->id_chucvu, $this->staff_id
            );
        } else {
             // Cập nhật KHÔNG BAO GỒM MẬT KHẨU
             $sql = "UPDATE " . $this->table_name . " 
                      SET hoten = ?, email = ?, dienthoai = ?, trangthai = ?, id_chucvu = ? 
                      WHERE staff_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sssiii", // 3 strings, 3 integers
                $this->hoten, $this->email, $this->dienthoai, $this->trangthai, $this->id_chucvu, $this->staff_id
            );
        }

        if ($stmt === false) {
            error_log("Prepare failed (Staff Update): " . $this->conn->error);
            return false; 
        }
        
        return $stmt->execute();
    }
    
    // 3. ĐỌC TẤT CẢ (READ ALL)
    public function readAll($limit, $offset) {
        // LEFT JOIN với bảng chucvu (c) để lấy tên chức vụ (ten_chucvu)
        $sql = "SELECT s.*, c.ten_chucvu 
                FROM " . $this->table_name . " s
                LEFT JOIN chucvu c ON s.id_chucvu = c.id_chucvu 
                ORDER BY s.staff_id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("Prepare failed (Staff readAll): " . $this->conn->error);
             return null;
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result(); 
    }
    
    // 4. TÌM KIẾM THEO ID (Find)
    public function find($id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE staff_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    // 5. XÓA (DELETE) 
    public function delete($staff_id) {
        if (empty($staff_id)) return false; 

        $sql = "DELETE FROM " . $this->table_name . " WHERE staff_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (Staff Delete): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $staff_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
            error_log("Execute failed (Staff Delete): " . $stmt->error);
            return false;
        }
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
    public function updateStatus($staff_id, $new_status) {
    $query = "UPDATE " . $this->table_name . " 
              SET trangthai = ? 
              WHERE staff_id = ?";

    // Chuẩn bị statement
    $stmt = $this->conn->prepare($query);

    // Gán tham số
    // 'ii' nghĩa là hai tham số đều là integer (int)
    $stmt->bind_param("ii", $new_status, $staff_id);

    // Thực thi
    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}

}