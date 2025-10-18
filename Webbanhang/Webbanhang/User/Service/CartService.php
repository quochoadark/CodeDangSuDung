<?php
// Yêu cầu: Giả định require_once '../Repository/CartRepository.php'; đã được thực hiện
require_once '../Repository/CartRepository.php'; 

class CartService {
    private $cartRepository;
    private $shipping_fee = 50000;

    public function __construct(CartRepository $repository) {
        $this->cartRepository = $repository;
    }
    
    // -----------------------------------------------------------------
    // --- LOGIC GIỎ HÀNG CƠ BẢN (SESSION) ---
    // -----------------------------------------------------------------

    /**
     * Thêm sản phẩm vào giỏ hàng, kiểm tra tồn kho.
     * @param int $user_id ID người dùng (để đồng bộ DB).
     * @param int $product_id ID sản phẩm.
     * @param int $quantity Số lượng muốn thêm.
     * @return array Kết quả thao tác.
     */
    public function addItem($user_id, $product_id, $quantity = 1) {
        if ($product_id <= 0 || $quantity <= 0) {
            return ['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ.'];
        }
        
        // 🔥 KIỂM TRA TỒN KHO TRƯỚC KHI THAO TÁC
        $tonkho = $this->cartRepository->getProductStock($product_id);
        if ($tonkho <= 0) {
            // Trường hợp sản phẩm hết hàng
            return ['success' => false, 'message' => 'Sản phẩm này đã hết hàng. Vui lòng chọn sản phẩm khác.'];
        }
        
        $current_quantity = $_SESSION['cart'][$product_id] ?? 0;
        $new_quantity = $current_quantity + $quantity;
        
        // KIỂM TRA SỐ LƯỢNG MỚI CÓ VƯỢT QUÁ TỒN KHO KHÔNG
        if ($new_quantity > $tonkho) {
            $new_quantity = $tonkho;
            if ($new_quantity == $current_quantity) {
                // Trường hợp đã đạt đến giới hạn tồn kho
                return ['success' => false, 'message' => "Không thể thêm sản phẩm, chỉ còn {$tonkho} sản phẩm trong kho."];
            }
        }
        
        $_SESSION['cart'][$product_id] = $new_quantity;
        $this->resetVoucher(); 
        $this->syncDatabase($user_id);
        
        return ['success' => true, 'message' => 'Thêm vào giỏ hàng thành công.'];
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng, kiểm tra tồn kho.
     * @param int $user_id ID người dùng (để đồng bộ DB).
     * @param int $product_id ID sản phẩm.
     * @param int $new_quantity Số lượng mới.
     * @return array Kết quả thao tác.
     */
    public function updateCartItem($user_id, $product_id, $new_quantity) { 
        if ($product_id <= 0) {
            return ['success' => false, 'message' => 'ID sản phẩm không hợp lệ'];
        }
        
        $tonkho = $this->cartRepository->getProductStock($product_id); 
        
        // 🔥 XỬ LÝ KHI SẢN PHẨM HẾT HÀNG (TONKHO <= 0)
        if ($tonkho <= 0) {
              unset($_SESSION['cart'][$product_id]);
              $this->syncDatabase($user_id);
              return ['success' => false, 'message' => 'Sản phẩm đã hết hàng và bị loại khỏi giỏ.'];
        }

        // Giới hạn theo tồn kho
        if ($new_quantity > $tonkho) {
            $new_quantity = $tonkho;
            $_SESSION['cart'][$product_id] = $new_quantity;
            $this->resetVoucher();
            $this->syncDatabase($user_id);
            return [
                'success' => true, 
                'quantity' => $new_quantity, 
                'action' => 'updated',
                'message' => "Số lượng được điều chỉnh xuống mức tồn kho tối đa là {$tonkho}.",
                'cart_count' => array_sum($_SESSION['cart'] ?? [])
            ];
        }

        if ($new_quantity > 0) {
            $_SESSION['cart'][$product_id] = $new_quantity;
            $action = 'updated';
            $message = 'Cập nhật giỏ hàng thành công.';
        } else {
            unset($_SESSION['cart'][$product_id]);
            $action = 'removed';
            $message = 'Sản phẩm đã được loại khỏi giỏ hàng.';
        }
        
        $this->resetVoucher();
        $this->syncDatabase($user_id);

        return [
            'success' => true, 
            'quantity' => $new_quantity, 
            'action' => $action,
            'message' => $message,
            'cart_count' => array_sum($_SESSION['cart'] ?? [])
        ];
    }

    // HÀM REMOVE ITEM - Giữ nguyên
    public function removeItem($user_id, $product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $this->resetVoucher();
            $this->syncDatabase($user_id); // Lỗi tiềm ẩn nhất: nằm trong syncDatabase -> Repository
            return ['success' => true, 'action' => 'removed', 'cart_count' => array_sum($_SESSION['cart'] ?? [])];
        }
        return ['success' => false, 'message' => 'Sản phẩm không có trong giỏ hàng'];
    }
    
