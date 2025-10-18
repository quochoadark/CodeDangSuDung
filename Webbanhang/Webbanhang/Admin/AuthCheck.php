<?php
// File: Admin/AuthCheck.php - Logic kiểm tra phiên và trạng thái khóa

// 1. Đảm bảo session đã được bắt đầu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⭐ BỔ SUNG: CÁC HEADER CHỐNG CACHE TRÌNH DUYỆT ⭐
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0'); 
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache'); 

$admin_root = "/Webbanhang/Admin/"; 

// 2. KIỂM TRA SESSION CƠ BẢN
// Nếu không có session Admin, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['admin_id']) || !isset($_SESSION['role_id'])) {
    header("Location: {$admin_root}view/Entry.php");
    exit();
}

// 3. ⭐ KIỂM TRA QUYỀN TRUY CẬP (CHỈ ADMIN ROLE ID = 2) ⭐
$current_role_id = $_SESSION['role_id'];

if ($current_role_id != 2) {
    // Hủy phiên hiện tại
    session_unset();
    session_destroy();
    
    // Tạo lại session để lưu thông báo lỗi
    session_start();
    $_SESSION['login_error'] = "Tài khoản của bạn không có quyền truy cập vào trang quản trị này.";
    
    // Chuyển hướng về trang đăng nhập
    header("Location: {$admin_root}view/Entry.php"); 
    exit();
}

// Lấy thông tin session an toàn (chỉ xảy ra nếu role_id là 2)
$current_user_id = $_SESSION['admin_id']; 

// 4. KIỂM TRA TRẠNG THÁI KHÓA CỦA ID HIỆN TẠI (Chỉ áp dụng cho Admin)
// Nạp Model và Kết nối CSDL
require_once __DIR__ . '/../Database/Database.php'; 
require_once __DIR__ . '/model/Login.php'; 

$db = new Database();
$conn = $db->conn;

if ($conn->connect_error) {
    error_log("Lỗi kết nối CSDL trong AuthCheck: " . $conn->connect_error);
    // Có thể chuyển hướng đến trang lỗi nếu cần
    exit();
}

$loginModel = new Login($conn);

// KIỂM TRA TRẠNG THÁI CỦA ID ĐANG ĐĂNG NHẬP
$status = $loginModel->checkStatusById($current_user_id);

// Nếu trạng thái là 0 (Khóa) hoặc không tìm thấy ID (false)
if ($status === 0 || $status === false) { 
    
    // Hủy phiên hiện tại của TÀI KHOẢN BỊ KHÓA
    session_unset();
    session_destroy();
    
    // Tạo lại session để lưu thông báo lỗi
    session_start();
    $_SESSION['login_error'] = "Tài khoản của bạn đã bị khóa hoặc không tồn tại.";
    
    // Chuyển hướng về trang đăng nhập
    header("Location: {$admin_root}view/Entry.php"); 
    exit(); 
}
// Nếu tài khoản hợp lệ (là Admin và không bị khóa), nó được phép tiếp tục tải trang.
?>
