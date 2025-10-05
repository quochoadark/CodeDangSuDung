-<?php
session_start();
require_once '../Database/Database.php'; 

// Hàm định dạng tiền tệ (formatVND)
function formatVND($number) {
    return number_format($number, 0, ',', '.') . ' VNĐ';
}

// Kiểm tra xem có dữ liệu đơn hàng trong session không
if (!isset($_SESSION['last_order'])) {
    // Nếu không có, chuyển hướng về trang chủ
    header("Location: index.php");
    exit();
}

$order_data = $_SESSION['last_order'];

// Kiểm tra phương thức thanh toán có phải là chuyển khoản không
if ($order_data['payment_method'] !== "Transfer") {
    // Nếu không phải, chuyển hướng về trang thành công thông thường
    header("Location: order-success.php");
    exit();
}

// Gán dữ liệu vào các biến để sử dụng
$order_id = $order_data['order_id'];
$total_amount = $order_data['total_amount'];
$customer_name = $order_data['customer_name'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng dẫn Thanh toán - Đơn hàng <?php echo $order_id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .guide-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            background-color: #ffffff;
        }
        .qr-code-box {
            text-align: center;
            border: 2px dashed #007bff;
            padding: 20px;
            margin-top: 15px;
            border-radius: 8px;
        }
        .qr-code-box img {
            max-width: 250px;
            height: auto;
        }
        .bank-info {
            background-color: #f8f9fa;
            border-radius: 6px;
            padding: 15px;
        }
    </style>
</head>
<body>

<div class="guide-container">
    <div class="text-center mb-4">
        <h1 class="text-primary">HƯỚNG DẪN THANH TOÁN</h1>
        <p class="lead">Vui lòng chuyển khoản **<?php echo formatVND($total_amount); ?>** để hoàn tất đơn hàng.</p>
        <p class="mb-0">**Mã đơn hàng của bạn:** <span class="text-danger fw-bold fs-5"><?php echo $order_id; ?></span></p>
        <p class="text-muted fst-italic">**Nội dung chuyển khoản:** **CK <?php echo $order_id; ?>** (Rất quan trọng!)</p>
    </div>

    <hr>

    <div class="row mt-4">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white fw-bold">
                    <img src="https://i.imgur.com/uR7Qh42.png" alt="Vietcombank Logo" class="me-2" style="max-height: 25px;">
                    Chuyển khoản Vietcombank
                </div>
                <div class="card-body bank-info">
                    <p>Chủ tài khoản: **NGUYEN VAN A**</p>
                    <p>Số tài khoản: **0011001234567**</p>
                    <div class="qr-code-box">
                        <small class="d-block mb-2">Quét mã QR để chuyển khoản nhanh</small>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-header bg-info text-white fw-bold">
                    <img src="https://i.imgur.com/t4xW6oH.png" alt="Momo/ZaloPay Logo" class="me-2" style="max-height: 25px;">
                    Ví điện tử (Momo / ZaloPay)
                </div>
                <div class="card-body bank-info">
                    <p>Tên tài khoản: **NGUYEN VAN A**</p>
                    <p>Số điện thoại: **090xxxxxxx**</p>
                    <div class="qr-code-box">
                        <small class="d-block mb-2">Quét mã QR để chuyển khoản nhanh</small>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <p class="text-success fw-bold">Sau khi chuyển khoản, vui lòng bấm vào nút dưới đây:</p>
        <a href="order-success.php" class="btn btn-primary btn-lg">
            <i class="fas fa-check-circle me-2"></i>Tôi đã chuyển khoản
        </a>
    </div>
</div>

</body>
</html>