<?php

// 1. Phải khởi động session để có thể truy cập và xóa nó
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Xóa tất cả các biến session
$_SESSION = array(); // Cách an toàn hơn để xóa toàn bộ dữ liệu session

// 3. Hủy session
session_destroy();

// 4. Chuyển hướng người dùng về trang đăng nhập
// Sử dụng đường dẫn tuyệt đối để đảm bảo an toàn
header("Location: /Webbanhang/User/View/Login.php");
exit(); // Quan trọng: Đảm bảo script dừng lại sau khi chuyển hướng
?>