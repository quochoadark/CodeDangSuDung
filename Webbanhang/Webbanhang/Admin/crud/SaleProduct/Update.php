<?php
// File: Admin/crud/SaleProduct/Update.php (Cập Nhật Khuyến Mãi Sản Phẩm)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/SaleProductController.php';

$controller = new SaleProductController();
// Hàm edit() xử lý POST, sau đó trả về dữ liệu cho form
$data = $controller->edit();

// --- 2. GÁN BIẾN DỮ LIỆU TỪ CONTROLLER ---
$promo_data = $data['promo_data'] ?? [];
$products = $data['products'] ?? [];
$error_message = $data['error_message'] ?? null;
// Lấy thông báo thành công từ Controller (sau khi redirect)
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Cập nhật khuyến mãi sản phẩm thành công!' : null);

$promo_id = $promo_data['promo_id'] ?? (isset($_GET['id']) ? (int) $_GET['id'] : 0);

// --- 3. KIỂM TRA DỮ LIỆU ---
if (empty($promo_data) && $error_message) {
    // Nếu không tìm thấy khuyến mãi và có lỗi (do ID không hợp lệ)
    // Sửa class card để có style đẹp hơn
    die('<div class="update-sale-card"><h2>Lỗi</h2><p>' . htmlspecialchars($error_message) . '</p><a href="../../index.php?page=khuyenmai" class="back-link">Quay lại</a></div>');
}
if (empty($promo_data)) {
    die('<div class="update-sale-card"><h2>Lỗi</h2><p>Không tìm thấy dữ liệu khuyến mãi hoặc thiếu ID.</p><a href="../../index.php?page=khuyenmai" class="back-link">Quay lại</a></div>');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Khuyến Mãi Sản Phẩm #<?= htmlspecialchars($promo_id) ?></title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body {
            background: #f5f7fa;
        }

        .update-sale-card { /* Đổi tên class cho phù hợp */
            max-width: 550px; 
            margin: 50px auto; 
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 30px;
        }

        .update-sale-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-group {
            display: flex;
            align-items: flex-start; /* Thay đổi để canh lỗi được dễ hơn */
            margin-bottom: 25px; /* Tăng khoảng cách để chứa lỗi */
            position: relative;
        }

        .form-group p {
            width: 140px;
            margin-right: 15px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
            flex-shrink: 0;
            padding-top: 10px;
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

        .btn-primary { /* Giữ nguyên màu xanh dương cho Cập nhật */
            background: #2563eb;
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

        .btn-primary:hover {
            background: #1d4ed8;
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
            width: calc(100% - 155px); 
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

        /* Tablet (Max-width: 768px) */
        @media (max-width: 768px) {
            .update-sale-card {
                max-width: 90%; 
                margin: 40px auto;
                padding: 25px 20px;
            }
        }
        
        /* Mobile (Max-width: 576px) */
        @media (max-width: 576px) {
            .update-sale-card {
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
    <div class="update-sale-card">
        <h2>Cập Nhật Khuyến Mãi Sản Phẩm #<?= htmlspecialchars($promo_id) ?></h2>

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

        <form id="promoUpdateForm" method="POST" action="Update.php?id=<?= htmlspecialchars($promo_id) ?>" onsubmit="return validatePromoForm();" novalidate>
            
            <div class="form-group">
                <p><strong>Sản Phẩm:</strong></p>
                <select id="product_id" name="product_id" required>
                    <option value="">-- Chọn Sản Phẩm --</option>
                    <?php 
                    $current_product_id = $promo_data['product_id'] ?? null;
                    foreach ($products as $p): ?>
                        <option value="<?= htmlspecialchars($p['product_id']) ?>" 
                                <?= ((string)$current_product_id === (string)$p['product_id']) ? 'selected' : '' ?>>
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
                    value="<?= htmlspecialchars($promo_data['mota'] ?? '') ?>"
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
                    value="<?= htmlspecialchars($promo_data['giam'] ?? '') ?>"
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
                    value="<?= htmlspecialchars($promo_data['ngaybatdau'] ?? date('Y-m-d')) ?>"
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
                    value="<?= htmlspecialchars($promo_data['ngayketthuc'] ?? '') ?>"
                    required
                >
                <div class="validation-error" id="ngayketthucError"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Cập Nhật Khuyến Mãi Sản Phẩm
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
        document.getElementById('promoUpdateForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
    });
</script>