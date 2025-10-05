<?php
// BẮT BUỘC: Khởi động session để lưu giỏ hàng
session_start();

// Đảm bảo kết nối Database hoạt động. Biến $conn phải được tạo ra từ file này.
// ĐỔI ĐƯỜNG DẪN NẾU CẦN
require_once '../Database/Database.php';

// Khởi tạo giỏ hàng nếu chưa tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Khởi tạo user_id (Lấy ID người dùng đã đăng nhập từ Session)
// Nếu chưa đăng nhập, $user_id sẽ là 0
$user_id = $_SESSION['user_id'] ?? 0; 

// Hàm format tiền tệ
function formatVND($number) {
    // Ép kiểu số nguyên và format theo chuẩn VNĐ
    $num = intval($number);
    return number_format($num, 0, ',', '.');  // Định dạng 1 số thêm dấu phẩy dấu chấm
}


// --- HÀM MỚI: ĐỒNG BỘ SESSION CART VÀO Database ---
function syncCartToDatabase($conn, $user_id, $session_cart) {
    if ($user_id <= 0 || !isset($conn)) {
        return; // Không làm gì nếu chưa đăng nhập hoặc không có kết nối DB
    }

    // 1. Xóa giỏ hàng cũ của user này trong DB để tránh trùng lặp
    $sql_delete = "DELETE FROM giohang WHERE user_id = ?";
    if ($stmt_del = $conn->prepare($sql_delete)) {
        $stmt_del->bind_param('i', $user_id);
        $stmt_del->execute();
        $stmt_del->close();
    } else {
        // Ghi log lỗi nếu cần
        // error_log("Lỗi chuẩn bị lệnh xóa giỏ hàng: " . $conn->error);
        return; 
    }

    // 2. Chèn tất cả sản phẩm từ Session Cart vào DB
    if (!empty($session_cart)) {
        $sql_insert = "INSERT INTO giohang (user_id, product_id, soluong) VALUES (?, ?, ?)";
        if ($stmt_ins = $conn->prepare($sql_insert)) {
            foreach ($session_cart as $product_id => $quantity) {
                $stmt_ins->bind_param('iii', $user_id, $product_id, $quantity);
                $stmt_ins->execute();
            }
            $stmt_ins->close();
        } else {
             // error_log("Lỗi chuẩn bị lệnh chèn giỏ hàng: " . $conn->error);
        }
    }
}


// --- 1. XỬ LÝ LOGIC GIỎ HÀNG (Thêm/Xóa/Cập nhật) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    $response = ['success' => false, 'message' => 'Lỗi không xác định'];
    
    // --- XỬ LÝ CẬP NHẬT SỐ LƯỢNG (ajax_update_quantity) ---
    if ($_POST['action'] === 'ajax_update_quantity') {
        $product_id = intval($_POST['product_id']);
        $new_quantity = intval($_POST['new_quantity']);
        $tonkho = intval($_POST['tonkho']);

        if ($product_id > 0) {
            if ($new_quantity > $tonkho) {
                $new_quantity = $tonkho;
            }

            if ($new_quantity > 0) {
                $_SESSION['cart'][$product_id] = $new_quantity;
                $response = ['success' => true, 'quantity' => $new_quantity, 'cart_count' => array_sum($_SESSION['cart'])];
            } else {
                // Sẽ bị xử lý như hành động xóa nếu new_quantity <= 0
                unset($_SESSION['cart'][$product_id]);
                unset($_SESSION['voucher_code']); 
                unset($_SESSION['discount_amount']);
                unset($_SESSION['voucher_giam_value']); 
                $response = ['success' => true, 'action' => 'removed', 'cart_count' => array_sum($_SESSION['cart'])];
            }
            
            // ĐỒNG BỘ DB SAU KHI CẬP NHẬT
            if ($user_id > 0) {
                syncCartToDatabase($conn, $user_id, $_SESSION['cart']);
            }
        }

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    }

    // --- XỬ LÝ XÓA SẢN PHẨM (remove_item) ---
    if ($_POST['action'] === 'remove_item' && isset($_POST['product_id_remove'])) {
        $product_id = intval($_POST['product_id_remove']);
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            // Reset giảm giá khi xóa sản phẩm
            unset($_SESSION['voucher_code']); 
            unset($_SESSION['discount_amount']);
            unset($_SESSION['voucher_giam_value']);
            
            // ĐỒNG BỘ DB SAU KHI XÓA
            if ($user_id > 0) {
                syncCartToDatabase($conn, $user_id, $_SESSION['cart']);
            }
            $response = ['success' => true, 'action' => 'removed', 'cart_count' => array_sum($_SESSION['cart'])];
        }
        
        // Luôn trả về JSON nếu là AJAX
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        } else {
            // Trường hợp POST truyền thống
            header("Location: cart.php"); 
            exit();
        }
    }


    // Logic THÊM SẢN PHẨM MỚI (giữ nguyên, nhưng thêm đồng bộ DB)
    if ($_POST['action'] === 'add_to_cart' && isset($_POST['product_id'])) {
        $product_id = intval($_POST['product_id']);
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        if ($product_id > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            
            // ĐỒNG BỘ DB SAU KHI THÊM
            if ($user_id > 0) {
                syncCartToDatabase($conn, $user_id, $_SESSION['cart']);
            }
        }
        // Chuyển hướng cho POST truyền thống
        header("Location: cart.php");
        exit();
    }


    // Chuyển hướng cho các POST khác (voucher)
    if (!$is_ajax && isset($_POST['voucher_code'])) {
        // Logic voucher sẽ xử lý chuyển hướng bên dưới
    }

}


