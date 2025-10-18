<?php
// File: /Webbanhang/User/Service/LoginService.php (ĐÃ PHÂN TÁCH LOGIC CHUYỂN HƯỚNG RA KHỎI SERVICE)

require_once __DIR__ . '/../Repository/LoginRepository.php'; 
require_once __DIR__ . '/../Model/LoginModel.php'; 

// Cần đảm bảo các file PHPMailer này tồn tại trong dự án của bạn
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class LoginService
{
    private $userRepository; 
    private $cookie_name = 'kh_remember_token'; 
    private $user_root = '/Webbanhang/User/'; 
    
    // ⭐ CẤU HÌNH MAIL
    private $mailConfig = [
        'Host' => 'smtp.gmail.com', // SMTP Host
        'Username' => 'tranquochoan349@gmail.com', // Email của bạn
        'Password' => 'dzzszqovfnwayiqf', // APP PASSWORD ĐÃ BỎ KHOẢNG TRẮNG
        'Port' => 587, 
        'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS, 
        'SenderName' => 'Website Bán Hàng'
    ];


    public function __construct(LoginRepository $userRepository)
    {
        $this->userRepository = $userRepository; 
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    // =========================================================
    // PHƯƠNG THỨC ĐĂNG NHẬP
    // =========================================================

    private function setRememberMeCookie(bool $should_set, LoginModel $user) 
    {
        $path = '/'; 
        $domain = '';
        $secure = false; 
        $httponly = true;
        
        if ($should_set) {
            $expiry_time = time() + (30 * 24 * 3600); 
            
            $token = bin2hex(random_bytes(32));
            $token_hash = hash('sha256', $token);
            
            $this->userRepository->setRememberToken($user->getId(), $token_hash); 
            setcookie($this->cookie_name, $token, $expiry_time, $path, $domain, $secure, $httponly);
            
        } else {
            $this->clearRememberMeCookie();
            if ($user->getId() !== null) { 
                $this->userRepository->setRememberToken($user->getId(), NULL); 
            }
        }
    }
    
    private function setSession(LoginModel $user)
    {
        $_SESSION['kh_user_id'] = $user->getId(); 
        $_SESSION['kh_hoten'] = $user->getHoTen(); 
        $_SESSION['kh_role'] = 'khachhang'; 
        $_SESSION['kh_is_logged_in'] = true; 
        error_log("DEBUG SESSION: Thiết lập session cho User: " . $user->getHoTen());
    }

    private function clearRememberMeCookie()
    {
        $path = '/'; 
        $domain = '';
        $secure = false; 
        $httponly = true;
        setcookie($this->cookie_name, '', time() - 3600, $path, $domain, $secure, $httponly);
    }
    
    public function isLoggedInBySession()
    {
        return isset($_SESSION['kh_user_id']); 
    }

    /**
     * Kiểm tra cookie remember me và đăng nhập tự động. 
     * @return bool True nếu đăng nhập thành công qua cookie và cần chuyển hướng, False nếu không.
     */
    public function checkRememberMeAndLogin(): bool
    {
        if ($this->isLoggedInBySession()) { 
            // Nếu đã đăng nhập bằng Session, không cần làm gì nữa.
            return false;
        }
        
        if (isset($_COOKIE[$this->cookie_name])) { 
            $token = $_COOKIE[$this->cookie_name]; 
            $token_hash = hash('sha256', $token);
            
            $user = $this->userRepository->findActiveByRememberToken($token_hash);
            
            if ($user) {
                $this->setSession($user);
                $this->setRememberMeCookie(true, $user); 
                // ⭐ Đăng nhập thành công, báo cho Controller chuyển hướng
                return true; 
            } else {
                $this->clearRememberMeCookie();
            }
        }
        return false;
    }
    
    public function processLogin(string $email, string $password, bool $remember_me): bool
    {
        $user = $this->userRepository->findActiveByEmail($email);

        if (!$user) {
            $this->clearRememberMeCookie(); 
            return false;
        }

        $is_password_valid = password_verify($password, $user->getMatKhau());

        if ($is_password_valid) {
            $this->setSession($user);
            $this->setRememberMeCookie($remember_me, $user);
            return true;
        }

        // Xóa token và cookie nếu đăng nhập thất bại
        $this->userRepository->setRememberToken($user->getId(), NULL); 
        $this->clearRememberMeCookie(); 

        return false;
    }
    
    // =========================================================
    // PHƯƠNG THỨC QUÊN MẬT KHẨU
    // =========================================================
    
    public function requestPasswordReset(string $email): string
    {
        $user = $this->userRepository->findActiveByEmail($email);
        
        // Trả về thông báo chung nếu không tìm thấy user
        if (!$user) {
            return "Nếu email của bạn tồn tại trong hệ thống, chúng tôi đã gửi liên kết đặt lại mật khẩu.";
        }
        
        // 1. Tạo Token GỐC (cho URL) và HASH (lưu vào DB)
        $token = bin2hex(random_bytes(32)); 
        $token_hash = hash('sha256', $token);
        $expiry_time = date("Y-m-d H:i:s", time() + (60 * 60)); // Hết hạn sau 1 giờ

        // 2. Lưu HASH TOKEN và Thời hạn vào DB
        $db_success = $this->userRepository->setResetToken($user->getId(), $token_hash, $expiry_time);

        if (!$db_success) {
            error_log("Lỗi DB QUAN TRỌNG: Không thể lưu reset token cho user: " . $user->getId());
            return "Đã xảy ra lỗi hệ thống khi lưu token vào cơ sở dữ liệu, vui lòng kiểm tra log DB.";
        }
        
        // 3. Gửi Email (Sử dụng token GỐC)
        try {
            $this->sendResetEmail($user->getEmail(), $user->getHoTen(), $token);
            return "Liên kết đặt lại mật khẩu đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư.";
        } catch (\Exception $e) {
            // Xóa token khỏi DB nếu gửi email thất bại
            $this->userRepository->setResetToken($user->getId(), NULL, NULL); 
            
            // Ghi log lỗi chi tiết
            $error_message = $e->getMessage();
            error_log("LỖI GỬI EMAIL: " . $error_message);

            // Phân tích lỗi và trả về thông báo thân thiện
            if (strpos($error_message, 'SMTP ERROR: Failed to connect to server') !== false || strpos($error_message, 'timed out') !== false) {
                return "Lỗi kết nối SMTP (Port 587). Vui lòng kiểm tra Tường lửa hoặc `extension=openssl` trong php.ini.";
            }
            if (strpos($error_message, 'Authentication failed') !== false) {
                return "Lỗi Xác thực SMTP. Vui lòng kiểm tra lại <b>App Password (đã bỏ khoảng trắng)</b>.";
            }
            
            return "Lỗi gửi email: Vui lòng kiểm tra lại cấu hình mail. Lỗi chi tiết: " . $error_message; 
        }
    }

    public function validateResetToken(string $token): LoginModel|string
    {
        if (empty($token)) {
            return "Liên kết đặt lại mật khẩu không hợp lệ.";
        }
        
        $token_hash = hash('sha256', $token);
        
        $user = $this->userRepository->findByResetToken($token_hash);
        
        if (!$user) {
            return "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã được sử dụng.";
        }
        
        $expiry_timestamp = strtotime($user->getResetTokenExpiry());
        
        if ($expiry_timestamp === false || $expiry_timestamp < time()) {
            $this->userRepository->setResetToken($user->getId(), NULL, NULL); 
            return "Liên kết đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu đặt lại mật khẩu mới.";
        }
        
        return $user; 
    }

    public function resetPassword(LoginModel $user, string $new_password, string $confirm_password): bool|string
    {
        if ($new_password !== $confirm_password) {
            return "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        }
        
        if (strlen($new_password) < 6) { 
            return "Mật khẩu phải có ít nhất 6 ký tự.";
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $pass_updated = $this->userRepository->updatePassword($user->getId(), $hashed_password);
        $token_cleared = $this->userRepository->setResetToken($user->getId(), NULL, NULL);
        
        if ($pass_updated && $token_cleared) {
            return true;
        }

        return "Đã xảy ra lỗi khi cập nhật mật khẩu. Vui lòng thử lại sau.";
    }

    /**
     * Cấu hình và gửi email đặt lại mật khẩu.
     */
    private function sendResetEmail(string $recipientEmail, string $recipientName, string $token)
    {
        $mail = new PHPMailer(true);
        
        try {
            // BẬT DEBUG - RẤT QUAN TRỌNG ĐỂ XÁC ĐỊNH LỖI KẾT NỐI
            $mail->SMTPDebug = 0; 
            
            // Cấu hình Server (587/STARTTLS)
            $mail->isSMTP();
            $mail->Host      = $this->mailConfig['Host'];
            $mail->SMTPAuth  = true;
            $mail->Username  = $this->mailConfig['Username'];
            $mail->Password  = $this->mailConfig['Password'];
            $mail->SMTPSecure = $this->mailConfig['SMTPSecure'];
            $mail->Port      = $this->mailConfig['Port'];
            
            // Người gửi và Người nhận
            $mail->setFrom($this->mailConfig['Username'], $this->mailConfig['SenderName']);
            $mail->addAddress($recipientEmail, $recipientName);
            
            // Nội dung Email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Yêu cầu Đặt lại Mật khẩu';
            
            // ⭐ ĐIỀU CHỈNH CÁCH TẠO RESET LINK
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
            // CHUYỂN HƯỚNG VỀ Entry.php ĐỂ CONTROLLER XỬ LÝ ACTION
            $resetLink = "{$protocol}://{$_SERVER['HTTP_HOST']}{$this->user_root}View/Entry.php?action=reset_password_view&token={$token}";
            
            error_log("DEBUG MAIL: Reset Link được tạo: " . $resetLink); // Log để kiểm tra link

            $mail->Body    = "
                <h2>Xin chào {$recipientName},</h2>
                <p>Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào liên kết dưới đây:</p>
                <p><a href='{$resetLink}' style='display: inline-block; padding: 10px 20px; color: #fff; background-color: #007bff; text-decoration: none; border-radius: 5px;'>ĐẶT LẠI MẬT KHẨU</a></p>
                <p>Liên kết này sẽ hết hạn sau 1 giờ. Nếu bạn không yêu cầu, vui lòng bỏ qua.</p>
                <p>Trân trọng, Đội ngũ Hỗ trợ</p>
            ";
            
            $mail->send();
            
        } catch (Exception $e) {
            // Ném ra lỗi để hàm requestPasswordReset bắt được và hiển thị thông báo thân thiện.
            throw new Exception("Không thể gửi mail. Lỗi PHPMailer: {$mail->ErrorInfo}");
        }
    }
}
