<?php
session_start();

// Thiết lập hiển thị lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Đường dẫn Database, Repository, và Service
require_once '../../Database/Database.php'; 
require_once '../Repository/CartRepository.php'; 
require_once '../Service/CartService.php'; 

// Hàm format tiền tệ
function formatVND($number) {
    $num = intval($number);
    return number_format($num, 0, ',', '.');
}

// Khởi tạo kết nối Database, Repository và Service
$db = new Database(); 
$conn = $db->conn; 
$cartRepository = new CartRepository($conn); 
$cartService = new CartService($cartRepository); 


// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Khởi tạo user_id 
$user_id = $_SESSION['kh_user_id'] ?? 0; 


$cartService->syncSessionFromDatabase($user_id);


// --- 3. XỬ LÝ HÀNH ĐỘNG POST (BAO GỒM CẢ AJAX) ---
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // XỬ LÝ CHUYỂN ĐẾN CHECKOUT (Vẫn là POST thường)
    if ($_POST['action'] === 'go_to_checkout') {
         // ... (giữ nguyên logic này)
         if (empty($_SESSION['cart'])) {
             $_SESSION['cart_message'] = 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm.';
             header("Location: CartController.php");
             exit();
         }
         
         $cartService->syncDatabase($user_id);
         
         header("Location: CheckoutController.php"); 
         exit();
    }
    
    // XỬ LÝ CẬP NHẬT SỐ LƯỢNG (AJAX)
    if ($_POST['action'] === 'ajax_update_quantity') {
        $product_id = intval($_POST['product_id']);
        $new_quantity = intval($_POST['new_quantity']);
        // 🔥 Khởi tạo response với các giá trị mặc định cho trường hợp lỗi
        $response = [
            'success' => false, 
            'message' => 'Lỗi cập nhật giỏ hàng.',
            'new_quantity' => $new_quantity, // Trả lại số lượng ban đầu để JS có thể khôi phục
            'action' => 'failed'
        ];

        try {
            $update_result = $cartService->updateCartItem($user_id, $product_id, $new_quantity); 
            
            // Lấy Summary mới nhất sau khi Service đã xử lý (đảm bảo tính chính xác)
            $summary = $cartService->getCartSummary();
            
            // Lấy tổng tiền mới của sản phẩm, nếu sản phẩm vẫn còn
            $new_item_total = 0;
            $product_found = false;
            foreach ($summary['items'] as $item) {
                if ($item['id'] == $product_id) {
                    $new_item_total = $item['total'];
                    $response['new_quantity'] = $item['quantity']; // Cập nhật số lượng thực tế (có thể bị giới hạn)
                    $product_found = true;
                    break;
                }
            }

            // Cập nhật các trường chung của Response
            $response['sub_total_text'] = formatVND($summary['sub_total']) . ' đ';
            $response['grand_total_text'] = formatVND($summary['grand_total']) . ' đ';
            $response['discount_text'] = '- ' . formatVND($summary['discount']) . ' đ';
            $response['discount_value'] = $summary['discount'];
            $response['is_cart_empty'] = empty($summary['items']);


            if ($update_result['success']) {
                $response['success'] = true;
                $response['message'] = $update_result['message'] ?? 'Cập nhật thành công!';
                $response['new_item_total_text'] = formatVND($new_item_total) . ' đ';
                $response['action'] = 'updated';
            } else {
                // Xử lý khi Service trả về success=false
                $response['success'] = false;
                $response['message'] = $update_result['message'] ?? 'Lỗi khi cập nhật.';
                
                // Nếu sản phẩm bị xóa do hết hàng (trong CartService)
                if (!$product_found && $update_result['action'] === 'removed') {
                     $response['action'] = 'removed';
                     $response['new_quantity'] = 0;
                }
            }

        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = "Lỗi hệ thống khi cập nhật: " . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    // XỬ LÝ XÓA SẢN PHẨM (AJAX)
    if ($_POST['action'] === 'remove_item' && isset($_POST['product_id'])) {
         // ... (giữ nguyên logic này)
         $product_id = intval($_POST['product_id']);
         $response = ['success' => false, 'message' => 'Lỗi không xác định.']; 

         try {
             $remove_result = $cartService->removeItem($user_id, $product_id);
             
             if ($remove_result['success']) {
                 $summary = $cartService->getCartSummary();
                 
                 $response['success'] = true;
                 $response['message'] = 'Xóa sản phẩm thành công.';
                 $response['grand_total_text'] = formatVND($summary['grand_total']) . ' đ';
                 $response['sub_total_text'] = formatVND($summary['sub_total']) . ' đ';
                 $response['discount_text'] = '- ' . formatVND($summary['discount']) . ' đ';
                 $response['discount_value'] = $summary['discount'];
                 $response['is_cart_empty'] = empty($summary['items']);
             } else {
                 $response['success'] = false;
                 $response['message'] = $remove_result['message'] ?? 'Không thể xóa sản phẩm.';
             }
         
         } catch (Exception $e) {
             $response['success'] = false;
             $response['message'] = "Lỗi hệ thống khi xóa: " . $e->getMessage();
         }
         
         header('Content-Type: application/json');
         echo json_encode($response);
         exit();
    }
    
    // XỬ LÝ ÁP DỤNG VOUCHER (AJAX)
    if ($_POST['action'] === 'apply_voucher_ajax' && isset($_POST['voucher_code'])) {
         // ... (giữ nguyên logic này)
         $summary_data = $cartService->getCartSummary(); 
         $sub_total = $summary_data['sub_total']; 
         
         $new_voucher_code = trim($_POST['voucher_code']);
         $response = ['success' => false, 'message' => 'Lỗi không xác định.'];

         try {
             $voucher_result = $cartService->processVoucher($new_voucher_code, $sub_total);
             
             // Sau khi Service xử lý (cập nhật Session), lấy lại Summary
             $summary = $cartService->getCartSummary();
             
             $response['success'] = $voucher_result['success'];
             $response['message'] = $voucher_result['message'];
             $response['voucher_code'] = $_SESSION['voucher_code'] ?? ''; // Lấy từ Session
             $response['grand_total_text'] = formatVND($summary['grand_total']) . ' đ';
             $response['sub_total_text'] = formatVND($summary['sub_total']) . ' đ';
             $response['discount_text'] = '- ' . formatVND($summary['discount']) . ' đ';
             $response['discount_value'] = $summary['discount'];
             
         } catch (Exception $e) {
             $response['success'] = false;
             $response['message'] = 'Lỗi xử lý voucher: ' . $e->getMessage();
         }

         header('Content-Type: application/json');
         echo json_encode($response);
         exit();
    }
    
    // XỬ LÝ THÊM SẢN PHẨM MỚI (Vẫn là POST thường)
    if ($_POST['action'] === 'add_to_cart' && isset($_POST['product_id'])) {
         // ... (giữ nguyên logic này)
         $product_id = intval($_POST['product_id']);
         $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
         
         $result = $cartService->addItem($user_id, $product_id, $quantity); 

         if (isset($result['message'])) {
             $_SESSION['cart_message'] = $result['message'];
         }

         header("Location: CartController.php");
         exit();
    }
}


// --- 4. XỬ LÝ GET (HIỂN THỊ GIỎ HÀNG) ---

// 5. TÍNH TOÁN VÀ LẤY DỮ LIỆU CẦN THIẾT CHO VIEW
$summary_data = $cartService->getCartSummary();
$cart_items = $summary_data['items'];
$sub_total = $summary_data['sub_total'];
$shipping_fee = $summary_data['shipping_fee'];
$discount = $summary_data['discount'];
$grand_total = $summary_data['grand_total'];


// Lấy thông báo từ session (chỉ dành cho các hành động POST/Reload)
$voucher_message = '';
if (isset($_SESSION['voucher_message'])) {
    $voucher_message = $_SESSION['voucher_message'];
    unset($_SESSION['voucher_message']); 
}

$cart_message = '';
if (isset($_SESSION['cart_message'])) {
    $cart_message = $_SESSION['cart_message'];
    unset($_SESSION['cart_message']); 
}

// Lấy mã voucher hiện tại để điền vào form
// 🔥 SỬA: Lấy từ SESSION thay vì $summary_data (vì Service không trả về)
$voucher_code = $_SESSION['voucher_code'] ?? ''; 


// -----------------------------------------------------------
// 6. Đóng DB và Load View
// -----------------------------------------------------------
if (isset($conn) && $conn) {
    $conn->close();
}
require_once '../View/Cart.php';