// --- 2. TÍNH TOÁN THÀNH TIỀN (SUB TOTAL) TỪ DB (Phải chạy trước logic Voucher) ---
$cart_items = [];
$sub_total = 0;
$shipping_fee = 50000; // phí ship mặc định

// *** ĐỒNG BỘ NGƯỢC: LẤY CART TỪ DB NẾU SESSION TRỐNG VÀ USER ĐÃ ĐĂNG NHẬP ***
// Mục đích: Khôi phục giỏ hàng khi người dùng đăng nhập lại (hoặc lần đầu truy cập sau khi đăng nhập)
if ($user_id > 0 && empty($_SESSION['cart']) && isset($conn) && $conn) {
    $sql_db_cart = "SELECT product_id, soluong FROM giohang WHERE user_id = ?";
    if ($stmt_db_cart = $conn->prepare($sql_db_cart)) {
        $stmt_db_cart->bind_param('i', $user_id);
        $stmt_db_cart->execute();
        $result_db_cart = $stmt_db_cart->get_result();
        
        while ($row = $result_db_cart->fetch_assoc()) {
            $_SESSION['cart'][$row['product_id']] = $row['soluong'];
        }
        $stmt_db_cart->close();
    }
}
// ******************************************************************************

// PHẦN LẤY DỮ LIỆU SẢN PHẨM TỪ DB
if (!empty($_SESSION['cart']) && isset($conn) && $conn) {
    $product_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $sql_products = "SELECT product_id, tensanpham, gia, img, tonkho FROM sanpham WHERE product_id IN ($placeholders)";

    if ($stmt = $conn->prepare($sql_products)) {
        $types = str_repeat('i', count($product_ids));
        $params = array_merge([$types], $product_ids);
        $refs = [];
        foreach ($params as $key => $value) {
            $refs[$key] = &$params[$key];
        }

        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($product_data = $result->fetch_assoc()) {
            $id = $product_data['product_id'];
            if (!isset($_SESSION['cart'][$id])) continue;

            $quantity = $_SESSION['cart'][$id];
            $price = $product_data['gia'];
            $total = $price * $quantity;
            $sub_total += $total; // Tính Sub Total

            $cart_items[] = [
                'id' => $id,
                'name' => $product_data['tensanpham'],
                'price' => $price,
                'image' => $product_data['img'],
                'quantity' => $quantity,
                'total' => $total,
                'tonkho' => $product_data['tonkho']
            ];
        }
        $stmt->close();
    }
}


// --- 3. XỬ LÝ MÃ KHUYẾN MÃI VÀ LƯU VÀO SESSION ---
$discount = 0; 
$voucher_code = isset($_SESSION['voucher_code']) ? $_SESSION['voucher_code'] : ''; 
$voucher_message = '';

