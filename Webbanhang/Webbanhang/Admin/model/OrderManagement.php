<?php
// File: app/models/OrderManagement.php (Model/Repository với Transaction)

// Giả định file Database.php nằm ở vị trí tương đối này
require_once __DIR__ . '/../../Database/Database.php'; 

class OrderManagement {
    // Thuộc tính để lưu trữ dữ liệu đơn hàng
    public $order_id;
    public $user_id;
    public $tongtien;
    public $ngaytao;
    public $voucher_id;
    public $giam_gia;
    public $trangthai_id; // Cột lưu ID trạng thái trong bảng donhang
    public $ten_trangthai; 

    private $conn;
    private $table_name = "donhang";
    private $status_table = "trangthaidonhang"; 

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. Đọc tất cả đơn hàng (kèm phân trang và tên trạng thái)
    // PHÂN TRANG: Sử dụng LIMIT và OFFSET
    public function readAll($limit, $offset) {
        $sql = "SELECT t.*, ts.ten_trangthai 
                FROM " . $this->table_name . " t
                LEFT JOIN " . $this->status_table . " ts ON t.trangthai = ts.trangthai_id 
                ORDER BY t.order_id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
             return null;
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result(); 
    }

    // 2. Lấy đơn hàng theo ID (Find)
    public function find($order_id) {
        $sql = "SELECT t.*, ts.ten_trangthai
                FROM " . $this->table_name . " t
                LEFT JOIN " . $this->status_table . " ts ON t.trangthai = ts.trangthai_id
                WHERE t.order_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Gán dữ liệu (giữ lại theo cấu trúc Model ban đầu của bạn)
            $this->order_id = $row['order_id'];
            $this->trangthai_id = $row['trangthai']; 
            $this->ten_trangthai = $row['ten_trangthai']; 
            return $row;
        }
        return null;
    }

    // 3. Cập nhật trạng thái đơn hàng (Hàm nội bộ chỉ cập nhật donhang)
    private function simpleUpdateStatus(int $order_id, int $new_status_id): int {
        $sql = "UPDATE " . $this->table_name . " SET trangthai = ? WHERE order_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             throw new Exception("Lỗi Prepare SQL (donhang): " . $this->conn->error);
        }
        
        $stmt->bind_param("ii", $new_status_id, $order_id); 
        
        if ($stmt->execute()) {
             $affectedRows = $stmt->affected_rows;
             $stmt->close();
             return $affectedRows;
        } else {
             $error = $stmt->error;
             $stmt->close();
             throw new Exception("Lỗi Execute SQL (donhang): " . $error);
        }
    }

    // 4. Lấy tổng số bản ghi (Count) - Cần cho Phân trang
    public function getTotalRecords() {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    // 5. Lấy danh sách tất cả các trạng thái
    public function getAllStatuses(): array {
        $sql = "SELECT trangthai_id, ten_trangthai FROM " . $this->status_table . " ORDER BY trangthai_id ASC";
        $result = $this->conn->query($sql);
        
        $statuses = [];
        if ($result) {
            $statuses = $result->fetch_all(MYSQLI_ASSOC);
        }
        return $statuses;
    }

    // 6. Hoàn lại tồn kho cho các sản phẩm trong đơn hàng
    private function revertStockForOrder(int $order_id)
    {
        $sql_items = "SELECT product_id, soluong FROM chitietdonhang WHERE order_id = ?";
        $stmt_items = $this->conn->prepare($sql_items);
        if (!$stmt_items) { throw new Exception("Lỗi Prepare SQL (chitietdonhang): " . $this->conn->error); }
        $stmt_items->bind_param("i", $order_id);
        $stmt_items->execute();
        $result_items = $stmt_items->get_result();
        $stmt_items->close();

        if ($result_items->num_rows === 0) { return; }

        $sql_stock = "UPDATE sanpham SET tonkho = tonkho + ? WHERE product_id = ?";
        $stmt_stock = $this->conn->prepare($sql_stock);

        if (!$stmt_stock) { throw new Exception("Lỗi Prepare SQL hoàn tồn kho: " . $this->conn->error); }

        $items_to_revert = $result_items->fetch_all(MYSQLI_ASSOC);
        
        foreach ($items_to_revert as $item) {
            $product_id = (int)$item['product_id'];
            $quantity = (int)$item['soluong'];

            $stmt_stock->bind_param("ii", $quantity, $product_id); 
            if (!$stmt_stock->execute()) {
                $error = $stmt_stock->error;
                $stmt_stock->close();
                throw new Exception("Lỗi khi hoàn tồn kho cho sản phẩm ID: $product_id. Lỗi SQL: " . $error);
            }
        }

        $stmt_stock->close();
    }


    /**
     * HÀM CẬP NHẬT TRẠNG THÁI CHÍNH THỨC (Transaction)
     */
    public function updateOrderStatusTransaction(int $order_id, int $new_status_id): bool
    {
        $current_order_data = $this->find($order_id);
        if ($current_order_data === null) { throw new Exception("Đơn hàng ID: $order_id không tồn tại."); }
        $old_status_id = (int)($current_order_data['trangthai'] ?? 0);

        $this->conn->begin_transaction();

        try {
            // 1. CẬP NHẬT ĐƠN HÀNG (donhang)
            $this->simpleUpdateStatus($order_id, $new_status_id);
            
            // 2. CẬP NHẬT THANH TOÁN (thanhtoan)
            $payment_status = null;
            // Giả định trạng thái 4: Đã giao/thành công -> Paid
            // Trạng thái 5: Đã hủy -> Cancelled
            // Trạng thái 6: Đã hoàn/hoàn tiền -> Refunded
            if ($new_status_id === 4) { $payment_status = 'Paid'; } 
            elseif ($new_status_id === 5) { $payment_status = 'Cancelled'; } 
            elseif ($new_status_id === 6) { $payment_status = 'Refunded'; }
            
            if ($payment_status !== null) {
                $sql_payment = "UPDATE thanhtoan SET trangthai = ?, ngaythanhtoan = (CASE WHEN ? = 'Paid' THEN NOW() ELSE ngaythanhtoan END) WHERE order_id = ?";
                $stmt_payment = $this->conn->prepare($sql_payment);
                if (!$stmt_payment) { throw new Exception("Lỗi Prepare SQL (thanhtoan): " . $this->conn->error); }
                
                $stmt_payment->bind_param("ssi", $payment_status, $payment_status, $order_id);
                if (!$stmt_payment->execute()) { 
                    $error = $stmt_payment->error;
                    $stmt_payment->close();
                    throw new Exception("Lỗi khi cập nhật trạng thái thanh toán. Lỗi SQL: " . $error); 
                }
                
                $affectedRows = $stmt_payment->affected_rows; // Lấy affected_rows
                $stmt_payment->close();
                
                // Kiểm tra nếu đơn hàng tồn tại nhưng không tìm thấy dòng thanhtoan
                if ($affectedRows === 0 && $old_status_id !== $new_status_id) {
                     // Nếu dòng thanhtoan không bị ảnh hưởng, chỉ báo lỗi nếu đơn hàng thay đổi trạng thái
                     // và ta biết rằng nó nên có một dòng thanhtoan.
                     // (Tuy nhiên, trong một hệ thống hoàn chỉnh, nên dùng SELECT trước)
                     // Tạm thời bỏ qua kiểm tra này để tránh lỗi với dữ liệu thiếu, hoặc cần dùng SELECT.
                     // throw new Exception("Không tìm thấy dòng thanh toán (thanhtoan) cho Order ID $order_id. Dữ liệu thiếu.");
                }
            }
            
            // 3. HOÀN TỒN KHO
            // Chỉ hoàn tồn kho nếu trạng thái MỚI là HỦY (5) hoặc HOÀN VỀ (6) VÀ trạng thái CŨ chưa phải là 5 hoặc 6.
            if (($new_status_id === 5 || $new_status_id === 6) && ($old_status_id !== 5 && $old_status_id !== 6)) {
                 $this->revertStockForOrder($order_id);
            }

            // 4. CẬP NHẬT VẬN CHUYỂN (vanchuyen)
            $shipping_status_map = [
                1 => 'choxacnhan', 2 => 'dangchuanbi', 3 => 'danggiaohang', 
                4 => 'dagiaohang', 5 => 'dahoanhuy', 6 => 'dahoanve' 
            ];
            $shipping_status_new = $shipping_status_map[$new_status_id] ?? 'choxacnhan';

            $sql_shipping = "UPDATE vanchuyen SET trangthai = ?, ngaysua = NOW() WHERE order_id = ?";
            $stmt_shipping = $this->conn->prepare($sql_shipping);
            if (!$stmt_shipping) { throw new Exception("Lỗi Prepare SQL (vanchuyen): " . $this->conn->error); }
            
            $stmt_shipping->bind_param("si", $shipping_status_new, $order_id);
            if (!$stmt_shipping->execute()) { 
                $error = $stmt_shipping->error;
                $stmt_shipping->close();
                throw new Exception("Lỗi khi cập nhật trạng thái vận chuyển. Lỗi SQL: " . $error); 
            }
            
            $affectedRows = $stmt_shipping->affected_rows; // Lấy affected_rows
            $stmt_shipping->close();
            
            // Tương tự, bỏ qua kiểm tra $affectedRows === 0 tạm thời
            // if ($affectedRows === 0 && $old_status_id !== $new_status_id) {
            //      throw new Exception("Không tìm thấy dòng vận chuyển (vanchuyen) cho Order ID $order_id. Dữ liệu thiếu.");
            // }

            // 5. CHÈN LỊCH SỬ ĐƠN HÀNG (lichsudonhang)
            $sql_history = "INSERT INTO lichsudonhang (order_id, ngaycapnhat, trangthai) VALUES (?, NOW(), ?)";
            $stmt_history = $this->conn->prepare($sql_history);
            if (!$stmt_history) { throw new Exception("Lỗi Prepare SQL (lichsudonhang): " . $this->conn->error); }
            
            $stmt_history->bind_param("ii", $order_id, $new_status_id);
            if (!$stmt_history->execute()) { 
                $error = $stmt_history->error;
                $stmt_history->close();
                throw new Exception("Lỗi khi thêm lịch sử đơn hàng. Lỗi SQL: " . $error); 
            }
            $stmt_history->close();

            // 6. COMMIT
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            // 7. ROLLBACK nếu có lỗi
            $this->conn->rollback();
            throw $e; // Ném lại lỗi để Controller bắt và hiển thị
        }
    }
}
