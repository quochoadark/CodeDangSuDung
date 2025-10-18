<?php
// File: app/Service/OrderTrackingService.php

// Giả định OrderRepository.php nằm ở vị trí tương đối này
require_once __DIR__ . '/../Repository/OrderTrackingRepository.php'; 

class OrderTrackingService {
    private $orderRepository;

    public function __construct(OrderTrackingRepository $orderRepository) {
        $this->orderRepository = $orderRepository;
    } 

    public function getTrackingData(int $order_id): array {
        // 1. Lấy thông tin đơn hàng hiện tại từ Repository
        $order_data = $this->orderRepository->findOrderById($order_id);
        
        if (!$order_data) {
            return ['error' => 'Không tìm thấy đơn hàng hoặc ID không hợp lệ.'];
        }

        // 2. Lấy TẤT CẢ trạng thái (theo thứ tự ID) từ Repository
        $status_flow = $this->orderRepository->getAllStatuses(); 
        
        $current_status_id = (int)($order_data['trangthai'] ?? 0);
        
        $tracking_steps = [];

        // 3. Xử lý logic tiến trình (Business logic)
        foreach ($status_flow as $status) {
            $status_id = (int)$status['trangthai_id'];
            $status_name = $status['ten_trangthai'];
            
            $step = [
                'id' => $status_id,
                'name' => $status_name,
                'status' => 'pending', 
                'is_current' => false
            ];

            // Logic xác định trạng thái đã qua, hiện tại hay đang chờ
            if ($status_id < $current_status_id) {
                $step['status'] = 'done'; // Đã hoàn thành
            } elseif ($status_id === $current_status_id) {
                $step['status'] = 'current'; // Hiện tại
                $step['is_current'] = true;
            } else {
                $step['status'] = 'pending'; // Đang chờ
            }

            $tracking_steps[] = $step;
        }

        // 4. Trả về dữ liệu đã xử lý
        return [
            'order_id' => $order_id,
            'tongtien' => $order_data['tongtien'],
            'ngaytao' => $order_data['ngaytao'],
            'current_status_text' => $order_data['ten_trangthai'],
            'steps' => $tracking_steps,
        ];
    }
}