// Nếu người dùng gửi mã khuyến mãi qua POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['voucher_code'])) {
    $new_voucher_code = trim($_POST['voucher_code']);
    
    if ($new_voucher_code !== $voucher_code || empty($new_voucher_code)) {
        
        $voucher_code = $new_voucher_code;
        
        if ($voucher_code !== '') {
            $sql_voucher = "SELECT giam, ngayhethan, soluong FROM makhuyenmai WHERE makhuyenmai = ? LIMIT 1";
            
            if (isset($conn) && $conn && $stmt_v = $conn->prepare($sql_voucher)) { 
                $stmt_v->bind_param('s', $voucher_code);
                $stmt_v->execute();
                $result_v = $stmt_v->get_result();
                
                if ($row_v = $result_v->fetch_assoc()) {
                    $today = date('Y-m-d');
                    $is_valid_date = $row_v['ngayhethan'] >= $today;
                    $is_available = is_null($row_v['soluong']) || $row_v['soluong'] > 0; 
                    
                    if ($is_valid_date && $is_available) {
                        
                        $giam_value = floatval($row_v['giam']);
                        $discount_temp = 0;
                        
                        if ($giam_value < 1) {
                            $discount_temp = $sub_total * $giam_value; // Giảm theo phần trăm
                        } else {
                            $discount_temp = $giam_value; // Giảm tiền cố định
                        }
                        
                        // LƯU VÀO SESSION
                        $_SESSION['voucher_code'] = $voucher_code; 
                        $_SESSION['discount_amount'] = $discount_temp;
                        $_SESSION['voucher_giam_value'] = $giam_value; 
                        $voucher_message = "Áp dụng mã **{$voucher_code}** thành công! Giảm: " . formatVND($discount_temp) . " VNĐ.";
                        
                    } else {
                        // Mã hết hạn/hết số lượng
                        unset($_SESSION['voucher_code']); unset($_SESSION['discount_amount']); unset($_SESSION['voucher_giam_value']);
                        $voucher_message = "Mã khuyến mãi không hợp lệ, đã hết hạn hoặc đã hết lượt sử dụng.";
                    }
                } else {
                    // Mã không tồn tại
                    unset($_SESSION['voucher_code']); unset($_SESSION['discount_amount']); unset($_SESSION['voucher_giam_value']);
                    $voucher_message = "Mã khuyến mãi không tồn tại.";
                }
                $stmt_v->close();
            } else {
                   // Lỗi kết nối DB 
                   unset($_SESSION['voucher_code']); unset($_SESSION['discount_amount']); unset($_SESSION['voucher_giam_value']);
                   $voucher_message = "Lỗi hệ thống: Không thể kết nối DB để kiểm tra mã.";
            }
        } else {
              // Xóa mã
              unset($_SESSION['voucher_code']); unset($_SESSION['discount_amount']); unset($_SESSION['voucher_giam_value']);
              $voucher_message = "Đã xóa mã khuyến mãi.";
        }
        
        // CHUYỂN HƯỚNG BẮT BUỘC ĐỂ XÓA POST DATA VÀ HIỂN THỊ KẾT QUẢ
        $_SESSION['voucher_message'] = $voucher_message; // Lưu thông báo vào Session
        header("Location: cart.php");
        exit();
    }
}

// --- 4. TÍNH TOÁN LẠI GIẢM GIÁ TỪ SESSION ---
if (isset($_SESSION['discount_amount']) && isset($_SESSION['voucher_code'])) {
    $voucher_code = $_SESSION['voucher_code']; 
    $discount = $_SESSION['discount_amount'];
    
    if (isset($_SESSION['voucher_giam_value'])) {
        $giam_value = floatval($_SESSION['voucher_giam_value']);
        
        if ($giam_value < 1) {
            // Là phần trăm, tính lại dựa trên Sub Total mới (vì Sub Total có thể đã thay đổi)
            $discount = $sub_total * $giam_value; 
            $_SESSION['discount_amount'] = $discount; // Cập nhật Session mới
        } 
    }
    // Đảm bảo discount không lớn hơn Tổng cộng
    if ($discount > ($sub_total + $shipping_fee)) {
        $discount = $sub_total + $shipping_fee;
    }
}


// --- 5. TÍNH TỔNG CUỐI CÙNG VÀ HIỂN THỊ THÔNG BÁO ---
$grand_total = $sub_total + $shipping_fee - $discount;
if ($grand_total < 0) $grand_total = 0; // tránh âm

// Lấy thông báo từ session (nếu có)
if (isset($_SESSION['voucher_message'])) {
    $voucher_message = $_SESSION['voucher_message'];
    unset($_SESSION['voucher_message']); // Xóa sau khi đã dùng
}

