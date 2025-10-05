<?php
// Tên file: /Webbanhang/Admin/Login.php

// 1. Kiểm tra trạng thái session và khởi động nếu cần.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Nhúng LoginController.php
// Đường dẫn: từ Admin/Login.php vào thư mục Controller/
require_once '../Controller/LoginController.php';

// 3. Khởi tạo và xử lý yêu cầu (GET/POST)
$controller = new LoginController();
$controller->handleRequest();

?>