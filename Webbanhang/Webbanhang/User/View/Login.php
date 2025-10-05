<?php
// Tên file: /Webbanhang/view/Login.php
// KHÔNG dùng session_start() ở đây vì nó đã được gọi trong index.php (Entry Point)
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng nhập Khách hàng</title>
    <!-- Các liên kết tài nguyên của template -->
    <link rel="shortcut icon" type="image/png" href="../../Admin/assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../../Admin/assets/css/Template/styles.min.css" />
    <!-- Thêm Bootstrap CSS nếu styles.min.css không bao gồm -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <!-- Giữ nguyên layout cột để card căn giữa -->
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                    
                                <p class="text-center fw-bold mt-3" style="margin-top:-30px; font-size: 20px;">Đăng nhập</p>
                                
                                <?php 
                                // Biến $login_error được truyền từ Controller
                                // Kiểm tra và hiển thị lỗi (nếu có)
                                if (isset($login_error) && !empty($login_error)): 
                                ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo htmlspecialchars($login_error); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="/Webbanhang/User/View/Entry.php"> 
                                    <div class="mb-3">
                                        <label for="exampleInputEmail1" class="form-label">Tên đăng nhập (Email)</label>
                                        <input type="email" class="form-control" id="exampleInputEmail1" name="email" aria-describedby="emailHelp" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="exampleInputPassword1" class="form-label">Mật khẩu</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input primary" type="checkbox" value="" id="flexCheckChecked" checked>
                                            <label class="form-check-label text-dark" for="flexCheckChecked">
                                                Nhớ trên thiết bị này
                                            </label>
                                        </div>
                                        <a class="text-primary fw-bold" href="#">Quên mật khẩu?</a>
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
    
    <!-- Các tài nguyên JS của template -->
    <script src="../../Admin/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../Admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>
