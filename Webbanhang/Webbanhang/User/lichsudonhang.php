<?php
session_start();
// Đảm bảo file Database.php đã khởi tạo biến $conn cho kết nối CSDL
require_once '../Database/Database.php'; 

function formatVND($number) {
    return number_format($number, 0, ',', '.') . ' VNĐ';
}

// 1. KIỂM TRA ĐĂNG NHẬP VÀ KẾT NỐI
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php"); 
    exit();
}

// Kiểm tra kết nối CSDL
if (!isset($conn)) {
    // Tùy chọn: Xử lý lỗi nếu biến $conn chưa được định nghĩa
    // die("Lỗi: Không tìm thấy kết nối cơ sở dữ liệu.");
}

$user_id = $_SESSION['user_id'];
$shipping_fee = 50000; 
$mode = 'list'; 

// 2. XÁC ĐỊNH CHẾ ĐỘ HIỂN THỊ
$order_id = $_GET['id'] ?? 0;
$order_id = intval($order_id);

if ($order_id > 0) {
    $mode = 'details';
}

$orders = [];
$order_info = null;
$order_details = [];
$status_history = [];

if ($mode === 'list') {
    // 3. CHẾ ĐỘ DANH SÁCH: Truy vấn tất cả đơn hàng
    $sql = "SELECT 
                DH.order_id, 
                DH.tongtien, 
                DH.ngaytao, 
                VC.receiver_name,
                TT.ten_trangthai,
                DH.trangthai AS status_id
            FROM 
                donhang DH
            JOIN 
                vanchuyen VC ON DH.order_id = VC.order_id
            JOIN 
                trangthaidonhang TT ON DH.trangthai = TT.trangthai_id
            WHERE 
                DH.user_id = ?
            ORDER BY 
                DH.ngaytao DESC";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result_orders = $stmt->get_result();

        while ($row = $result_orders->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close(); // Đóng statement sau khi hoàn tất truy vấn
    }

} elseif ($mode === 'details') {
    // 4. CHẾ ĐỘ CHI TIẾT: Truy vấn đơn hàng cụ thể

    // 4a. Thông tin tổng quan và vận chuyển
    $sql_info = "SELECT 
                    DH.*, 
                    VC.receiver_name, VC.receiver_phone, VC.receiver_address, VC.notes, 
                    VC.phuongthuctt, TT.ten_trangthai
                FROM 
                    donhang DH
                JOIN 
                    vanchuyen VC ON DH.order_id = VC.order_id
                JOIN 
                    trangthaidonhang TT ON DH.trangthai = TT.trangthai_id
                WHERE 
                    DH.order_id = ? AND DH.user_id = ?";

    $stmt_info = $conn->prepare($sql_info);
    if ($stmt_info) {
        $stmt_info->bind_param("ii", $order_id, $user_id);
        $stmt_info->execute();
        $result_info = $stmt_info->get_result();

        if ($result_info->num_rows > 0) {
            $order_info = $result_info->fetch_assoc();
        } else {
            // Quay lại trang danh sách nếu ID không hợp lệ hoặc không thuộc user này
            header("Location: lichsudonhang.php"); 
            exit();
        }
        $stmt_info->close(); // Đóng statement 4a
    }

    // 4b. Chi tiết sản phẩm
    $sql_details = "SELECT 
                        CT.soluong, 
                        CT.gia AS gia_mua, 
                        SP.tensanpham, 
                        SP.img
                    FROM 
                        chitietdonhang CT
                    JOIN 
                        sanpham SP ON CT.product_id = SP.product_id
                    WHERE 
                        CT.order_id = ?";

    $stmt_details = $conn->prepare($sql_details);
    if ($stmt_details) {
        $stmt_details->bind_param("i", $order_id);
        $stmt_details->execute();
        $result_details = $stmt_details->get_result();

        while ($row = $result_details->fetch_assoc()) {
            $order_details[] = $row;
        }
        $stmt_details->close(); // Đóng statement 4b
    }
    
    // 4c. Lịch sử thay đổi trạng thái
    $sql_history = "SELECT 
                        LS.ngaycapnhat, 
                        TT.ten_trangthai
                    FROM 
                        lichsudonhang LS
                    JOIN 
                        trangthaidonhang TT ON LS.trangthai = TT.trangthai_id
                    WHERE 
                        LS.order_id = ?
                    ORDER BY 
                        LS.ngaycapnhat ASC";

    $stmt_history = $conn->prepare($sql_history);
    if ($stmt_history) {
        $stmt_history->bind_param("i", $order_id);
        $stmt_history->execute();
        $result_history = $stmt_history->get_result();

        while ($row = $result_history->fetch_assoc()) {
            $status_history[] = $row;
        }
        $stmt_history->close(); // Đóng statement 4c
    }
}
// KHÔNG ĐÓNG $conn TẠI ĐÂY! Việc đóng kết nối CSDL sẽ được thực hiện ở cuối trang/hoặc tự động sau khi script kết thúc.
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
    </style>
</head>
<body>

<div class="container py-5">

