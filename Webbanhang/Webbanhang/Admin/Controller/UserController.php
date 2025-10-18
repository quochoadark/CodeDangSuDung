<?php
// File: Admin/Controllers/UserController.php (ĐÃ CẬP NHẬT)

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../model/User.php'; 
require_once __DIR__ . '/../model/Message.php'; // ⚠️ INCLUDE MESSAGE MODEL

// THÊM PHPMailer REQUIRES
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer-master/PHPMailer-master/src/Exception.php';

// Bắt đầu Session nếu chưa có để lấy Staff ID
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class UserController
{
    private $db;
    private $userModel;
    private $messageModel; // ⚠️ KHAI BÁO MESSAGE MODEL

    // Đường dẫn gốc của trang danh sách (khachhang)
    private $redirect_base = "/Webbanhang/Admin/index.php?page=khachhang";

    public function __construct()
    {
        // Tạo kết nối Database
        $Database = new Database();
        $this->db = $Database->conn;

        // Khởi tạo Models
        $this->userModel = new User($this->db);
        $this->messageModel = new Message($this->db); // ⚠️ KHỞI TẠO MESSAGE MODEL
    }
    
    // --- Hỗ trợ: Lấy danh sách Tier/Vai trò từ bảng hangkhachhang ---
    private function getTiers()
    {
        $tier_result = $this->db->query("SELECT tier_id, tenhang FROM hangkhachhang ORDER BY tier_id ASC");
        $tiers = [];
        if ($tier_result) {
            $tiers = $tier_result->fetch_all(MYSQLI_ASSOC);
        }
        return $tiers;
    }

    // --- 1. Xử lý Thêm mới Người dùng (CREATE) - BỊ VÔ HIỆU HÓA ---
    public function create()
    {
        // Chức năng Thêm mới đã bị vô hiệu hóa, chỉ trả về view trống
        return [
            'tiers' => $this->getTiers(),
            'error_message' => "Chức năng Thêm mới khách hàng đã bị vô hiệu hóa.",
            'user_data' => []
        ];
    }

    // --- 2. Hiển thị form Sửa và xử lý Sửa (UPDATE/EDIT) - BỊ VÔ HIỆU HÓA ---
    public function edit()
    {
        header("Location: " . $this->redirect_base);
        exit();
    }

    // --- 3. Xử lý hiển thị Chi tiết Người dùng (READ ONE/DETAIL) ---
    public function detail()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user_info = null;
        $error = null;

        if ($id > 0) {
            $user_info = $this->userModel->find($id);
            if (!$user_info) {
                header("Location: " . $this->redirect_base);
                exit();
            }
        } else {
            $error = "Thiếu ID người dùng.";
        }

        return [
            'user_info' => $user_info,
            'tiers' => $this->getTiers(),
            'error' => $error
        ];
    }

    // --- 4. Hiển thị Danh sách (READ/INDEX) ---
    public function index()
    {
        $tier_id_filter = isset($_GET['tier_id']) ? (int)$_GET['tier_id'] : 0;

        $limit = 8;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        $total_records = $this->userModel->getTotalRecords($tier_id_filter);
        $total_pages = ceil($total_records / $limit);

        $users_result = $this->userModel->readAll($limit, $offset, $tier_id_filter);

        return [
            'users' => $users_result,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'current_tier_id' => $tier_id_filter,
            'tiers' => $this->getTiers(),
            'error_message' => null
        ];
    }

    // --- 5. Xử lý Yêu cầu (Handle Request) ---
    public function handleRequest()
    {
        $action = $_GET['action'] ?? '';
        $user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $tier_id_filter = isset($_GET['tier_id']) ? '&tier_id=' . (int)$_GET['tier_id'] : '';
        $page_filter = isset($_GET['p']) ? '&p=' . (int)$_GET['p'] : '';

        $redirect_base_with_params = $this->redirect_base . $tier_id_filter . $page_filter;

        // --- A. Xử lý XÓA - BỊ VÔ HIỆU HÓA ---
        if ($action == 'delete' && $user_id > 0) {
            // Chuyển hướng ngay lập tức, không thực hiện xóa
            header("Location: " . $redirect_base_with_params);
            exit();
        }

        // --- B. Xử lý KHÓA / MỞ KHÓA ---
        if (($action == 'block' || $action == 'unblock') && $user_id > 0) {
            $new_status = ($action == 'block') ? 0 : 1;

            if ($this->userModel->updateStatus($user_id, $new_status)) {
                header("Location: " . $redirect_base_with_params);
                exit();
            } else {
                header("Location: " . $redirect_base_with_params);
                exit();
            }
        }

        // --- C. Hiển thị danh sách ---
        return $this->index();
    }
    
    // ==================== CHỨC NĂNG GỬI EMAIL (PHPMailer) ====================
    
    // --- Hiển thị Form Gửi Email (Điều chỉnh cho User) ---
    public function sendEmailForm()
    {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $admin_id = $_SESSION['admin_id'] ?? 0; // Lấy STAFF ID TỪ SESSION
        $user_info = null;
        $error = null;

        if (!$this->userModel->isAdmin($admin_id)) {
            $error = "Bạn không có quyền Admin để gửi email.";
        } else if ($id > 0) {
            $user_info = $this->userModel->find($id); // Tìm user
            
            if (!$user_info) {
                $error = "Không tìm thấy người dùng/khách hàng.";
            } 
        } else {
            $error = "Thiếu ID người dùng.";
        }

        return [
            'user_info' => $user_info,
            'error_message' => $error,
            'success_message' => null
        ];
    }
    
    // --- Xử lý Gửi Email (Sử dụng PHPMailer & Message Model) ---
    public function handleSendEmail()
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return $this->sendEmailForm(); 
        }

        $user_id = (int)($_POST['user_id'] ?? 0); 
        $admin_id = $_SESSION['admin_id'] ?? 0; // LẤY STAFF ID TỪ SESSION
        $subject = trim($_POST['subject'] ?? '');
        $body = $_POST['body'] ?? '';
        
        $data = $this->sendEmailForm(); 

        // Kiểm tra quyền Admin
        if (!$this->userModel->isAdmin($admin_id)) {
            $data['error_message'] = "Bạn không có quyền Admin để gửi email.";
            return $data;
        }

        // Kiểm tra lỗi chung
        if ($data['error_message'] || !$user_id || empty($subject) || empty($body)) {
            $data['error_message'] = $data['error_message'] ?? "Vui lòng nhập đầy đủ Tiêu đề và Nội dung.";
            $data['user_data'] = $_POST;
            return $data;
        }

        try {
            $recipient_email = $data['user_info']['email'];
            $recipient_name = $data['user_info']['hoten'];

            // 1. GỬI EMAIL THỰC TẾ
            if ($this->executeSendMail($recipient_email, $recipient_name, $subject, $body)) {
                
                // 2. LƯU THÔNG TIN TIN NHẮN VÀO CSDL bằng Message Model
                $full_noidung = "Tiêu đề: " . $subject . "\n" . $body;

                $this->messageModel->setUserId($user_id);
                $this->messageModel->setStaffId($admin_id);
                $this->messageModel->setNoidung($full_noidung);
                
                if ($this->messageModel->create()) {
                    $data['success_message'] = "Email đã được gửi và lưu thành công (Message Model) đến " . htmlspecialchars($recipient_name) . ".";
                } else {
                    error_log("LỖI: Gửi Email thành công, nhưng không lưu được tin nhắn vào bảng tinnhan.");
                    $data['success_message'] = "Email đã được gửi thành công đến " . htmlspecialchars($recipient_name) . ", nhưng không thể lưu tin nhắn vào CSDL.";
                }

            } else {
                throw new Exception("Lỗi gửi email. Vui lòng kiểm tra log server.");
            }
            
        } catch (Exception $e) {
            $data['error_message'] = "Lỗi: " . $e->getMessage();
            $data['user_data'] = $_POST;
        }
        
        return $data;
    }

    // --- Hàm Gửi Mail Thực Tế (Giữ nguyên cấu hình SMTP) ---
    private function executeSendMail($to_email, $to_name, $subject, $body)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            // ==================== CẤU HÌNH SMTP CỦA BẠN TẠI ĐÂY ====================
            $mail->isSMTP(); 
            $mail->Host      = 'smtp.gmail.com'; 
            $mail->SMTPAuth  = true; 
            $mail->Username  = 'tranquochoan349@gmail.com'; 
            $mail->Password  = 'exbw gzao mzfg wtzy'; // Thay bằng App Password của bạn
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS; 
            $mail->Port      = 465; 
            $mail->CharSet   = 'UTF-8';
            
            // Người gửi
            $mail->setFrom('tranquochoan349@gmail.com', 'Admin Webbanhang');
            $mail->addAddress($to_email, $to_name); 

            // Nội dung
            $mail->isHTML(true); 
            $mail->Subject = $subject;
            $mail->Body    = nl2br(htmlspecialchars($body)); 
            
            $mail->send();
            return true;

        } catch (PHPMailer\PHPMailer\Exception $e) {
            error_log("Lỗi Mailer: {$mail->ErrorInfo}");
            return false;
        } catch (Exception $e) {
            error_log("Lỗi chung: " . $e->getMessage());
            return false;
        }
    }
}