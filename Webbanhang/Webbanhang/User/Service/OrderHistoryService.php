<?php
// File: Service/OrderService.php

require_once '../Repository/OrderHistoryRepository.php'; 

class OrderHistoryService {
    private $orderRepository;
    private $shipping_fee = 50000; // Phí ship mặc định

    public function __construct(OrderHistoryRepository $repository) {
        $this->orderRepository = $repository;
    }

    // Lấy danh sách đơn hàng
    public function getOrderList(int $userId): array {
        return $this->orderRepository->getOrdersByUserId($userId);
    }
    
    // Lấy chi tiết đơn hàng (Gộp data từ nhiều hàm Repository)
    public function getOrderDetails(int $orderId, int $userId): ?array {
        // 1. Lấy thông tin cơ bản VÀ kiểm tra quyền
        $order_info_basic = $this->orderRepository->getBasicOrderInfoAndAuth($orderId, $userId);

        if (!$order_info_basic) {
            return null; // Không tìm thấy đơn hàng hoặc sai user_id
        }
        
        // 2. Lấy thông tin Vận chuyển
        $shipping_info = $this->orderRepository->getShippingInfo($orderId);
        
        // 3. Lấy chi tiết sản phẩm
        $details_items = $this->orderRepository->getOrderDetailsItems($orderId);
        
        // 4. Lấy lịch sử trạng thái
        $status_history = $this->orderRepository->getStatusHistory($orderId);
        
        // 🔥 Xử lý logic và tính toán cho chi tiết sản phẩm
        $order_details = [];
        foreach ($details_items as $row) {
            // Tính toán giá gốc và giảm giá đơn vị (giả định cột gia_goc_sp là giá niêm yết)
            $row['gia_goc'] = (float)($row['gia_goc_sp'] ?? $row['gia_mua']);
            $row['giam_gia_sp_unit'] = $row['gia_goc'] - $row['gia_mua'];
            $order_details[] = $row;
        }

        // Ghép dữ liệu lại
        $order_info = array_merge($order_info_basic, $shipping_info ?? []);

        return [
            'order_info' => $order_info,
            'order_details' => $order_details,
            'status_history' => $status_history
        ];
    }

    // Xây dựng luồng trạng thái (Timeline)
    public function buildStatusFlow(int $currentStatusId, array $statusHistory): array {
        $status_flow = [];
        $all_statuses = $this->orderRepository->getAllOrderStatuses(); 
        
        foreach ($all_statuses as $status) {
            $status_id = (int)$status['trangthai_id'];
            $ngaycapnhat = '';
            
            // Tìm ngày cập nhật cuối cùng cho trạng thái này
            foreach ($statusHistory as $history) {
                if ((int)$history['trangthai_id'] === $status_id) {
                    $ngaycapnhat = $history['ngaycapnhat'];
                }
            }

            $step = [
                'id' => $status_id,
                'name' => $status['ten_trangthai'],
                'ngaycapnhat' => $ngaycapnhat,
                'status' => ''
            ];
            
            // Xác định trạng thái của bước (done, current, pending)
            if ($status_id < $currentStatusId) {
                $step['status'] = 'done';
            } elseif ($status_id === $currentStatusId) {
                $step['status'] = 'current';
            } else {
                $step['status'] = 'pending';
            }
            
            // Nếu đơn hàng bị hủy (5) hoặc hoàn hàng (6) (các ID > 4), các bước 1-4 vẫn là 'done'
            if (($currentStatusId > 4) && $status_id <= 4) {
                $step['status'] = 'done';
            }

            $status_flow[] = $step;
        }
        return $status_flow;
    }
    
    // Xử lý hủy đơn hàng
    public function cancelOrder(int $orderId, int $userId): string {
        $CANCEL_STATUS_ID = 5; 
        
        // 1. Lấy thông tin để kiểm tra trạng thái hiện tại
        $order_row = $this->orderRepository->getBasicOrderInfoAndAuth($orderId, $userId);

        if (!$order_row) {
            return "not_found";
        }

        $current_status = (int) $order_row['trangthai'];

        // 2. Chuyển logic cập nhật và transaction sang Repository
        return $this->orderRepository->updateStatusAndLogHistory($orderId, $userId, $CANCEL_STATUS_ID, $current_status);
    }
}