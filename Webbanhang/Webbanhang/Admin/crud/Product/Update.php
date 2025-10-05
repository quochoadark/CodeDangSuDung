<?php 
// File: Admin/view/crud/Product/Update.php

// --- 1. NẠP CONTROLLER ---
// Đường dẫn này phải trỏ đúng đến Controller
require_once __DIR__ . '/../../Controller/ProductController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM SỬA (edit) ---
$controller = new ProductController();

// Hàm edit() sẽ xử lý POST và trả về dữ liệu cần thiết cho form (hoặc chuyển hướng nếu thành công)
$data = $controller->edit(); 
// Nếu việc cập nhật thành công, Controller đã thực hiện header("Location:...") và exit()

// --- 3. GÁN BIẾN DỮ LIỆU TỪ CONTROLLER ---
$categories = $data['categories'] ?? [];
$product_data = $data['product_data'] ?? null;
$error_message = $data['error_message'] ?? null; 

// Kiểm tra nếu không tìm thấy sản phẩm, hiển thị lỗi và dừng.
if (!$product_data && $error_message) {
    die('<div class="add-category-card"><h2>Lỗi</h2><p>' . htmlspecialchars($error_message) . '</p><a href="../../index.php?page=sanpham" class="back-link">Quay lại</a></div>');
}

// Nếu không tìm thấy dữ liệu (ví dụ: bị lỗi DB hoặc ID không hợp lệ)
if (!$product_data) {
    die('<div class="add-category-card"><h2>Lỗi</h2><p>Không tìm thấy dữ liệu sản phẩm hoặc thiếu ID.</p><a href="../../index.php?page=sanpham" class="back-link">Quay lại</a></div>');
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cập nhật Sản Phẩm</title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
<style>
    /* CSS được giữ nguyên từ code gốc */
    body {
        background: #f5f7fa;
    }
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
        color: #fff;
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
        width: 130px; 
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
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: .25rem;
    }
</style>
</head>
<body>
<div class="add-category-card">
    <h2>Cập nhật Sản Phẩm</h2>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <p><strong>Tên sản phẩm:</strong></p>
            <input type="text" id="tensanpham" name="tensanpham" 
                    value="<?= htmlspecialchars($product_data['tensanpham'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <p><strong>Danh mục:</strong></p>
            <select id="category_id" name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php
                if (!empty($categories)):
                    foreach($categories as $row): 
                        $selected = ($row['category_id'] == $product_data['category_id']) ? 'selected' : ''; ?>
                        <option value="<?= $row['category_id'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($row['tendanhmuc']) ?>
                        </option>
                <?php endforeach; endif; ?>
            </select>
        </div>

        <div class="form-group">
            <p><strong>Giá (VNĐ):</strong></p>
            <input type="number" id="gia" name="gia" 
                    value="<?= htmlspecialchars($product_data['gia'] ?? '') ?>" step="1000" min="0" required>
        </div>

        <div class="form-group">
            <p><strong>Số lượng tồn:</strong></p>
            <input type="number" id="tonkho" name="tonkho" 
                    value="<?= htmlspecialchars($product_data['tonkho'] ?? '') ?>" min="0" required>
        </div>

        <div class="form-group">
            <p><strong>Ngày tạo:</strong></p>
            <input type="date" id="ngaytao" name="ngaytao" 
                    value="<?= htmlspecialchars($product_data['ngaytao'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <p><strong>Mô tả:</strong></p>
            <textarea id="mota" name="mota" rows="3"><?= htmlspecialchars($product_data['mota'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <p><strong>Ảnh hiện tại:</strong></p>
            <?php if(!empty($product_data['img'])): ?>
                <img src="../../uploads/<?= htmlspecialchars($product_data['img']) ?>" 
                    alt="Ảnh sản phẩm" style="width: 100px; height:auto; border:1px solid #ccc;">
            <?php else: ?>
                <p>Chưa có ảnh</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <p><strong>Chọn ảnh mới:</strong></p>
            <input type="file" id="img" name="img" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Cập nhật</button>
    </form>

    <a href="../../index.php?page=sanpham" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại bảng sản phẩm
    </a>
</div>
</body>
</html>