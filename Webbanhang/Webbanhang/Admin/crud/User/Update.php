<?php
// File: Admin/crud/User/Update.php (Cập nhật Người dùng)
require_once __DIR__ . '/../../Controller/UserController.php'; 

$controller = new UserController();
// Hàm edit() xử lý cả request GET (lấy data cũ) và POST (cập nhật)
$data = $controller->edit(); 

$user_data = $data['user_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$tiers = $data['tiers'] ?? []; 

// Lấy ID người dùng hiện tại
$user_id = $user_data['user_id'] ?? (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (empty($user_data) && $error_message) {
    die(htmlspecialchars($error_message)); // Dừng nếu không tìm thấy ID
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Người Dùng</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        body { background: #f5f7fa; }
        .update-card {
            max-width: 450px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        .update-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        .form-label {
            font-weight: 500;
            color: #2d3748;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 18px;
            font-size: 1rem;
            padding: 10px 12px;
        }
        .btn-primary {
            background: #2563eb;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 10px 0;
            width: 100%;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #5a6a85;
            text-decoration: none;
            font-size: 0.98rem;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #16a34a;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .form-group p {
            width: 140px;
            margin-right: 10px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="update-card">
        <h2>Cập Nhật Người Dùng</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="Update.php?id=<?= htmlspecialchars($user_id) ?>">
            <div class="form-group">
                <p><strong>Họ và Tên:</strong></p>
                <input type="text" id="hoten" name="hoten" placeholder="Nhập họ và tên" 
                       value="<?= htmlspecialchars($user_data['hoten'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <p><strong>Email:</strong></p>
                <input type="email" id="email" name="email" placeholder="Nhập email" 
                       value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <p><strong>Mật khẩu mới:</strong></p>
                <input type="password" id="matkhau" name="matkhau" placeholder="Để trống nếu không đổi">
            </div>
            
            <div class="form-group">
                <p><strong>Vai trò/Tier:</strong></p>
                <select id="tier_id" name="tier_id" required>
                    <option value="">-- Chọn vai trò --</option>
                    <?php 
                    $selected_tier = $user_data['tier_id'] ?? '';
                    
                    // Nếu bạn không có bảng 'tier' thực tế, bạn có thể thay thế bằng dữ liệu tĩnh:
                    $tiers_static = [
                        ['tier_id' => 1, 'tier_name' => 'Admin'],
                        ['tier_id' => 2, 'tier_name' => 'Nhân Viên'],
                        ['tier_id' => 3, 'tier_name' => 'Khách hàng']
                    ];
                    $tiers = $tiers ?: $tiers_static; // Ưu tiên tiers từ controller
                    
                    foreach ($tiers as $tier): ?>
                        <option value="<?= htmlspecialchars($tier['tier_id']) ?>"
                                <?= ((int)$selected_tier == (int)$tier['tier_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tier['tenhang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <p><strong>Điện thoại:</strong></p>
                <input type="text" id="dienthoai" name="dienthoai" placeholder="Nhập số điện thoại" 
                       value="<?= htmlspecialchars($user_data['dienthoai'] ?? '') ?>" required>
            </div>
            
            <div class="form-group">
                <p><strong>Địa chỉ:</strong></p>
                <input type="text" id="diachi" name="diachi" placeholder="Nhập địa chỉ" 
                       value="<?= htmlspecialchars($user_data['diachi'] ?? '') ?>">
            </div>

            <div class="form-group">
                <p><strong>Trạng thái:</strong></p>
                <select id="trangthai" name="trangthai" required>
                    <?php $selected_status = (int)($user_data['trangthai'] ?? 1); ?>
                    <option value="1" <?= ($selected_status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="0" <?= ($selected_status == 0) ? 'selected' : '' ?>>Khóa</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </form>

        <a href="../../index.php?page=khachhang" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng người dùng
        </a>
    </div>
</body>
</html>