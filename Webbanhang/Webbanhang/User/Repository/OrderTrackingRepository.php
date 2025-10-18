<?php
// File: app/Repository/OrderRepository.php

// Giả định file Database.php nằm ở vị trí tương đối này
require_once __DIR__ . '/../../Database/Database.php'; 

class OrderTrackingRepository {
    private $conn;
    private $table_name = "donhang";
    private $status_table = "trangthaidonhang"; // Tên bảng trạng thái

    public function __construct(mysqli $db_conn) {
        $this->conn = $db_conn;
    }

    public function findOrderById(int $order_id): ?array {
        $sql = "SELECT t.*, ts.ten_trangthai
                FROM " . $this->table_name . " t
                LEFT JOIN " . $this->status_table . " ts ON t.trangthai = ts.trangthai_id
                WHERE t.order_id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (findOrderById): " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row;
    }
    public function getAllStatuses(): array {
        $sql = "SELECT trangthai_id, ten_trangthai FROM " . $this->status_table . " ORDER BY trangthai_id ASC";
        $result = $this->conn->query($sql);
        
        if ($result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            error_log("SQL Error in getAllStatuses: " . $this->conn->error);
            return [];
        }
    }
    
    public function updateStatus(int $order_id, int $new_status_id): bool {
        $sql = "UPDATE " . $this->table_name . " SET trangthai = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (updateStatus): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("ii", $new_status_id, $order_id); 
        
        if ($stmt->execute()) {
            $success = $stmt->affected_rows > 0;
            $stmt->close();
            return $success;
        } else {
            error_log("Execute failed (updateStatus): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Cập nhật đồng thời trạng thái đơn hàng, thanh toán và vận chuyển
     * trong một giao dịch. (Sử dụng cho Admin)
     * @param int $order_id ID của đơn hàng.
     * @param int $new_status_id ID trạng thái mới (từ bảng trangthaidonhang).
     * @return bool Thành công hay thất bại.
     * @throws Exception nếu có lỗi trong giao dịch.
     */
    public function updateOrderStatusTransaction(int $order_id, int $new_status_id): bool
    {
        // Bắt đầu giao dịch
        $this->conn->begin_transaction();

        try {
            // 1. CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (Bảng donhang)
            if (!$this->updateStatus($order_id, $new_status_id)) {
                throw new Exception("Lỗi khi cập nhật trạng thái đơn hàng.");
            }
            
            // 2. CẬP NHẬT TRẠNG THÁI THANH TOÁN (Bảng thanhtoan)
            
            // 2a. Logic khi đơn hàng HOÀN TẤT (ID = 4)
            if ($new_status_id === 4) { // 4: Đã giao hàng -> Khách đã nhận VÀ thanh toán thành công
                $payment_status = 'Paid';
                
                // Cập nhật trạng thái thanh toán và ngày thanh toán
                $sql_payment = "UPDATE thanhtoan SET trangthai = ?, ngaythanhtoan = NOW() WHERE order_id = ?";
                $stmt_payment = $this->conn->prepare($sql_payment);
                $stmt_payment->bind_param("si", $payment_status, $order_id);
                
                if (!$stmt_payment->execute()) {
                    throw new Exception("Lỗi khi cập nhật trạng thái thanh toán (Paid).");
                }
                $stmt_payment->close();
            } 
            // 2b. Logic cho trạng thái HỦY/HOÀN (ID = 5, 6)
            elseif ($new_status_id === 5 || $new_status_id === 6) { 
                $payment_status = ($new_status_id === 5) ? 'Cancelled' : 'Refunded';
                
                // Cập nhật trạng thái thanh toán sang Cancelled/Refunded (Không cập nhật ngày thanh toán)
                $sql_payment = "UPDATE thanhtoan SET trangthai = ? WHERE order_id = ?";
                $stmt_payment = $this->conn->prepare($sql_payment);
                $stmt_payment->bind_param("si", $payment_status, $order_id);
                
                if (!$stmt_payment->execute()) {
                    throw new Exception("Lỗi khi cập nhật trạng thái thanh toán (Hủy/Hoàn).");
                }
                $stmt_payment->close();
            }
            // Các trạng thái khác (1, 2, 3) sẽ giữ nguyên trạng thái thanh toán ban đầu (Pending/Unpaid)

            // 3. CẬP NHẬT TRẠNG THÁI VẬN CHUYỂN (Bảng vanchuyen)
            // Ánh xạ trạng thái đơn hàng (ID) sang tên trạng thái Vận chuyển (string)
            $shipping_status_map = [
                1 => 'choxacnhan', // Trạng thái 1: Chờ xác nhận
                2 => 'dangchuanbi', // Trạng thái 2: Đã xác nhận
                3 => 'danggiaohang', // Trạng thái 3: Đang giao hàng
                4 => 'dagiaohang', // Trạng thái 4: Đã giao hàng
                5 => 'dahoanhuy', // Trạng thái 5: Đã hủy
                6 => 'dahoanve' // Trạng thái 6: Hoàn hàng
            ];
            $shipping_status_new = $shipping_status_map[$new_status_id] ?? 'choxacnhan';

            $sql_shipping = "UPDATE vanchuyen SET trangthai = ?, ngaysua = NOW() WHERE order_id = ?";
            $stmt_shipping = $this->conn->prepare($sql_shipping);
            $stmt_shipping->bind_param("si", $shipping_status_new, $order_id);
            
            if (!$stmt_shipping->execute()) {
                throw new Exception("Lỗi khi cập nhật trạng thái vận chuyển.");
            }
            $stmt_shipping->close();

            // 4. CHÈN LỊCH SỬ ĐƠN HÀNG (Bảng lichsudonhang - Giả định có bảng này)
            $sql_history = "INSERT INTO lichsudonhang (order_id, ngaycapnhat, trangthai) VALUES (?, NOW(), ?)";
            $stmt_history = $this->conn->prepare($sql_history);
            $stmt_history->bind_param("ii", $order_id, $new_status_id);
            if (!$stmt_history->execute()) {
                throw new Exception("Lỗi khi thêm lịch sử đơn hàng.");
            }
            $stmt_history->close();


            // Hoàn tất giao dịch
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // Rollback nếu có lỗi
            $this->conn->rollback();
            error_log("Order Status Update Failed (Order ID: $order_id): " . $e->getMessage());
            throw $e;
        }
    }
}