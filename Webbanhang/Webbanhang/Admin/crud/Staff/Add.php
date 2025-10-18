<?php
// File: Admin/crud/Staff/Add.php (Thêm Nhân Viên)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/StaffController.php'; 

$controller = new StaffController();
// Gọi hàm create() của StaffController để xử lý POST request và lấy dữ liệu cần thiết
$data = $controller->create(); 

// --- 2. GÁN BIẾN DỮ LIỆU TỪ CONTROLLER ---
$staff_data = $data['staff_data'] ?? []; // Dữ liệu cũ nếu POST thất bại
$error_message = $data['error_message'] ?? null;
$roles = $data['roles'] ?? []; // Danh sách Chức vụ
// Lấy thông báo thành công từ Controller (sau khi redirect)
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Thêm nhân viên thành công!' : null);

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
    <title>Thêm Nhân Viên</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body { background: #f5f7fa; }
        .add-staff-card { 
            max-width: 500px; 
            margin: 50px auto; 
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 30px;
        }
        .add-staff-card h2 {
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
            padding-top: 10px; /* Căn chỉnh với input */
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
        /* --- Thông báo lỗi/thành công --- */
        .alert-danger {
            color: #842029;
            background-color: #f8d7da;
            border-color: #f5c2c7;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
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

        /* ==============================
        MEDIA QUERIES - Responsive
        ============================== */

        /* Tablet (Max-width: 768px) */
        @media (max-width: 768px) {
            .add-staff-card {
                max-width: 90%; 
                margin: 40px auto;
                padding: 25px 20px;
            }
        }
        
        /* Mobile (Max-width: 576px) */
        @media (max-width: 576px) {
            .add-staff-card {
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
    <div class="add-staff-card">
        <h2>Thêm Nhân Viên Mới</h2>

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

        <form id="staffAddForm" method="POST" action="Add.php" onsubmit="return validateStaffForm();" novalidate>
            <div class="form-group">
                <p><strong>Họ và Tên:</strong></p>
                <input type="text" id="hoten" name="hoten" placeholder="Nhập họ và tên" 
                        value="<?= getOldValue('hoten', $staff_data) ?>" required>
                <div class="validation-error" id="hotenError"></div>
            </div>

            <div class="form-group">
                <p><strong>Email:</strong></p>
                <input type="text" id="email" name="email" placeholder="Nhập email" 
                        value="<?= getOldValue('email', $staff_data) ?>" required>
                <div class="validation-error" id="emailError"></div>
            </div>

            <div class="form-group">
                <p><strong>Mật khẩu:</strong></p>
                <input type="password" id="matkhau" name="matkhau" placeholder="Nhập mật khẩu" required minlength="6">
                <div class="validation-error" id="matkhauError"></div>
            </div>
            
            <div class="form-group">
                <p><strong>Chức vụ:</strong></p>
                <select id="id_chucvu" name="id_chucvu" required>
                    <option value="">-- Chọn Chức vụ --</option>
                    <?php 
                    $selected_role = $staff_data['id_chucvu'] ?? '';
                    
                    foreach ($roles as $role): 
                        $selected = ((string)$selected_role === (string)$role['id_chucvu']) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($role['id_chucvu']) ?>"
                                <?= $selected ?>>
                            <?= htmlspecialchars($role['ten_chucvu']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="validation-error" id="id_chucvuError"></div>
            </div>

            <div class="form-group">
                <p><strong>Điện thoại:</strong></p>
                <input type="text" id="dienthoai" name="dienthoai" placeholder="Nhập số điện thoại (10-11 số)" 
                        value="<?= getOldValue('dienthoai', $staff_data) ?>"
                        minlength="10" 
                        maxlength="11"
                        pattern="[0-9]{10,11}">
                <div class="validation-error" id="dienthoaiError"></div>
            </div>
            
            <div class="form-group">
                <p><strong>Trạng thái:</strong></p>
                <select id="trangthai" name="trangthai" required>
                    <?php 
                    $selected_status = $staff_data['trangthai'] ?? '1'; 
                    ?>
                    <option value="1" <?= ($selected_status == '1') ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="0" <?= ($selected_status == '0') ? 'selected' : '' ?>>Ngừng hoạt động</option>
                </select>
                </div>

            <button type="submit" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Thêm Nhân Viên
            </button>
        </form>

        <a href="../../index.php?page=nhanvien" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng nhân viên
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

    function validateStaffForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        const fieldIds = ['hoten', 'email', 'matkhau', 'id_chucvu', 'dienthoai'];
        fieldIds.forEach(id => clearErrorState(id));

        const hoTen = document.getElementById('hoten').value.trim();
        const email = document.getElementById('email').value.trim();
        const matKhau = document.getElementById('matkhau').value;
        const idChucVu = document.getElementById('id_chucvu').value;
        const dienThoai = document.getElementById('dienthoai').value.trim();

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{10,11}$/;

        // --- HỌ VÀ TÊN ---
        if (hoTen === '') {
            setErrorState('hoten', 'Vui lòng nhập Họ và Tên.');
            isValid = false;
        }

        // --- EMAIL ---
        if (email === '') {
            setErrorState('email', 'Vui lòng nhập Email.');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            setErrorState('email', 'Vui lòng nhập định dạng email hợp lệ.');
            isValid = false;
        }

        // --- MẬT KHẨU ---
        if (matKhau === '') {
            setErrorState('matkhau', 'Vui lòng nhập Mật khẩu.');
            isValid = false;
        } else if (matKhau.length < 6) {
            setErrorState('matkhau', 'Mật khẩu phải có ít nhất 6 ký tự.');
            isValid = false;
        }

        // --- CHỨC VỤ ---
        if (idChucVu === '') {
            setErrorState('id_chucvu', 'Vui lòng chọn Chức vụ.');
            isValid = false;
        }

        // --- ĐIỆN THOẠI (Không bắt buộc, nhưng nếu nhập thì phải đúng format) ---
        if (dienThoai !== '') {
            if (!phoneRegex.test(dienThoai)) {
                setErrorState('dienthoai', 'Số điện thoại không hợp lệ (10 hoặc 11 chữ số).');
                isValid = false;
            }
        }

        // Cuộn đến trường lỗi đầu tiên nếu validation thất bại
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
        // Xóa thông báo lỗi khi người dùng bắt đầu nhập/thay đổi
        document.getElementById('staffAddForm').addEventListener('input', function(event) {
            if (event.target.id) {
                clearErrorState(event.target.id);
            }
        });
    });
</script>