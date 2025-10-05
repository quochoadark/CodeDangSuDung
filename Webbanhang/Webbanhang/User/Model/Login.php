<?php

class Login {
    private $conn;

    public function __construct($db_connection) {
        // Khởi tạo kết nối CSDL
        $this->conn = $db_connection;
    }

    public function getUserByEmail($email) {
        // Lưu ý: Dùng bảng 'nguoidung'
        $sql = "SELECT user_id, hoten, email, matkhau FROM nguoidung WHERE email = ? AND trangthai = 'hoat dong' LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Lỗi Prepare: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
}
?>
