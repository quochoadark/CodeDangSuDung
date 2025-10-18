<?php
// Tên tệp: View/order-success.php (hoặc View/order-confirmation.php)
// CHỨC NĂNG: Lấy chi tiết đơn hàng từ DB để hiển thị sau khi đặt hàng thành công.

session_start();

function formatVND($amount) {
    $num = intval($amount);
    return number_format($num, 0, ',', '.') . ' VNĐ';
}

// --- 1. GỌI CÁC FILE CẦN THIẾT ---
// Giả định vị trí: .../User/View/order-success.php
require_once '../../Database/Database.php'; 
// 🔥 THÊM CÁC FILE REPOSITORY VÀ SERVICE CẦN THIẾT
require_once '../Repository/CheckoutRepository.php'; 
// CheckoutService cần CartRepository
require_once '../Repository/CartRepository.php'; // 🔥 Bạn cần đảm bảo file này tồn tại!
require_once '../Service/CheckoutService.php'; // Đổi tên file service thành CheckoutService.php

// --- 2. XỬ LÝ ID ĐƠN HÀNG VÀ BẢO MẬT ---
$order_id = $_GET['order_id'] ?? 0;
$user_id = $_SESSION['kh_user_id'] ?? 0;

// Yêu cầu: Đơn hàng phải tồn tại và người dùng phải đăng nhập
if ($order_id == 0 || $user_id == 0) {
    // Chuyển hướng nếu thiếu thông tin
    header("Location: ../index.php"); 
    exit();
}

// --- 3. KHỞI TẠO DB, REPOSITORY VÀ SERVICE ---
$db = new Database(); 
$conn = $db->conn; 

// 🔥 KHỞI TẠO CÁC DEPENDENCY
$orderRepository = new CheckoutRepository($conn); 
// Khởi tạo CartRepository (Dù có thể không dùng trực tiếp ở đây, nhưng CheckoutService cần)
$cartRepository = new CartRepository($conn); 
$checkoutService = new CheckoutService($orderRepository, $cartRepository); 

// Lấy toàn bộ dữ liệu đơn hàng và các chi tiết sản phẩm
// 🔥 GỌI HÀM TỪ SERVICE ĐỂ LẤY DỮ LIỆU
$order_data = $checkoutService->getOrderDetailsForConfirmation($order_id, $user_id); 

// Kiểm tra: Đơn hàng không tồn tại hoặc không thuộc về người dùng hiện tại
if (!$order_data) {
    // Đóng kết nối
    if (isset($conn) && $conn) $conn->close();
    header("Location: index.php");
    exit();
}

// --- 4. GÁN DỮ LIỆU ĐỂ SỬ DỤNG TRONG HTML ---
$order_date = date('H:i:s d/m/Y', strtotime($order_data['ngay_dat'])); // Định dạng lại ngày
// Tong tien da bao gom phi ship trong DB, co the can tinh lai Subtotal de hien thi chi tiet hon (khong bat buoc)
$total_amount = $order_data['tong_tien']; 
$payment_method_code = $order_data['phuong_thuc_tt'];

// Chuyển mã TT (COD/Transfer) thành tên hiển thị
$payment_method = ($payment_method_code == 'Transfer') 
                      ? 'Chuyển khoản Ngân hàng' 
                      : 'Thanh toán khi nhận hàng (Tiền mặt)';

// Thông tin người nhận
$customer_name = $order_data['ten_nguoi_nhan'];
$customer_address = $order_data['dia_chi_nhan'];
$customer_phone = $order_data['sdt_nguoi_nhan'];

// Chi tiết sản phẩm trong đơn hàng
$order_items = $order_data['items'] ?? [];

