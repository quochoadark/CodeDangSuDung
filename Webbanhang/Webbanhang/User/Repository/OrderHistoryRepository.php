<?php
// File: Repository/OrderRepository.php

class OrderHistoryRepository {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // 1. Lấy danh sách đơn hàng cơ bản cho trang lịch sử
    public function getOrdersByUserId(int $userId): array {
        $sql = "
            SELECT 
                DH.order_id, DH.tongtien, DH.ngaytao, 
                S.receiver_name,
                T.ten_trangthai,
                T.trangthai_id AS status_id
            FROM 
                donhang DH
            LEFT JOIN 
                vanchuyen S ON DH.order_id = S.order_id
            LEFT JOIN 
                trangthaidonhang T ON DH.trangthai = T.trangthai_id
            WHERE 
                DH.user_id = ?
            ORDER BY 
                DH.ngaytao DESC
        ";
            
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $orders = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $orders;
        }
        error_log("SQL Error in getOrdersByUserId: " . $this->conn->error);
        return [];
    }

    // 2. Lấy thông tin cơ bản của đơn hàng VÀ kiểm tra quyền (để dùng trong Service)
    public function getBasicOrderInfoAndAuth(int $orderId, int $userId): ?array {
        $sql = "SELECT 
            DH.order_id, DH.tongtien, DH.ngaytao, DH.giam_gia AS giam_gia_voucher, 
            DH.trangthai, T.ten_trangthai
        FROM 
            donhang DH
        LEFT JOIN 
            trangthaidonhang T ON DH.trangthai = T.trangthai_id
        WHERE 
            DH.order_id = ? AND DH.user_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("SQL Prepare Error (basic info): " . $this->conn->error);
             return null;
        }
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        $order_info = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $order_info;
    }

    // 3. Lấy thông tin Vận chuyển
    public function getShippingInfo(int $orderId): ?array {
        $sql = "SELECT 
            receiver_name, receiver_phone, receiver_address, notes, 
            phuongthuctt, 
            0 AS phiship
        FROM 
            vanchuyen 
        WHERE 
            order_id = ?";

        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $shipping_info = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $shipping_info;
        } else {
            error_log("SQL Prepare Error (shipping info): " . $this->conn->error);
            return null;
        }
    }

    // 4. Lấy chi tiết sản phẩm
    public function getOrderDetailsItems(int $orderId): array {
        $sql = "SELECT 
            CT.product_id, CT.soluong, CT.gia AS gia_mua, 
            (CT.soluong * CT.gia) AS item_total, 
            SP.tensanpham, SP.gia AS gia_goc_sp, SP.img 
        FROM 
            chitietdonhang CT
        LEFT JOIN 
            sanpham SP ON CT.product_id = SP.product_id
        WHERE 
            CT.order_id = ?";
            
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $result = $stmt->get_result();
            $details = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $details;
        } else {
             error_log("SQL Prepare Error (details): " . $this->conn->error);
             return [];
        }
    }

    // 5. Lấy lịch sử trạng thái
    public function getStatusHistory(int $orderId): array {
        $sql = "SELECT
            T.ten_trangthai,
            LS.ngaycapnhat,
            LS.trangthai as trangthai_id
        FROM
            lichsudonhang LS
        LEFT JOIN
            trangthaidonhang T ON LS.trangthai = T.trangthai_id
        WHERE
            LS.order_id = ?
        ORDER BY
            LS.ngaycapnhat ASC";
            
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("i", $orderId);
            $stmt->execute();
            $history = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $history;
        } else {
            error_log("SQL Prepare Error (status history): " . $this->conn->error);
            return [];
        }
    }

    // 6. Lấy tất cả các trạng thái của luồng chính
    public function getAllOrderStatuses(): array {
        $sql = "SELECT trangthai_id, ten_trangthai FROM trangthaidonhang WHERE trangthai_id IN (1, 2, 3, 4) ORDER BY trangthai_id ASC";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    // 7. Cập nhật trạng thái và ghi lịch sử (Transaction)
    public function updateStatusAndLogHistory(int $orderId, int $userId, int $newStatusId, int $currentStatus): string {
        $CANCELLABLE_STATUSES = [1, 2]; // Cho phép hủy
        
        // Kiểm tra xem trạng thái hiện tại có nằm trong danh sách được phép hủy không
        if (!in_array($currentStatus, $CANCELLABLE_STATUSES)) {
            return "invalid_status";
        }

        // Bắt đầu Transaction
        $this->conn->begin_transaction();
        $success = true;

        // Cập nhật trạng thái trong bảng donhang
        $sql_update = "UPDATE donhang SET trangthai = ? WHERE order_id = ? AND user_id = ?";
        $stmt_update = $this->conn->prepare($sql_update);
        if (!$stmt_update) {
            $success = false;
        } else {
            $stmt_update->bind_param("iii", $newStatusId, $orderId, $userId);
            if (!$stmt_update->execute()) {
                $success = false;
            }
            $stmt_update->close();
        }

        // Ghi vào lịch sử đơn hàng
        if ($success) {
            $sql_history = "INSERT INTO lichsudonhang (order_id, trangthai, ngaycapnhat) VALUES (?, ?, NOW())";
            $stmt_history = $this->conn->prepare($sql_history);
            if (!$stmt_history) {
                 $success = false;
            } else {
                $stmt_history->bind_param("ii", $orderId, $newStatusId);
                if (!$stmt_history->execute()) {
                    $success = false;
                }
                $stmt_history->close();
            }
        }

        if ($success) {
            $this->conn->commit();
            return "success";
        } else {
            $this->conn->rollback();
            error_log("Transaction failed to update/log history for order ID: " . $orderId . ". Error: " . $this->conn->error);
            return "error";
        }
    }
}