<?php
// File: Admin/crud/User/SendEmail.php (Gửi Email Khách Hàng)

// Đường dẫn cần điều chỉnh tùy theo vị trí file Controller.php
// CHÚ Ý: Đã thay đổi thành UserController.php
require_once __DIR__ . '/../../Controller/UserController.php'; 

// CHÚ Ý: Đã thay đổi thành UserController
$controller = new UserController();

// Gọi phương thức xử lý dựa trên loại request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Xử lý khi người dùng nhấn Gửi
    // Lưu ý: Đảm bảo UserController có phương thức handleSendEmail()
    $data = $controller->handleSendEmail(); 
} else {
    // Hiển thị form ban đầu
    // Lưu ý: Đảm bảo UserController có phương thức sendEmailForm()
    $data = $controller->sendEmailForm(); 
}

// CHÚ Ý: Đã thay đổi các biến staff_ thành user_
$user_info = $data['user_info'] ?? [];
$user_data_posted = $data['user_data'] ?? [];
$error_message = $data['error_message'] ?? null;
$success_message = $data['success_message'] ?? null;

// Lấy giá trị mặc định cho form
$subject_value = htmlspecialchars($user_data_posted['subject'] ?? '');
$body_value = htmlspecialchars($user_data_posted['body'] ?? ''); 
// CHÚ Ý: Đã thay đổi staff_id thành user_id
$user_id_value = $user_info['user_id'] ?? $user_data_posted['user_id'] ?? 0;

// Xử lý trường hợp không tìm thấy khách hàng
if (empty($user_info) && $user_id_value != 0) {
    // CHÚ Ý: Đã thay đổi đường dẫn quay lại thành khachhang
    die('<div class="send-email-card" style="max-width: 400px; margin-top: 100px;"><h2>Lỗi</h2><p>Không tìm thấy khách hàng để gửi email.</p><a href="../../index.php?page=khachhang" class="back-link">Quay lại</a></div>');
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gửi Email Khách Hàng</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* ==============================
        CSS Dành cho PC (Mặc định)
        ============================== */
        body { background: #f5f7fa; }
        .send-email-card { /* Đổi tên class */
            max-width: 550px; 
            margin: 50px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 30px;
        }
        .send-email-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        /* Cấu trúc Form Group tùy chỉnh giống Add.php */
        .form-group { 
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .form-group p {
            width: 140px; 
            margin-right: 15px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
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
        }
        .form-group textarea {
            resize: vertical;
        }
        /* --- Nút bấm --- */
        .btn-success {
            background: #16a34a;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            color: #fff;
            font-size: 1.1rem;
            padding: 12px 20px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-success:hover {
            background: #15803d;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 20px;
            transition: background 0.2s;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        /* --- Thông báo --- */
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
            border-color: #badbcc;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        .recipient-info-p {
            margin-bottom: 20px;
            color: #5a6a85;
            text-align: center;
        }
        .recipient-info-p strong {
            color: #000;
            font-weight: 600;
        }
        /* Footer cho nút bấm */
        .form-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
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

        /* ==============================
        MEDIA QUERIES - Responsive
        ============================== */

        /* Mobile (Max-width: 576px) */
        @media (max-width: 576px) {
            .send-email-card {
                max-width: 95%; 
                margin: 20px auto;
                padding: 20px 15px;
            }
            .form-group {
                flex-direction: column; 
                align-items: stretch;
                margin-bottom: 20px;
            }
            .form-group p {
                width: 100%; 
                margin-right: 0;
                margin-bottom: 5px; 
            }
            .form-group input,
            .form-group textarea {
                width: 100%;
                font-size: 0.95rem;
            }
            .form-footer {
                flex-direction: column-reverse;
                gap: 15px;
            }
            .form-footer button,
            .form-footer a {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="send-email-card">
        <h2><i class="fas fa-envelope"></i> Gửi Email đến Khách hàng</h2>

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
        
        <?php 
        // Chỉ hiển thị FORM nếu KHÔNG có thông báo gửi thành công
        if (!$success_message): 
        ?>
            
            <p class="recipient-info-p">
                Gửi đến: <strong><?= htmlspecialchars($user_info['hoten'] ?? 'N/A') ?></strong> 
                (<?= htmlspecialchars($user_info['email'] ?? 'N/A') ?>)
            </p>

            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?= $user_id_value ?>">

                <div class="form-group">
                    <p><strong>Tiêu đề Email:</strong></p>
                    <input type="text" id="subject" name="subject" placeholder="Nhập tiêu đề email" 
                                value="<?= $subject_value ?>" required>
                </div>

                <div class="form-group" style="align-items: flex-start;">
                    <p style="padding-top: 10px;"><strong>Nội dung:</strong></p>
                    <textarea id="body" name="body" rows="8" required 
                                placeholder="Nhập nội dung email..."><?= $body_value ?></textarea>
                </div>
                
                <div class="form-footer">
                    <a href="../../index.php?page=khachhang" class="btn btn-secondary">
                        <i class="fas fa-times-circle"></i> Hủy
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> Gửi Email Ngay
                    </button>
                </div>
            </form>
            
        <?php endif; // Kết thúc khối hiển thị FORM ?>
        
        <a href="../../index.php?page=khachhang" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng khách hàng
        </a>
    </div>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Custom HTML5 Validation tiếng Việt
    const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
    
    requiredFields.forEach(field => {
        field.oninvalid = function() {
            if (field.validity.valueMissing) {
                field.setCustomValidity('Vui lòng điền vào trường này');
            } else {
                field.setCustomValidity('');
            }
        };

        field.oninput = function() {
            field.setCustomValidity('');
        };
    });
});
</script>