// Đóng kết nối DB
if (isset($conn) && $conn) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng Thành công - Đơn hàng <?php echo htmlspecialchars($order_id); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* CSS giữ nguyên từ file gốc */
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #ffffff;
            border-top: 5px solid #28a745;
        }

        .icon-success {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        .payment-info-box {
            padding: 20px;
            margin-top: 20px;
            border-radius: 6px;
        }

        .payment-bank {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
        }

        .payment-cod {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
        }

        .bank-details img {
            max-height: 40px;
            margin-right: 10px;
        }
    </style>
</head>

<body>

    <div class="success-container text-center">
        <div class="icon-success">&#10003;</div>
        <h1 class="text-success mb-3">ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p class="lead">Cảm ơn **<?php echo htmlspecialchars($customer_name); ?>** đã tin tưởng chúng tôi. Đơn hàng của bạn đã được ghi nhận.</p>

        <hr>

        <h2 class="text-start">Tóm tắt Đơn hàng</h2>

        <div class="text-start mb-4 p-3 border rounded">
            <p><strong>Mã đơn hàng:</strong> <span class="text-danger fw-bold"><?php echo htmlspecialchars($order_id); ?></span></p>
            <p><strong>Ngày đặt hàng:</strong> <?php echo htmlspecialchars($order_date); ?></p>
            <p><strong>Tổng tiền thanh toán:</strong> <span class="text-danger fw-bold fs-4"><?php echo formatVND($total_amount); ?></span></p>
            <p><strong>Phương thức thanh toán:</strong> <span class="fw-bold"><?php echo htmlspecialchars($payment_method); ?></span></p>
            <p><strong>Giao đến:</strong> <?php echo htmlspecialchars($customer_address); ?> (SĐT: <?php echo htmlspecialchars($customer_phone); ?>)</p>
        </div>

        <h3 class="text-start mt-4">Chi tiết Sản phẩm</h3>
        <div class="table-responsive mb-4">
            <table class="table table-bordered table-hover text-start">
                <thead class="table-light">
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="width: 100px;">Đơn giá</th>
                        <th style="width: 80px;">Số lượng</th>
                        <th style="width: 120px;" class="text-end">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($order_items)): ?>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['ten_san_pham']); ?></td>
                                <td><?php echo formatVND($item['don_gia_da_giam']); ?></td> 
                                <td><?php echo htmlspecialchars($item['so_luong']); ?></td>
                                <td class="text-end fw-bold"><?php echo formatVND($item['thanh_tien']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Không có chi tiết sản phẩm.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php
        // --- PHẦN ẢNH HƯỞNG BỞI PHƯƠNG THỨC THANH TOÁN ---
        if ($payment_method_code == "Transfer") {
            ?>
            <div class="payment-info-box payment-bank">
                <h4 class="text-warning">VUI LÒNG CHUYỂN KHOẢN</h4>
                <p>Để hoàn tất đơn hàng, bạn vui lòng chuyển khoản **<?php echo formatVND($total_amount); ?>** đến một
                    trong các tài khoản sau:</p>

                <div class="bank-details text-start mt-3">
                    <h6 class="fw-bold">1. Vietcombank</h6>
                    <p class="mb-1">**Chủ tài khoản:** CÔNG TY TNHH ABC</p>
                    <p class="mb-1">**Số tài khoản:** **0011001234567**</p>
                    <p class="mb-3">**Nội dung chuyển khoản:** **CK <?php echo htmlspecialchars($order_id); ?>** (Rất
                        quan trọng!)</p>

                    <h6 class="fw-bold">2. Ví điện tử (Momo / ZaloPay)</h6>
                    <p class="mb-1">Quét mã QR (hoặc chuyển đến SĐT): **090xxxxxxx**</p>
                    <p class="mb-1">**Nội dung chuyển khoản:** **CK <?php echo htmlspecialchars($order_id); ?>**</p>
                </div>
                <p class="mt-3 text-muted fst-italic">Đơn hàng sẽ được xác nhận và xử lý sau khi chúng tôi nhận được
                    thanh toán.</p>
            </div>

            <?php
        } else { // Phương thức Thanh toán khi nhận hàng (COD)
            ?>
            <div class="payment-info-box payment-cod">
                <h4 class="text-info">THANH TOÁN KHI NHẬN HÀNG (Tiền mặt)</h4>
                <p>Bạn không cần phải làm gì thêm! Vui lòng chuẩn bị sẵn **<?php echo formatVND($total_amount); ?>** để
                    thanh toán cho nhân viên giao hàng khi nhận sản phẩm.</p>
                <p class="mt-3 fst-italic">Chúng tôi sẽ gọi điện xác nhận trong vòng 24 giờ làm việc. Cảm ơn bạn!</p>
            </div>
            <?php
        }
        ?>

        <hr class="mt-4">

        <div class="mt-4">
            <a href="../index.php" class="btn btn-outline-success me-2">Về Trang chủ</a>
            <a href="../Controller/OrderHistoryController.php" class="btn btn-primary">Xem Lịch sử Đơn hàng</a>
        </div>

    </div>

</body>

</html>