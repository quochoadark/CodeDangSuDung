<?php
// File: Admin/crud/Staff/Detail.php (Chi Tiết Nhân Viên)

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
    die('<div class="staff-detail-card error-card"><h2>Lỗi</h2><p>' . htmlspecialchars($error_message) . '</p><a href="../../index.php?page=nhanvien" class="back-link">Quay lại</a></div>');
}

// Mapping roles
$role_map = [];
foreach ($roles as $role) {
    $role_map[$role['id_chucvu']] = $role['ten_chucvu'];
}

if (!$staff_info):
    // Trường hợp không tìm thấy nhân viên
    die('<div class="staff-detail-card error-card"><h2>Lỗi</h2><p>Không tìm thấy nhân viên với ID đã cung cấp.</p><a href="../../index.php?page=nhanvien" class="back-link">Quay lại</a></div>'); 
endif;

// Giữ lại các biến để sử dụng trong HTML
$staff_id = htmlspecialchars($staff_info['staff_id'] ?? 'N/A');
$hoten = htmlspecialchars($staff_info['hoten'] ?? 'N/A');
$email = htmlspecialchars($staff_info['email'] ?? 'N/A');
$dienthoai = htmlspecialchars($staff_info['dienthoai'] ?? 'N/A');
$id_chucvu = (int)($staff_info['id_chucvu'] ?? 0);
$trangthai = (int)($staff_info['trangthai'] ?? 0);

$ten_chucvu = htmlspecialchars($role_map[$id_chucvu] ?? 'Không xác định');
$trangthai_text = $trangthai === 1 ? 'Hoạt động' : 'Ngừng hoạt động';
$trangthai_class = $trangthai === 1 ? 'status-active' : 'status-inactive';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chi Tiết Nhân Viên #<?= $staff_id ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body { background: #f5f7fa; }
        .staff-detail-card { /* Đổi tên class */
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 30px;
        }
        .staff-detail-card h2 {
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
            align-items: center;
        }
        .detail-info p:last-child {
            border-bottom: none;
        }
        .detail-info strong {
            color: #2d3748;
            font-weight: 500;
        }
        .detail-info span {
            font-weight: 600;
            color: #4a5568;
            max-width: 60%;
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
            .staff-detail-card {
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
            }
        }
    </style>
</head>
<body>
    <div class="staff-detail-card">
        <h2><i class="fas fa-user-circle"></i> Thông Tin Chi Tiết Nhân Viên</h2>

        <?php if ($staff_info): ?>
        <div class="detail-info">
            <p><strong>ID:</strong> <span>#<?= $staff_id ?></span></p>
            <p><strong>Họ và Tên:</strong> <span><?= $hoten ?></span></p>
            <p><strong>Email:</strong> <span><?= $email ?></span></p>
            <p><strong>Chức vụ:</strong> <span><?= $ten_chucvu ?></span></p>
            <p><strong>Điện thoại:</strong> <span><?= $dienthoai ?></span></p>
            <p><strong>Trạng thái:</strong> <span class="<?= $trangthai_class ?>"><?= $trangthai_text ?></span></p>
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