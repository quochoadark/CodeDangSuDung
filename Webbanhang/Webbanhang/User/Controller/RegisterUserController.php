<?php
// File: /Webbanhang/User/Controller/RegistrationController.php

require_once __DIR__ . '/../Model/RegisterUserModel.php';

class RegisterUserController
{
    private $userModel;
    private $conn;
    public $registration_success = false;
    public $error_message = '';

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
        $this->userModel = new RegisterUserModel($db_connection);
    }

    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processRegistration();
        }
    }

    private function processRegistration()
    {
        // --- Lấy dữ liệu từ form ---
        $hoten = trim($_POST['hoten'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $matkhau_raw = $_POST['matkhau'] ?? '';
        $dienthoai = trim($_POST['dienthoai'] ?? '');
        $diachi = trim($_POST['diachi'] ?? '');

        // --- Kiểm tra dữ liệu ---
        if (empty($hoten) || empty($email) || empty($matkhau_raw) || empty($dienthoai) || empty($diachi)) {
            $this->error_message = "Vui lòng điền đầy đủ thông tin.";
            return;
        }

        $this->userModel->setHoTen($hoten);
        $this->userModel->setEmail($email);
        $this->userModel->setMatKhau($matkhau_raw);
        $this->userModel->setDienThoai($dienthoai);
        $this->userModel->setDiaChi($diachi);

        // --- Gọi Model để lưu vào DB ---
        if ($this->userModel->save()) {
            $this->registration_success = true;
        } else {
            $this->error_message = "Đăng ký thất bại. Email có thể đã tồn tại hoặc xảy ra lỗi hệ thống.";
        }
    }

    public function closeConnection()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>
