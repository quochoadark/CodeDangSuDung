<?php
// File: Admin/crud/Voucher/Add.php (Thêm Mã Khuyến Mãi)
require_once __DIR__ . '/../../Controller/VoucherController.php';

$controller = new VoucherController();
$data = $controller->create();

$voucher_data = $data['voucher_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Thêm mã khuyến mãi thành công!' : null);

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
    <title>Thêm Mã Khuyến Mãi</title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        /* CSS Dành cho PC (Mặc định) */
        body { background: #f5f7fa; }
        .add-voucher-card {
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 30px;
        }
        .add-voucher-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        .form-group {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px; 
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
        .form-group input:focus, .form-group select:focus {
            border-color: #16a34a;
            outline: none;
            box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
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
            margin-top: 25px;
            color: #5a6a85;
            text-decoration: none;
            font-size: 0.98rem;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #16a34a;
        }
        /* Chú thích cho trường nhập */
        .input-tooltip {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: -10px;
            margin-left: 155px;
            margin-bottom: 15px; 
            display: block;
        }
        /* Style cho thông báo lỗi Validation */
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
        /* MEDIA QUERIES */
        @media (max-width: 576px) {
            .form-group { flex-direction: column; align-items: stretch; margin-bottom: 30px; }
            .form-group p { width: 100%; margin-right: 0; margin-bottom: 5px; padding-top: 0;}
            .input-tooltip { margin-left: 0; text-align: left; margin-top: -15px; margin-bottom: 15px; }
            .validation-error { width: 100%; text-align: left; bottom: -22px; } 
        }
    </style>
</head>
<body>
    <div class="add-voucher-card">
        <h2><i class="fas fa-gift"></i> Thêm Mã Khuyến Mãi Mới</h2>

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

        <form id="voucherAddForm" method="POST" action="Add.php" onsubmit="return validateVoucherAddForm();" novalidate>
            <div class="form-group">
                <p><strong>Mã Khuyến Mãi:</strong></p>
                <input
                    type="text"
                    id="makhuyenmai"
                    name="makhuyenmai"
                    placeholder="VD: GIAM20K, FREESHIP"
                    value="<?= getOldValue('makhuyenmai', $voucher_data) ?>"
                    required
                >
                <div class="validation-error" id="makhuyenmaiError"></div>
            </div>
            
            <div class="form-group">
                <p><strong>Giá Trị Giảm:</strong></p>
                <input
                    type="number"
                    step="0.01"
                    min="0.01"
                    id="giam"
                    name="giam"
                    placeholder="VD: 50000 hoặc 0.1"
                    value="<?= getOldValue('giam', $voucher_data) ?>"
                    required
                >
                <div class="validation-error" id="giamError"></div>
            </div>
            <p class="input-tooltip">Nhập số tiền giảm (VD: 50000) hoặc tỉ lệ giảm (VD: 0.1 cho 10%). Phải lớn hơn 0.</p>

            <div class="form-group">
                <p><strong>Ngày Hết Hạn:</strong></p>
                <input
                    type="date"
                    id="ngayhethan"
                    name="ngayhethan"
                    value="<?= getOldValue('ngayhethan', $voucher_data) ?: date('Y-m-d', strtotime('+1 month')) ?>"
                    required
                >
                <div class="validation-error" id="ngayhethanError"></div>
            </div>
            
            <div class="form-group">
                <p><strong>Số Lượng (Lần):</strong></p>
                <input
                    type="number"
                    min="1"
                    step="1"
                    id="soluong"
                    name="soluong"
                    placeholder="Để trống nếu không giới hạn"
                    value="<?= getOldValue('soluong', $voucher_data) ?>"
                >
                <div class="validation-error" id="soluongError"></div>
            </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Thêm Mã Khuyến Mãi
            </button>
        </form>

        <a href="../../index.php?page=voucher" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng mã khuyến mãi
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

    /**
     * Hàm chính thực hiện Client-side Validation cho form Thêm Voucher (Đã fix lỗi số lượng)
     */
    function validateVoucherAddForm() {
        let isValid = true;
        
        const fieldIds = ['makhuyenmai', 'giam', 'ngayhethan', 'soluong'];
        fieldIds.forEach(id => clearErrorState(id));

        const maKhuyenMai = document.getElementById('makhuyenmai').value.trim();
        const giam = parseFloat(document.getElementById('giam').value);
        const ngayHetHan = document.getElementById('ngayhethan').value;
        const soLuongInput = document.getElementById('soluong').value.trim();

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const expiryDate = new Date(ngayHetHan);

        // --- MÃ KHUYẾN MÃI (Bắt buộc) ---
        if (maKhuyenMai === '') {
            setErrorState('makhuyenmai', 'Vui lòng nhập Mã Khuyến Mãi.');
            isValid = false;
        }

        // --- GIÁ TRỊ GIẢM (Bắt buộc) ---
        if (isNaN(giam)) {
            setErrorState('giam', 'Vui lòng nhập Giá Trị Giảm.');
            isValid = false;
        } else if (giam <= 0) {
            setErrorState('giam', 'Giá Trị Giảm phải lớn hơn 0.');
            isValid = false;
        }

        // --- NGÀY HẾT HẠN (Bắt buộc, không trong quá khứ) ---
        if (ngayHetHan === '') {
            setErrorState('ngayhethan', 'Vui lòng chọn Ngày Hết Hạn.');
            isValid = false;
        } else if (expiryDate < today) {
            setErrorState('ngayhethan', 'Ngày Hết Hạn không thể là ngày trong quá khứ.');
            isValid = false;
        }

        if (!isValid) {
            const firstErrorField = document.querySelector('.input-error');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorField.focus();
            }
        }
        
        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('voucherAddForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
    });
</script>