<?php
// File: /Webbanhang/Admin/Controller/LoginController.php (ĐÃ SỬA VÀ LOẠI BỎ indexStaff)

// 1. Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Nạp Model
require_once __DIR__ . '/../Model/Login.php';
require_once __DIR__ . '/../../Database/Database.php'; // Nạp DB

class LoginController
{
    private $loginModel;
    private $cookie_name = 'ad_remember_token'; 
    private $admin_root = '/Webbanhang/Admin/';
    // ⭐ ĐIỀU CHỈNH: Chỉ còn 1 trang đích duy nhất cho Admin
    private const ADMIN_DASHBOARD = 'index.php'; 

    public function __construct()
    {
        // Nạp kết nối CSDL
        $db = new Database();
        $conn = $db->conn;

        if ($conn->connect_error) {
            die("Lỗi kết nối CSDL: " . $conn->connect_error);
        }

        // Khởi tạo model
        $this->loginModel = new Login($conn);
        
        // KIỂM TRA REMEMBER ME ngay khi khởi tạo Controller
        $this->checkRememberMe();
    }
    
    /**
     * ⭐ HÀM CHUYỂN HƯỚNG: Luôn chuyển hướng đến trang Admin (index.php)
     */
    private function redirectToDashboard()
    {
        // Chuyển hướng đến /Webbanhang/Admin/index.php
        header("Location: {$this->admin_root}" . self::ADMIN_DASHBOARD);
        exit();
    }
    
    /**
     * Kiểm tra cookie Remember Me và tự động đăng nhập nếu hợp lệ
     */
    private function checkRememberMe()
    {
        // 1. Nếu đã đăng nhập thành công, chuyển hướng ngay lập tức (không cần kiểm tra role ở đây)
        if (isset($_SESSION['admin_id'])) { 
            $this->redirectToDashboard(); 
        }
        
        // 2. Kiểm tra cookie token
        if (isset($_COOKIE[$this->cookie_name])) {
            $token = $_COOKIE[$this->cookie_name]; 
            $token_hash = hash('sha256', $token);
            
            // Tìm người dùng trong DB bằng Token Hash
            if ($this->loginModel->findByRememberToken($token_hash)) {
                
                // Lấy role_id từ Model sau khi tìm thấy
                $role_id = $this->loginModel->getIdChucVu(); 
                
                // ⭐ KIỂM TRA QUYỀN TẠI ĐÂY: Chỉ Admin (Role ID = 2) mới được phép tiếp tục
                if ($role_id != 2) {
                    $this->clearRememberMeCookie(); // Xóa cookie không có quyền
                    $_SESSION['login_error'] = "Tài khoản không có quyền truy cập Admin.";
                    return; // Dừng lại, không chuyển hướng
                }

                // Đăng nhập thành công qua cookie: Thiết lập Session
                $_SESSION['admin_id'] = $this->loginModel->getStaffId(); 
                $_SESSION['hoten'] = $this->loginModel->getHoTen(); 
                $_SESSION['role_id'] = $role_id;
                
                // Cập nhật token mới (Token Rotation)
                $this->setRememberMeCookie(true, $this->loginModel->getStaffId());
                
                // Chuyển hướng đến trang Admin
                $this->redirectToDashboard();
            } else {
                // Token không hợp lệ/hết hạn => Xóa cookie
                $this->clearRememberMeCookie();
            }
        }
    }
    
    /**
     * Tạo hoặc Xóa cookie Remember Me
     */
    private function setRememberMeCookie(bool $should_set, $staff_id)
    {
        // Thiết lập cookie trên toàn bộ trang Admin
        $path = '/Webbanhang/Admin/'; 
        $domain = '';
        $secure = false; 
        $httponly = true;
        
        if ($should_set) {
            $expiry_time = time() + (30 * 24 * 3600); // 30 ngày
            
            // 1. Tạo Token gốc, mã hóa, lưu hash vào DB và trả về token gốc
            $token = $this->loginModel->createRememberToken($staff_id); 
            
            if ($token) {
                // 2. Đặt cookie (lưu $token GỐC)
                setcookie($this->cookie_name, $token, $expiry_time, $path, $domain, $secure, $httponly);
            }
            
        } else {
            // Xóa cookie
            $this->clearRememberMeCookie();
            
            // Xóa token khỏi DB
            $this->loginModel->clearRememberToken($staff_id); 
        }
    }
    
    // Hàm xóa cookie
    private function clearRememberMeCookie()
    {
        $path = '/Webbanhang/Admin/'; 
        $domain = '';
        $secure = false; 
        $httponly = true;
        setcookie($this->cookie_name, '', time() - 3600, $path, $domain, $secure, $httponly);
    }

    /**
     * Hàm xử lý chung: GET/POST
     */
    public function handleRequest()
    {
        // Nếu đã đăng nhập thành công, chuyển hướng đến trang chủ
        if (isset($_SESSION['admin_id'])) { 
            $this->redirectToDashboard();
        }
        
        $login_error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            // Lấy lỗi từ session (nếu có)
            if (isset($_SESSION['login_error'])) {
                $login_error = $_SESSION['login_error'];
                unset($_SESSION['login_error']);
            }

            $this->loadView($login_error);
        }
    }

    /**
     * Xử lý đăng nhập
     */
    private function processLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']); 

        // Gán giá trị vào model
        $this->loginModel->setEmail($email);

        // Tìm người dùng theo email (chỉ tìm tài khoản ACTIVE)
        $found = $this->loginModel->findActiveByEmail();

        if ($found) {
            $staff_id = $this->loginModel->getStaffId();
            $hashedPassword = $this->loginModel->getMatKhau();
            $role_id = $this->loginModel->getIdChucVu(); 

            // ⭐ KIỂM TRA QUYỀN TRUY CẬP NGAY SAU KHI TÌM THẤY TÀI KHOẢN
            if ($role_id != 2) {
                $_SESSION['login_error'] = "Tài khoản của bạn không phải là Admin.";
                header("Location: {$this->admin_root}view/Entry.php");
                exit();
            }

            // So sánh mật khẩu
            if (password_verify($password, $hashedPassword)) {
                // Đăng nhập thành công
                
                $_SESSION['admin_id'] = $staff_id; 
                $_SESSION['hoten'] = $this->loginModel->getHoTen(); 
                $_SESSION['role_id'] = $role_id; 
                
                // Xử lý Remember Me
                $this->setRememberMeCookie($remember_me, $staff_id);

                // Chuyển hướng đến trang Admin
                $this->redirectToDashboard();
            }
        }

        // Nếu sai thông tin đăng nhập hoặc sai mật khẩu
        $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
        header("Location: {$this->admin_root}view/Entry.php");
        exit();
    }

    /**
     * Tải view Login
     */
    private function loadView($login_error)
    {
        $viewPath = __DIR__ . '/../view/Login.php'; 

        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("Không tìm thấy View: " . $viewPath);
        }
    }
}
