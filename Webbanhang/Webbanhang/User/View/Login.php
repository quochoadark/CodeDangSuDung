<?php
// Tên file: /Webbanhang/User/View/Login.php
// Biến $login_error được truyền từ Controller

// Lưu ý: View không cần session_start() nếu đã được gọi trong Controller/Entry.php

// Định nghĩa BASE_URL hoặc sử dụng đường dẫn tuyệt đối trực tiếp
// Giả định thư mục gốc của webserver là /Webbanhang/
$base_path = '/Webbanhang/Admin/assets/'; 
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Khách hàng</title>
    <link rel="shortcut icon" type="image/png" href="<?php echo $base_path; ?>images/logos/favicon.png" />
    
    <link rel="stylesheet" href="<?php echo $base_path; ?>css/Template/styles.min.css" />

    <style>
        /* CSS bổ sung để hiển thị rõ lỗi */
        /* Mặc định cho .is-invalid đã có trong styles.min.css */
        .invalid-feedback {
            /* Mặc định display: none, chỉ JS gọi .show() mới hiển thị */
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545; /* Màu đỏ lỗi */
        }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <p class="text-center fw-bold mt-3" style="margin-top:-30px; font-size: 20px;">Đăng nhập Khách hàng</p>
                                <?php
                                if (isset($login_error) && !empty($login_error)):
                                ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($login_error); ?>
                                    </div>
                                <?php endif; ?>

                                <form id="loginForm" method="post" action="/Webbanhang/User/View/Entry.php" onsubmit="return validateLoginForm();" novalidate>
                                    <div class="mb-3">
                                        <label for="emailInput" class="form-label">Tên đăng nhập (Email)</label>
                                        <input type="text" class="form-control" id="emailInput" name="email" aria-describedby="emailHelp" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                        <div class="invalid-feedback" id="emailInputError"></div> 
                                    </div>
                                    <div class="mb-4">
                                        <label for="passwordInput" class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" id="passwordInput" name="password" required>
                                        <div class="invalid-feedback" id="passwordInputError"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" value="on" id="flexCheckChecked" name="remember_me" checked>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                Nhớ trên thiết bị này
                                            </label>
                                        </div>
                                        <a class="text-primary fw-bold" href="ForgotPassword.php">Quên mật khẩu?</a>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Đăng nhập</button>
                                    
                                    <div class="d-flex align-items-center justify-content-center">
                                        <p class="fs-4 mb-0 fw-bold">Chưa có tài khoản?</p>
                                        <a class="text-primary fw-bold ms-2" href="/Webbanhang/User/View/Register.php">Đăng ký ngay</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo $base_path; ?>libs/jquery/dist/jquery.min.js"></script>
    <script src="<?php echo $base_path; ?>libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    
    <script>
    // Hàm xóa trạng thái lỗi
    function clearErrorState(fieldId) {
        $('#' + fieldId).removeClass('is-invalid');
        // Xóa thông báo lỗi
        $('#' + fieldId + 'Error').hide().text(''); 
    }

    // Hàm thiết lập trạng thái lỗi
    function setErrorState(fieldId, message) {
        $('#' + fieldId).addClass('is-invalid');
        // Đảm bảo ID này khớp với HTML: fieldId + 'Error'
        $('#' + fieldId + 'Error').text(message).show(); 
    }

    // Hàm Validation chính
    function validateLoginForm() {
        let isValid = true;
        
        // 1. Xóa trạng thái lỗi cũ
        clearErrorState('emailInput');
        clearErrorState('passwordInput');

        const email = $('#emailInput').val().trim();
        const password = $('#passwordInput').val().trim();
        
        // Regex kiểm tra định dạng email cơ bản
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // --- VALIDATE EMAIL ---
        if (email === '') {
            setErrorState('emailInput', 'Vui lòng nhập Email của bạn.');
            isValid = false;
        } else if (!emailRegex.test(email)) {
            setErrorState('emailInput', 'Email không đúng định dạng.');
            isValid = false;
        }

        // --- VALIDATE PASSWORD ---
        if (password === '') {
            setErrorState('passwordInput', 'Vui lòng nhập Mật khẩu.');
            isValid = false;
        } else if (password.length < 6) {
            setErrorState('passwordInput', 'Mật khẩu phải có ít nhất 6 ký tự.');
            isValid = false;
        }

        return isValid;
    }

    $(document).ready(function() {
        // Xóa thông báo lỗi khi người dùng bắt đầu nhập
        $('#emailInput, #passwordInput').on('input', function() {
            clearErrorState(this.id);
        });

        // Nếu có lỗi PHP ($login_error) hiển thị sẵn, cuộn lên để người dùng thấy
        if ($('.alert-danger').length) {
            $('html, body').animate({
                scrollTop: $('.alert-danger').offset().top - 50
            }, 500);
        }
    });
    </script>
</body>
</html>