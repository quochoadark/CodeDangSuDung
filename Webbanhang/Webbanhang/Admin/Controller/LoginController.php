<?php
// Tên file: /Webbanhang/Admin/Controller/LoginController.php

// Khởi động session nếu chưa có, ngăn lỗi session_start() đã chạy
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng Model: File này cần được nhúng ở đây.
require_once __DIR__ . '/../model/Login.php'; 

class LoginController {
    
    private $staffModel;

    public function __construct() {
        // Đường dẫn từ Controller (/Admin/Controller/) lùi 2 cấp (đến /WEBBANHANG/) rồi vào Database/
        require_once __DIR__ . '/../../Database/database.php'; 
        
        // 1. Tạo đối tượng từ class database
        $db = new database(); 
        // 2. Lấy biến kết nối công khai $conn từ đối tượng $db
        $conn = $db->conn;      

        // Kiểm tra lỗi kết nối 
        if ($conn->connect_error) {
             die("Lỗi kết nối CSDL: Không thể tạo đối tượng kết nối hợp lệ.");
        }
        
        // Khởi tạo Model với kết nối DB
        $this->staffModel = new Login($conn); 
    }

    public function handleRequest() {
        // ⭐ Đảm bảo biến lỗi luôn được khởi tạo
        $login_error = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            // Lấy lỗi từ session (nếu có) trước khi tải View
            if (isset($_SESSION['login_error'])) {
                $login_error = $_SESSION['login_error'];
                unset($_SESSION['login_error']); // Xóa lỗi sau khi đã lấy để không hiển thị lại
            }
            $this->loadView($login_error); // Truyền biến lỗi vào View
        }
    }

    private function processLogin() {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $row = $this->staffModel->getStaffByEmail($email);
        
        if ($row) {
            // Kiểm tra mật khẩu (đã mã hóa bằng bcrypt)
            if (password_verify($password, $row['matkhau'])) {
                // Đăng nhập thành công
                $_SESSION['user_id'] = $row['staff_id'];
                $_SESSION['hoten'] = $row['hoten']; 
                $_SESSION['role_id'] = $row['id_chucvu']; 

                $role_id = $row['id_chucvu'];
                
                // PHÂN QUYỀN VÀ CHUYỂN HƯỚNG
                if ($role_id == 2) { // Admin
                    $_SESSION['role_name'] = 'admin';
                    header("Location: /Webbanhang/Admin/index.php"); 
                    exit();
                } else if ($role_id == 1) { // Nhân viên bán hàng
                    $_SESSION['role_name'] = 'nhanvien';
                    header("Location: /Webbanhang/Admin/indexStaff.php"); 
                    exit();
                } else {
                    $_SESSION['login_error'] = "Tài khoản không có quyền truy cập phù hợp.";
                }
            }
        } 

        // Xử lý thất bại (không tìm thấy user, mật khẩu sai, hoặc lỗi phân quyền)
        if (!isset($_SESSION['login_error'])) {
             $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        }
        
        // Chuyển hướng về View đăng nhập qua Entry Point
        header("Location: /Webbanhang/Admin/view/Entry.php"); // ⭐ Đảm bảo đường dẫn này trỏ đến file chạy Controller
        exit();
    }
    
    private function loadView($login_error) {
        // Biến $login_error đã được truyền vào phạm vi này
        require_once __DIR__ . '/../view/Login.php'; 
    }
}