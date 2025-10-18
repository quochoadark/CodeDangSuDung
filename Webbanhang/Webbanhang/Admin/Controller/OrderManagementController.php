<?php
// File: app/Controllers/OrderManagementController.php (Controller Admin)

require_once __DIR__ . '/../../Database/Database.php'; 
// Đảm bảo tên file này khớp với tên bạn lưu: OrderManagement.php
require_once __DIR__ . '/../model/OrderManagement.php'; 

class OrderManagementController {
    private $db;
    private $orderModel;
    // Đường dẫn gốc khi điều hướng, thêm 'p=1' để reset về trang đầu tiên nếu cần
    private $redirect_base = "/Webbanhang/Admin/index.php?page=donhang";

    public function __construct() {
        $Database = new Database(); 
        $this->db = $Database->conn; 
        $this->orderModel = new OrderManagement($this->db); 
    } 
    
    // --- 1. Hiển thị Danh sách Đơn hàng (INDEX) ---
    public function index() {
        $limit = 8; // Giới hạn 8 đơn hàng mỗi trang
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        // Đảm bảo trang luôn hợp lệ (>= 1)
        if ($page < 1) {
            $page = 1;
        }

        $total_records = $this->orderModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        // Xử lý khi không có bản ghi nào
        if ($total_records == 0) {
            return [
                'orders' => null, 
                'total_pages' => 1,
                'current_page' => 1,
                'message' => $_GET['message'] ?? null 
            ];
        }
        
        // Điều chỉnh trang nếu người dùng cố truy cập trang lớn hơn tổng số trang
        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $offset = ($page - 1) * $limit;

        $orders_result = $this->orderModel->readAll($limit, $offset); 

        return [
            'orders' => $orders_result, 
            'total_pages' => $total_pages,
            'current_page' => $page,
            'message' => $_GET['message'] ?? null 
        ];
    }

    // --- 2. Xử lý Cập nhật Trạng thái (EDIT) ---
    public function editStatus() {
        $order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $order_data = null;
        $error_message = null;

        if ($order_id > 0) {
            $order_data = $this->orderModel->find($order_id); 
            if (!$order_data) {
                header("Location: " . $this->redirect_base . "&message=notfound");
                exit();
            }
        } else {
            header("Location: " . $this->redirect_base);
            exit();
        }

        // --- Xử lý khi Submit form (POST request) ---
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $order_id > 0) {
            try {
                $new_status_id = intval($_POST["trangthai_id"] ?? 0); 

                if ($new_status_id <= 0) {
                    throw new Exception("Vui lòng chọn trạng thái mới hợp lệ (ID > 0).");
                }
                
                // GỌI HÀM TRANSACTION để cập nhật đồng bộ 4 bảng
                if ($this->orderModel->updateOrderStatusTransaction($order_id, $new_status_id)) { 
                    header("Location: " . $this->redirect_base . "&message=success_update"); 
                    exit(); 
                } else {
                    throw new Exception("Cập nhật trạng thái thất bại. Không có thay đổi nào được ghi nhận.");
                }

            } catch (Exception $e) {
                // Lỗi chi tiết từ Transaction sẽ được hiển thị
                error_log("Transaction Update Failed: " . $e->getMessage());
                $error_message = "LỖI HỆ THỐNG: " . $e->getMessage();
            }
        }
        
        // Trả về dữ liệu cho View
        return [
            'order_data' => $order_data,
            'status_list' => $this->orderModel->getAllStatuses(), 
            'error_message' => $error_message,
        ];
    }
    
    // --- 3. Xử lý Yêu cầu (Handle Request) ---
    public function handleRequest() {
        // Lấy tham số 'action' từ URL (ví dụ: ?page=donhang&action=editStatus)
        $action = $_GET['action'] ?? 'index';

        switch ($action) {
            case 'editStatus':
                return $this->editStatus();
            case 'index':
            default:
                // Trả về phương thức index kèm theo tham số phân trang 'p' nếu có
                return $this->index();
        }
    }
}