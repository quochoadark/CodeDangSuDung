<?php
// Tên file: /Webbanhang/User/Controller/LoginController.php (Controller Khách hàng)

// Khởi động session nếu chưa có, ngăn lỗi session_start() đã chạy
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⭐ 1. NHÚNG MODEL KHÁCH HÀNG: Sửa từ 'Login.php' sang 'User.php'
require_once __DIR__ . '/../Model/Login.php'; 

class LoginController {
    
    // Đổi tên biến model để dễ quản lý
    private $userModel;

    public function __construct() {
        // Đường dẫn đến Database: Giả sử database.php nằm ở /Webbanhang/Database/
        // Nếu Controller nằm ở /Webbanhang/User/Controller/, ta cần lùi 2 cấp (../..).
        // Tuy nhiên, dựa trên các file trước, ta dùng đường dẫn tương đối theo cấu trúc đã sửa.
        require_once __DIR__ . '/../../Database/database.php'; 
        
        // 1. Tạo đối tượng từ class database
        $db = new database(); 
        
        // 2. Lấy biến kết nối công khai $conn từ đối tượng $db (Đúng theo cách bạn muốn dùng)
        // Nếu dòng này vẫn lỗi, hãy kiểm tra chắc chắn class database của bạn có biến public $conn.
        $conn = $db->conn;     

        // Kiểm tra lỗi kết nối 
        if ($conn->connect_error) {
             die("Lỗi kết nối CSDL: Không thể tạo đối tượng kết nối hợp lệ.");
        }
        
        // ⭐ 2. KHỞI TẠO MODEL: Sửa từ 'new Login($conn)' sang 'new User($conn)'
        $this->userModel = new Login($conn); 
    }

    public function handleRequest() {
        $login_error = "";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            // Lấy lỗi từ session (nếu có) trước khi tải View
            if (isset($_SESSION['login_error'])) {
                $login_error = $_SESSION['login_error'];
                unset($_SESSION['login_error']);
            }
            $this->loadView($login_error);
        }
    }

    private function processLogin() {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Giả sử Model User có phương thức authenticate($email) hoặc getUserByEmail($email)
        $row = $this->userModel->getUserByEmail($email);
        
        if ($row) {
            // Kiểm tra mật khẩu (đã mã hóa bằng bcrypt)
            if (password_verify($password, $row['matkhau'])) {
                // Đăng nhập thành công

                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $row['user_id']; // ID khách hàng
                $_SESSION['hoten'] = $row['hoten']; 
                
                // ⭐ 3. BỎ PHÂN QUYỀN: Khách hàng luôn có role là 'khachhang' (hoặc không cần role nếu không cần kiểm tra trong Navbar)
                $_SESSION['role'] = 'khachhang';
                
                // ⭐ 4. CHUYỂN HƯỚNG ĐẾN 1 TRANG DUY NHẤT
                header("Location: /Webbanhang/User/index.php"); 
                exit();
            }
        } 

        // Xử lý thất bại (không tìm thấy user hoặc mật khẩu sai)
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        
        // Chuyển hướng về View đăng nhập qua Entry Point
        header("Location: /Webbanhang/User/View/Entry.php"); 
        exit();
    }
    
    private function loadView($login_error) {
        // Truyền biến $login_error vào View
        $viewPath = __DIR__ . '/../view/Login.php'; 
        
        if (file_exists($viewPath)) {
            require_once $viewPath; 
        } else {
            die("Lỗi: Không tìm thấy View Login tại đường dẫn $viewPath");
        }
    }
}