    // -----------------------------------------------------------------
    // --- ĐỒNG BỘ DỮ LIỆU ---
    // -----------------------------------------------------------------
    
    public function syncDatabase($user_id) {
        if ($user_id > 0) {
            // 🔥 Nếu Repository ném ra Exception, nó sẽ được bắt ở Controller
            $this->cartRepository->syncCartToDatabase($user_id, $_SESSION['cart'] ?? []);
        }
    }
    
    public function syncSessionFromDatabase($user_id) {
        if ($user_id > 0 && empty($_SESSION['cart'])) {
            $_SESSION['cart'] = $this->cartRepository->getCartFromDatabase($user_id);
        }
    }

    // -----------------------------------------------------------------
    // --- TÍNH TOÁN CHI TIẾT VÀ TỔNG CỘNG ---
    // -----------------------------------------------------------------

    public function getCartSummary() {
        $cart_items = [];
        $sub_total = 0; 
        $current_cart = $_SESSION['cart'] ?? [];

        if (empty($current_cart)) {
            return [
                'items' => [], 
                'sub_total' => 0, 
                'shipping_fee' => $this->shipping_fee, 
                'discount' => 0, 
                'grand_total' => $this->shipping_fee
            ];
        }

        $product_ids = array_keys($current_cart);
        $products_data = $this->cartRepository->getProductDetails($product_ids);

        foreach ($current_cart as $id => $quantity) {
            if (!isset($products_data[$id])) {
                 // Nếu sản phẩm không còn tồn tại trong DB, loại bỏ khỏi giỏ
                unset($_SESSION['cart'][$id]);
                continue; 
            }
            
            $product_data = $products_data[$id];
             // Kiểm tra tồn kho lần nữa khi tính tổng (đề phòng sản phẩm hết hàng đột ngột)
            if ($product_data['tonkho'] <= 0) {
                 unset($_SESSION['cart'][$id]);
                 continue; 
            }

            $original_price = floatval($product_data['gia']);
            $price = $original_price; 
            $item_discount = 0; 
            $promo_description = null;

            // 1. ÁP DỤNG KHUYẾN MÃI SẢN PHẨM (Product Promotion)
            $promotion = $this->cartRepository->getProductPromotion($id);
            
            if ($promotion) {
                $giam_value = floatval($promotion['giam']);
                $promo_description = $promotion['mota'];

                if ($giam_value < 1 && $giam_value > 0) {
                    $item_discount = $original_price * $giam_value;
                } else {
                    $item_discount = $giam_value;
                }

                $price = max(0, $original_price - $item_discount);
            }
            
            // 2. TÍNH TỔNG CHO SẢN PHẨM ĐÃ GIẢM
            $total = $price * $quantity; 
            $sub_total += $total;

            $cart_items[] = [
                'id' => $id,
                'name' => $product_data['tensanpham'],
                'original_price' => $original_price,
                'price' => $price, 
                'image' => $product_data['img'],
                'quantity' => $quantity,
                'total' => $total,
                'tonkho' => $product_data['tonkho'],
                'item_discount' => $item_discount,
                'promo_description' => $promo_description
            ];
        }
        
        // 3. XỬ LÝ VOUCHER TỔNG HỢP (Nếu có)
        $discount = 0;
        if (isset($_SESSION['voucher_code']) && isset($_SESSION['voucher_giam_value'])) {
            $discount = $this->recalculateVoucherDiscount($sub_total, $_SESSION['voucher_giam_value']);
        }
        
        $grand_total = max(0, $sub_total + $this->shipping_fee - $discount);
        
        // Trả về mảng kết hợp
        return [
            'items' => $cart_items, 
            'sub_total' => $sub_total, 
            'shipping_fee' => $this->shipping_fee, 
            'discount' => $discount, 
            'grand_total' => $grand_total
        ];
    }
    
