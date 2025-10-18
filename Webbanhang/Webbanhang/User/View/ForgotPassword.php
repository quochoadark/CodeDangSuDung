<?php 
// File: /Webbanhang/User/View/ForgotPassword.php
// Biến $message và $error được truyền từ Controller

$pageTitle = "Quên Mật Khẩu"; 
$loginPath = '/Webbanhang/User/View/Entry.php'; // Đường dẫn quay lại trang đăng nhập
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../../Admin/assets/css/Template/styles.min.css" />
    <script src="../../Admin/assets/libs/jquery/dist/jquery.min.js"></script>

    <style>
        /* CSS hiển thị lỗi */
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
                                    Lấy lại Mật khẩu
                                </p>

                                <?php if (isset($error) && !empty($error)): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($error); ?>
                                    </div>
                                <?php elseif (isset($message) && !empty($message)): ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php echo htmlspecialchars($message); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="/Webbanhang/User/View/Entry.php"
                                      onsubmit="return validateForgotPasswordForm();" novalidate>
                                    <input type="hidden" name="action" value="request_reset">

                                    <div class="mb-3">
                                        <label for="emailInput" class="form-label">Email đã đăng ký</label>
                                        <input type="email" class="form-control" id="emailInput" name="email" required>
                                        <div class="invalid-feedback" id="emailInputError"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                        Gửi liên kết đặt lại
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

        // Thiết lập lỗi
        function setErrorState(fieldId, message) {
            $('#' + fieldId).addClass('is-invalid');
            $('#' + fieldId + 'Error').text(message).show();
        }

        // Kiểm tra form
        function validateForgotPasswordForm() {
            let isValid = true;
            clearErrorState('emailInput');

            const email = $('#emailInput').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email === '') {
                setErrorState('emailInput', 'Vui lòng nhập Email để đặt lại mật khẩu.');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                setErrorState('emailInput', 'Email không đúng định dạng. Vui lòng kiểm tra lại.');
                isValid = false;
            }

            return isValid;
        }

        $(document).ready(function() {
            $('#emailInput').on('input', function() {
                clearErrorState(this.id);
            });

            if ($('.alert').length) {
                $('html, body').animate({
                    scrollTop: $('.alert').offset().top - 50
                }, 500);
            }
        });
    </script>
</body>
</html>
