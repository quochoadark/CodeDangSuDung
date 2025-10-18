<?php
// File: View/HistoryOrder.php (Đã thêm chức năng HỦY ĐƠN HÀNG và logic Timeline)

$data = $data ?? []; 
$mode = $data['mode'] ?? 'list'; 
$orders = $data['orders'] ?? [];
$order_info = $data['order_info'] ?? null;
$order_details = $data['order_details'] ?? [];
$status_history = $data['status_history'] ?? [];
$shipping_fee = $data['shipping_fee'] ?? 50000;
$error_message = $data['error_message'] ?? '';
$status_flow = $data['status_flow'] ?? []; 
$cancel_message = $data['cancel_message'] ?? null; 

// Hàm format tiền tệ (Đảm bảo hàm này có thể được gọi)
if (!function_exists('formatVND')) {
    function formatVND($number) {
        $num = intval(round((float)$number)); 
        return number_format($num, 0, ',', '.') . ' ₫';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch sử Đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .order-card:hover {
            transform: scale(1.01);
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        /* Bổ sung Style cho Thanh Tiến Trình (Sẽ được định nghĩa chi tiết trong OrderTracking.php) */
        .progress-tracker {
            position: relative;
            padding-bottom: 50px; 
        }
        .step {
            position: relative;
            flex: 1;
            z-index: 3;
            min-width: 100px;
        }
        .step-icon-wrap {
            transition: all 0.3s ease;
        }
        .timeline-list li::before {
            content: "•";
            color: #0d6efd;
            font-weight: bold;
            display: inline-block;
            width: 1rem;
        }
        .product-img {
            width: 60px; height: 60px; object-fit: cover; border-radius: 8px;
        }
        .price-original {
            text-decoration: line-through;
            color: #888;
            font-size: 0.9em;
        }
        .text-discount {
            color: #d9534f !important; 
            font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <?php 
        // HIỂN THỊ THÔNG BÁO HỦY (NEW)
        if ($cancel_message): 
            $alert_class = (strpos($cancel_message, 'Lỗi') !== false || strpos($cancel_message, 'không thể hủy') !== false) ? 'alert-danger' : 'alert-success';
    ?>
        <div class="alert <?= $alert_class ?> text-center shadow-sm">
            <i class="fa fa-info-circle me-2"></i><?php echo htmlspecialchars($cancel_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger text-center shadow-sm">
            <i class="fa fa-times-circle me-2"></i><?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($mode === 'list'): ?>
        
        <div class="mb-4">
            <a href="../index.php" class="btn btn-outline-primary">
                <i class="fa fa-home me-1"></i> Quay lại Trang chủ
            </a>
        </div>
        <h1 class="mb-4 text-center text-primary"><i class="fa fa-box-archive me-2"></i>Lịch sử Đơn hàng</h1>

        <?php if (empty($orders)): ?>
            <div class="alert alert-info text-center shadow-sm">
                <i class="fa fa-info-circle me-2"></i>Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($orders as $order): ?>
                    <?php 
                        $status_id = $order['status_id'] ?? 0;
                        $is_cancellable = $status_id == 1; // Chỉ cho hủy nếu là Chờ xác nhận (ID 1)
                        $is_canceled = $status_id == 5; // Đã hủy (ID 5)
                    ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card order-card h-100">
                            <div class="card-header bg-light d-flex justify-content-between">
                                <span class="fw-bold">
                                    <?php echo 'DH-' . str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                                </span>
                                <span class="text-muted small"><?php echo date("d/m/Y H:i", strtotime($order['ngaytao'])); ?></span>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['receiver_name'] ?? 'N/A'); ?></p>
                                <p class="mb-1"><strong>Tổng tiền:</strong> <span class="text-danger"><?php echo formatVND($order['tongtien']); ?></span></p>
                                <p class="mb-3">
                                    <span class="badge 
                                    <?php 
                                        if ($status_id == 1) echo 'bg-warning text-dark';
                                        else if ($status_id == 3) echo 'bg-primary';
                                        else if ($status_id == 4) echo 'bg-success';
                                        else if ($status_id == 5) echo 'bg-danger'; 
                                        else if ($status_id == 6) echo 'bg-info text-dark';
                                        else echo 'bg-secondary';
                                    ?>">
                                        <?php echo $order['ten_trangthai'] ?? 'Không rõ'; ?>
                                    </span>
                                </p>

                                <div class="d-flex justify-content-between gap-2">
                                    <?php if ($is_canceled): ?>
                                        <button class="btn btn-sm btn-danger w-100" disabled>
                                            <i class="fa fa-times-circle me-1"></i>Đã Bị Hủy
                                        </button>
                                    <?php else: ?>
                                        <a href="OrderHistoryController.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary w-50">
                                            <i class="fa fa-eye me-1"></i>Xem Chi Tiết
                                        </a>

                                        <?php if ($is_cancellable): ?>
                                            <button 
                                                class="btn btn-sm btn-outline-danger w-50" 
                                                onclick="confirmCancel(<?php echo $order['order_id']; ?>)">
                                                <i class="fa fa-ban me-1"></i>Hủy Đơn Hàng
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-secondary w-50" disabled>
                                                <i class="fa fa-lock me-1"></i>Không thể hủy
                                            </button>
                                        <?php endif; ?>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <script>
            function confirmCancel(orderId) {
                if (confirm('Bạn có chắc chắn muốn hủy đơn hàng #' + orderId + ' này không? Hành động này không thể hoàn tác.')) {
                    // Gọi đến Controller với action=cancel
                    window.location.href = 'OrderHistoryController.php?action=cancel&order_id=' + orderId;
                }
            }
        </script>

    <?php elseif ($mode === 'details' && $order_info): ?>

        <?php 
            // KHAI BÁO BIẾN TÍNH TOÁN (Giữ nguyên logic của bạn)
            $giam_gia_voucher = (float)($order_info['giam_gia_voucher'] ?? 0); 
            $shipping_fee = (float)($shipping_fee ?? 50000); 
            
            $total_item_discount = 0;
            $subtotal_at_original_price = 0; 
            $subtotal_at_final_price = 0; 

            foreach ($order_details as $detail) {
                $gia_goc = (float)($detail['gia_goc'] ?? 0); 
                $gia_mua = (float)($detail['gia_mua'] ?? 0);
                $soluong = (int)($detail['soluong'] ?? 0);
                $giam_gia_unit = (float)($detail['giam_gia_sp_unit'] ?? 0);
                
                $subtotal_at_original_price += $gia_goc * $soluong;
                $subtotal_at_final_price += $gia_mua * $soluong;
                $total_item_discount += $giam_gia_unit * $soluong;
            }
        ?>
        
        <div class="mb-4">
            <a href="../index.php" class="btn btn-outline-primary">
                <i class="fa fa-home me-1"></i> Quay lại Trang chủ
            </a>
        </div>
        <h1 class="mb-4 text-center text-primary">
            <i class="fa fa-file-invoice me-2"></i>
            Chi tiết Đơn hàng <?php echo 'DH-' . str_pad($order_info['order_id'], 6, '0', STR_PAD_LEFT); ?>
        </h1>

        <?php 
         require_once 'OrderTracking.php';
        ?>
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0">Thông tin Người nhận</h5></div>
                    <div class="card-body">
                        <p><strong>Trạng thái:</strong> 
                            <span class="badge 
                                <?php 
                                    $status_id = $order_info['trangthai'] ?? 0;
                                    if ($status_id == 1) echo 'bg-warning text-dark';
                                    else if ($status_id == 3) echo 'bg-primary';
                                    else if ($status_id == 4) echo 'bg-success';
                                    else if ($status_id == 5) echo 'bg-danger'; 
                                    else if ($status_id == 6) echo 'bg-info text-dark';
                                    else echo 'bg-secondary';
                                ?>"
                            ><?php echo $order_info['ten_trangthai'] ?? 'N/A'; ?></span>
                        </p>
                        <p><strong>Ngày đặt:</strong> <?php echo date("d/m/Y H:i", strtotime($order_info['ngaytao'])); ?></p>
                        <hr>
                        <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order_info['receiver_name'] ?? ''); ?></p>
                        <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order_info['receiver_phone'] ?? ''); ?></p>
                        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order_info['receiver_address'] ?? ''); ?></p>
                        <p><strong>Ghi chú:</strong> <?php echo !empty($order_info['notes']) ? htmlspecialchars($order_info['notes']) : 'Không có'; ?></p>
                        <p><strong>Thanh toán:</strong> 
                            <?php echo "Tiền mặt"?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white"><h5 class="mb-0">Sản phẩm đã mua</h5></div>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($order_details as $detail): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="../../Admin/uploads/<?php echo htmlspecialchars($detail['img'] ?? ''); ?>" class="product-img me-3" alt="Sản phẩm">
                                <div>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($detail['tensanpham'] ?? 'Sản phẩm không rõ'); ?></h6>
                                    <small class="text-muted">
                                        <?php 
                                            $giam_gia_unit = (float)($detail['giam_gia_sp_unit'] ?? 0);
                                        ?>
                                        <?php if ($giam_gia_unit > 0): ?>
                                            <span class="price-original"><?php echo formatVND($detail['gia_goc'] ?? 0); ?></span>
                                            <span class="text-discount"><?php echo formatVND($detail['gia_mua'] ?? 0); ?></span>
                                        <?php else: ?>
                                            <?php echo formatVND($detail['gia_mua'] ?? 0); ?>
                                        <?php endif; ?>
                                             x <?php echo $detail['soluong'] ?? 0; ?>
                                    </small>
                                </div>
                            </div>
                            <strong class="text-danger"><?php echo formatVND($detail['item_total'] ?? 0); ?></strong>
                        </li>
                        <?php endforeach; ?>

                        <li class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <strong>Tổng tiền hàng (Giá gốc):</strong>
                                <span><?php echo formatVND($subtotal_at_original_price); ?></span>
                            </div>
                            
                            <?php if ($total_item_discount > 0): ?>
                            <div class="d-flex justify-content-between text-discount">
                                <strong>Giảm giá theo sản phẩm:</strong>
                                <span>- <?php echo formatVND($total_item_discount); ?></span> 
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <strong>Tổng tiền hàng (Giá mua):</strong>
                                <span><?php echo formatVND($subtotal_at_final_price); ?></span> 
                            </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between">
                                <strong>Phí vận chuyển:</strong>
                                <span>50.000 đ</span>
                            </div>
                            
                            <?php if ($giam_gia_voucher > 0): ?>
                            <div class="d-flex justify-content-between text-success fw-bold">
                                <strong>Giảm giá Voucher:</strong>
                                <span>- <?php echo formatVND($giam_gia_voucher); ?></span> 
                            </div>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item bg-light">
                            <div class="d-flex justify-content-between">
                                <h4>Tổng cộng:</h4>
                                <h4 class="text-danger">
                                    <?php 
                                        echo formatVND($order_info['tongtien'] ?? 0); 
                                    ?>
                                </h4> 
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="d-flex justify-content-end gap-2">
                    <a href="OrderHistoryController.php" class="btn btn-outline-secondary">
                        <i class="fa fa-arrow-left me-1"></i>Trở về Lịch sử Đơn hàng
                    </a>
                    <a href="../../index.php" class="btn btn-primary">
                        <i class="fa fa-home me-1"></i> Trang chủ
                    </a>
                </div>
                </div>
        </div>

    <?php endif; ?>

</div>
</body>
</html>