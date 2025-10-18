<?php
// File: Admin/view/category/Add.php (hoặc Create.php)

// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/CategoryController.php';

// Khởi tạo Controller và gọi phương thức create()
$controller = new CategoryController();

// Phương thức create() trong Controller sẽ:
// 1. Nếu là GET request: Chỉ hiển thị form.
// 2. Nếu là POST request: Xử lý thêm, sau đó chuyển hướng (header location) nếu thành công
//    hoặc trả về mảng chứa thông báo lỗi nếu thất bại.
$data = $controller->create();

$error_message = $data['error_message'] ?? null;
// Lấy giá trị cũ để giữ lại khi có lỗi validation
$old_tendanhmuc = $_POST['tendanhmuc'] ?? ''; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Danh Mục Mới</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
        }
        .add-category-card {
            max-width: 400px; /* Nhỏ hơn vì ít trường hơn */
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
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
            position: relative; /* Thêm position relative để định vị lỗi */
        }
        .form-group p {
            width: 130px;
            margin-right: 10px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
            flex-shrink: 0;
        }
        .form-group input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .error-message {
            color: #dc2626;
            margin-bottom: 15px;
            text-align: center;
        }
        
        /* CSS cho Validation */
        .input-error {
            border-color: #dc2626 !important; /* Màu đỏ cho input bị lỗi */
        }
        .validation-error {
            position: absolute;
            bottom: -18px; /* Đặt dưới input */
            right: 0;
            font-size: 0.85rem;
            color: #dc2626;
            margin-top: 5px;
            width: 100%; /* Đảm bảo thông báo lỗi không bị tràn */
            text-align: right; /* Căn phải cho gọn */
        }

        /* ==============================
        MEDIA QUERIES - Dành cho Mobile
        ==============================
        */
        @media (max-width: 576px) {
            .add-category-card {
                max-width: 95%;
                margin: 30px auto;
                padding: 20px 15px;
            }

            .form-group {
                flex-direction: column;
                align-items: flex-start;
                margin-bottom: 30px; /* Tăng khoảng cách để chứa lỗi */
            }

            .form-group p {
                width: 100%;
                margin-right: 0;
                margin-bottom: 8px;
                text-align: left;
            }

            .form-group input {
                width: 100%;
                font-size: 0.95rem;
            }

            .btn-success {
                font-size: 1rem;
            }
            .validation-error {
                bottom: -25px; /* Điều chỉnh vị trí lỗi trên mobile */
                text-align: left;
            }
        }
    </style>
</head>
<body>
    <div class="add-category-card">
        <h2>Thêm Danh Mục Mới</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form id="categoryForm" method="POST" action="Add.php" onsubmit="return validateForm();" novalidate>
            
            <div class="form-group">
                <p><strong>Tên danh mục:</strong></p>
                <input type="text" id="tendanhmuc" name="tendanhmuc" placeholder="Nhập tên danh mục" value="<?= htmlspecialchars($old_tendanhmuc) ?>" required>
                <div id="tendanhmucError" class="validation-error"></div>
            </div>

            <button type="submit" class="btn btn-success">Thêm Danh Mục</button>
        </form>

        <a href="../../index.php?page=danhmuc" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng danh mục
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    const MIN_LENGTH = 3; // Định nghĩa độ dài tối thiểu

    // Hàm xóa trạng thái lỗi
    function clearErrorState(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        
        if (field) field.classList.remove('input-error');
        if (errorDiv) errorDiv.innerHTML = '';
    }

    // Hàm thiết lập trạng thái lỗi
    function setErrorState(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + 'Error');
        
        if (field) field.classList.add('input-error');
        if (errorDiv) errorDiv.innerHTML = message;
    }

    // Hàm Validation chính
    function validateForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        clearErrorState('tendanhmuc');

        const tenDanhMucInput = document.getElementById('tendanhmuc');
        const tenDanhMuc = tenDanhMucInput.value.trim();
        
        // --- VALIDATE TÊN DANH MỤC ---
        if (tenDanhMuc === '') {
            setErrorState('tendanhmuc', 'Vui lòng nhập Tên danh mục.');
            isValid = false;
        } else if (tenDanhMuc.length < MIN_LENGTH) {
            setErrorState('tendanhmuc', 'Tên danh mục phải có ít nhất ' + MIN_LENGTH + ' ký tự.');
            isValid = false;
        }

        // Nếu validation thất bại, ngăn chặn gửi form
        if (!isValid) {
            // Focus vào trường bị lỗi đầu tiên
            if (!tenDanhMucInput.classList.contains('input-error')) {
                 tenDanhMucInput.focus();
            }
        }
        
        return isValid;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tenDanhMucInput = document.getElementById('tendanhmuc');

        // Xóa thông báo lỗi khi người dùng bắt đầu nhập
        if (tenDanhMucInput) {
            tenDanhMucInput.addEventListener('input', function() {
                // Tự động kiểm tra và xóa lỗi ngay khi người dùng nhập
                clearErrorState('tendanhmuc');
            });
        }
    });
    </script>
</body>
</html>