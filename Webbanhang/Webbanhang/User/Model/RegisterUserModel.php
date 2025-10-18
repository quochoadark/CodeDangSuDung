<?php
// File: /Webbanhang/User/Model/RegisterUser.php

class RegisterUserModel
{
    private $conn;

    // Các thuộc tính tương ứng với bảng `nguoidung`
    private $user_id;
    private $hoten;
    private $email;
    private $matkhau;
    private $dienthoai;
    private $diachi;
    private $tier_id = 4;
    private $trangthai = 'hoạt động';

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function setHoTen($hoten)      { $this->hoten = trim($hoten); }
    public function setEmail($email)      { $this->email = trim($email); }
    public function setMatKhau($matkhau)  { $this->matkhau = password_hash($matkhau, PASSWORD_DEFAULT); }
    public function setDienThoai($sdt)    { $this->dienthoai = trim($sdt); }
    public function setDiaChi($diachi)    { $this->diachi = trim($diachi); }

    public function getEmail()            { return $this->email; }
    public function getHoTen()            { return $this->hoten; }
    public function getDienThoai()        { return $this->dienthoai; }

    private function emailExists()
    {
        $sql = "SELECT user_id FROM nguoidung WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi Prepare (emailExists): " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function save()
    {
        // Kiểm tra trùng email
        if ($this->emailExists()) {
            error_log("Email đã tồn tại: " . $this->email);
            return false;
        }

        $sql = "INSERT INTO nguoidung (hoten, email, matkhau, dienthoai, diachi, tier_id, trangthai)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Lỗi Prepare (save): " . $this->conn->error);
            return false;
        }

        $stmt->bind_param(
            "sssssis",
            $this->hoten,
            $this->email,
            $this->matkhau,
            $this->dienthoai,
            $this->diachi,
            $this->tier_id,
            $this->trangthai
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Lỗi khi INSERT người dùng: " . $stmt->error);
        }

        $stmt->close();
        return $result;
    }
}
?>
