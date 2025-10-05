<?php
// File: Admin/crud/Staff/Add.php (Thêm Nhân Viên)
// Đường dẫn cần điều chỉnh tùy theo vị trí file database.php
require_once __DIR__ . '/../../Controller/StaffController.php'; 

$controller = new StaffController();
// Gọi hàm create() của StaffController để xử lý POST request và lấy dữ liệu cần thiết
$data = $controller->create(); 

// Lấy dữ liệu đã nhập (nếu có lỗi) để giữ lại trên form
$staff_data = $data['staff_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$roles = $data['roles'] ?? []; // Danh sách Chức vụ từ bảng chucvu

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Nhân Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* GIỮ NGUYÊN CSS TỪ FILE USER/ADD.PHP CỦA BẠN */
        body { background: #f5f7fa; }
        .add-category-card {
            max-width: 450px;
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        .add-category-card h2 {
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
        .btn-success {
            background: #16a34a;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 10px 0;
            width: 100%;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-success:hover {
            background: #15803d;
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
        .form-group textarea {
            resize: vertical;
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
    <div class="add-category-card">
        <h2>Thêm Nhân Viên</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <form method="POST" action="Add.php">
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
                <p><strong>Mật khẩu:</strong></p>
                <input type="password" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu" required>
            </div>
            
            <div class="form-group">
                <p><strong>Chức vụ:</strong></p>
                <select id="id_chucvu" name="id_chucvu" required>
                    <option value="">-- Chọn Chức vụ --</option>
                    <?php 
                    $selected_role = $staff_data['id_chucvu'] ?? '';
                    
                    foreach ($roles as $role): 
                        // LƯU Ý: Đảm bảo StaffController đã cung cấp $roles chứa 'id_chucvu' và 'ten_chucvu'
                    ?>
                        <option value="<?= htmlspecialchars($role['id_chucvu']) ?>"
                                <?= ($selected_role == $role['id_chucvu']) ? 'selected' : '' ?>>
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
                    <?php $selected_status = $staff_data['trangthai'] ?? 1; ?>
                    <option value="1" <?= ($selected_status == 1) ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="0" <?= ($selected_status == 0) ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Thêm Nhân Viên</button>
        </form>

        <a href="../../index.php?page=nhanvien" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng nhân viên
        </a>
    </div>
</body>
</html>