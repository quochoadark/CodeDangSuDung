<?php
// File: Admin/Model/Staff.php (ĐÃ LOẠI BỎ UPDATE & DELETE)
require_once __DIR__ . '/../../Database/Database.php';

class Staff
{
    // =====================================================
    // 1. THUỘC TÍNH (Properties)
    // =====================================================
    public $staff_id;
    private $hoten;
    private $email;
    private $matkhau;
    private $dienthoai;
    private $trangthai;
    private $id_chucvu;

    private $conn;
    private $table_name = "nhanvien"; // Tên bảng

    // =====================================================
    // 2. CONSTRUCTOR
    // =====================================================
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // =====================================================
    // 3. SETTERS
    // =====================================================
    public function setHoTen($hoten)
    {
        if (empty(trim($hoten))) {
            throw new Exception("Họ tên không được để trống.");
        }
        $this->hoten = trim($hoten);
    }

    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ.");
        }
        $this->email = trim($email);
    }

    public function setMatKhau($matkhau)
    {
        if (strlen($matkhau) < 6) {
            throw new Exception("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        $this->matkhau = $matkhau;
    }

    public function setDienThoai($dienthoai)
    {
        $this->dienthoai = trim($dienthoai);
    }

    public function setTrangThai($trangthai)
    {
        $this->trangthai = (int)$trangthai;
    }

    public function setIdChucVu($id_chucvu)
    {
        if ((int)$id_chucvu === 0) {
            throw new Exception("Vui lòng chọn chức vụ.");
        }
        $this->id_chucvu = (int)$id_chucvu;
    }

    // =====================================================
    // 4. ACTIVE RECORD METHODS
    // =====================================================

    /**
     * 1. LƯU (INSERT)
     */
    public function save()
    {
        if (!empty($this->staff_id)) {
            return false;
        }

        $sql = "INSERT INTO {$this->table_name} 
                (hoten, email, matkhau, dienthoai, trangthai, id_chucvu)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed (Staff Insert): " . $this->conn->error);
            return false;
        }

        $hashed_password = password_hash($this->matkhau, PASSWORD_DEFAULT);

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

    /**
     * 2. CẬP NHẬT (UPDATE) - BỊ VÔ HIỆU HÓA
     */
    public function update($is_update_password = false)
    {
        return false;
    }

    /**
     * 3. ĐỌC TẤT CẢ (READ ALL)
     */
    public function readAll($limit, $offset)
    {
        $sql = "SELECT s.*, c.ten_chucvu 
                FROM {$this->table_name} s
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

    /**
     * 4. TÌM KIẾM THEO ID (Find)
     */
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table_name} WHERE staff_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row;
        }
        return null;
    }

    /**
     * 5. XÓA (DELETE) - BỊ VÔ HIỆU HÓA
     */
    public function delete($staff_id)
    {
        return false;
    }

    /**
     * 6. Lấy tổng số bản ghi (Count)
     */
    public function getTotalRecords()
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table_name}";
        $result = $this->conn->query($sql);

        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    /**
     * 7. Cập nhật trạng thái (Khóa/Mở khóa)
     */
    public function updateStatus($staff_id, $new_status)
    {
        $query = "UPDATE {$this->table_name}
                  SET trangthai = ?
                  WHERE staff_id = ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $new_status, $staff_id);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }

        $stmt->close();
        return false;
    }
}
