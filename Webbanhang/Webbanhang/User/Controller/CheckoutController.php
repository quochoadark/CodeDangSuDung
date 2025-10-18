<?php
session_start();

// Thiết lập lỗi (Tùy chọn)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Yêu cầu các tệp cần thiết 
require_once '../../Database/Database.php'; 
require_once '../Repository/CartRepository.php'; 
require_once '../Service/CartService.php'; 
require_once '../Repository/CheckoutRepository.php'; 
require_once '../Service/CheckoutService.php'; 

// Khởi tạo kết nối Database, Repository và Service
$db = new Database(); 
$conn = $db->conn; 

$cartRepository = new CartRepository($conn);
$cartService = new CartService($cartRepository);

$orderRepository = new CheckoutRepository($conn); 
$checkoutService = new CheckoutService($orderRepository, $cartRepository); 

// Lấy thông tin cơ bản
$user_id = $_SESSION['kh_user_id'] ?? 0;
$shipping_fee = 50000; 

// Hàm format tiền tệ (Dùng chung)
function formatVND($number) {
    $num = intval($number);
    return number_format($num, 0, ',', '.');
}

// Kiểm tra đăng nhập
if ($user_id <= 0) {
    $_SESSION['redirect_to'] = '../View/Checkout.php';
    header("Location: ../View/Login.php"); 
    exit();
}

// Cần đồng bộ Session từ DB trước khi tính tổng
$cartService->syncSessionFromDatabase($user_id); 

// LẤY DỮ LIỆU GIỎ HÀNG BẰNG CART SERVICE
$summaryData = $cartService->getCartSummary();
$cart_items = $summaryData['items'];
$sub_total = $summaryData['sub_total'];
$discount = $summaryData['discount'];
$grand_total = $summaryData['grand_total'];
$voucher_code = $summaryData['voucher_code'] ?? ''; // Thêm biến voucher_code cho View

// -----------------------------------------------------------
// 1. KIỂM TRA GIỎ HÀNG RỖNG VÀ CHUYỂN HƯỚNG
// (Điều này ngăn chặn lỗi hiển thị 2 lần: rỗng và chi tiết đơn hàng)
// -----------------------------------------------------------
if (empty($cart_items)) {
    // Chỉ chuyển hướng nếu đây là GET hoặc POST thường
    if (!isset($_POST['action']) || $_POST['action'] !== 'place_order_ajax') {
        // Sử dụng session message của CartController
        $_SESSION['cart_message'] = "Giỏ hàng rỗng! Vui lòng thêm sản phẩm để thanh toán.";
        header("Location: CartController.php"); 
        exit();
    }
}

$current_voucher_id = ($_SESSION['applied_voucher_id'] ?? null);
$discount_for_model = $discount; 

// -----------------------------------------------------------
// 2. XỬ LÝ HÀNH ĐỘNG POST
// -----------------------------------------------------------
// 🔥 Lấy thông báo lỗi (từ quá trình đặt hàng thất bại trước đó) và XÓA NGAY
$error_message = $_SESSION['checkout_error'] ?? null;
unset($_SESSION['checkout_error']); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // 2A. XỬ LÝ ĐẶT HÀNG BẰNG AJAX
    if ($_POST['action'] === 'place_order_ajax') {
        $response = ['success' => false, 'message' => 'Lỗi không xác định.'];
        header('Content-Type: application/json'); // Thiết lập header cho JSON

        $recipient_name = trim($_POST['recipient_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? 'Tiền mặt');
        $note = trim($_POST['note'] ?? '');
        
        // SERVER-SIDE VALIDATION
        if (empty($cart_items)) {
            $response['message'] = "Giỏ hàng đã bị trống. Vui lòng quay lại trang giỏ hàng.";
        } elseif (empty($recipient_name) || empty($phone_number) || empty($address)) {
            $response['message'] = "Vui lòng nhập đầy đủ Tên, SĐT và Địa chỉ nhận hàng.";
        } else {
            // GỌI SERVICE ĐẶT HÀNG
            $result = $checkoutService->placeOrder(
                $user_id, $cart_items, $grand_total, $current_voucher_id, $discount_for_model, 
                $recipient_name, $phone_number, $address, $payment_method, $note
            );
            
            if ($result['success']) {
                // Xóa session giỏ hàng/voucher
                unset($_SESSION['cart']); 
                unset($_SESSION['discount_amount']);
                unset($_SESSION['voucher_code']);
                unset($_SESSION['voucher_giam_value']); 
                unset($_SESSION['applied_voucher_id']);
                
                $response['success'] = true;
                $response['message'] = $result['message'] ?? "Đặt hàng thành công!";
                $response['order_id'] = $result['order_id'];
            } else {
                $response['message'] = $result['message'];
            }
        }
        
        // TRẢ VỀ JSON CHO CLIENT
        echo json_encode($response);
        exit(); // QUAN TRỌNG: DỪNG XỬ LÝ
    }


    // 2B. XỬ LÝ ĐẶT HÀNG BẰNG FORM POST THÔNG THƯỜNG (Giữ lại)
    if ($_POST['action'] === 'place_order') {
        $recipient_name = trim($_POST['recipient_name'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $payment_method = trim($_POST['payment_method'] ?? 'Tiền mặt');
        $note = trim($_POST['note'] ?? '');
        
        if (empty($recipient_name) || empty($phone_number) || empty($address)) {
            $_SESSION['checkout_error'] = "Vui lòng nhập đầy đủ Tên, SĐT và Địa chỉ nhận hàng.";
            $_SESSION['temp_address'] = ['name' => $recipient_name, 'phone' => $phone_number, 'address' => $address];
            header("Location: CheckoutController.php");
            exit();
        } else {
            $result = $checkoutService->placeOrder(
                $user_id, $cart_items, $grand_total, $current_voucher_id, $discount_for_model, 
                $recipient_name, $phone_number, $address, $payment_method, $note
            );
        
            if ($result['success']) {
                // Xóa session
                unset($_SESSION['cart']); 
                unset($_SESSION['discount_amount']);
                unset($_SESSION['voucher_code']);
                unset($_SESSION['voucher_giam_value']); 
                unset($_SESSION['applied_voucher_id']);
                
                header("Location: ../View/order-success.php?order_id=" . $result['order_id']);
                exit();
            } else {
                $_SESSION['checkout_error'] = $result['message'];
                header("Location: CheckoutController.php");
                exit();
            }
        }
    }
}


// -----------------------------------------------------------
// 3. LẤY DỮ LIỆU CHO VIEW (GET Request)
// -----------------------------------------------------------

// Lấy thông tin địa chỉ mặc định của người dùng từ Service
$user_profile = $checkoutService->getUserProfile($user_id);

// Nếu có dữ liệu tạm thời (do POST thất bại), ưu tiên dữ liệu đó
if (isset($_SESSION['temp_address'])) {
    $default_address = $_SESSION['temp_address'];
    unset($_SESSION['temp_address']);
} else {
    $default_address = [
        'name' => $user_profile['hoten'] ?? '',
        'phone' => $user_profile['dienthoai'] ?? '',
        'address' => $user_profile['diachi'] ?? '',
    ];
}


if (isset($conn) && $conn) {
    $conn->close();
}

// Load View và truyền tất cả các biến cần thiết sang View
require_once '../View/Checkout.php';