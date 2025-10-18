<?php
class CartRepository {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function syncCartToDatabase($user_id, $session_cart) {
        if ($user_id <= 0 || !$this->conn) {
            error_log("Cart sync failed: Invalid user_id or connection.");
            return;
        }

        // 1. Xóa giỏ hàng cũ
        $sql_delete = "DELETE FROM giohang WHERE user_id = ?";
        if ($stmt_del = $this->conn->prepare($sql_delete)) {
            $stmt_del->bind_param('i', $user_id);
            if (!$stmt_del->execute()) { // 🔥 Kiểm tra lỗi execute
                 error_log("SQL Error on DELETE giohang: " . $stmt_del->error);
            }
            $stmt_del->close();
        } else {
            // 🔥 Log lỗi prepare
            error_log("Prepare failed on DELETE giohang: " . $this->conn->error);
            // Có thể throw Exception ở đây nếu muốn Service bắt lỗi
        }

        // 2. Chèn tất cả sản phẩm từ Session Cart vào DB
        if (!empty($session_cart)) {
            $sql_insert = "INSERT INTO giohang (user_id, product_id, soluong) VALUES (?, ?, ?)";
            if ($stmt_ins = $this->conn->prepare($sql_insert)) {
                foreach ($session_cart as $product_id => $quantity) {
                    $stmt_ins->bind_param('iii', $user_id, $product_id, $quantity);
                    if (!$stmt_ins->execute()) { // 🔥 Kiểm tra lỗi execute
                         error_log("SQL Error on INSERT giohang: " . $stmt_ins->error);
                    }
                }
                $stmt_ins->close();
            } else {
                 // 🔥 Log lỗi prepare
                error_log("Prepare failed on INSERT giohang: " . $this->conn->error);
            }
        }
    }

    public function getCartFromDatabase($user_id) {
        $db_cart = [];
        $sql_db_cart = "SELECT product_id, soluong FROM giohang WHERE user_id = ?";
        
        if ($stmt_db_cart = $this->conn->prepare($sql_db_cart)) {
            $stmt_db_cart->bind_param('i', $user_id);
            $stmt_db_cart->execute();
            $result_db_cart = $stmt_db_cart->get_result();
            
            while ($row = $result_db_cart->fetch_assoc()) {
                $db_cart[$row['product_id']] = $row['soluong'];
            }
            $stmt_db_cart->close();
        }
        return $db_cart;
    }

    public function getProductStock($product_id) {
        $sql = "SELECT tonkho FROM sanpham WHERE product_id = ?";
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param('i', $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row['tonkho'] ?? 0;
        }
        return 0; 
    }


    public function getProductDetails(array $product_ids) {
        if (empty($product_ids) || !$this->conn) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $sql = "SELECT product_id, tensanpham, gia, img, tonkho FROM sanpham WHERE product_id IN ($placeholders)";

        if ($stmt = $this->conn->prepare($sql)) {
            $types = str_repeat('i', count($product_ids));
            
            // Sử dụng toán tử Splat (...) cho bind_param (PHP 5.6+)
            $stmt->bind_param($types, ...$product_ids);
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $products = [];
            while ($row = $result->fetch_assoc()) {
                $products[$row['product_id']] = $row;
            }
            $stmt->close();
            return $products;
        }
        return [];
    }


    public function getProductPromotion($product_id) {
        $current_date = date('Y-m-d H:i:s');
        
        $sql = "SELECT giam, mota, product_id
                FROM khuyenmai_sanpham 
                WHERE (product_id = ? OR product_id IS NULL)
                AND ngaybatdau <= ? AND ngayketthuc >= ?
                ORDER BY product_id DESC, giam DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("iss", $product_id, $current_date, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();
        $promo = $result->fetch_assoc();
        $stmt->close();

        return $promo;
    }
    
    public function getVoucherByCode($voucher_code) {
        $sql_voucher = "SELECT voucher_id, giam, ngayhethan, soluong FROM makhuyenmai WHERE makhuyenmai = ? LIMIT 1"; 
        
        if ($this->conn && $stmt_v = $this->conn->prepare($sql_voucher)) { 
            $stmt_v->bind_param('s', $voucher_code);
            $stmt_v->execute();
            $result_v = $stmt_v->get_result();
            $voucher_data = $result_v->fetch_assoc();
            $stmt_v->close();
            return $voucher_data;
        }
        return null;
    }
}