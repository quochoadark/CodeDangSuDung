<?php
// Models/RegisterUser.php

class RegisterUser {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    /**
     * Thêm người dùng mới vào CSDL
     * @param array $data Dữ liệu người dùng
     * @return bool True nếu thành công, False nếu thất bại
     */
    public function create_user(array $data) {
        // Gán giá trị mặc định nếu cần
        $tier_id = $data['tier_id'] ?? 4;
        $trangthai = $data['trangthai'] ?? 'hoạt động';

        // Trước khi thêm, kiểm tra xem email đã tồn tại chưa
        if ($this->email_exists($data['email'])) {
            error_log("Email đã tồn tại: " . $data['email']);
            return false;
        }

        // Câu lệnh SQL chèn người dùng
        $sql = "INSERT INTO nguoidung (hoten, email, matkhau, dienthoai, diachi, tier_id, trangthai)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Lỗi SQL khi chuẩn bị: " . $this->conn->error);
            return false;
        }

        // Gán giá trị đúng thứ tự với câu lệnh SQL
        $stmt->bind_param("sssssis", 
            $data['hoten'], 
            $data['email'],            // ✅ Đúng vị trí
            $data['hashed_matkhau'],   // ✅ Đúng vị trí
            $data['dienthoai'], 
            $data['diachi'], 
            $tier_id, 
            $trangthai
        );

        // Thực thi và kiểm tra kết quả
        $result = $stmt->execute();

        if (!$result) {
            error_log("Lỗi thực thi SQL: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }
    
    /**
     * Kiểm tra xem email đã tồn tại trong CSDL chưa
     */
    public function email_exists($email) {
        $sql = "SELECT user_id FROM nguoidung WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log("Lỗi khi chuẩn bị kiểm tra email: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }
}
?>
