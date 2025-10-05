<?php
// File: Admin/crud/Staff/Update.php (Cập nhật Nhân viên)
require_once __DIR__ . '/../../Controller/StaffController.php'; 

$controller = new StaffController();
// Hàm update() xử lý cả request GET (lấy data cũ) và POST (cập nhật)
$data = $controller->update(); 

$staff_data = $data['staff_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$roles = $data['roles'] ?? []; 

// Lấy ID nhân viên hiện tại
$staff_id = $staff_data['staff_id'] ?? (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (empty($staff_data) && !is_null($error_message)) {
    // Dừng nếu không tìm thấy ID và không có thông báo lỗi (tránh bị hiển thị nhầm)
    // Nếu $staff_data rỗng, và update() đã chuyển hướng về index nếu không tìm thấy
    // Đây chỉ là trường hợp lỗi phát sinh trong quá trình POST
}

if (empty($staff_data) && $staff_id > 0) {
    // Trường hợp không tìm thấy nhân viên khi load trang (ID không tồn tại)
    die("Không tìm thấy nhân viên có ID: " . $staff_id); 
} else if ($staff_id === 0) {
    die("Thiếu ID nhân viên.");
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Nhân Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* GIỮ NGUYÊN CSS TỪ USER/UPDATE.PHP CỦA BẠN */
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
        <h2>Cập Nhật Nhân Viên</h2>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="Update.php?id=<?= htmlspecialchars($staff_id) ?>">
            <div class="form-group">
                <p><strong>Họ và Tên:</strong></p>
                <input type="text" id="hoten" name="hoten" placeholder="Nhập họ và tên" 
                       value="<?= htmlspecialchars($staff_data['hoten'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <p><strong>Email:</strong></p>
                <input type="email" id="email" name="email" placeholder="Nhập email" 
                       value="<?= htmlspecialchars($staff_data['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <p><strong>Mật khẩu mới:</strong></p>
                <input type="password" id="matkhau" name="matkhau" placeholder="Để trống nếu không đổi">
            </div>
            
            <div class="form-group">
                <p><strong>Chức vụ:</strong></p>
                <select id="id_chucvu" name="id_chucvu" required>
                    <option value="">-- Chọn Chức vụ --</option>
                    <?php 
                    $selected_role = $staff_data['id_chucvu'] ?? '';
                    
                    foreach ($roles as $role): 
                    ?>
                        <option value="<?= htmlspecialchars($role['id_chucvu']) ?>"
                                <?= ((int)$selected_role == (int)$role['id_chucvu']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['ten_chucvu']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <p><strong>Điện thoại:</strong></p>
                <input type="text" id="dienthoai" name="dienthoai" placeholder="Nhập số điện thoại" 
                       value="<?= htmlspecialchars($staff_data['dienthoai'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <p><strong>Trạng thái:</strong></p>
                <select id="trangthai" name="trangthai" required>
                    <?php $selected_status = (int)($staff_data['trangthai'] ?? 1); ?>
                    <option value="1" <?= ($selected_status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="0" <?= ($selected_status == 0) ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập Nhật</button>
        </form>

        <a href="../../index.php?page=nhanvien" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng nhân viên
        </a>
    </div>
</body>
</html>