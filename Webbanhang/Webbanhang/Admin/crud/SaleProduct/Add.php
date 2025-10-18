<?php
// File: Admin/crud/SaleProduct/Add.php (Thêm Khuyến Mãi Sản Phẩm)

// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/SaleProductController.php';

$controller = new SaleProductController();

// Xử lý logic và lấy dữ liệu cần thiết cho form
$data = $controller->create();
$promo_data = $data['promo_data'] ?? []; // Dữ liệu cũ nếu POST thất bại
$products = $data['products'] ?? [];
$error_message = $data['error_message'] ?? null;
// Thêm biến thông báo thành công sau khi chuyển hướng (redirect)
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Thêm khuyến mãi sản phẩm thành công!' : null);

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
    <title>Thêm Khuyến Mãi Sản Phẩm</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body {
            background: #f5f7fa;
        }

        .add-sale-card { /* Đổi tên class để phù hợp hơn */
            max-width: 550px; 
            margin: 50px auto; 
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 30px;
        }

        .add-sale-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-group {
            display: flex;
            align-items: flex-start; /* Thay đổi để canh lỗi được dễ hơn */
            margin-bottom: 25px; /* Tăng khoảng cách để chứa lỗi */
            position: relative; /* Quan trọng cho định vị lỗi */
        }

        .form-group p {
            width: 140px;
            margin-right: 15px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
            flex-shrink: 0;
            padding-top: 10px; /* Căn chỉnh với input */
        }

        .form-group input,
        .form-group select {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            min-height: 40px;
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
            width: calc(100% - 155px); /* Độ rộng của phần input */
            text-align: right; 
        } 
        
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border: 1px solid #f5c2c7;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
        }

        .alert-success {
            color: #0f5132;
            background-color: #d1e7dd;
            border: 1px solid #badbcc;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 0.25rem;
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
        
        /* ==============================
        MEDIA QUERIES - Responsive
        ============================== */
        @media (max-width: 768px) {
            .add-sale-card {
                max-width: 90%; 
                margin: 40px auto;
                padding: 25px 20px;
            }
        }
        
        /* Mobile (Max-width: 576px) */
        @media (max-width: 576px) {
            .add-sale-card {
                max-width: 95%; 
                margin: 20px auto;
                padding: 20px 15px;
            }
            .form-group {
                flex-direction: column; /* Chuyển từ ngang sang dọc */
                align-items: stretch;
                margin-bottom: 30px; /* Tăng khoảng cách để chứa lỗi */
            }
            .form-group p {
                width: 100%; 
                margin-right: 0;
                margin-bottom: 5px; /* Khoảng cách với input */
                padding-top: 0;
            }
            .form-group input,
            .form-group select {
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
    <div class="add-sale-card">
        <h2>Thêm Khuyến Mãi Sản Phẩm Mới</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <form id="promoForm" method="POST" action="Add.php" onsubmit="return validatePromoForm();" novalidate>
            
            <div class="form-group">
                <p><strong>Sản Phẩm:</strong></p>
                <select id="product_id" name="product_id" required>
                    <option value="">-- Chọn Sản Phẩm --</option>
                    <?php 
                    $old_product_id = getOldValue('product_id', $promo_data);
                    foreach ($products as $p): ?>
                        <option value="<?= htmlspecialchars($p['product_id']) ?>" 
                                <?= ((string)$old_product_id === (string)$p['product_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['tensanpham']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="validation-error" id="product_idError"></div>
            </div>

            <div class="form-group">
                <p><strong>Mô Tả:</strong></p>
                <input
                    type="text"
                    id="mota"
                    name="mota"
                    placeholder="VD: Flash sale 1 ngày"
                    value="<?= getOldValue('mota', $promo_data) ?>"
                    required
                >
                <div class="validation-error" id="motaError"></div>
            </div>

            <div class="form-group">
                <p><strong>Giá Trị Giảm:</strong></p>
                <input
                    type="number"
                    step="any"
                    id="giam"
                    name="giam"
                    placeholder="VD: 50000 (tiền) hoặc 0.15 (15%)"
                    value="<?= getOldValue('giam', $promo_data) ?>"
                    min="0"
                    required
                >
                <div class="validation-error" id="giamError"></div>
            </div>

            <div class="form-group">
                <p><strong>Ngày Bắt Đầu:</strong></p>
                <input
                    type="date"
                    id="ngaybatdau"
                    name="ngaybatdau"
                    value="<?= getOldValue('ngaybatdau', $promo_data) ?: date('Y-m-d') ?>"
                    required
                >
                <div class="validation-error" id="ngaybatdauError"></div>
            </div>

            <div class="form-group">
                <p><strong>Ngày Kết Thúc:</strong></p>
                <input
                    type="date"
                    id="ngayketthuc"
                    name="ngayketthuc"
                    value="<?= getOldValue('ngayketthuc', $promo_data) ?>"
                    required
                >
                <div class="validation-error" id="ngayketthucError"></div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Thêm Khuyến Mãi Sản Phẩm
            </button>
        </form>

        <a href="../../index.php?page=khuyenmai" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng khuyến mãi sản phẩm
        </a>
    </div>
</body>
</html>

<script>
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

    function validatePromoForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        const fieldIds = ['product_id', 'mota', 'giam', 'ngaybatdau', 'ngayketthuc'];
        fieldIds.forEach(id => clearErrorState(id));

        const productId = document.getElementById('product_id').value;
        const moTa = document.getElementById('mota').value.trim();
        const giam = document.getElementById('giam').value.trim();
        const ngayBatDau = document.getElementById('ngaybatdau').value.trim();
        const ngayKetThuc = document.getElementById('ngayketthuc').value.trim();

        // --- SẢN PHẨM ---
        if (productId === '') {
            setErrorState('product_id', 'Vui lòng chọn Sản phẩm.');
            isValid = false;
        }

        // --- MÔ TẢ ---
        if (moTa === '') {
            setErrorState('mota', 'Vui lòng nhập Mô tả khuyến mãi.');
            isValid = false;
        }

        // --- GIÁ TRỊ GIẢM ---
        if (giam === '') {
            setErrorState('giam', 'Vui lòng nhập Giá trị Giảm.');
            isValid = false;
        } else if (isNaN(giam) || parseFloat(giam) < 0) {
            setErrorState('giam', 'Giá trị giảm không hợp lệ (phải là số $\\geq 0$).');
            isValid = false;
        }

        // --- NGÀY BẮT ĐẦU ---
        if (ngayBatDau === '') {
             setErrorState('ngaybatdau', 'Vui lòng chọn Ngày Bắt Đầu.');
             isValid = false;
        }
        
        // --- NGÀY KẾT THÚC ---
        if (ngayKetThuc === '') {
             setErrorState('ngayketthuc', 'Vui lòng chọn Ngày Kết Thúc.');
             isValid = false;
        }

        // --- LOGIC NGÀY (Chỉ kiểm tra nếu cả hai ngày đều có giá trị) ---
        if (ngayBatDau && ngayKetThuc) {
            const startDate = new Date(ngayBatDau);
            const endDate = new Date(ngayKetThuc);
            
            if (endDate < startDate) {
                setErrorState('ngayketthuc', 'Ngày Kết Thúc phải sau hoặc bằng Ngày Bắt Đầu.');
                isValid = false;
            }
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
        document.getElementById('promoForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
    });
</script>