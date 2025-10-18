<?php
// File: /Webbanhang/User/Logout.php
// ✅ ĐÃ TỐI ƯU VÀ AN TOÀN: XÓA BIẾN SESSION USER VÀ TOKEN, KHÔNG ẢNH HƯỞNG ADMIN

// 1. Khởi động session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ⭐ THÊM HEADER CHỐNG CACHE ⭐
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
// ⭐ KẾT THÚC CHỐNG CACHE ⭐

// 2. Nạp Database (Đã loại bỏ việc nạp LoginModel vì nó không được dùng đúng cách trong file này)
require_once __DIR__ . '/../../Database/Database.php';

// 3. Cấu hình
$cookie_name   = 'kh_remember_token';
$user_root     = '/Webbanhang/User/';
$login_page    = 'View/Login.php';

// 4. Lấy user_id trước khi xóa session
$user_id_to_clear = $_SESSION['kh_user_id'] ?? null;

// 5. Xóa biến session của User
unset($_SESSION['kh_user_id']);
unset($_SESSION['kh_hoten']);
unset($_SESSION['kh_role']);
unset($_SESSION['kh_is_logged_in']);

// 6. Xóa Remember Me (Token DB + Cookie)
if ($user_id_to_clear) {
    try {
        $db   = new Database();
        $conn = $db->conn;

        if (!$conn->connect_error) {
            
            // ✅ ĐÃ SỬA: Thực hiện xóa remember token và expiry khỏi DB bằng câu lệnh SQL an toàn
            $stmt = $conn->prepare("UPDATE nguoidung SET remember_token_hash = NULL, token_expiry = NULL WHERE user_id = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user_id_to_clear);
                $stmt->execute();
            } else {
                 error_log("Lỗi Prepare khi xóa remember token lúc User Logout: " . $conn->error);
            }
        }
    } catch (\Throwable $th) {
        error_log("Lỗi DB khi xóa remember token lúc User Logout: " . $th->getMessage());
    }
}

// Xóa cookie Remember Me
setcookie($cookie_name, '', time() - 3600, '/', '', false, true);

// 7. Chuyển hướng về trang đăng nhập
header("Location: {$user_root}{$login_page}");
exit();