<?php
// File: Admin/view/product/Add.php (Hoàn Chỉnh CSS)

// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/ProductController.php';

// Khởi tạo Controller và gọi phương thức create()
$controller = new ProductController();
$data = $controller->create(); 

$categories = $data['categories'] ?? []; 
$error_message = $data['error_message'] ?? null; 
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Thêm sản phẩm thành công!' : null);

// Lấy lại dữ liệu cũ nếu POST thất bại
$old_input = $data['old_input'] ?? [];

// Helper function để lấy giá trị cũ
function getOldValue($key, $old_input) {
    return htmlspecialchars($old_input[$key] ?? '');
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Sản Phẩm Mới</title>
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
        .add-product-card { 
            max-width: 600px; 
            margin: 50px auto; 
            background: #fff; 
            border-radius: 12px; 
            box-shadow: 0 4px 24px rgba(0,0,0,0.08); 
            padding: 32px 35px; 
        }
        .add-product-card h2 { 
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
            align-items: flex-start; 
            margin-bottom: 18px; 
            position: relative; 
        } 
        .form-group p { 
            width: 150px; 
            margin-right: 15px; 
            font-weight: 500; 
            color: #2d3748; 
            margin-bottom: 0; 
            padding-top: 10px; 
            flex-shrink: 0; 
        }
        .form-group input, 
        .form-group select, 
        .form-group textarea { 
            flex: 1; 
            padding: 10px 12px; 
            border: 1px solid #e2e8f0; 
            border-radius: 8px; 
            font-size: 1rem; 
            min-height: 40px; 
        }
        .form-group textarea { 
            resize: vertical; 
            padding-top: 12px; 
        }
        
        /* --- Thông báo lỗi/thành công --- */
        .error-message { 
            color: #dc2626; 
            margin-bottom: 15px; 
            text-align: center; 
            padding: 10px; 
            border: 1px dashed #dc2626; 
            border-radius: 8px; 
        }
        .success-message { 
            color: #16a34a; 
            background: #ecfdf5; 
            padding: 10px; 
            border-radius: 8px; 
            margin-bottom: 15px; 
            text-align: center; 
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
            .add-product-card { 
                max-width: 95%; 
                margin: 20px auto; 
                padding: 20px 15px; 
            }
            .form-group { 
                flex-direction: column; 
                align-items: stretch; 
                margin-bottom: 30px; 
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
            .validation-error { 
                width: 100%; 
                text-align: left; 
                bottom: -22px; 
            } 
        }
    </style>
</head>
<body>
    <div class="add-product-card">
        <h2>Thêm Sản Phẩm Mới</h2>
        
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <p class="success-message"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?></p>
        <?php endif; ?>

        <form id="productForm" method="POST" action="Add.php" enctype="multipart/form-data" onsubmit="return validateProductForm();" novalidate>
            
            <div class="form-group" id="group_tensanpham">
                <p><strong>Tên sản phẩm:</strong></p>
                <input type="text" id="tensanpham" name="tensanpham" placeholder="Nhập tên sản phẩm" value="<?= getOldValue('tensanpham', $old_input) ?>" required>
                <div class="validation-error" id="tensanphamError"></div>
            </div>

            <div class="form-group" id="group_category_id">
                <p><strong>Danh mục:</strong></p>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php
                    $old_category_id = getOldValue('category_id', $old_input);
                    if (!empty($categories)):
                        foreach ($categories as $row): 
                            $selected = ((string)$row['category_id'] === $old_category_id) ? 'selected' : '';
                        ?>
                            <option value="<?= htmlspecialchars($row['category_id']) ?>" <?= $selected ?>>
                                <?= htmlspecialchars($row['tendanhmuc']) ?>
                            </option>
                        <?php endforeach;
                    endif; 
                    ?>
                </select>
                <div class="validation-error" id="category_idError"></div>
            </div>

            <div class="form-group" id="group_gia">
                <p><strong>Giá (VNĐ):</strong></p>
                <input type="number" id="gia" name="gia" placeholder="Nhập giá sản phẩm" step="1000" min="0" value="<?= getOldValue('gia', $old_input) ?>" required>
                <div class="validation-error" id="giaError"></div>
            </div>

            <div class="form-group" id="group_tonkho">
                <p><strong>Số lượng tồn kho:</strong></p>
                <input type="number" id="tonkho" name="tonkho" placeholder="Nhập số lượng tồn kho" min="0" value="<?= getOldValue('tonkho', $old_input) ?>" required>
                <div class="validation-error" id="tonkhoError"></div>
            </div>

            <div class="form-group" id="group_ngaytao">
                <p><strong>Ngày tạo:</strong></p>
                <input type="date" id="ngaytao" name="ngaytao" value="<?= getOldValue('ngaytao', $old_input) ?: date('Y-m-d') ?>" required>
                <div class="validation-error" id="ngaytaoError"></div>
            </div>

            <div class="form-group" id="group_mota">
                <p><strong>Mô tả:</strong></p>
                <textarea id="mota" name="mota" rows="3" placeholder="Nhập mô tả sản phẩm"><?= getOldValue('mota', $old_input) ?></textarea>
                <div class="validation-error" id="motaError"></div>
            </div>

            <div class="form-group" id="group_img">
                <p><strong>Ảnh sản phẩm:</strong></p>
                <input type="file" id="img" name="img" accept="image/*" required>
                <div class="validation-error" id="imgError"></div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Thêm Sản Phẩm
            </button>
        </form>

        <a href="../../index.php?page=sanpham" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng sản phẩm
        </a>
    </div>

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

    function validateProductForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        const fieldIds = ['tensanpham', 'category_id', 'gia', 'tonkho', 'ngaytao', 'img'];
        fieldIds.forEach(id => clearErrorState(id));

        const tenSP = document.getElementById('tensanpham').value.trim();
        const categoryId = document.getElementById('category_id').value;
        const gia = document.getElementById('gia').value.trim();
        const tonKho = document.getElementById('tonkho').value.trim();
        const ngayTao = document.getElementById('ngaytao').value.trim();
        const imgFile = document.getElementById('img').value;

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

        // --- ẢNH SẢN PHẨM ---
        if (imgFile === '') {
            setErrorState('img', 'Vui lòng chọn Ảnh sản phẩm.');
            isValid = false;
        }

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
        document.getElementById('productForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
        
        // Căn chỉnh lỗi PHP (nếu có)
        if (document.querySelector('.error-message')) {
             document.querySelector('.error-message').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
    </script>
</body>
</html>