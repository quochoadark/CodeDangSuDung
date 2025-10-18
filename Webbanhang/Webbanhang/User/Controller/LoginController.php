<?php
// File: /Webbanhang/User/Controller/LoginController.php (ĐÃ SỬA LỖI LOGIC CHUYỂN HƯỚNG VÀ LOAD VIEW)

// Nạp các file cần thiết
require_once __DIR__ . '/../Service/LoginService.php'; 
require_once __DIR__ . '/../Repository/LoginRepository.php';
require_once __DIR__ . '/../../Database/database.php'; 

class LoginController
{
    private $loginService;
    private $user_root = '/Webbanhang/User/'; 

    public function __construct()
    {
        // 1. Khởi động session nếu chưa có (Dù Service đã có, Controller cũng nên đảm bảo)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $db = new Database();
        $conn = $db->conn;

        if ($conn->connect_error) {
            die("Lỗi kết nối CSDL: " . $conn->connect_error);
        }

        $userRepository = new LoginRepository($conn); 
        $this->loginService = new LoginService($userRepository);
        
        // 2. KIỂM TRA REMEMBER ME (Service sẽ thực hiện logic, Controller chịu trách nhiệm chuyển hướng nếu thành công)
        $redirect_to_index = $this->loginService->checkRememberMeAndLogin();
        
        if ($redirect_to_index === true) {
            header("Location: {$this->user_root}index.php");
            exit();
        }
    }
    
    /**
     * Hàm xử lý chung: Điều phối các yêu cầu Đăng nhập và Quên mật khẩu.
     */
    public function handleRequest()
    {
        // 1. Kiểm tra trạng thái đăng nhập (sau khi đã kiểm tra Remember Me ở __construct)
        if ($this->loginService->isLoggedInBySession()) { 
             header("Location: {$this->user_root}index.php");
             exit();
        }
        
        // 2. Lấy hành động được yêu cầu (từ GET/URL hoặc POST/Form)
        $action = $_REQUEST['action'] ?? null;

        switch ($action) {
            case 'forgot_password_view':
            case 'request_reset': // Xử lý POST của form Quên Mật khẩu
                $this->handleForgotPasswordRequest();
                break;
            case 'reset_password_view':
            case 'reset_password': // Xử lý POST của form Đặt lại Mật khẩu
                $this->handleResetPasswordRequest();
                break;
            default:
                // Xử lý Đăng nhập mặc định (POST cho form login, GET cho view login)
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->processLogin();
                } else {
                    // SỬA LỖI LOGIC: View Login nằm trong View/Entry.php (tôi giả định)
                    $this->loadView('Login'); 
                }
                break;
        }
    }
    
    // ⭐ PHƯƠNG THỨC ĐĂNG NHẬP
    private function processLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember_me = isset($_POST['remember_me']); 

        // Gọi Service, Service chỉ trả về TRUE/FALSE, KHÔNG CHUYỂN HƯỚNG
        $success = $this->loginService->processLogin($email, $password, $remember_me);

        if ($success) {
            // Đăng nhập thành công, Controller chuyển hướng
            header("Location: {$this->user_root}index.php"); 
            exit();
        } else {
            // Đăng nhập thất bại
            $_SESSION['login_error'] = "Tên đăng nhập hoặc mật khẩu không đúng.";
            // ĐẢM BẢO CHUYỂN HƯỚNG VỀ ENTRY POINT
            header("Location: {$this->user_root}View/Entry.php"); 
            exit();
        }
    }

    /**
     * Hàm tải View (Login, ForgotPassword, ResetPassword)
     * @param string $viewName Tên View (Ví dụ: 'Login', 'ForgotPassword')
     */
    private function loadView(string $viewName)
    {
        $login_error = '';
        $message = '';

        // Lấy thông báo lỗi/thành công từ Session (Nếu có)
        if (isset($_SESSION['login_error'])) {
            $login_error = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        if (isset($_SESSION['success_message'])) {
            $message = $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            $login_error = $_SESSION['error_message'];
            unset($_SESSION['error_message']);
        }

        $viewPath = __DIR__ . "/../View/{$viewName}.php"; 
        
        if (file_exists($viewPath)) {
            // Include View, trong đó có thể sử dụng biến $login_error và $message
            require_once $viewPath;
        } else {
            die("Không tìm thấy View: " . $viewPath);
        }
    }
    
    // =========================================================
    // PHƯƠNG THỨC MỚI: XỬ LÝ LUỒNG QUÊN MẬT KHẨU
    // =========================================================

    /**
     * Tải và xử lý form yêu cầu gửi email đặt lại mật khẩu.
     */
    public function handleForgotPasswordRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request_reset') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $_SESSION['error_message'] = "Vui lòng nhập email.";
            } else {
                $result = $this->loginService->requestPasswordReset($email);

                // Dựa vào chuỗi trả về để quyết định thông báo
                if (str_contains($result, 'Lỗi') || str_contains($result, 'hệ thống') || str_contains($result, 'SMTP')) {
                    $_SESSION['error_message'] = $result;
                } else {
                    $_SESSION['success_message'] = $result;
                }
            }

            // Sau khi xử lý xong, luôn chuyển hướng về Entry Point của ForgotPassword View
            header("Location: {$this->user_root}View/ForgotPassword.php");
            exit();
        }

        // Khi load trang (GET), tải View và hiển thị thông báo
        $this->loadView('ForgotPassword');
    }

    /**
     * Tải form đặt lại mật khẩu và xử lý việc đặt lại.
     */
    public function handleResetPasswordRequest()
    {
        $token = trim($_REQUEST['token'] ?? ''); 

        // 1. Xác thực Token (cả GET/POST đều cần check)
        $user_or_error = $this->loginService->validateResetToken($token);
        
        if (is_string($user_or_error)) {
            // Token không hợp lệ hoặc hết hạn. Lưu lỗi vào session và chuyển hướng về trang ResetPassword
            $_SESSION['error_message'] = $user_or_error;
            $this->loadView('ResetPassword');
            return;
        }
        
        $user = $user_or_error; // Lấy Model User
        
        // 2. Xử lý POST (Đặt lại mật khẩu)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_password') {
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            $result = $this->loginService->resetPassword($user, $new_password, $confirm_password);
            
            if ($result === true) {
                // Đặt lại thành công
                $_SESSION['login_error'] = "Mật khẩu đã được đặt lại thành công. Vui lòng đăng nhập.";
                header("Location: {$this->user_root}View/Entry.php");
                exit();
            } else {
                // Đặt lại thất bại
                $_SESSION['error_message'] = $result; // Thông báo lỗi từ Service
            }
            
            // Chuyển hướng POST sang GET để tránh resubmit form
            header("Location: {$this->user_root}View/ResetPassword.php?action=reset_password_view&token={$token}");
            exit();
        }
        
        // 3. Tải view ResetPassword (Khi là GET ban đầu)
        $this->loadView('ResetPassword');
    }
}