<?php if ($mode === 'list'): ?>
    <h1 class="mb-4 text-center text-primary"><i class="fa fa-box-archive me-2"></i>Lịch sử Đơn hàng</h1>

    <?php if (empty($orders)): ?>
        <div class="alert alert-info text-center shadow-sm">
            <i class="fa fa-info-circle me-2"></i>Bạn chưa có đơn hàng nào. Hãy bắt đầu mua sắm ngay!
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($orders as $order): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card order-card h-100">
                        <div class="card-header bg-light d-flex justify-content-between">
                            <span class="fw-bold">
                                <?php echo 'DH-' . str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?>
                            </span>
                            <span class="text-muted small"><?php echo date("d/m/Y H:i", strtotime($order['ngaytao'])); ?></span>
                        </div>
                        <div class="card-body">
                            <p class="mb-1"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['receiver_name']); ?></p>
                            <p class="mb-1"><strong>Tổng tiền:</strong> <span class="text-danger"><?php echo formatVND($order['tongtien']); ?></span></p>
                            <p class="mb-3">
                                <span class="badge 
                                <?php 
                                    if ($order['status_id'] == 1) echo 'bg-warning text-dark';
                                    else if ($order['status_id'] == 3) echo 'bg-primary';
                                    else if ($order['status_id'] == 4) echo 'bg-success';
                                    else if ($order['status_id'] == 5) echo 'bg-danger'; 
                                    else echo 'bg-secondary';
                                ?>">
                                <?php echo $order['ten_trangthai']; ?>
                                </span>
                            </p>
                            <a href="lichsudonhang.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fa fa-eye me-1"></i>Xem Chi Tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="mt-5 text-center">
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left me-1"></i>Quay về trang chủ
        </a>
    </div>

<?php elseif ($mode === 'details' && $order_info): ?>

    <?php 
        // Lấy thông tin giảm giá
        $giam_gia = (float)($order_info['giam_gia'] ?? 0); 
    ?>

    <h1 class="mb-4 text-center text-primary">
        <i class="fa fa-file-invoice me-2"></i>
        Chi tiết Đơn hàng <?php echo 'DH-' . str_pad($order_info['order_id'], 6, '0', STR_PAD_LEFT); ?>
    </h1>
        
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Thông tin Người nhận</h5></div>
                <div class="card-body">
                    <p><strong>Trạng thái:</strong> 
                        <span class="badge 
                            <?php 
                                // Logic màu trạng thái chi tiết
                                if ($order_info['trangthai'] == 1) echo 'bg-warning text-dark';
                                else if ($order_info['trangthai'] == 3) echo 'bg-primary';
                                else if ($order_info['trangthai'] == 4) echo 'bg-success';
                                else if ($order_info['trangthai'] == 5) echo 'bg-danger'; 
                                else echo 'bg-secondary';
                            ?>"
                        ><?php echo $order_info['ten_trangthai']; ?></span>
                    </p>
                    <p><strong>Ngày đặt:</strong> <?php echo date("d/m/Y H:i", strtotime($order_info['ngaytao'])); ?></p>
                    <hr>
                    <p><strong>Người nhận:</strong> <?php echo htmlspecialchars($order_info['receiver_name']); ?></p>
                    <p><strong>Điện thoại:</strong> <?php echo htmlspecialchars($order_info['receiver_phone']); ?></p>
                    <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order_info['receiver_address']); ?></p>
                    <p><strong>Ghi chú:</strong> <?php echo !empty($order_info['notes']) ? htmlspecialchars($order_info['notes']) : 'Không có'; ?></p>
                    <p><strong>Thanh toán:</strong> 
                        <?php echo $order_info['phuongthuctt'] == 'COD' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản'; ?>
                    </p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-secondary text-white"><h5 class="mb-0">Lịch sử Trạng thái</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush timeline-list">
                        <?php if (empty($status_history)): ?>
                            <li class="list-group-item">Chưa có cập nhật trạng thái nào.</li>
                        <?php else: ?>
                            <?php foreach ($status_history as $history): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span><?php echo htmlspecialchars($history['ten_trangthai']); ?></span>
                                    <small class="text-muted"><?php echo date("d/m/Y H:i", strtotime($history['ngaycapnhat'])); ?></small>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white"><h5 class="mb-0">Sản phẩm đã mua</h5></div>
                <ul class="list-group list-group-flush">
                    <?php 
                    $subtotal_details = 0;
                    foreach ($order_details as $detail): 
                        $item_total = $detail['soluong'] * $detail['gia_mua'];
                        $subtotal_details += $item_total;
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="../Admin/uploads/<?php echo htmlspecialchars($detail['img']); ?>" class="product-img me-3" alt="">
                            <div>
                                <h6 class="mb-0"><?php echo htmlspecialchars($detail['tensanpham']); ?></h6>
                                <small class="text-muted"><?php echo formatVND($detail['gia_mua']); ?> x <?php echo $detail['soluong']; ?></small>
                            </div>
                        </div>
                        <strong class="text-danger"><?php echo formatVND($item_total); ?></strong>
                    </li>
                    <?php endforeach; ?>

                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <strong>Tổng tiền hàng:</strong>
                            <span><?php echo formatVND($subtotal_details); ?></span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <strong>Phí vận chuyển:</strong>
                            <span><?php echo formatVND($shipping_fee); ?></span>
                        </div>
                        
                        <?php if ($giam_gia > 0): ?>
                        <div class="d-flex justify-content-between text-success fw-bold">
                            <strong>Giảm giá:</strong>
                            <span>- <?php echo formatVND($giam_gia); ?></span> 
                        </div>
                        <?php endif; ?>
                    </li>
                    <li class="list-group-item bg-light">
                        <div class="d-flex justify-content-between">
                            <h4>Tổng cộng:</h4>
                            <h4 class="text-danger"><?php echo formatVND($order_info['tongtien']); ?></h4> 
                        </div>
                    </li>
                </ul>
            </div>
            <a href="lichsudonhang.php" class="btn btn-outline-secondary">
                <i class="fa fa-arrow-left me-1"></i>Trở về Lịch sử Đơn hàng
            </a>
        </div>
    </div>

<?php endif; ?>

</div>
</body>
</html>