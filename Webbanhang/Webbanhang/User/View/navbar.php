<?php
// Tên file: navbar.php

// BƯỚC 1: Đảm bảo session được khởi động
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Khởi tạo biến đếm giỏ hàng
$cart_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

// BƯỚC 2: Kiểm tra trạng thái đăng nhập và tên người dùng
$is_logged_in = isset($_SESSION['hoten']);
$user_name = $is_logged_in ? htmlspecialchars($_SESSION['hoten']) : 'Khách';

?>
<div class="container-fluid fixed-top">
    <div class="container px-0">
        <nav class="navbar navbar-light bg-white navbar-expand-xl">
            <a href="index.php" class="navbar-brand">
                <h1 class="text-primary display-6">LaptopShop</h1>
            </a>
            <button class="navbar-toggler py-2 px-3" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse">
                <span class="fa fa-bars text-primary"></span>
            </button>
            <div class="collapse navbar-collapse bg-white" id="navbarCollapse">
                <div class="navbar-nav mx-auto">
                    <a href="index.php" class="nav-item nav-link active">Trang chủ</a>
                    <a href="shop.php" class="nav-item nav-link">Sản phẩm</a>
                    <a href="shop-detail.php" class="nav-item nav-link">Chi tiết</a>
                    <a href="checkout.php" class="nav-item nav-link">Thanh toán</a>
                    <a href="testimonial.php" class="nav-item nav-link">Đánh giá</a>
                </div>
                <div class="d-flex m-3 me-0">
                    <a href="cart.php" class="position-relative me-4 my-auto">
                        <i class="fa fa-shopping-bag fa-2x"></i>
                        <span
                            class="position-absolute bg-secondary rounded-circle d-flex align-items-center justify-content-center text-dark px-1"
                            style="top: -5px; left: 15px; height: 20px; min-width: 20px;"> 
                            <?php echo $cart_count; ?> 
                        </span>
                    </a>
                    
                    <?php 
                    if ($is_logged_in): 
                    ?>
                        <div class="dropdown my-auto">
                            <a class="nav-link d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user fa-2x"></i> 
                                <span style="font-weight: 500; font-size: 1.15rem; margin-left: 8px;">
                                    <?php echo $user_name; ?>
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li><a class="dropdown-item" href="#">Thông tin của tôi</a></li>
                                <li><a class="dropdown-item" href="lichsudonhang.php">Lịch sử mua hàng</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="/Webbanhang/User/View/Logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="dropdown my-auto">
                            <a class="nav-link d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user fa-2x"></i>
                                </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item" href="/Webbanhang/User/View/Login.php">
                                        <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </div>
        </nav>
    </div>
</div>