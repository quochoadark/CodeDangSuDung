<?php
// File: Admin/crud/User/Detail.php (Chi tiết Người dùng)

// Chỉ cần Controller
require_once __DIR__ . '/../../Controller/UserController.php';

// Khởi tạo Controller và gọi hàm detail()
$controller = new UserController();
$data = $controller->detail(); // Lấy dữ liệu thông qua Controller

$user_info = $data['user_info'] ?? null;
$tiers = $data['tiers'] ?? []; 
$error_message = $data['error'] ?? null;

// --- XỬ LÝ LỖI SỚM ---
if ($error_message && !$user_info) {
    die('<div class="user-detail-card error-card"><h2>Lỗi</h2><p>' . htmlspecialchars($error_message) . '</p><a href="../../index.php?page=khachhang" class="back-link">Quay lại</a></div>');
}

// Mapping Tiers (Hạng khách hàng)
$tier_map = [];
foreach ($tiers as $tier) {
    // Chắc chắn rằng key của tier là 'tier_id' và value là 'tenhang'
    $tier_map[$tier['tier_id']] = $tier['tenhang'];
}

if (!$user_info):
    die('<div class="user-detail-card error-card"><h2>Lỗi</h2><p>Không tìm thấy người dùng với ID đã cung cấp.</p><a href="../../index.php?page=khachhang" class="back-link">Quay lại</a></div>'); 
endif;

// Tạo các biến trung gian để clean code HTML
$user_id = htmlspecialchars($user_info['user_id'] ?? 'N/A');
$hoten = htmlspecialchars($user_info['hoten'] ?? 'N/A');
$email = htmlspecialchars($user_info['email'] ?? 'N/A');
$dienthoai = htmlspecialchars($user_info['dienthoai'] ?? 'N/A');
// Sử dụng nl2br cho địa chỉ để hiển thị xuống dòng nếu có
$diachi = nl2br(htmlspecialchars($user_info['diachi'] ?? 'N/A')); 
$tier_id = (int)($user_info['tier_id'] ?? 0);
$trangthai = (int)($user_info['trangthai'] ?? 0);

$ten_hang = htmlspecialchars($tier_map[$tier_id] ?? 'Không xác định');
$trangthai_text = $trangthai === 1 ? 'Hoạt động' : 'Khóa';
$trangthai_class = $trangthai === 1 ? 'status-active' : 'status-inactive';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi Tiết Người Dùng #<?= $user_id ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body { background: #f5f7fa; }
        .user-detail-card { /* Đổi tên class */
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 30px;
        }
        .user-detail-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        .detail-info {
            font-size: 1rem;
        }
        .detail-info p {
            margin-bottom: 0;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start; /* Giúp căn chỉnh cho Địa chỉ (đa dòng) */
        }
        .detail-info p:last-child {
            border-bottom: none;
        }
        .detail-info strong {
            color: #2d3748;
            font-weight: 500;
            flex-shrink: 0; /* Đảm bảo strong không bị co lại */
            width: 130px;
        }
        .detail-info span {
            font-weight: 600;
            color: #4a5568;
            max-width: calc(100% - 140px); /* Tối ưu hóa không gian hiển thị */
            text-align: right;
        }
        .status-active {
            color: #10b981; /* Green */
        }
        .status-inactive {
            color: #ef4444; /* Red */
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
        .error-card {
             border-left: 5px solid #ef4444;
        }
        /* ==============================
        MEDIA QUERIES - Responsive
        ============================== */

        /* Mobile (Max-width: 576px) */
        @media (max-width: 576px) {
            .user-detail-card {
                max-width: 95%; 
                margin: 20px auto;
                padding: 20px 15px;
            }
            .detail-info p {
                flex-direction: column;
                align-items: flex-start;
            }
            .detail-info strong {
                width: 100%;
                margin-bottom: 4px;
            }
            .detail-info span {
                text-align: left;
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="user-detail-card">
        <h2><i class="fas fa-user-circle"></i> Thông Tin Chi Tiết Người Dùng</h2>

        <?php if ($user_info): ?>
        <div class="detail-info">
            <p><strong>ID:</strong> <span>#<?= $user_id ?></span></p>
            <p><strong>Họ và Tên:</strong> <span><?= $hoten ?></span></p>
            <p><strong>Email:</strong> <span><?= $email ?></span></p>
            <p><strong>Hạng:</strong> <span><?= $ten_hang ?></span></p>
            <p><strong>Điện thoại:</strong> <span><?= $dienthoai ?></span></p>
            <p><strong>Địa chỉ:</strong> <span><?= $diachi ?></span></p>
            <p><strong>Trạng thái:</strong> <span class="<?= $trangthai_class ?>"><?= $trangthai_text ?></span></p>
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