<?php 
// File: /Webbanhang/User/View/ResetPassword.php
// Biến $error và $token (token gốc) được truyền từ Controller

$pageTitle = "Đặt lại Mật khẩu";
$loginPath = '/Webbanhang/User/View/Entry.php'; // Đường dẫn quay lại trang đăng nhập
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Admin/assets/css/Template/styles.min.css" />
    <script src="../../Admin/assets/libs/jquery/dist/jquery.min.js"></script>

    <style>
        .invalid-feedback {
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
            display: none;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
         data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">

        <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <p class="text-center fw-bold mt-3" style="margin-top:-30px; font-size:20px;">
                                    Đặt lại Mật khẩu mới
                                </p>

                                <?php if (isset($error) && !empty($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="" onsubmit="return validateResetForm();" novalidate>
                                    <input type="hidden" name="action" value="reset_password">
                                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">

                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">Mật khẩu mới</label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                        <div class="invalid-feedback" id="newPasswordError"></div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="confirmPassword" class="form-label">Xác nhận Mật khẩu mới</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                        <div class="invalid-feedback" id="confirmPasswordError"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                        Đổi Mật khẩu
                                    </button>

                                    <div class="d-flex align-items-center justify-content-center">
                                        <a class="text-primary fw-bold" href="<?php echo $loginPath; ?>">
                                            Quay lại Đăng nhập
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Xóa trạng thái lỗi
        function clearErrorState(fieldId) {
            $('#' + fieldId).removeClass('is-invalid');
            $('#' + fieldId + 'Error').hide().text('');
        }

        // Thiết lập trạng thái lỗi
        function setErrorState(fieldId, message) {
            $('#' + fieldId).addClass('is-invalid');
            $('#' + fieldId + 'Error').text(message).show();
        }

        // Hàm kiểm tra form
        function validateResetForm() {
            let isValid = true;

            clearErrorState('newPassword');
            clearErrorState('confirmPassword');

            const newPassword = $('#newPassword').val().trim();
            const confirmPassword = $('#confirmPassword').val().trim();

            // --- 1. VALIDATE MẬT KHẨU MỚI ---
            if (newPassword === '') {
                setErrorState('newPassword', 'Vui lòng nhập mật khẩu mới.');
                isValid = false;
            } else if (newPassword.length < 6) {
                setErrorState('newPassword', 'Mật khẩu mới phải có ít nhất 6 ký tự.');
                isValid = false;
            }

            // --- 2. VALIDATE XÁC NHẬN MẬT KHẨU ---
            if (confirmPassword === '') {
                setErrorState('confirmPassword', 'Vui lòng xác nhận mật khẩu mới.');
                isValid = false;
            } else if (newPassword !== confirmPassword) {
                setErrorState('confirmPassword', 'Xác nhận mật khẩu không khớp với mật khẩu mới.');
                isValid = false;
            }

            return isValid;
        }

        $(document).ready(function() {
            // Xóa lỗi khi người dùng nhập lại
            $('#newPassword, #confirmPassword').on('input', function() {
                clearErrorState(this.id);
            });

            // Cuộn đến vị trí lỗi nếu có
            if ($('.alert-danger').length) {
                $('html, body').animate({
                    scrollTop: $('.alert-danger').offset().top - 50
                }, 500);
            }
        });
    </script>
</body>
</html>
