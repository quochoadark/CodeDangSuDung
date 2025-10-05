<?php
// Controllers/RegistrationController.php

require_once __DIR__ . '/../Model/RegisterUser.php'; 
// Yêu cầu file Database.php sẽ được gọi trong file register.php chính

class RegisterUserController {
    private $userModel;
    private $conn;
    public $registration_success = false;
    public $error_message = '';

    public function __construct($db_connection) {
        $this->conn = $db_connection;
        $this->userModel = new RegisterUser($db_connection);
    }

    public function handleRequest() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Lấy dữ liệu
            $hoten = trim($_POST['hoten'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $matkhau_raw = $_POST['matkhau'] ?? '';
            $dienthoai = trim($_POST['dienthoai'] ?? '');
            $diachi = trim($_POST['diachi'] ?? '');

            // Xác thực dữ liệu cơ bản
            if (empty($hoten) || empty($email) || empty($matkhau_raw) || empty($dienthoai) || empty($diachi)) {
                $this->error_message = "Vui lòng điền đầy đủ tất cả các trường.";
            } else {
                // Mã hóa mật khẩu
                $hashed_matkhau = password_hash($matkhau_raw, PASSWORD_DEFAULT);

                // Chuẩn bị dữ liệu cho Model
                $user_data = [
                    'hoten' => $hoten,
                    'email' => $email,
                    'hashed_matkhau' => $hashed_matkhau,
                    'dienthoai' => $dienthoai,
                    'diachi' => $diachi,
                    // tier_id và trangthai được gán mặc định trong Model, hoặc gán ở đây:
                    'tier_id' => 4,
                    'trangthai' => 'hoạt động'
                ];

                // Gọi Model để lưu dữ liệu
                if ($this->userModel->create_user($user_data)) {
                    $this->registration_success = true;
                } else {
                    // Trong thực tế, cần xử lý lỗi chi tiết hơn (ví dụ: email đã tồn tại)
                    $this->error_message = "Đăng ký thất bại. Email có thể đã tồn tại hoặc lỗi hệ thống.";
                }
            }
        }
    }
    
    public function closeConnection() {
         $this->conn->close();
    }
}