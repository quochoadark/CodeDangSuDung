<?php

// Đổi tên class để phù hợp với tên file
class Login {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function getStaffByEmail($email) {
        // CHỈNH SỬA: Lấy id_chucvu. Giả sử trạng thái 'hoat dong' là 1.
        $sql = "SELECT staff_id, email, matkhau, id_chucvu, hoten FROM nhanvien WHERE email = ? AND trangthai = 1"; 

        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            // Log lỗi nếu cần
            return null;
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}