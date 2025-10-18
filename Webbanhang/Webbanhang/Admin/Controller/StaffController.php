<?php 
// File: Admin/Controllers/StaffController.php (ĐÃ CẬP NHẬT)

require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../model/Staff.php';

// ⚠️ ĐÃ LOẠI BỎ PHPMailer REQUIRES

class StaffController
{
    private $db;
    private $staffModel;
    private $redirect_base = "/Webbanhang/Admin/index.php?page=nhanvien";
    
    private $ID_NHAN_VIEN_BAN_HANG = 1; // Dựa trên bảng chucvu
    private $ID_ADMIN = 2; // Dựa trên bảng chucvu

    public function __construct()
    {
        $Database = new Database();
        $this->db = $Database->conn;
        $this->staffModel = new Staff($this->db);
    }

    // --- Getter ---
    public function getStaffModel()
    {
        return $this->staffModel;
    }

    // --- Lấy danh sách Chức vụ ---
    public function getRoles()
    {
        $role_result = $this->db->query("SELECT id_chucvu, ten_chucvu FROM chucvu ORDER BY id_chucvu ASC");

        $roles = [];
        if ($role_result) {
            $roles = $role_result->fetch_all(MYSQLI_ASSOC);
        }
        return $roles;
    }

    // --- Hiển thị Chi tiết Nhân viên ---
    public function detail()
    {
        // ... (Logic giữ nguyên)
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $staff_info = null;
        $error = null;

        if ($id > 0) {
            $staff_info = $this->staffModel->find($id);
            if (!$staff_info) {
                header("Location: " . $this->redirect_base);
                exit();
            }
        } else {
            $error = "Thiếu ID nhân viên.";
        }

        return [
            'staff_info' => $staff_info,
            'roles' => $this->getRoles(),
            'error' => $error
        ];
    }

    // --- 1. Thêm mới Nhân viên (Create) ---
    public function create()
    {
        // ... (Logic giữ nguyên)
        $data = [
            'roles' => $this->getRoles(),
            'error_message' => null,
            'staff_data' => []
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $this->staffModel->setHoTen($_POST["hoten"] ?? '');
                $this->staffModel->setEmail($_POST["email"] ?? '');
                $this->staffModel->setMatKhau($_POST["matkhau"] ?? '');
                $this->staffModel->setDienThoai($_POST["dienthoai"] ?? '');
                $this->staffModel->setTrangThai((int)($_POST["trangthai"] ?? 1));
                $this->staffModel->setIdChucVu((int)($_POST["id_chucvu"] ?? 0));

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

    // --- 2. Hiển thị Danh sách (Index) ---
    public function index()
    {
        // ... (Logic giữ nguyên)
        $limit = 8;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        $total_records = $this->staffModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        $staffs_result = $this->staffModel->readAll($limit, $offset);

        return [
            'staffs' => $staffs_result,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'error_message' => null
        ];
    }

    // --- 3. Xử lý yêu cầu (block / unblock) ---
    public function handleRequest()
    {
        // ... (Logic giữ nguyên)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $logged_in_staff_id = (int)($_SESSION['admin_id'] ?? 0);
        $action = $_GET['action'] ?? '';
        $staff_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        // --- Ngăn Admin tự thao tác với chính tài khoản của mình ---
        if (
            $staff_id > 0 &&
            $staff_id === $logged_in_staff_id &&
            in_array($action, ['delete', 'block', 'unblock'])
        ) {
            header("Location: " . $this->redirect_base);
            exit();
        }
        
        // --- Xử lý Khóa / Mở khóa ---
        if (in_array($action, ['block', 'unblock']) && $staff_id > 0) {
            $staff_info = $this->staffModel->find($staff_id);

            // Chỉ cho phép thao tác với Nhân viên bán hàng
            if ($staff_info && (int)$staff_info['id_chucvu'] === $this->ID_NHAN_VIEN_BAN_HANG) {
                $new_status = ($action === 'block') ? 0 : 1;
                $this->staffModel->updateStatus($staff_id, $new_status);
            }

            header("Location: " . $this->redirect_base);
            exit();
        }

        // --- Xử lý Xóa (BỊ LOẠI BỎ) ---
        if ($action === 'delete') {
            header("Location: " . $this->redirect_base); // Chỉ chuyển hướng, không thực hiện xóa
            exit();
        }

        // --- Mặc định: hiển thị danh sách ---
        return $this->index();
    }
    
    // ==================== CHỨC NĂNG GỬI EMAIL ĐÃ BỊ LOẠI BỎ KHỎI STAFFCONTROLLER ====================
}