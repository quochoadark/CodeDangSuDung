<?php
session_start();
// Đảm bảo đường dẫn chính xác đến file kết nối DB của bạn
require_once '../Database/Database.php';

// --- HÀM HỖ TRỢ ---
function formatVND($number) {
    // Ép kiểu số nguyên và format theo chuẩn VNĐ
    $num = intval($number);
    return number_format($num, 0, ',', '.') . ' VNĐ';
}

// Debug mode (bật khi cần debug, tắt khi chạy production)
$DEBUG = true; // Thay đổi thành false khi chạy thực tế

// Nếu chưa đăng nhập
if (!isset($_SESSION['user_id'])) { 
    $_SESSION['redirect_url'] = 'checkout.php'; 
    header("Location: Login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$cart_items = $_SESSION['cart']; // kỳ vọng: [ product_id => quantity, ... ]
$shipping_fee = 50000; // Phí vận chuyển cố định
$discount = 0; // Khởi tạo giảm giá
$voucher_id = null; // Khởi tạo voucher_id


// --- LẤY THÔNG TIN NGƯỜI DÙNG ---
$user_info = null;
$sql_user = "SELECT hoten, email, dienthoai, diachi FROM nguoidung WHERE user_id = ?";
if ($stmt_user = $conn->prepare($sql_user)) {
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $result_user = $stmt_user->get_result();
    if ($result_user && $result_user->num_rows > 0) {
        $user_info = $result_user->fetch_assoc();
    }
    $stmt_user->close();
}


// ----------------------------------------------------------------------------------
// --- TRUY VẤN DỮ LIỆU GIỎ HÀNG VÀ TÍNH SUB_TOTAL ĐỂ HIỂN THỊ ---
// ----------------------------------------------------------------------------------
$checkout_products = [];
$total_price_sub = 0;
if (!empty($cart_items)) {
    // Sanitize id list
    $product_ids = array_map('intval', array_keys($cart_items));
    $in_list = implode(',', $product_ids);

    $sql_cart_data = "SELECT product_id, tensanpham, gia, img, tonkho FROM sanpham WHERE product_id IN ($in_list)";
    if ($result_cart = $conn->query($sql_cart_data)) {
        while ($row = $result_cart->fetch_assoc()) {
            $product_id = (int)$row['product_id'];
            $quantity = (int)($cart_items[$product_id] ?? 0);
            
            // Kiểm tra tồn kho lần nữa (đề phòng)
            if ($quantity <= 0 || $quantity > (int)$row['tonkho']) {
                // Nếu số lượng không hợp lệ, chuyển hướng về giỏ hàng
                $_SESSION['error_message'] = "Số lượng sản phẩm '{$row['tensanpham']}' trong giỏ hàng vượt quá tồn kho.";
                header("Location: cart.php"); 
                exit();
            }

            $price = (float)$row['gia'];
            $sub_total = $price * $quantity;
            $total_price_sub += $sub_total;

            $checkout_products[] = [
                'product_id' => $product_id,
                'tensanpham' => $row['tensanpham'],
                'img' => $row['img'],
                'gia' => $price,
                'soluong' => $quantity,
                'tong' => $sub_total
            ];
        }
        $result_cart->free();
    } else {
        if ($DEBUG) error_log("Lỗi truy vấn hiển thị giỏ hàng: " . $conn->error);
    }
}


// ----------------------------------------------------------------------------------
// --- XỬ LÝ LẤY GIẢM GIÁ TỪ SESSION & TÍNH TOÁN LẠI ---
// ----------------------------------------------------------------------------------
$voucher_code = isset($_SESSION['voucher_code']) ? $_SESSION['voucher_code'] : ''; 

if (isset($_SESSION['discount_amount']) && isset($_SESSION['voucher_giam_value'])) {
    $giam_value = floatval($_SESSION['voucher_giam_value']);
    
    // 1. Tính toán lại giảm giá (bắt buộc vì sub_total có thể đã thay đổi)
    if ($giam_value < 1) {
        $discount = $total_price_sub * $giam_value; // Giảm theo phần trăm
    } else {
        $discount = $giam_value; // Giảm cố định (tiền)
    }

    // 2. Đảm bảo discount không lớn hơn sub_total + shipping_fee
    if ($discount > ($total_price_sub + $shipping_fee)) {
        $discount = $total_price_sub + $shipping_fee;
    }

    // 3. LẤY voucher_id (BẮT BUỘC TRUY VẤN DB LẠI VÌ CHỈ CÓ MÃ TRONG SESSION)
    if ($voucher_code) {
        $sql_voucher_id = "SELECT voucher_id FROM voucher WHERE makhuyenmai = ? LIMIT 1";
        if ($stmt_v_id = $conn->prepare($sql_voucher_id)) {
            $stmt_v_id->bind_param('s', $voucher_code);
            $stmt_v_id->execute();
            $result_v_id = $stmt_v_id->get_result();
            if ($row_v_id = $result_v_id->fetch_assoc()) {
                $voucher_id = (int)$row_v_id['voucher_id'];
            }
            $stmt_v_id->close();
        }
    }
}

// --- TỔNG CUỐI CÙNG ---
$grand_total = $total_price_sub + $shipping_fee - $discount;
if ($grand_total < 0) $grand_total = 0;


// ----------------------------------------------------------------------------------
// --- LOGIC XỬ LÝ ĐẶT HÀNG (POST) ---
// ----------------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $receiver_name       = trim($_POST['receiver_name'] ?? '');
    $receiver_phone      = trim($_POST['receiver_phone'] ?? '');
    $receiver_address    = trim($_POST['receiver_address'] ?? '');
    $notes               = trim($_POST['notes'] ?? '');
    $payment_method      = trim($_POST['payment_method'] ?? 'COD'); 
    
    if (empty($receiver_name) || empty($receiver_phone) || empty($receiver_address)) {
        $_SESSION['error_message'] = "Vui lòng điền đầy đủ Họ tên, Điện thoại và Địa chỉ nhận hàng.";
        header("Location: checkout.php");
        exit();
    }
    
    // Chuẩn bị dữ liệu giỏ hàng (giữ nguyên logic cũ)
    $cart_items_sanitized = [];
    foreach ($cart_items as $pid => $qty) {
        $pid = (int)$pid;
        $qty = (int)$qty;
        if ($pid > 0 && $qty > 0) {
            $cart_items_sanitized[$pid] = $qty;
        }
    }
    if (empty($cart_items_sanitized)) {
        $_SESSION['error_message'] = "Giỏ hàng không hợp lệ.";
        header("Location: cart.php");
        exit();
    }

    // Bắt đầu transaction
    $conn->begin_transaction();
    try {
        // --- 1. LẤY THÔNG TIN SẢN PHẨM TỪ DB (FOR UPDATE) & KIỂM TRA TỒN KHO ---
        $product_ids = array_keys($cart_items_sanitized);
        $in_list = implode(',', array_map('intval', $product_ids));
        
        // Lấy lại thông tin sản phẩm trong transaction
        $sql_products = "SELECT product_id, tensanpham, gia, tonkho FROM sanpham WHERE product_id IN ($in_list) FOR UPDATE";
        if (!$result_products = $conn->query($sql_products)) {
            throw new Exception("Lỗi truy vấn sản phẩm: " . $conn->error);
        }

        $inventory_check = [];
        while ($row = $result_products->fetch_assoc()) {
            $inventory_check[(int)$row['product_id']] = $row;
        }

        $total_price_sub = 0.0;
        $order_details_data = [];
        foreach ($cart_items_sanitized as $pid => $qty) {
            if (!isset($inventory_check[$pid])) {
                throw new Exception("Sản phẩm ID: $pid không tồn tại trong hệ thống.");
            }
            $product_info = $inventory_check[$pid];
            $tonkho = (int)$product_info['tonkho'];

            if ($qty <= 0 || $qty > $tonkho) {
                throw new Exception("Lỗi số lượng: Sản phẩm **{$product_info['tensanpham']}** chỉ còn **{$tonkho}** trong kho. Yêu cầu: {$qty}.");
            }

            $price = (float)$product_info['gia'];
            $sub_total = $price * $qty;
            $total_price_sub += $sub_total;

            $order_details_data[] = [
                'product_id' => $pid,
                'soluong'    => $qty,
                'gia'        => $price
            ];
        }

        // CẦN TÍNH LẠI GRAND_TOTAL TRONG TRANSACTION (Sử dụng các biến đã tính ở ngoài để đảm bảo tính nhất quán)
        $voucher_id_post = $voucher_id; // Lấy voucher_id đã tính ở ngoài
        $discount_post = $discount;     // Lấy discount đã tính ở ngoài

        $grand_total_post = $total_price_sub + $shipping_fee - $discount_post;
        if ($grand_total_post < 0) $grand_total_post = 0;


        // --- 2. LƯU ĐƠN HÀNG (BỔ SUNG VOUCHER_ID VÀ GIAM_GIA) ---
        $initial_status = 1;
        $stmt_order = null;

        if (is_null($voucher_id_post)) {
            // TRƯỜNG HỢP A: KHÔNG CÓ MÃ GIẢM GIÁ (4 placeholders)
            $sql_order = "INSERT INTO donhang (user_id, tongtien, trangthai, ngaytao, giam_gia) VALUES (?, ?, ?, NOW(), ?)";
            
            if (!$stmt_order = $conn->prepare($sql_order)) {
                throw new Exception("Lỗi prepare insert order (No Voucher): " . $conn->error);
            }
            // Chuỗi định nghĩa kiểu: i (user_id), d (tongtien), i (trangthai), d (giam_gia) -> Tổng cộng 4
            if (!$stmt_order->bind_param("idid", $user_id, $grand_total_post, $initial_status, $discount_post)) {
                throw new Exception("Lỗi bind_param donhang (No Voucher): " . $stmt_order->error);
            }
        } else {
            // TRƯỜNG HỢP B: CÓ MÃ GIẢM GIÁ (5 placeholders)
            $sql_order = "INSERT INTO donhang (user_id, tongtien, trangthai, ngaytao, voucher_id, giam_gia) VALUES (?, ?, ?, NOW(), ?, ?)";
            
            if (!$stmt_order = $conn->prepare($sql_order)) {
                throw new Exception("Lỗi prepare insert order (With Voucher): " . $conn->error);
            }
            // Chuỗi định nghĩa kiểu: i (user_id), d (tongtien), i (trangthai), i (voucher_id), d (giam_gia) -> Tổng cộng 5
            if (!$stmt_order->bind_param("ididd", $user_id, $grand_total_post, $initial_status, $voucher_id_post, $discount_post)) {
                throw new Exception("Lỗi bind_param donhang (With Voucher): " . $stmt_order->error);
            }
        }
        
        if (!$stmt_order->execute()) {
            throw new Exception("Lỗi insert donhang: " . $stmt_order->error);
        }
        $order_id = $conn->insert_id;
        $stmt_order->close();


        // --- 3. CẬP NHẬT LƯỢT SỬ DỤNG VOUCHER (NẾU CÓ) ---
        if (!is_null($voucher_id_post)) {
            // Chỉ giảm soluong nếu nó KHÔNG NULL (mã có giới hạn)
            $sql_update_voucher = "UPDATE voucher SET luotsudung = luotsudung + 1, soluong = soluong - 1 WHERE voucher_id = ? AND soluong IS NOT NULL";
            if ($stmt_voucher_update = $conn->prepare($sql_update_voucher)) {
                $stmt_voucher_update->bind_param("i", $voucher_id_post);
                if (!$stmt_voucher_update->execute()) {
                    throw new Exception("Lỗi update luotsudung voucher: " . $stmt_voucher_update->error);
                }
                // Nếu soluong là NULL, chỉ update luotsudung
                if ($stmt_voucher_update->affected_rows === 0) {
                    $sql_update_voucher_all = "UPDATE voucher SET luotsudung = luotsudung + 1 WHERE voucher_id = ?";
                    if ($stmt_v_update_all = $conn->prepare($sql_update_voucher_all)) {
                        $stmt_v_update_all->bind_param("i", $voucher_id_post);
                        $stmt_v_update_all->execute();
                        $stmt_v_update_all->close();
                    }
                }
                $stmt_voucher_update->close();
            } else {
                throw new Exception("Lỗi prepare update voucher: " . $conn->error);
            }
        }


        // --- 4. LƯU VẬN CHUYỂN ---
        $sql_shipping = "INSERT INTO vanchuyen (order_id, receiver_name, receiver_phone, receiver_address, notes, trangthai, phuongthuctt, ngaysua) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        if (!$stmt_shipping = $conn->prepare($sql_shipping)) {
            throw new Exception("Lỗi prepare vanchuyen: " . $conn->error);
        }
        $shipping_status = 1;
        // types: i (order_id), s (receiver_name), s (receiver_phone), s (receiver_address), s (notes), i (shipping_status), s (payment_method)
        if (!$stmt_shipping->bind_param("issssis", $order_id, $receiver_name, $receiver_phone, $receiver_address, $notes, $shipping_status, $payment_method)) {
            throw new Exception("Lỗi bind_param vanchuyen: " . $stmt_shipping->error);
        }
        if (!$stmt_shipping->execute()) {
            throw new Exception("Lỗi insert vanchuyen: " . $stmt_shipping->error);
        }
        $stmt_shipping->close();

        // --- 5. LƯU THANH TOÁN ---
        $sql_payment = "INSERT INTO thanhtoan (order_id, phuongthuc, trangthai, ngaythanhtoan) VALUES (?, ?, ?, NOW())";
        if (!$stmt_payment = $conn->prepare($sql_payment)) {
            throw new Exception("Lỗi prepare thanhtoan: " . $conn->error);
        }
        $payment_status = 1;
        if (!$stmt_payment->bind_param("isi", $order_id, $payment_method, $payment_status)) {
            throw new Exception("Lỗi bind_param thanhtoan: " . $stmt_payment->error);
        }
        if (!$stmt_payment->execute()) {
            throw new Exception("Lỗi insert thanhtoan: " . $stmt_payment->error);
        }
        $stmt_payment->close();

        // --- 6. LƯU CHI TIẾT ĐƠN HÀNG & CẬP NHẬT TỒN KHO ---
        $sql_detail = "INSERT INTO chitietdonhang (order_id, product_id, soluong, gia) VALUES (?, ?, ?, ?)";
        if (!$stmt_detail = $conn->prepare($sql_detail)) {
            throw new Exception("Lỗi prepare chitietdonhang: " . $conn->error);
        }

        $sql_update_stock = "UPDATE sanpham SET tonkho = tonkho - ? WHERE product_id = ?";
        if (!$stmt_stock = $conn->prepare($sql_update_stock)) {
            throw new Exception("Lỗi prepare update stock: " . $conn->error);
        }

        foreach ($order_details_data as $item) {
            $pid = (int)$item['product_id'];
            $qty = (int)$item['soluong'];
            $price = (float)$item['gia'];

            if (!$stmt_detail->bind_param("iiid", $order_id, $pid, $qty, $price)) {
                throw new Exception("Lỗi bind_param chitietdonhang: " . $stmt_detail->error);
            }
            if (!$stmt_detail->execute()) {
                throw new Exception("Lỗi insert chitietdonhang: " . $stmt_detail->error);
            }

            if (!$stmt_stock->bind_param("ii", $qty, $pid)) {
                throw new Exception("Lỗi bind_param update stock: " . $stmt_stock->error);
            }
            if (!$stmt_stock->execute()) {
                throw new Exception("Lỗi update tonkho: " . $stmt_stock->error);
            }
        }

        $stmt_detail->close();
        $stmt_stock->close();


        // --- 7. LƯU LỊCH SỬ TRẠNG THÁI (BỔ SUNG) ---
        // $initial_status = 1 (đã định nghĩa ở bước 2)
        $sql_history_insert = "
            INSERT INTO lichsudonhang (order_id, trangthai, ngaycapnhat) 
            VALUES (?, ?, NOW())
        ";

        if (!$stmt_history = $conn->prepare($sql_history_insert)) {
            throw new Exception("Lỗi prepare insert lichsudonhang: " . $conn->error);
        }
        
        // types: i (order_id), i (initial_status)
        if (!$stmt_history->bind_param("ii", $order_id, $initial_status)) {
            throw new Exception("Lỗi bind_param lichsudonhang: " . $stmt_history->error);
        }

        if (!$stmt_history->execute()) {
            throw new Exception("Lỗi insert lichsudonhang: " . $stmt_history->error);
        }
        $stmt_history->close();


        // --- 8. COMMIT ---
        $conn->commit();

        // Lưu thông tin để hiển thị order success
        $_SESSION['last_order'] = [
            'order_id' => $order_id,
            'total_amount' => $grand_total_post,
            'payment_method' => $payment_method,
            'customer_name' => $receiver_name,
            'customer_address' => $receiver_address,
            'customer_phone' => $receiver_phone,
            'order_date' => date("d/m/Y H:i:s")
        ];

        // Xóa giỏ & Voucher trong Session
        unset($_SESSION['cart']);
        unset($_SESSION['voucher_code']);
        unset($_SESSION['discount_amount']);
        unset($_SESSION['voucher_giam_value']);

        // Redirect theo phương thức thanh toán
        if ($payment_method === "Transfer") {
            header("Location: payment-guide.php");
        } else {
            header("Location: order-success.php");
        }
        exit();

    } catch (Exception $e) {
        // rollback và báo lỗi
        $conn->rollback();
        // ghi log nếu debug
        if ($DEBUG) {
            error_log("Checkout error: " . $e->getMessage());
        }
        $_SESSION['error_message'] = "Lỗi đặt hàng: " . $e->getMessage();
        header("Location: checkout.php");
        exit();
    }
}
// Đóng kết nối DB (Chạy cuối cùng)
if (isset($conn) && $conn) {
    $conn->close();
}
// ... (HTML hiển thị) ...
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Thanh toán - LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

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
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <?php include 'navbar.php'; ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            // Lấy tên file từ href của mỗi đường dẫn
            const linkPath = link.getAttribute('href').split('/').pop();

            // Kiểm tra xem đường dẫn hiện tại có khớp với href của link không
            if (currentPath.endsWith(linkPath) && linkPath !== '') {
                // Nếu khớp, thêm lớp 'active'
                link.classList.add('active');
            } else {
                // Nếu không, đảm bảo là không có lớp 'active'
                link.classList.remove('active');
            }

            // Xử lý riêng cho dropdown để tránh xung đột
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown && currentPath.includes(linkPath)) {
                parentDropdown.querySelector('.nav-link.dropdown-toggle').classList.add('active');
            }
        });

        // Xóa lớp active ban đầu khỏi trang chủ, vì nó được thêm cứng trong HTML
        const homeLink = document.querySelector('.navbar-nav .nav-link[href="index.php"]');
        if (homeLink) {
            homeLink.classList.remove('active');
        }
    });
    </script>
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Thanh toán</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="./index.php" style = "color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Thanh toán</li>
        </ol>
    </div>
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-4">Chi tiết hóa đơn</h1>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <form action="checkout.php" method="POST">
                <div class="row g-5">
                    
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <h3>Thông tin nhận hàng</h3>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-item w-100">
                                    <label class="form-label my-3">Họ và Tên người nhận <sup>*</sup></label>
                                    <input type="text" class="form-control" name="receiver_name" required
                                           value="<?php echo htmlspecialchars($user_info['hoten'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-item">
                            <label class="form-label my-3">Địa chỉ nhận hàng <sup>*</sup></label>
                            <input type="text" class="form-control" name="receiver_address" required
                                   value="<?php echo htmlspecialchars($user_info['diachi'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-item">
                            <label class="form-label my-3">Điện thoại <sup>*</sup></label>
                            <input type="tel" class="form-control" name="receiver_phone" required
                                   value="<?php echo htmlspecialchars($user_info['dienthoai'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-item">
                            <label class="form-label my-3">Email</label>
                            <input type="email" class="form-control" disabled
                                   value="<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>">
                        </div>
                        
                        <hr>
                        
                        <div class="form-item">
                            <label class="form-label my-3">Ghi chú (Tùy chọn)</label>
                        </div>
                        <div class="form-item">
                            <textarea name="notes" class="form-control" rows="5"
                                placeholder="Ghi chú về đơn hàng, ví dụ: thời gian giao hàng, yêu cầu đặc biệt..."></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Sản phẩm</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Giá</th>
                                        <th scope="col">SL</th>
                                        <th scope="col">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($checkout_products as $item): ?>
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center mt-2">
                                                <img src="../Admin/uploads/<?php echo htmlspecialchars($item['img']); ?>" class="img-fluid"
                                                    style="width: 100px; height: 60px; object-fit: cover;" alt="">
                                            </div>
                                        </th>
                                        <td class="py-4"><?php echo htmlspecialchars($item['tensanpham']); ?></td>
                                        <td class="py-4"><?php echo formatVND($item['gia']); ?></td>
                                        <td class="py-4"><?php echo $item['soluong']; ?></td>
                                        <td class="py-4 text-primary"><?php echo formatVND($item['tong']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>

                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark">Tạm tính</p>
                                        </td>
                                        <td class="py-3 text-primary">
                                            <p class="mb-0"><?php echo formatVND($total_price_sub); ?></p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark text-uppercase">Phí Vận chuyển</p>
                                        </td>
                                        <td class="py-3 text-primary">
                                            <p class="mb-0"><?php echo formatVND($shipping_fee); ?></p>
                                        </td>
                                    </tr>
                                    <?php if ($discount > 0): ?>
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-success text-uppercase">Giảm giá</p>
                                        </td>
                                        <td class="py-3 text-success">
                                            <p class="mb-0">- <?php echo formatVND($discount); ?></p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark text-uppercase">TỔNG CỘNG</p>
                                        </td>
                                        <td class="py-3 text-success h5">
                                            <p class="mb-0" style = "font-family: 'Arial';"><?php echo formatVND($grand_total); ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="py-4">
                            <h4>Phương thức thanh toán</h4>
                            
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Delivery-1"
                                            name="payment_method" value="COD" checked>
                                        <label class="form-check-label" for="Delivery-1">Thanh toán khi nhận hàng (COD)</label>
                                    </div>
                                    <p class="text-start text-dark">Thanh toán bằng tiền mặt khi nhận hàng.</p>
                                </div>
                            </div>
                            
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Transfer-1"
                                            name="payment_method" value="Transfer">
                                        <label class="form-check-label" for="Transfer-1">Chuyển khoản ngân hàng</label>
                                    </div>
                                    <p class="text-start text-dark">Vui lòng chuyển khoản trực tiếp vào tài khoản ngân hàng của chúng tôi. Đơn hàng sẽ được xử lý sau khi chúng tôi nhận được tiền.</p>
                                </div>
                            </div>

                        </div>

                        <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <input type="hidden" name="place_order" value="1">
                            <button type="submit"
                                class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">
                                Đặt hàng
                            </button>
                        </div>
                        
                    </div>
                </div>
            </form>
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
</body>

</html>