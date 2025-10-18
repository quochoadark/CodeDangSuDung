<?php
// Tên tệp: Repository/CheckoutRepository.php
// Chức năng: Thao tác trực tiếp với Database (CRUD và Transaction)

class CheckoutRepository
{
    private $conn;

    public function __construct($db_conn)
    {
        $this->conn = $db_conn;
    }

    /**
     * Lấy thông tin cơ bản của User.
     */
    public function getUserProfileById($user_id)
    {
        $sql = "SELECT hoten, dienthoai, diachi FROM nguoidung WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed for getUserProfile: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        return $data;
    }

    /**
     * 🔥 THÊM HÀM: Lấy Email và Họ tên của User.
     */
    public function getUserEmailById($user_id)
    {
        $sql = "SELECT email, hoten FROM nguoidung WHERE user_id = ?";
        
        if (!$this->conn) {
            error_log("Lỗi: Kết nối DB (conn) không hợp lệ trong CheckoutRepository.");
            return null; 
        }

        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Lỗi Prepare SQL trong getUserEmailById: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        
        return $data; 
    }

    /**
     * Cập nhật số lượng và lượt sử dụng của Voucher.
     * @param int $voucher_id ID của voucher.
     */
    private function updateVoucherUsage($voucher_id)
    {
        if ($voucher_id > 0) {
            $sql = "
                UPDATE makhuyenmai 
                SET 
                    soluong = CASE WHEN soluong IS NULL THEN NULL ELSE soluong - 1 END, 
                    luotsudung = luotsudung + 1 
                WHERE voucher_id = ? AND (soluong IS NULL OR soluong > 0)
            ";

            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Lỗi Prepare SQL Voucher Usage: " . $this->conn->error);
            }

            $stmt->bind_param('i', $voucher_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    /**
     * Thực hiện toàn bộ giao dịch đặt hàng.
     * @return int order_id vừa được tạo.
     * @throws Exception nếu có lỗi trong bất kỳ bước nào.
     */
    public function executePlaceOrderTransaction(
        $user_id, $grand_total, $voucher_id, $discount, $cart_items, 
        $recipient_name, $phone_number, $address, $payment_method, $note
    ) {
        $this->conn->begin_transaction();

        try {
            $trang_thai_id = 1; // ID trạng thái khởi tạo "Chờ xác nhận"
            $order_id = 0;

            // 1. CHÈN BẢNG 'donhang'
            if ($voucher_id === null) {
                $sql_order = "INSERT INTO donhang (user_id, tongtien, ngaytao, giam_gia, trangthai) VALUES (?, ?, NOW(), ?, ?)";
                $stmt_order = $this->conn->prepare($sql_order);
                $stmt_order->bind_param("iddi", $user_id, $grand_total, $discount, $trang_thai_id);
            } else {
                $sql_order = "INSERT INTO donhang (user_id, tongtien, ngaytao, voucher_id, giam_gia, trangthai) VALUES (?, ?, NOW(), ?, ?, ?)";
                $stmt_order = $this->conn->prepare($sql_order);
                $stmt_order->bind_param("ididi", $user_id, $grand_total, $voucher_id, $discount, $trang_thai_id);
            }

            if (!$stmt_order || !$stmt_order->execute()) {
                throw new Exception("Lỗi khi tạo đơn hàng: " . ($stmt_order ? $stmt_order->error : $this->conn->error));
            }

            $order_id = $this->conn->insert_id;
            $stmt_order->close();

            // 2. CHÈN 'chitietdonhang' & CẬP NHẬT TỒN KHO
            $sql_detail = "INSERT INTO chitietdonhang (order_id, product_id, soluong, gia, gia_goc, giam_gia_sp) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt_detail = $this->conn->prepare($sql_detail);
            
            $sql_stock = "UPDATE sanpham SET tonkho = tonkho - ? WHERE product_id = ? AND tonkho >= ?";
            $stmt_stock = $this->conn->prepare($sql_stock);

            if (!$stmt_detail || !$stmt_stock) {
                throw new Exception("Lỗi Prepare SQL Detail/Stock: " . $this->conn->error);
            }

            foreach ($cart_items as $item) {
                $product_id = $item['id'];
                $quantity = $item['quantity'];
                $price_final = $item['price'];
                $original_price = $item['original_price'] ?? $price_final;
                $unit_discount_amount = $original_price - $price_final;

                // Lưu ý: "issssd" cho order_id, product_id, quantity, price_final, original_price, unit_discount_amount (Tùy thuộc vào kiểu dữ liệu trong DB của bạn, tôi giữ kiểu ban đầu)
                $stmt_detail->bind_param("iiiddd", $order_id, $product_id, $quantity, $price_final, $original_price, $unit_discount_amount);
                
                if (!$stmt_detail->execute()) {
                    throw new Exception("Lỗi khi thêm chi tiết đơn hàng: " . $stmt_detail->error);
                }

                $stmt_stock->bind_param("iii", $quantity, $product_id, $quantity);
                if (!$stmt_stock->execute() || $this->conn->affected_rows === 0) {
                    throw new Exception("Sản phẩm ID: $product_id không đủ số lượng trong kho.");
                }
            }
            $stmt_detail->close();
            $stmt_stock->close();

            // 3. CHÈN 'vanchuyen'
            $sql_shipping = "INSERT INTO vanchuyen (order_id, receiver_name, receiver_phone, receiver_address, notes, phuongthuctt, trangthai, ngaysua) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $shipping_status = 'choxacnhan'; // Trạng thái vận chuyển khởi tạo
            $stmt_shipping = $this->conn->prepare($sql_shipping);
            $stmt_shipping->bind_param("issssss", $order_id, $recipient_name, $phone_number, $address, $note, $payment_method, $shipping_status);
            if (!$stmt_shipping || !$stmt_shipping->execute()) {
                throw new Exception("Lỗi khi thêm thông tin vận chuyển: " . ($stmt_shipping ? $stmt_shipping->error : $this->conn->error));
            }
            $stmt_shipping->close();

            // 4. CHÈN 'thanhtoan'
            $payment_status = ($payment_method === 'COD') ? 'Pending' : 'Unpaid';
            $sql_payment = "INSERT INTO thanhtoan (order_id, phuongthuc, trangthai, ngaythanhtoan) VALUES (?, ?, ?, NULL)";
            
            $stmt_payment = $this->conn->prepare($sql_payment);
            $stmt_payment->bind_param("iss", $order_id, $payment_method, $payment_status);
            if (!$stmt_payment || !$stmt_payment->execute()) {
                throw new Exception("Lỗi khi thêm thông tin thanh toán: " . ($stmt_payment ? $stmt_payment->error : $this->conn->error));
            }
            $stmt_payment->close();

            // 5. CHÈN 'lichsudonhang'
            $sql_history = "INSERT INTO lichsudonhang (order_id, ngaycapnhat, trangthai) VALUES (?, NOW(), ?)";
            $stmt_history = $this->conn->prepare($sql_history);
            $stmt_history->bind_param("ii", $order_id, $trang_thai_id);
            if (!$stmt_history || !$stmt_history->execute()) {
                throw new Exception("Lỗi khi thêm lịch sử đơn hàng: " . ($stmt_history ? $stmt_history->error : $this->conn->error));
            }
            $stmt_history->close();

            // 6. CẬP NHẬT VOUCHER
            if ($voucher_id) {
                $this->updateVoucherUsage($voucher_id);
            }

            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e; // Re-throw exception để Service bắt
        }
    }
    
    /**
     * Lấy chi tiết đơn hàng cho trang xác nhận.
     */
    public function getOrderDetailsForConfirmation($order_id, $user_id)
    {
        $order_data = null;
        $sql_order = "
            SELECT 
                dh.order_id, dh.user_id, dh.tongtien AS tong_tien, dh.ngaytao AS ngay_dat, 
                dh.giam_gia AS giam_gia_voucher, vc.receiver_name AS ten_nguoi_nhan, 
                vc.receiver_phone AS sdt_nguoi_nhan, vc.receiver_address AS dia_chi_nhan, 
                vc.phuongthuctt AS phuong_thuc_tt
            FROM donhang dh JOIN vanchuyen vc ON dh.order_id = vc.order_id
            WHERE dh.order_id = ? AND dh.user_id = ?
        ";

        $stmt_order = $this->conn->prepare($sql_order);
        if (!$stmt_order) {
            error_log("Prepare failed for getOrderDetails (Order): " . $this->conn->error);
            return null;
        }

        $stmt_order->bind_param("ii", $order_id, $user_id);
        $stmt_order->execute();
        $result_order = $stmt_order->get_result();

        if ($result_order->num_rows > 0) {
            $order_data = $result_order->fetch_assoc();
            $order_data['shipping_fee'] = 50000;
            $order_data['items'] = [];
        } else {
            return null;
        }
        $stmt_order->close();

        $sql_items = "
            SELECT 
                ctdh.soluong AS so_luong, ctdh.gia AS don_gia_da_giam, 
                ctdh.gia_goc AS don_gia_goc, ctdh.giam_gia_sp AS giam_gia_sp, 
                p.tensanpham AS ten_san_pham, (ctdh.soluong * ctdh.gia) AS thanh_tien
            FROM chitietdonhang ctdh JOIN sanpham p ON ctdh.product_id = p.product_id
            WHERE ctdh.order_id = ?
        ";

        $stmt_items = $this->conn->prepare($sql_items);
        if (!$stmt_items) {
            error_log("Prepare failed for getOrderDetails (Items): " . $this->conn->error);
            return $order_data;
        }

        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();

        while ($row = $result_items->fetch_assoc()) {
            $order_data['items'][] = $row;
        }

        $stmt_items->close();
        return $order_data;
    }
}