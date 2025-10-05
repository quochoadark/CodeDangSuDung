<?php
// File: Admin/Controllers/StaffController.php (Đã sửa đổi và bổ sung)
require_once __DIR__ . '/../../Database/database.php'; 
require_once __DIR__ . '/../model/Staff.php'; 

class StaffController {
    private $db;
    private $staffModel; 
    private $redirect_base = "/Webbanhang/Admin/index.php?page=nhanvien";

    public function __construct() {
        $database = new Database();
        $this->db = $database->conn;
        $this->staffModel = new Staff($this->db); 
    } 

    // --- Phương thức Getter để lấy Staff Model ---
    public function getStaffModel() {
        return $this->staffModel;
    }
    
    // --- Hỗ trợ: Lấy danh sách Chức vụ từ bảng chucvu (PUBLIC) ---
    public function getRoles() { 
        $role_result = $this->db->query("SELECT id_chucvu, ten_chucvu FROM chucvu ORDER BY id_chucvu ASC");
        
        $roles = [];
        if ($role_result) {
            $roles = $role_result->fetch_all(MYSQLI_ASSOC);
        }
        return $roles;
    }
    
    // --- BỔ SUNG: Xử lý hiển thị Chi tiết Nhân viên (READ ONE) ---
    public function detail() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $staff_info = null;
        $error = null;

        if ($id > 0) {
            $staff_info = $this->staffModel->find($id);
            if (!$staff_info) {
                // Nếu không tìm thấy, chuyển hướng về trang danh sách
                header("Location: " . $this->redirect_base);
                exit();
            }
        } else {
            $error = "Thiếu ID nhân viên.";
        }
        
        // Trả về dữ liệu cho View
        return [
            'staff_info' => $staff_info, 
            'roles' => $this->getRoles(),
            'error' => $error
        ];
    }
    
    // --- 1. Xử lý Thêm mới Nhân viên (CREATE) ---
    public function create() {
        // ... (Giữ nguyên code create())
        $data = [
            'roles' => $this->getRoles(),
            'error_message' => null,
            'staff_data' => [] 
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                // Gán dữ liệu từ form vào model
                $this->staffModel->setHoTen($_POST["hoten"] ?? '');
                $this->staffModel->setEmail($_POST["email"] ?? '');
                $this->staffModel->setMatKhau($_POST["matkhau"] ?? ''); 
                $this->staffModel->setDienThoai($_POST["dienthoai"] ?? '');
                $this->staffModel->setTrangThai(intval($_POST["trangthai"] ?? 1));
                $this->staffModel->setIdChucVu(intval($_POST["id_chucvu"] ?? 0));
                
                if (empty($_POST["matkhau"])) {
                    throw new Exception("Vui lòng nhập mật khẩu.");
                }

                if ($this->staffModel->save()) {
                    header("Location: " . $this->redirect_base); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi lưu dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['staff_data'] = $_POST;
            }  
        }
        
        return $data;
    }
    
    // --- 2. Hiển thị form Sửa và xử lý Sửa (UPDATE) ---
    public function update() {
        // ... (Giữ nguyên code update())
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $staff_data = null;
        $error_message = null;
        
        // --- A. Lấy dữ liệu nhân viên hiện tại ---
        if ($id > 0) {
            $staff_data = $this->staffModel->find($id); 
            if (!$staff_data) {
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
                $this->staffModel->staff_id = $id; 
                
                $this->staffModel->setHoTen($_POST["hoten"] ?? '');
                $this->staffModel->setEmail($_POST["email"] ?? '');
                $this->staffModel->setDienThoai($_POST["dienthoai"] ?? '');
                $this->staffModel->setTrangThai(intval($_POST["trangthai"] ?? 1));
                $this->staffModel->setIdChucVu(intval($_POST["id_chucvu"] ?? 0));
                
                $is_update_password = false;
                if (!empty($_POST["matkhau"])) {
                    $this->staffModel->setMatKhau($_POST["matkhau"]); 
                    $is_update_password = true;
                }
                
                $staff_data = array_merge($staff_data ?? [], $_POST); 

                if ($this->staffModel->update($is_update_password)) { 
                    header("Location: " . $this->redirect_base); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi cập nhật dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $staff_data = array_merge($staff_data ?? [], $_POST);
            }
        }
        
        return [
            'roles' => $this->getRoles(),
            'staff_data' => $staff_data,
            'error_message' => $error_message,
        ];
    }
    
    // --- 3. Hiển thị Danh sách (READ/INDEX) ---
    public function index() {
        // ... (Giữ nguyên code index())
        // 1. Cấu hình Phân trang
        $limit = 10;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        // 2. Lấy tổng số bản ghi
        $total_records = $this->staffModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        // 3. Lấy dữ liệu nhân viên
        $staffs_result = $this->staffModel->readAll($limit, $offset); 

        // 4. Trả về dữ liệu cho View
        return [
            'staffs' => $staffs_result, 
            'total_pages' => $total_pages,
            'current_page' => $page,
            'error_message' => null
        ];
    }
    
    // --- 4. Xử lý Yêu cầu (Handle Request) ---
  public function handleRequest() {
        
        $action = $_GET['action'] ?? '';
        $staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        // ID CHỨC VỤ CỦA NHÂN VIÊN BÁN HÀNG (GIẢ ĐỊNH)
        // BẠN CẦN THAY SỐ NÀY NẾU ID TRONG DB CỦA BẠN KHÁC
        $ID_NHAN_VIEN_BAN_HANG = 1; 

        
        // --- A. XỬ LÝ YÊU CẦU XÓA (DELETE) ---
        if ($action == 'delete' && $staff_id > 0) {
            // Lấy dữ liệu nhân viên để kiểm tra chức vụ trước khi xóa
            $staff_info = $this->staffModel->find($staff_id);
            
            if ($staff_info && (int)$staff_info['id_chucvu'] === $ID_NHAN_VIEN_BAN_HANG) {
                 $this->staffModel->delete($staff_id);
            } 
            // KHÔNG cần xử lý lỗi phức tạp ở đây, chỉ cần chuyển hướng
            header("Location: " . $this->redirect_base); 
            exit(); 
        }
        
        // --- B. XỬ LÝ YÊU CẦU KHÓA/MỞ KHÓA (BLOCK/UNBLOCK) ---
        if (($action == 'block' || $action == 'unblock') && $staff_id > 0) {
            
            // 1. Lấy dữ liệu nhân viên
            $staff_info = $this->staffModel->find($staff_id);
            
            // 2. KIỂM TRA CHỨC VỤ: CHỈ CHO PHÉP NẾU LÀ NHÂN VIÊN BÁN HÀNG
            if ($staff_info && (int)$staff_info['id_chucvu'] === $ID_NHAN_VIEN_BAN_HANG) {
                
                $new_status = ($action == 'block') ? 0 : 1; // 0: Ngừng hoạt động, 1: Hoạt động
                
                // Thực hiện cập nhật trạng thái
                $this->staffModel->updateStatus($staff_id, $new_status);
                
            } else {
                // TÙY CHỌN: Bạn có thể thêm logic ghi log hoặc thông báo lỗi nếu cố gắng khóa ADMIN
                // Hiện tại, chúng ta chỉ KHÔNG làm gì và chuyển hướng
            }
            
            header("Location: " . $this->redirect_base); 
            exit(); 
        }
        
        // --- C. HIỂN THỊ DANH SÁCH (READ/INDEX) ---
        return $this->index(); 
    }
}