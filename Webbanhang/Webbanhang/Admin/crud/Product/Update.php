<?php 
// File: Admin/view/crud/Product/Update.php (Hoàn Chỉnh CSS)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/ProductController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM SỬA (edit) ---
$controller = new ProductController();
$data = $controller->edit(); 
$categories = $data['categories'] ?? [];
$product_data = $data['product_data'] ?? null;
$error_message = $data['error_message'] ?? null; 
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Cập nhật sản phẩm thành công!' : null);
$product_id = $product_data['product_id'] ?? (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (!$product_data && $error_message) {
    die('<div class="update-product-card"><h2>Lỗi</h2><p>' . htmlspecialchars($error_message) . '</p><a href="../../index.php?page=sanpham" class="back-link">Quay lại</a></div>');
}
if (!$product_data) {
    die('<div class="update-product-card"><h2>Lỗi</h2><p>Không tìm thấy dữ liệu sản phẩm hoặc thiếu ID.</p><a href="../../index.php?page=sanpham" class="back-link">Quay lại</a></div>');
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cập nhật Sản Phẩm #<?= htmlspecialchars($product_id) ?></title>
<link rel="stylesheet" href="../../assets/css/style.css">
<link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
<style>
    /* ==============================
    CSS Dành cho PC (Mặc định)
    ============================== 
    */
    body { 
        background: #f5f7fa; 
    }
    .update-product-card { 
        max-width: 600px; 
        margin: 50px auto; 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 4px 24px rgba(0,0,0,0.08); 
        padding: 32px 35px; 
    }
    .update-product-card h2 { 
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
        color: #fff; 
        font-size: 1.1rem; 
        padding: 12px 0; 
        width: 100%; 
        margin-top: 15px; 
        transition: background 0.2s; 
        cursor: pointer; 
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
    
    /* --- Form Group Styling --- */
    .form-group { 
        display: flex; 
        align-items: center; 
        margin-bottom: 18px; 
        position: relative;
    }
    .form-group.textarea-group { 
        align-items: flex-start; 
    }
    .form-group p { 
        width: 150px; 
        margin-right: 15px; 
        font-weight: 500; 
        color: #2d3748; 
        margin-bottom: 0; 
        flex-shrink: 0; 
    }
    .form-group.textarea-group p { 
        padding-top: 10px; 
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
    
    /* --- Thông báo lỗi/thành công --- */
    .alert-danger { 
        color: #721c24; 
        background-color: #f8d7da; 
        border-color: #f5c6cb; 
        padding: 10px; 
        margin-bottom: 20px; 
        border: 1px solid transparent; 
        border-radius: .25rem; 
        text-align: left; 
    }
    .alert-success { 
        color: #155724; 
        background-color: #d4edda; 
        border-color: #c3e6cb; 
        padding: 10px; 
        margin-bottom: 20px; 
        border: 1px solid transparent; 
        border-radius: .25rem; 
        text-align: left; 
    }
    .current-img-group { 
        display: flex; 
        align-items: center; 
        flex: 1; 
    }
    .current-img-group img { 
        display: block; 
        width: 100px; 
        height: auto; 
        border: 1px solid #ccc; 
        border-radius: 8px; 
        margin-right: 15px; 
    }
    
    /* --- Validation CSS mới --- */
    .input-error { 
        border-color: #dc2626 !important; 
    }
    .validation-error { 
        position: absolute; 
        bottom: -18px; 
        right: 0; 
        font-size: 0.85rem; 
        color: #dc2626; 
        width: calc(100% - 165px); 
        text-align: right; 
    }

    /* ==============================
    MEDIA QUERIES - Responsive
    ============================== 
    */
    @media (max-width: 576px) { 
        .update-product-card { 
            max-width: 95%; 
            margin: 20px auto; 
            padding: 20px 15px; 
        }
        .form-group { 
            flex-direction: column; 
            align-items: stretch; 
            margin-bottom: 30px; 
        } 
        .form-group.textarea-group { 
            align-items: stretch; 
        }
        .form-group p { 
            width: 100%; 
            margin-right: 0; 
            margin-bottom: 5px; 
            padding-top: 0; 
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea { 
            width: 100%; 
            font-size: 0.95rem; 
        }
        .current-img-group { 
            flex-direction: column; 
            align-items: flex-start; 
        }
        .current-img-group img { 
            margin-bottom: 10px; 
        }
        .validation-error { 
            width: 100%; 
            text-align: left; 
            bottom: -22px; 
        } 
    }
</style>
</head>
<body>
<div class="update-product-card">
    <h2>Cập nhật Sản Phẩm #<?= htmlspecialchars($product_id) ?></h2>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>

    <form id="productUpdateForm" method="POST" action="Update.php?id=<?= htmlspecialchars($product_id) ?>" enctype="multipart/form-data" onsubmit="return validateUpdateForm();" novalidate>
        <div class="form-group">
            <p><strong>Tên sản phẩm:</strong></p>
            <input type="text" id="tensanpham" name="tensanpham" 
                     value="<?= htmlspecialchars($product_data['tensanpham'] ?? '') ?>" required>
            <div class="validation-error" id="tensanphamError"></div>
        </div>

        <div class="form-group">
            <p><strong>Danh mục:</strong></p>
            <select id="category_id" name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php
                $current_cat_id = $product_data['category_id'] ?? null;
                if (!empty($categories)):
                    foreach($categories as $row): 
                        $selected = ((string)$row['category_id'] === (string)$current_cat_id) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($row['category_id']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($row['tendanhmuc']) ?>
                        </option>
                <?php endforeach; endif; ?>
            </select>
            <div class="validation-error" id="category_idError"></div>
        </div>

        <div class="form-group">
            <p><strong>Giá (VNĐ):</strong></p>
            <input type="number" id="gia" name="gia" 
                     value="<?= htmlspecialchars($product_data['gia'] ?? '') ?>" step="1000" min="0" required>
            <div class="validation-error" id="giaError"></div>
        </div>

        <div class="form-group">
            <p><strong>Số lượng tồn:</strong></p>
            <input type="number" id="tonkho" name="tonkho" 
                     value="<?= htmlspecialchars($product_data['tonkho'] ?? '') ?>" min="0" required>
            <div class="validation-error" id="tonkhoError"></div>
        </div>

        <div class="form-group">
            <p><strong>Ngày tạo:</strong></p>
            <input type="date" id="ngaytao" name="ngaytao" 
                     value="<?= htmlspecialchars($product_data['ngaytao'] ?? '') ?>" required>
            <div class="validation-error" id="ngaytaoError"></div>
        </div>

        <div class="form-group textarea-group">
            <p><strong>Mô tả:</strong></p>
            <textarea id="mota" name="mota" rows="3"><?= htmlspecialchars($product_data['mota'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <p><strong>Ảnh hiện tại:</strong></p>
            <div class="current-img-group">
                <?php if(!empty($product_data['img'])): ?>
                    <img src="../../uploads/<?= htmlspecialchars($product_data['img']) ?>" 
                         alt="Ảnh sản phẩm">
                <?php else: ?>
                    <p>Chưa có ảnh</p>
                <?php endif; ?>
                <input type="hidden" id="current_img_name" name="current_img_name" value="<?= htmlspecialchars($product_data['img'] ?? '') ?>">
            </div>
        </div>

        <div class="form-group">
            <p><strong>Chọn ảnh mới:</strong></p>
            <input type="file" id="img" name="img" accept="image/*">
            <div class="validation-error" id="imgError"></div>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Cập nhật
        </button>
    </form>

    <a href="../../index.php?page=sanpham" class="back-link">
        <i class="fas fa-arrow-left"></i> Quay lại bảng sản phẩm
    </a>
</div>
</body>
</html>
<script>
    const MIN_LENGTH_PRODUCT_NAME = 5;

    function clearErrorState(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        if (field) field.classList.remove('input-error');
        if (errorDiv) errorDiv.innerHTML = '';
    }

    function setErrorState(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        if (field) field.classList.add('input-error');
        if (errorDiv) errorDiv.innerHTML = message;
    }

    function validateUpdateForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        const fieldIds = ['tensanpham', 'category_id', 'gia', 'tonkho', 'ngaytao'];
        fieldIds.forEach(id => clearErrorState(id));

        const tenSP = document.getElementById('tensanpham').value.trim();
        const categoryId = document.getElementById('category_id').value;
        const gia = document.getElementById('gia').value.trim();
        const tonKho = document.getElementById('tonkho').value.trim();
        const ngayTao = document.getElementById('ngaytao').value.trim();

        // --- TÊN SẢN PHẨM ---
        if (tenSP === '') {
            setErrorState('tensanpham', 'Vui lòng nhập Tên sản phẩm.');
            isValid = false;
        } else if (tenSP.length < MIN_LENGTH_PRODUCT_NAME) {
            setErrorState('tensanpham', `Tên sản phẩm phải có ít nhất ${MIN_LENGTH_PRODUCT_NAME} ký tự.`);
            isValid = false;
        }

        // --- DANH MỤC ---
        if (categoryId === '') {
            setErrorState('category_id', 'Vui lòng chọn Danh mục.');
            isValid = false;
        }

        // --- GIÁ ---
        if (gia === '') {
            setErrorState('gia', 'Vui lòng nhập Giá sản phẩm.');
            isValid = false;
        } else if (isNaN(gia) || parseFloat(gia) < 0) {
            setErrorState('gia', 'Giá không hợp lệ (phải là số $\\geq 0$).');
            isValid = false;
        }

        // --- TỒN KHO ---
        if (tonKho === '') {
            setErrorState('tonkho', 'Vui lòng nhập Số lượng tồn kho.');
            isValid = false;
        } else if (isNaN(tonKho) || parseInt(tonKho) < 0) {
            setErrorState('tonkho', 'Tồn kho không hợp lệ (phải là số nguyên $\\geq 0$).');
            isValid = false;
        }
        
        // --- NGÀY TẠO ---
        if (ngayTao === '') {
             setErrorState('ngaytao', 'Vui lòng chọn Ngày tạo.');
             isValid = false;
        }
        
        // **Ảnh mới (img) không bị kiểm tra required.**

        // Cuộn đến trường lỗi đầu tiên nếu validation thất bại
        if (!isValid) {
            const firstErrorField = document.querySelector('.input-error');
            if (firstErrorField) {
                firstErrorField.focus();
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Xóa thông báo lỗi khi người dùng bắt đầu nhập/thay đổi
        document.getElementById('productUpdateForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
        
        // Căn chỉnh lỗi PHP (nếu có)
        if (document.querySelector('.alert-danger')) {
             document.querySelector('.alert-danger').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
</script>