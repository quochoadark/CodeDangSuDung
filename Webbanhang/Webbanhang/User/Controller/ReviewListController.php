<?php
// Tên tệp: User/Controller/ReviewListController.php

// 1. Nạp Database và Service
require_once __DIR__ . '/../../Database/Database.php'; 
require_once __DIR__ . '/../Repository/ReviewRepository.php'; 
require_once __DIR__ . '/../Service/ReviewService.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$all_reviews = [];
$db = null;

try {
    // 1. Khởi tạo Database và kết nối
    $db = new Database();
    $conn = $db->conn; 

    if ($conn->connect_error) {
        throw new Exception("Lỗi kết nối CSDL: " . $conn->connect_error);
    }

    // 2. Khởi tạo Repository và Service
    $reviewRepository = new ReviewRepository($conn);
    $reviewService = new ReviewService($reviewRepository);
    
    // 3. Lấy dữ liệu từ Service
    $all_reviews = $reviewService->getAllReviewsForTestimonial(); 

} catch (Exception $e) {
    // Gán mảng rỗng và có thể ghi log lỗi
    error_log("Lỗi trong ReviewListController: " . $e->getMessage());
    $all_reviews = []; 
} finally {
    // 4. Đóng kết nối
    if ($db !== null && isset($db->conn) && $db->conn instanceof mysqli) {
        $db->conn->close();
    }
}


include __DIR__ . '/../View/testimonial.php'; 
?>