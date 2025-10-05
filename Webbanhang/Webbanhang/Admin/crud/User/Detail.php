<?php
// File: Admin/crud/User/Detail.php (Chi tiết Người dùng - SỬA ĐỔI)
// Chỉ cần Controller
require_once __DIR__ . '/../../Controller/UserController.php';

// Khởi tạo Controller và gọi hàm detail()
$controller = new UserController();
$data = $controller->detail(); // Lấy dữ liệu thông qua Controller

$user_info = $data['user_info'] ?? null;
$tiers = $data['tiers'] ?? []; 
$error_message = $data['error'] ?? null;

// Nếu có lỗi (ví dụ: thiếu ID) và không có user_info, dừng lại
if ($error_message && !$user_info) {
    die(htmlspecialchars($error_message));
}

// Mapping Tiers (Hạng khách hàng)
$tier_map = [];
foreach ($tiers as $tier) {
    // Chắc chắn rằng key của tier là 'tier_id' và value là 'tenhang'
    $tier_map[$tier['tier_id']] = $tier['tenhang'];
}

if (!$user_info):
    die("Không có dữ liệu người dùng để hiển thị."); 
endif;

// Tạo các biến trung gian để clean code HTML (Tùy chọn, nhưng được khuyến khích)
$hoten = htmlspecialchars($user_info['hoten'] ?? 'N/A');
$email = htmlspecialchars($user_info['email'] ?? 'N/A');
$dienthoai = htmlspecialchars($user_info['dienthoai'] ?? 'N/A');
$diachi = htmlspecialchars($user_info['diachi'] ?? 'N/A');
$tier_id = (int)($user_info['tier_id'] ?? 0);
$trangthai = (int)($user_info['trangthai'] ?? 0);

$ten_hang = htmlspecialchars($tier_map[$tier_id] ?? 'Không xác định');
$trangthai_text = $trangthai === 1 ? 'Hoạt động' : 'Khóa';

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi Tiết Người Dùng</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        body { background: #f5f7fa; }
        .detail-card {
            max-width: 500px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        .detail-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        .detail-info {
            font-size: 1rem;
        }
        .detail-info p {
            margin-bottom: 12px;
            padding: 8px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .detail-info p:last-child {
            border-bottom: none;
        }
        .detail-info strong {
            display: inline-block;
            width: 130px;
            color: #2d3748;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 24px;
            color: #5a6a85;
            text-decoration: none;
            font-size: 0.98rem;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #16a34a;
        }
    </style>
</head>
<body>
    <div class="detail-card">
        <h2>Thông Tin Chi Tiết Người Dùng</h2>

        <?php if ($user_info): ?>
        <div class="detail-info">
            <p><strong>ID:</strong> <?= htmlspecialchars($user_info['user_id']) ?></p>
            <p><strong>Họ và Tên:</strong> <?= $hoten ?></p>
            <p><strong>Email:</strong> <?= $email ?></p>
            <p><strong>Tier:</strong> <?= $ten_hang ?></p>
            <p><strong>Điện thoại:</strong> <?= $dienthoai ?></p>
            <p><strong>Địa chỉ:</strong> <?= $diachi ?></p>
            <p><strong>Trạng thái:</strong> <?= $trangthai_text ?></p>
        </div>
        <?php else: ?>
        <p class="text-center">Không có dữ liệu để hiển thị.</p>
        <?php endif; ?>

        <a href="../../index.php?page=khachhang" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng người dùng
        </a>
    </div>
</body>
</html>