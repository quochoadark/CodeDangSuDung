<?php
// File: Admin/crud/Staff/Detail.php (Đã sửa đổi để gọi Controller)

// Chỉ cần Controller để xử lý logic
require_once __DIR__ . '/../../Controller/StaffController.php'; 

// Khởi tạo Controller và gọi hàm detail()
$controller = new StaffController();
$data = $controller->detail(); // Lấy dữ liệu thông qua Controller

$staff_info = $data['staff_info'] ?? null;
$roles = $data['roles'] ?? []; 
$error_message = $data['error'] ?? null;

// Nếu có lỗi (ví dụ: thiếu ID) và không có staff_info, dừng lại
if ($error_message && !$staff_info) {
    die(htmlspecialchars($error_message));
}

// Mapping roles
$role_map = [];
foreach ($roles as $role) {
    $role_map[$role['id_chucvu']] = $role['ten_chucvu'];
}

if (!$staff_info):
    // Trường hợp detail() không tìm thấy và đã chuyển hướng,
    // nhưng nếu code vẫn chạy đến đây, hiển thị thông báo.
    die("Không tìm thấy nhân viên."); 
endif;

// Giữ lại các biến để sử dụng trong HTML
$hoten = htmlspecialchars($staff_info['hoten'] ?? 'N/A');
$email = htmlspecialchars($staff_info['email'] ?? 'N/A');
$dienthoai = htmlspecialchars($staff_info['dienthoai'] ?? 'N/A');
$id_chucvu = (int)($staff_info['id_chucvu'] ?? 0);
$trangthai = (int)($staff_info['trangthai'] ?? 0);

$ten_chucvu = htmlspecialchars($role_map[$id_chucvu] ?? 'Không xác định');
$trangthai_text = $trangthai === 1 ? 'Hoạt động' : 'Ngừng hoạt động';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi Tiết Nhân Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* CSS... */
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
        <h2>Thông Tin Chi Tiết Nhân Viên</h2>

        <?php if ($staff_info): ?>
        <div class="detail-info">
            <p><strong>ID:</strong> <?= htmlspecialchars($staff_info['staff_id']) ?></p>
            <p><strong>Họ và Tên:</strong> <?= $hoten ?></p>
            <p><strong>Email:</strong> <?= $email ?></p>
            <p><strong>Chức vụ:</strong> <?= $ten_chucvu ?></p>
            <p><strong>Điện thoại:</strong> <?= $dienthoai ?></p>
            <p><strong>Trạng thái:</strong> <?= $trangthai_text ?></p>
        </div>
        <?php else: ?>
        <p class="text-center">Không có dữ liệu để hiển thị.</p>
        <?php endif; ?>

        <a href="../../index.php?page=nhanvien" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng nhân viên
        </a>
    </div>
</body>
</html>