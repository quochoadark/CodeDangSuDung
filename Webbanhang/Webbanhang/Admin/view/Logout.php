<?php
// File: /Webbanhang/Admin/Logout.php (ĐÃ BỔ SUNG: XÓA TOKEN REMEMBER ME VÀ HỦY SESSION HOÀN TOÀN)

// 1. Phải khởi động session để có thể truy cập và xóa nó
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⭐ QUAN TRỌNG: THÊM HEADER CHỐNG CACHE ⭐
// Ngăn trình duyệt lưu cache trang này
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0'); 
header('Cache-Control: post-check=0, pre-check=0', FALSE);
header('Pragma: no-cache'); 
// ⭐ KẾT THÚC CHỐNG CACHE ⭐

// Nạp Model và DB để xử lý xóa Remember Token
require_once __DIR__ . '/../Model/Login.php'; // ⭐ SỬA: Đã đồng bộ tên Model là Login
require_once __DIR__ . '/../../Database/Database.php'; 

// Cấu hình
$cookie_name = 'ad_remember_token'; 
$admin_root_path = '/Webbanhang/Admin/'; 
$login_page = 'view/Entry.php'; // Trang đăng nhập Admin

// Lấy admin_id trước khi hủy session
$admin_id_to_clear = $_SESSION['admin_id'] ?? null; 

// 2. XỬ LÝ XÓA REMEMBER ME TOKEN (Nếu có)
if ($admin_id_to_clear) {
    
    // a. Xóa Token trong DB
    try {
        $db = new Database();
        $conn = $db->conn;

        if (!$conn->connect_error) {
            // ⭐ SỬA: Dùng class Model đã được lưu là Login
            $loginModel = new Login($conn); 
            
            // Xóa token khỏi DB
            $loginModel->clearRememberToken($admin_id_to_clear); 
        }
    } catch (\Throwable $th) {
        error_log("Lỗi DB khi xóa remember token lúc Admin Logout: " . $th->getMessage());
    }
}

// b. Xóa Cookie Remember Me trên trình duyệt
$cookie_path = '/Webbanhang/Admin/'; 
setcookie($cookie_name, '', time() - 3600, $cookie_path, '', false, true); 

// 3. HỦY SESSION HOÀN TOÀN (Cần thiết để xóa phiên)
// Xóa tất cả các biến session
session_unset(); 
// Hủy session trên server và xóa cookie session ID
session_destroy(); 

// 4. Chuyển hướng người dùng về trang đăng nhập
header("Location: {$admin_root_path}{$login_page}"); 
exit(); 
?>