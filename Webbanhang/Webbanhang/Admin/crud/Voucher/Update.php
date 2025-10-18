<?php
// File: Admin/crud/Voucher/Update.php (Cập Nhật Mã Khuyến Mãi)
require_once __DIR__ . '/../../Controller/VoucherController.php';

$controller = new VoucherController();
// Hàm edit() sẽ xử lý cả GET (lấy dữ liệu) và POST (cập nhật dữ liệu)
$data = $controller->edit(); 

$voucher_data = $data['voucher_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Cập nhật mã khuyến mãi thành công!' : null);

// Lấy ID từ data hoặc GET để đảm bảo form action có ID
$voucher_id = $voucher_data['voucher_id'] ?? (isset($_GET['id']) ? (int) $_GET['id'] : 0); 

if (empty($voucher_data) && $error_message) {
    // Nếu không tìm thấy voucher_data và có lỗi (do ID không hợp lệ)
    die('<div class="update-voucher-card error-card" style="max-width: 400px; margin: 100px auto; padding: 25px; background: #fff; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"><h2>Lỗi</h2><p style="color: #dc2626;">' . htmlspecialchars("Không tìm thấy Mã khuyến mãi hoặc ID không hợp lệ.") . '</p><a href="../../index.php?page=voucher" class="back-link">Quay lại</a></div>');
}

// Helper function để lấy giá trị
function getValue($key, $data) {
    return htmlspecialchars($data[$key] ?? '');
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Mã Khuyến Mãi #<?= htmlspecialchars($voucher_id) ?></title>

    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">

    <style>
        /* CSS Dành cho PC (Mặc định) */
        body { background: #f5f7fa; }
        .update-voucher-card { 
            max-width: 500px;
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 32px 30px;
        }
        .update-voucher-card h2 {
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
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        .btn-primary {
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
    <div class="update-voucher-card">
        <h2><i class="fas fa-edit"></i> Cập Nhật Mã Khuyến Mãi</h2>

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

        <form id="voucherUpdateForm" method="POST" action="Update.php?id=<?= htmlspecialchars($voucher_id) ?>" onsubmit="return validateVoucherUpdateForm(event);" novalidate>
            <input type="hidden" name="voucher_id" value="<?= htmlspecialchars($voucher_id) ?>">
            
            <div class="form-group">
                <p><strong>Mã Khuyến Mãi:</strong></p>
                <input
                    type="text"
                    id="makhuyenmai"
                    name="makhuyenmai"
                    placeholder="VD: GIAM20K, FREESHIP"
                    value="<?= getValue('makhuyenmai', $voucher_data) ?>"
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
                    value="<?= getValue('giam', $voucher_data) ?>"
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
                    value="<?= getValue('ngayhethan', $voucher_data) ?>"
                    required
                >
                <div class="validation-error" id="ngayhethanError"></div>
            </div>

            <div class="form-group">
                <p><strong>Số Lượng (Lần):</p>
                <input
                    type="number"
                    min="1"
                    step="1"
                    id="soluong"
                    name="soluong"
                    placeholder="Để trống nếu không giới hạn"
                    value="<?= getValue('soluong', $voucher_data) ?>"
                >
                <div class="validation-error" id="soluongError"></div>
            </div>
            
            <div class="form-group">
                <p><strong>Trạng thái:</strong></p>
                <select id="trangthai" name="trangthai" required>
                    <?php $current_status = $voucher_data['trangthai'] ?? 1; ?>
                    <option value="1" <?= $current_status == 1 ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="0" <?= $current_status == 0 ? 'selected' : '' ?>>Ngừng/Hết hạn</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Lưu Cập Nhật
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
     * Hàm chính thực hiện Client-side Validation cho form Cập Nhật.
     * Đã FIX: Kiểm tra nghiêm ngặt Số Lượng (chặn chữ/số thập phân) và ngăn submit nếu có lỗi.
     */
    function validateVoucherUpdateForm(event) { // Nhận sự kiện event
        let isValid = true;
        
        const fieldIds = ['makhuyenmai', 'giam', 'ngayhethan', 'soluong'];
        fieldIds.forEach(id => clearErrorState(id));

        const maKhuyenMai = document.getElementById('makhuyenmai').value.trim();
        const giam = parseFloat(document.getElementById('giam').value);
        const ngayHetHan = document.getElementById('ngayhethan').value;
        const soLuongInput = document.getElementById('soluong').value.trim();

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

        // --- NGÀY HẾT HẠN (Bắt buộc) ---
        if (ngayHetHan === '') {
            setErrorState('ngayhethan', 'Vui lòng chọn Ngày Hết Hạn.');
            isValid = false;
        } 
        if (!isValid) {
            // Ngăn chặn form submit
            if (event && event.preventDefault) {
                 event.preventDefault(); 
            }
            const firstErrorField = document.querySelector('.input-error');
            if (firstErrorField) {
                firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstErrorField.focus();
            }
            return false;
        }
        
        return true; // Cho phép submit nếu hợp lệ
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Gắn sự kiện để xóa thông báo lỗi khi người dùng bắt đầu nhập/thay đổi
        document.getElementById('voucherUpdateForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
    });
</script>