    // -----------------------------------------------------------------
    // --- LOGIC VOUCHER ---
    // -----------------------------------------------------------------
    
    public function processVoucher($voucher_code, $sub_total) {
        
        $response = ['success' => false, 'message' => 'Mã khuyến mãi không tồn tại.', 'discount' => 0, 'voucher_code' => '', 'giam_value' => 0];
        
        if (empty($voucher_code)) {
            $this->resetVoucher();
            $response['success'] = true;
            $response['message'] = "Đã xóa mã khuyến mãi.";
            return $response;
        }

        $row_v = $this->cartRepository->getVoucherByCode($voucher_code);
        
        if ($row_v) {
            $today = date('Y-m-d');
            $is_valid_date = $row_v['ngayhethan'] >= $today;
            // Giả sử soluong NULL nghĩa là không giới hạn
            $is_available = is_null($row_v['soluong']) || $row_v['soluong'] > 0; 
            
            if ($is_valid_date && $is_available) {
                
                $giam_value = floatval($row_v['giam']);
                $discount_temp = $this->recalculateVoucherDiscount($sub_total, $giam_value);
                
                // Lưu vào session sau khi tính toán thành công
                $_SESSION['voucher_code'] = $voucher_code; 
                $_SESSION['voucher_giam_value'] = $giam_value; 
                $_SESSION['applied_voucher_id'] = $row_v['voucher_id'];
                
                $response['success'] = true;
                $response['discount'] = $discount_temp;
                $response['voucher_code'] = $voucher_code;
                $response['giam_value'] = $giam_value;
                $response['message'] = "Áp dụng mã **{$voucher_code}** thành công! Giảm: " . number_format($discount_temp, 0, ',', '.') . " VNĐ.";
                
            } else {
                $response['message'] = "Mã khuyến mãi không hợp lệ, đã hết hạn hoặc đã hết lượt sử dụng.";
                $this->resetVoucher();
            }
        } else {
            $this->resetVoucher();
        }
        
        return $response;
    }

    private function recalculateVoucherDiscount($sub_total, $giam_value) {
        $discount_temp = 0;
        
        if ($giam_value < 1) {
            $discount_temp = $sub_total * $giam_value; // Giảm theo phần trăm
        } else {
            $discount_temp = $giam_value; // Giảm tiền cố định
        }
        
        // Đảm bảo discount không lớn hơn Tổng phụ (không tính phí ship)
        if ($discount_temp > $sub_total) { 
            $discount_temp = $sub_total;
        }
        
        // Cập nhật lại discount_amount trong Session sau khi tính lại
        $_SESSION['discount_amount'] = $discount_temp;
        
        return $discount_temp;
    }
    
    private function resetVoucher() {
        unset($_SESSION['voucher_code']); 
        unset($_SESSION['discount_amount']);
        unset($_SESSION['voucher_giam_value']);
        unset($_SESSION['applied_voucher_id']);
    }
}