<?php
// File: Controller/HistoryOrderController.php - PHIÊN BẢN MỚI

session_start();

// Thiết lập lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Đường dẫn Database, Repository và Service
require_once '../../Database/Database.php'; 
require_once '../Repository/OrderHistoryRepository.php'; // Mới
require_once '../Service/OrderHistoryService.php';       // Mới

// Hàm format tiền tệ
function formatVND($number) {
    $num = intval(round((float)$number)); 
    return number_format($num, 0, ',', '.') . ' ₫'; 
}


$user_id = $_SESSION['kh_user_id'] ?? 0; 
$error_message = '';
$cancel_message = null;

if ($user_id <= 0) {
    $_SESSION['redirect_to'] = '../Controller/OrderHistoryController.php';
    header("Location: ../View/Login.php"); 
    exit();
}

$db = new Database(); 
$conn = $db->conn; 

if (!$conn) {
    die("Lỗi kết nối cơ sở dữ liệu. Vui lòng kiểm tra lại file Database.php.");
}

// Khởi tạo Repository và Service
$orderRepository = new OrderHistoryRepository($conn);
$orderService = new OrderHistoryService($orderRepository);

// -----------------------------------------------------------
// 2. XỬ LÝ YÊU CẦU HỦY ĐƠN HÀNG
// -----------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] === 'cancel' && isset($_GET['order_id'])) {
    $order_to_cancel_id = (int)$_GET['order_id'];
    
    if ($order_to_cancel_id > 0) {
        // 🔥 Gọi Service để hủy
        $result = $orderService->cancelOrder($order_to_cancel_id, $user_id);
        
        switch ($result) {
            case 'success':
                $_SESSION['cancel_message'] = 'Đơn hàng #' . $order_to_cancel_id . ' đã được hủy thành công.';
                break;
            case 'invalid_status':
                $_SESSION['cancel_message'] = 'Lỗi: Đơn hàng chỉ có thể hủy ở trạng thái "Chờ xác nhận" hoặc "Đã xác nhận".';
                break;
            case 'not_found':
                $_SESSION['cancel_message'] = 'Lỗi: Không tìm thấy đơn hàng hoặc bạn không có quyền hủy.';
                break;
            case 'error':
                $_SESSION['cancel_message'] = 'Lỗi hệ thống khi hủy đơn hàng. Vui lòng thử lại.';
                break;
        }
    } else {
        $_SESSION['cancel_message'] = 'ID đơn hàng không hợp lệ.';
    }

    header('Location: OrderHistoryController.php');
    exit();
}

// Lấy thông báo hủy (nếu có) và xóa khỏi session
$cancel_message = $_SESSION['cancel_message'] ?? null;
unset($_SESSION['cancel_message']); 

// -----------------------------------------------------------
// 3. TẢI DỮ LIỆU VÀ CHUẨN BỊ CHO VIEW
// -----------------------------------------------------------

$mode = 'list';
$order_info = null;
$order_details = [];
$status_history = [];
$status_flow = []; 
$shipping_fee = 50000; 
$orders = [];

$selected_order_id = (int)($_GET['order_id'] ?? 0);

if ($selected_order_id > 0) {
    // 🔥 Gọi Service để lấy chi tiết
    $order_data = $orderService->getOrderDetails($selected_order_id, $user_id); 
    
    if ($order_data && $order_data['order_info']) {
        $mode = 'details';
        $order_info = $order_data['order_info'];
        $order_details = $order_data['order_details'];
        $status_history = $order_data['status_history'];
        
        $shipping_fee = (float)($order_info['phiship'] ?? 50000); 

        // 🔥 Gọi Service để xây dựng luồng trạng thái
        $status_flow = $orderService->buildStatusFlow((int)$order_info['trangthai'], $status_history);
        
    } else {
        $error_message = 'Đơn hàng không tồn tại hoặc bạn không có quyền truy cập.'; 
        $mode = 'list';
    }
}

// 🔥 Luôn tải danh sách đơn hàng
$orders = $orderService->getOrderList($user_id); 


// Đóng gói tất cả dữ liệu vào mảng $data để truyền sang View
$data = [
    'mode' => $mode,
    'orders' => $orders,
    'order_info' => $order_info,
    'order_details' => $order_details,
    'status_history' => $status_history,
    'status_flow' => $status_flow, 
    'shipping_fee' => $shipping_fee,
    'error_message' => $error_message,
    'cancel_message' => $cancel_message 
];


// -----------------------------------------------------------
// 4. ĐÓNG KẾT NỐI VÀ LOAD VIEW
// -----------------------------------------------------------

if (isset($conn) && $conn) {
    $db->closeConnection();
}

require_once '../View/HistoryOrder.php'; 
?>