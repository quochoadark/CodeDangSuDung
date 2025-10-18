<?php
// File: app/Controllers/OrderTrackingController.php

require_once __DIR__ . '/../../Database/Database.php'; 
// 🔥 Đã đổi tên Model (CancelTracking) thành OrderRepository
require_once __DIR__ . '/../Repository/OrderTrackingRepository.php'; 
require_once __DIR__ . '/../Service/OrderTrackingService.php'; 

class OrderTrackingController {
    private $orderTrackingService;

    public function __construct() {
        $Database = new Database(); 
        $conn = $Database->conn;
        
        // 🔥 Khởi tạo Repository và truyền vào Service
        $orderRepository = new OrderTrackingRepository($conn);
        $this->orderTrackingService = new OrderTrackingService($orderRepository);
    } 

    // Phương thức chính để lấy dữ liệu cho thanh tiến trình
    public function index($order_id) {
        // Xử lý đầu vào (làm sạch/validate)
        $order_id = filter_var($order_id, FILTER_VALIDATE_INT);
        if (!$order_id) {
             // Có thể chuyển hướng hoặc trả về lỗi
             return ['error' => 'ID đơn hàng không hợp lệ.'];
        }
        
        // Gọi Service để thực hiện logic nghiệp vụ
        $trackingData = $this->orderTrackingService->getTrackingData($order_id);
        return $trackingData;
    }
}