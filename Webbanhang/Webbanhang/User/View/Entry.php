<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Nhúng LoginController.php
// Đường dẫn: từ Admin/Login.php vào thư mục Controller/
require_once __DIR__ . '/../Controller/LoginController.php';

// 3. Khởi tạo và xử lý yêu cầu (GET/POST)
$controller = new LoginController();
$controller->handleRequest();

?>