// Đóng kết nối DB (Chạy cuối cùng)
if (isset($conn) && $conn) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Giỏ hàng - LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <?php include 'navbar.php'; ?>
    <script>
    // Script này nên nằm trong file main.js hoặc được đặt sau navbar.php
    document.addEventListener("DOMContentLoaded", function() {
        // ... (Logic active link) ...
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href').split('/').pop();
            if (currentPath.endsWith(linkPath) && linkPath !== '') {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }

            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown && currentPath.includes(linkPath)) {
                parentDropdown.querySelector('.nav-link.dropdown-toggle').classList.add('active');
            }
        });

        const homeLink = document.querySelector('.navbar-nav .nav-link[href="index.php"]');
        if (homeLink) {
            homeLink.classList.remove('active');
        }

        // *** LOGIC CẬP NHẬT SỐ LƯỢNG KHI MỚI TẢI TRANG ***
        // Hàm này sẽ được gọi sau khi `navbar.php` (chứa icon giỏ hàng) đã được include.
        function initialCartUpdate() {
            let totalQuantity = 0;
            // Tính tổng số lượng từ các sản phẩm trong giỏ hàng (cart_items được tạo từ PHP)
            <?php 
            $total_cart_quantity = 0;
            foreach ($_SESSION['cart'] as $qty) {
                $total_cart_quantity += $qty;
            }
            echo "totalQuantity = $total_cart_quantity;";
            ?>
            
            // Cập nhật lên icon giỏ hàng
            const cartCountElement = document.querySelector('.cart-count'); // Giả sử icon giỏ hàng có class .cart-count
            if (cartCountElement) {
                cartCountElement.textContent = totalQuantity;
                cartCountElement.style.display = totalQuantity > 0 ? 'inline-flex' : 'none';
            }
        }
        
        initialCartUpdate(); // Gọi ngay khi DOM đã tải

    });
    </script>
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Giỏ hàng</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="./index.php" style="color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Giỏ hàng</li>
        </ol>
    </div>
    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="table-responsive">
                <table class="table cart-table">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">Sản phẩm</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Giá</th>
                            <th scope="col">Số lượng</th>
                            <th scope="col">Tổng</th>
                            <th scope="col">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cart_items)): ?>
                        <tr class="empty-cart-message">
                            <td colspan="6" class="text-center py-5">
                                <h4 class="text-muted">Giỏ hàng của bạn đang trống! 🛒</h4>
                                <p>Hãy thêm sản phẩm vào giỏ hàng từ trang sản phẩm.</p>
                                <a href="./shop.php" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <th scope="row" class="text-center">
                                    <div class="d-flex justify-content-center"> 
                                        <img 
                                            src="../Admin/uploads/<?php echo htmlspecialchars($item['image']); ?>"
                                            class="img-fluid" 
                                            style="width: 100px; height: 100px; object-fit: contain; margin: 10px 0;"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                        >
                                    </div>
                                </th>
                                <td>
                                    <p class="mb-0 mt-4"><?php echo htmlspecialchars($item['name']); ?></p>
                                </td>
                                <td>
                                    <p class="mb-0 mt-4"><span class="js-product-price"><?php echo formatVND($item['price']); ?></span> VNĐ</p>
                                </td>
                                
                                <td>
                                    <div class="input-group quantity d-flex align-items-center" style="width: 120px; margin-top: 20px;" data-product-id="<?php echo htmlspecialchars($item['id']); ?>">
                                        
                                        <div class="input-group-btn">
                                            <button type="button" 
                                                     class="btn btn-sm btn-minus rounded-circle border-0 text-dark" 
                                                     data-id="<?php echo htmlspecialchars($item['id']); ?>"
                                                     data-tonkho="<?php echo htmlspecialchars($item['tonkho'] ?? 99); ?>"
                                                     data-action="minus"
                                                     <?php echo ($item['quantity'] <= 1) ? 'disabled' : ''; ?>
                                                   >
                                                     <i class="fa fa-minus"></i>
                                                 </button>
                                        </div>
                                        
                                        <span class="quantity-value text-center px-2 h5 mb-0" style="display: inline-block; width: 30px;">
                                            <?php echo htmlspecialchars($item['quantity']); ?>
                                        </span>
                                        
                                        <div class="input-group-btn">
                                            <button type="button" 
                                                     class="btn btn-sm btn-plus rounded-circle border-0 text-dark" 
                                                     data-id="<?php echo htmlspecialchars($item['id']); ?>"
                                                     data-tonkho="<?php echo htmlspecialchars($item['tonkho'] ?? 99); ?>"
                                                     data-action="plus"
                                                     <?php echo ($item['quantity'] >= ($item['tonkho'] ?? 99)) ? 'disabled' : ''; ?>
                                                   >
                                                     <i class="fa fa-plus"></i>
                                                 </button>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <p class="mb-0 mt-4"><span class="js-product-total"><?php echo formatVND($item['total']); ?></span> VNĐ</p>
                                </td>
                                
                                <td>
                                    <button type="button" 
                                            class="btn btn-md rounded-circle bg-light border mt-4 js-remove-item-direct"
                                            data-id="<?php echo htmlspecialchars($item['id']); ?>">
                                        <i class="fa fa-times text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="card my-4">
                <div class="card-body">
                    <h5 class="card-title mb-3"><i class="fa fa-tag text-primary me-2"></i>Mã giảm giá</h5>
                    <form action="cart.php" method="POST" class="d-flex align-items-center">
                        <input type="text" name="voucher_code" 
                            class="form-control me-3" 
                            placeholder="Nhập mã giảm giá" 
                            style="max-width:250px;"
                            value="<?php echo htmlspecialchars($voucher_code); ?>"
                        >
                        <button class="btn btn-primary rounded-pill px-4 py-2" type="submit">
                            Áp dụng
                        </button>
                        
                        <?php if (!empty($voucher_code)): ?>
                            <a href="cart.php" 
                                class="btn btn-outline-danger rounded-pill px-3 py-2 ms-2"
                                onclick="event.preventDefault(); document.getElementById('remove-voucher-form').submit();"
                            >
                                Xóa mã
                            </a>
                        <?php endif; ?>
                    </form>
                    
                    <form id="remove-voucher-form" action="cart.php" method="POST" style="display: none;">
                        <input type="hidden" name="voucher_code" value="">
                    </form>
                    
                    <?php if (!empty($voucher_message)): ?>
                        <div class="mt-3 alert <?php echo (strpos($voucher_message, 'thành công') !== false) ? 'alert-success' : 'alert-warning'; ?>" role="alert">
                            <?php echo $voucher_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row g-4 justify-content-end mt-5">
                <div class="col-8"></div>
                <div class="col-sm-8 col-md-7 col-lg-6 col-xl-4">
                   <div id="checkoutSummary" class="bg-light rounded">
                    <div class="p-4">
                        <h1 class="display-6 mb-4">Tổng Tiền</h1>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0 me-4">Thành tiền</h5>
                            <p class="mb-0"><span class="cart-sub-total"><?php echo formatVND($sub_total); ?></span> VNĐ</p>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h5 class="mb-0 me-4">Phí vận chuyển</h5>
                            <p class="mb-0"><span class="cart-shipping-fee"><?php echo formatVND($shipping_fee); ?></span> VNĐ</p>
                        </div>
                       <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-3" id="discount-row">
                            <h5 class="mb-0 me-4 text-success">Giảm giá</h5>
                            <p class="mb-0 text-success">- <span class="cart-discount"><?php echo formatVND($discount); ?></span> VNĐ</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                        <h5 class="mb-0 ps-4 me-4">Tổng cộng</h5>
                        <p class="mb-0 pe-4"><span class="cart-grand-total"><?php echo formatVND($grand_total); ?></span> VNĐ</p>
                    </div>
                    <a href="./checkout.php"
                        class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 ms-4"
                        role="button">
                        Tiến hành thanh toán
                    </a>
                </div>
                </div>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>


    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
                class="fa fa-arrow-up"></i></a>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="js/main.js"></script>


