<?php
// BẮT BUỘC: Bắt đầu session ở đầu file
session_start();

require_once '../Database/Database.php'; 

// --- LẤY DỮ LIỆU TỪ SESSION VÀ KIỂM TRA ---
if (!isset($_SESSION['last_order'])) {
    // Nếu không có dữ liệu đơn hàng trong session, chuyển hướng về trang chủ
    header("Location: index.php");
    exit();
}

// Gán dữ liệu từ Session vào các biến PHP để sử dụng trong HTML
$order_data = $_SESSION['last_order'];
$order_id = $order_data['order_id'];
$order_date = $order_data['order_date'];
$total_amount = $order_data['total_amount'];
$payment_method = $order_data['payment_method'];
$customer_name = $order_data['customer_name'];
$customer_address = $order_data['customer_address'];
$customer_phone = $order_data['customer_phone'];

// Quan trọng: Xóa dữ liệu khỏi session sau khi đã hiển thị để tránh
// việc tải lại trang/mở lại trang thành công cũ
unset($_SESSION['last_order']); 

// Hàm hiển thị số tiền dưới dạng VNĐ
function format_vnd($amount) {
    return number_format($amount, 0, ',', '.') . ' VNĐ';
}
?>
<!DOCTYPE html>
<html lang="vi">
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt hàng Thành công - Đơn hàng <?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
            background-color: #fff3cd; /* Màu vàng nhạt */
            border: 1px solid #ffeeba;
        }
        .payment-cod {
            background-color: #d1ecf1; /* Màu xanh nhạt */
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
    <div class="icon-success">&#10003;</div> <h1 class="text-success mb-3">ĐẶT HÀNG THÀNH CÔNG!</h1>
    <p class="lead">Cảm ơn **<?php echo $customer_name; ?>** đã tin tưởng chúng tôi. Đơn hàng của bạn đã được ghi nhận.</p>

    <hr>

    ## Tóm tắt Đơn hàng

    <div class="text-start mb-4 p-3 border rounded">
        <p><strong>Mã đơn hàng:</strong> <span class="text-danger fw-bold"><?php echo $order_id; ?></span></p>
        <p><strong>Ngày đặt hàng:</strong> <?php echo $order_date; ?></p>
        <p><strong>Tổng tiền thanh toán:</strong> <span class="text-danger fw-bold fs-4"><?php echo format_vnd($total_amount); ?></span></p>
        <p><strong>Phương thức thanh toán:</strong> <span class="fw-bold"><?php echo $payment_method; ?></span></p>
        <p><strong>Giao đến:</strong> <?php echo $customer_address; ?> (SĐT: <?php echo $customer_phone; ?>)</p>
    </div>

    <?php
    // --- PHẦN ẢNH HƯỞNG BỞI PHƯƠNG THỨC THANH TOÁN ---
    if ($payment_method == "Chuyển khoản Ngân hàng") {
    ?>
    <div class="payment-info-box payment-bank">
        <h4 class="text-warning">VUI LÒNG CHUYỂN KHOẢN</h4>
        <p>Để hoàn tất đơn hàng, bạn vui lòng chuyển khoản **<?php echo format_vnd($total_amount); ?>** đến một trong các tài khoản sau:</p>
        
        <div class="bank-details text-start mt-3">
            <h6 class="fw-bold">1. Vietcombank</h6>
            <p class="mb-1">**Chủ tài khoản:** CÔNG TY TNHH ABC</p>
            <p class="mb-1">**Số tài khoản:** **0011001234567**</p>
            <p class="mb-3">**Nội dung chuyển khoản:** **CK <?php echo $order_id; ?>** (Rất quan trọng!)</p>

            <h6 class="fw-bold">2. Ví điện tử (Momo / ZaloPay)</h6>
            <p class="mb-1">Quét mã QR (hoặc chuyển đến SĐT): **090xxxxxxx**</p>
            <p class="mb-1">**Nội dung chuyển khoản:** **CK <?php echo $order_id; ?>**</p>
        </div>
        <p class="mt-3 text-muted fst-italic">Đơn hàng sẽ được xác nhận và xử lý sau khi chúng tôi nhận được thanh toán.</p>
    </div>

    <?php
    } else { // Phương thức Thanh toán khi nhận hàng (COD)
    ?>
    <div class="payment-info-box payment-cod">
        <h4 class="text-info">THANH TOÁN KHI NHẬN HÀNG (COD)</h4>
        <p>Bạn không cần phải làm gì thêm! Vui lòng chuẩn bị sẵn **<?php echo format_vnd($total_amount); ?>** để thanh toán cho nhân viên giao hàng khi nhận sản phẩm.</p>
        <p class="mt-3 fst-italic">Chúng tôi sẽ gọi điện xác nhận trong vòng 24 giờ làm việc. Cảm ơn bạn!</p>
    </div>
    <?php
    }
    ?>
    
    <hr class="mt-4">
    
    <div class="mt-4">
        <a href="index.php" class="btn btn-outline-success me-2">Về Trang chủ</a>
        <a href="lichsudonhang.php" class="btn btn-primary">Xem Lịch sử Đơn hàng</a>
    </div>

</div>

</body>
</html>