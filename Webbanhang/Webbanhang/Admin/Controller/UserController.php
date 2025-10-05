<?php
// File: Admin/Controllers/UserController.php
require_once __DIR__ . '/../../Database/database.php'; 
require_once __DIR__ . '/../model/User.php'; // Sử dụng User Model

class UserController {
    private $db;
    private $userModel;
    // Đường dẫn gốc của trang danh sách (nhanvien)
    private $redirect_base = "/Webbanhang/Admin/index.php?page=khachhang";

    public function __construct() {
        // Tạo kết nối Database
        $database = new Database();
        $this->db = $database->conn;

        // Khởi tạo User Model
        $this->userModel = new User($this->db); 
    } 

    // --- Hỗ trợ: Lấy danh sách Tier/Vai trò từ bảng hangkhachhang ---
    private function getTiers() {
        $tier_result = $this->db->query("SELECT tier_id, tenhang FROM hangkhachhang ORDER BY tier_id ASC");
        
        $tiers = [];
        if ($tier_result) {
            $tiers = $tier_result->fetch_all(MYSQLI_ASSOC);
        }
        return $tiers;
    }
    
    // --- 1. Xử lý Thêm mới Người dùng (CREATE) ---
    public function create() {
        $data = [
            'tiers' => $this->getTiers(),
            'error_message' => null,
            'user_data' => [] 
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                // Gán dữ liệu từ form vào model
                $this->userModel->setHoTen($_POST["hoten"] ?? '');
                $this->userModel->setEmail($_POST["email"] ?? '');
                $this->userModel->setMatKhau($_POST["matkhau"] ?? ''); 
                $this->userModel->setDienThoai($_POST["dienthoai"] ?? '');
                $this->userModel->setDiaChi($_POST["diachi"] ?? '');
                $this->userModel->setTierId(intval($_POST["tier_id"] ?? 0));
                $this->userModel->setTrangThai(intval($_POST["trangthai"] ?? 1));
                
                if (empty($_POST["matkhau"])) {
                    throw new Exception("Vui lòng nhập mật khẩu.");
                }

                // Gọi Model để lưu vào DB
                if ($this->userModel->save()) {
                    header("Location: " . $this->redirect_base); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi lưu dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['user_data'] = $_POST;
            }  
        }
        
        return $data;
    }
    
    // --- 2. Hiển thị form Sửa và xử lý Sửa (UPDATE/EDIT) ---
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user_data = null;
        $error_message = null;
        
        // --- A. Lấy dữ liệu người dùng hiện tại ---
        if ($id > 0) {
            $user_data = $this->userModel->find($id); 
            if (!$user_data) {
                header("Location: " . $this->redirect_base);
                exit();
            }
        } else {
            header("Location: " . $this->redirect_base);
            exit();
        }
        
        // --- B. Xử lý khi Submit form (POST request) ---
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
            try {
                $this->userModel->user_id = $id; 
                
                $this->userModel->setHoTen($_POST["hoten"] ?? '');
                $this->userModel->setEmail($_POST["email"] ?? '');
                $this->userModel->setDienThoai($_POST["dienthoai"] ?? '');
                $this->userModel->setDiaChi($_POST["diachi"] ?? '');
                $this->userModel->setTierId(intval($_POST["tier_id"] ?? 0));
                $this->userModel->setTrangThai(intval($_POST["trangthai"] ?? 1));
                
                $is_update_password = false;
                if (!empty($_POST["matkhau"])) {
                    $this->userModel->setMatKhau($_POST["matkhau"]); 
                    $is_update_password = true;
                }
                
                $user_data = array_merge($user_data ?? [], $_POST); 

                if ($this->userModel->update($is_update_password)) { 
                    header("Location: " . $this->redirect_base); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi cập nhật dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $user_data = array_merge($user_data ?? [], $_POST);
            }
        }
        
        return [
            'tiers' => $this->getTiers(),
            'user_data' => $user_data,
            'error_message' => $error_message,
        ];
    }
    
    // --- 3. Xử lý hiển thị Chi tiết Người dùng (READ ONE/DETAIL) ---
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $user_info = null;
        $error = null;

        if ($id > 0) {
            $user_info = $this->userModel->find($id);
            if (!$user_info) {
                // Nếu không tìm thấy, chuyển hướng về trang danh sách
                header("Location: " . $this->redirect_base);
                exit();
            }
        } else {
            $error = "Thiếu ID người dùng.";
        }
        
        // Trả về dữ liệu cho View
        return [
            'user_info' => $user_info, 
            'tiers' => $this->getTiers(), // SỬA: Dùng getTiers() thay vì getRoles()
            'error' => $error
        ];
    }
    
    // --- 4. Hiển thị Danh sách (READ/INDEX) ---
    public function index() {
        // 1. Cấu hình Phân trang
        $limit = 10;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        // 2. Lấy tổng số bản ghi
        $total_records = $this->userModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        // 3. Lấy dữ liệu người dùng
        $users_result = $this->userModel->readAll($limit, $offset); 

        // 4. Trả về dữ liệu cho View
        return [
            'users' => $users_result, 
            'total_pages' => $total_pages,
            'current_page' => $page,
            'error_message' => null
        ];
    }
    
    // --- 5. Xử lý Yêu cầu (Handle Request) ---
    public function handleRequest() {
        
        $action = $_GET['action'] ?? '';
        $user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // --- A. XỬ LÝ YÊU CẦU XÓA (DELETE) ---
        if ($action == 'delete' && $user_id > 0) {
            
            if ($this->userModel->delete($user_id)) {
                header("Location: " . $this->redirect_base); 
                exit(); 
            } else {
                header("Location: " . $this->redirect_base); // Vẫn chuyển hướng về trang gốc
                exit(); 
            }
        }
        
        // --- B. XỬ LÝ YÊU CẦU KHÓA/MỞ KHÓA (BLOCK/UNBLOCK) ---
        if (($action == 'block' || $action == 'unblock') && $user_id > 0) {
            
            // Trang thai moi: 0 (Khóa) nếu action là 'block', 1 (Hoạt động) nếu action là 'unblock'
            $new_status = ($action == 'block') ? 0 : 1;
            
            if ($this->userModel->updateStatus($user_id, $new_status)) {
                header("Location: " . $this->redirect_base); 
                exit(); 
            } else {
                header("Location: " . $this->redirect_base); 
                exit(); 
            }
        }
        
        // --- C. HIỂN THỊ DANH SÁCH (READ/INDEX) ---
        return $this->index(); 
    }
}