<script>
// Format số VNĐ (định nghĩa lại trong JS để AJAX sử dụng)
function formatVND(number) {
    // Chuyển string (có dấu chấm) thành số nguyên
    let num = parseInt(number.toString().replace(/\./g, ''));
    if (isNaN(num)) return '0';
    // Format số
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// *** HÀM MỚI: CẬP NHẬT SỐ LƯỢNG SẢN PHẨM TRÊN ICON GIỎ HÀNG ***
function updateCartIconQuantity(newCount) {
    const cartCountElement = document.querySelector('.cart-count'); 
    if (cartCountElement) {
        cartCountElement.textContent = newCount;
        cartCountElement.style.display = newCount > 0 ? 'inline-flex' : 'none';
    }
}

// Tính lại tổng tiền giỏ hàng (QUAN TRỌNG: Bao gồm discount)
function recalculateCartSummary() {
    let subTotal = 0;
    // Lặp qua tổng tiền từng sản phẩm để tính Sub Total
    $('.js-product-total').each(function() {
        // Cần loại bỏ " VNĐ" và dấu "." để parse thành số
        let totalText = $(this).text().replace(/\./g, '').replace(' VNĐ', '');
        let productTotal = parseInt(totalText);
        if (!isNaN(productTotal)) subTotal += productTotal;
    });

    const shippingFeeText = $('.cart-shipping-fee').text().replace(/\./g, '').replace(' VNĐ', '');
    const shippingFee = parseInt(shippingFeeText) || 0; 

    // Lấy giảm giá từ HTML (giá trị này được PHP tính toán và in ra từ session)
    const discountElement = document.querySelector('.cart-discount');
    // Nếu thẻ giảm giá không tồn tại, discount = 0
    const discountText = discountElement ? discountElement.textContent.replace(/\./g, '').replace(' VNĐ', '') : '0';
    let discount = parseInt(discountText) || 0;
    
    // Nếu có mã giảm giá % (tính lại dựa trên subTotal mới)
    // NOTE: Trong môi trường AJAX, để xử lý triệt để % cần gọi lại server. 
    // Tuy nhiên, ở đây ta chỉ cần dựa vào giá trị đã được PHP tính toán và in ra.
    // Nếu giảm giá > SubTotal + Ship, giới hạn lại
    if (discount > (subTotal + shippingFee)) {
        discount = subTotal + shippingFee;
        $('.cart-discount').text(formatVND(discount));
    }


    const grandTotal = subTotal + shippingFee - discount;

    $('.cart-sub-total').text(formatVND(subTotal)); 
    $('.cart-grand-total').text(formatVND(grandTotal > 0 ? grandTotal : 0)); 

    // Ẩn/hiện Total section nếu giỏ hàng trống
    if (subTotal === 0) {
        $('#checkoutSummary').closest('.row').hide();
    } else {
        $('#checkoutSummary').closest('.row').show();
    }
}

// Kiểm tra ẩn/hiện nút + -
function checkQuantityButtons($rowElement, currentQuantity, maxStock) {
    const $minusButton = $rowElement.find('.btn-minus');
    const $plusButton = $rowElement.find('.btn-plus');

    $minusButton.prop('disabled', currentQuantity <= 1);
    $plusButton.prop('disabled', currentQuantity >= maxStock);
}


$(document).ready(function () {
    // Khởi tạo lại tổng tiền khi load trang
    recalculateCartSummary();

    // AJAX cập nhật số lượng
    function updateCartItemAjax(productId, newQuantity, maxStock, $rowElement) {
        const $quantitySpan = $rowElement.find('.quantity-value');
        const $totalSpan = $rowElement.find('.js-product-total');
        const $priceSpan = $rowElement.find('.js-product-price');

        const productPrice = parseInt($priceSpan.text().replace(/\./g, '').replace(' VNĐ', '')) || 0;
        const oldValue = parseInt($quantitySpan.text().trim());

        // Thay bằng loading spinner
        $quantitySpan.html('<i class="fas fa-spinner fa-spin"></i>'); 

        $.ajax({
            url: 'cart.php', 
            type: 'POST',
            data: {
                action: 'ajax_update_quantity', 
                product_id: productId,
                new_quantity: newQuantity,
                tonkho: maxStock
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.action === 'removed') {
                        // Xóa hàng khỏi bảng
                        $rowElement.remove();
                        // Cập nhật lại icon giỏ hàng
                        updateCartIconQuantity(response.cart_count);

                        // Kiểm tra nếu giỏ hàng trống thì tải lại trang (để reset giao diện)
                        if ($('.cart-table tbody').find('tr').length === 1 && $('.empty-cart-message').length > 0) {
                             window.location.reload(); 
                        } else if ($('.cart-table tbody').find('tr').length === 0) {
                            window.location.reload(); // Tải lại để hiển thị giỏ hàng trống
                        }
                    } else {
                        // Cập nhật số lượng mới
                        $quantitySpan.text(response.quantity); 
                        // Tổng tiền của sản phẩm đó
                        const newTotal = response.quantity * productPrice;
                        $totalSpan.text(formatVND(newTotal));
                        // Kiểm tra lại nút + -
                        checkQuantityButtons($rowElement, response.quantity, maxStock);
                        // Cập nhật lại icon giỏ hàng
                        updateCartIconQuantity(response.cart_count);
                    }
                    
                    // Cập nhật tổng tiền
                    recalculateCartSummary();

                    // Nếu có mã giảm giá, cần tải lại trang để tính lại logic voucher (Phần trăm/Tiền cố định)
                    const voucherCodeInput = $('input[name="voucher_code"]').val();
                    if (voucherCodeInput !== '') {
                        window.location.reload();
                    }

                } else {
                    // Nếu thất bại, khôi phục lại giá trị cũ
                    $quantitySpan.text(oldValue); 
                    alert(response.message || 'Cập nhật thất bại. Vui lòng thử lại.');
                }
            },
            error: function() {
                $quantitySpan.text(oldValue); 
                alert('Lỗi kết nối máy chủ. Vui lòng thử lại.');
            }
        });
    }

    // Sự kiện click nút + / -
    $(".input-group").on('click', '.btn-minus, .btn-plus', function (e) {
        e.preventDefault();

        var $button = $(this);
        var $rowElement = $button.closest('tr');
        var productId = $button.data('id');
        var maxStock = parseInt($button.data('tonkho')); 
        var $quantitySpan = $rowElement.find('.quantity-value');
        var oldValue = parseInt($quantitySpan.text().trim());
        var newValue = oldValue;

        if ($button.data('action') === 'plus') {
            if (oldValue < maxStock) newValue = oldValue + 1;
            else return;
        } else if ($button.data('action') === 'minus') {
            // Không xóa trực tiếp ở đây, chỉ giảm số lượng
            if (oldValue > 1) newValue = oldValue - 1;
            else if (oldValue === 1) {
                // Nếu giảm từ 1 xuống 0, cần gọi hàm xóa trực tiếp
                $('.js-remove-item-direct[data-id="'+ productId +'"]').trigger('click');
                return;
            } else return;
        } 
        
        // Chỉ gọi AJAX nếu số lượng thay đổi
        if (newValue !== oldValue) {
             updateCartItemAjax(productId, newValue, maxStock, $rowElement);
        }
    });

    // *** SỰ KIỆN MỚI: XÓA SẢN PHẨM TRỰC TIẾP BẰNG AJAX (KHÔNG CẦN CONFIRM) ***
    $(document).on('click', '.js-remove-item-direct', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $rowElement = $button.closest('tr');
        var productId = $button.data('id');
        
        // Thay bằng loading spinner/disable button
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'cart.php', 
            type: 'POST',
            data: {
                action: 'remove_item', 
                product_id_remove: productId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.action === 'removed') {
                    // Xóa hàng khỏi bảng
                    $rowElement.remove();
                    // Cập nhật lại icon giỏ hàng
                    updateCartIconQuantity(response.cart_count);
                    
                    // Cập nhật tổng tiền (bao gồm giảm giá nếu bị reset)
                    recalculateCartSummary();

                    // Nếu có mã giảm giá (voucher_code bị reset), cần tải lại trang để hiển thị thông báo "Đã xóa mã" và cập nhật khu vực Giảm giá
                    const voucherCodeInput = $('input[name="voucher_code"]').val();
                    if (voucherCodeInput !== '') {
                        window.location.reload();
                    }
                    
                    // Kiểm tra nếu giỏ hàng trống thì tải lại trang (để reset giao diện)
                    if ($('.cart-table tbody').find('tr').length === 0) {
                         window.location.reload(); 
                    }

                } else {
                    alert('Xóa sản phẩm thất bại. Vui lòng thử lại.');
                    $button.prop('disabled', false).html('<i class="fa fa-times text-danger"></i>');
                }
            },
            error: function() {
                alert('Lỗi kết nối máy chủ. Vui lòng thử lại.');
                $button.prop('disabled', false).html('<i class="fa fa-times text-danger"></i>');
            }
        });
    });
});
</script>

</body>

</html>