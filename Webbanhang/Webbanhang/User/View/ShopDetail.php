<?php
// Tên tệp: User/View/ShopDetail.php
// Các biến: $product, $reviews, $average_rating, $review_count, $ton_kho, $product_id, $quantity
// được truyền từ ShopDetailController.php

// Đảm bảo session_start đã được gọi ở Controller
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra xem dữ liệu sản phẩm có tồn tại hay không (Dựa vào Controller)
$product_exists = isset($product) && is_array($product) && !empty($product);

// Chuẩn bị các biến cho hiển thị (Đảm bảo không lỗi Undefined Index/Variable)
if ($product_exists) {
    $product_id = $product_id ?? 0;
    $ton_kho = $ton_kho ?? 0; 
    $average_rating = $average_rating ?? 0.0; 
    $reviews = $reviews ?? []; 
    $review_count = count($reviews);
    
    // Lấy thông tin người dùng từ Session
    $is_logged_in_check = isset($_SESSION['kh_user_id']); 
    $user_name_display = $is_logged_in_check 
        ? (isset($_SESSION['kh_hoten']) ? htmlspecialchars($_SESSION['kh_hoten']) : 'Người dùng') 
        : '';
    $user_email_display = $is_logged_in_check 
        ? (isset($_SESSION['kh_email']) ? htmlspecialchars($_SESSION['kh_email']) : '') 
        : '';
} else {
    // Trường hợp không có sản phẩm
    $product_id = 0; // Đảm bảo $product_id được định nghĩa
    $reviews = [];
    $review_count = 0;
    $is_logged_in_check = isset($_SESSION['kh_user_id']); 
    $user_name_display = '';
    $user_email_display = '';
}

// --- Lấy và Xóa thông báo lỗi/thành công từ Controller ---
$session_error = $_SESSION['error_message'] ?? '';
$session_success = $_SESSION['success_message'] ?? '';
unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
// -----------------------------------------------------------
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Chi tiết sản phẩm - LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <style>
        /* Đã sửa đổi CSS để đảm bảo màu vàng nhất quán */
        .fa-star.text-secondary,
        .rating-stars .fa-star.text-secondary {
            color: #FFC107 !important; /* Màu vàng Gold */
        }
        /* Tùy chỉnh để hiển thị thông báo lỗi đánh giá */
        #review-alert-container {
            min-height: 40px; /* Đảm bảo chiều cao cho thông báo lỗi */
        }
    </style>
</head>

<body>
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>

    <?php include 'navbar.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

            navLinks.forEach(link => {
                const linkPath = link.getAttribute('href').split('/').pop();
                if (currentPath.endsWith(linkPath) && linkPath !== '') {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
                const parentDropdown = link.closest('.dropdown');
                if (parentDropdown && currentPath.includes(linkPath)) {
                    parentDropdown.querySelector('.nav-link.dropdown-toggle').classList.add('active');
                }
            });
            const shopLink = document.querySelector('.navbar-nav .nav-link[href="ShopController.php"]');
            if (shopLink) shopLink.classList.add('active');
            const homeLink = document.querySelector('.navbar-nav .nav-link[href="index.php"]');
            if (homeLink) homeLink.classList.remove('active');
        });
    </script>

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Chi tiết sản phẩm</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item">
                <a href="./index.php" style="color: #7CFC00;">Trang chủ</a>
            </li>
            <li class="breadcrumb-item active text-white">Chi tiết sản phẩm</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <div class="row g-4 mb-5 justify-content-center">
                <div class="col-lg-10 col-xl-10 mx-auto">
                    <div class="row g-4">
                        <?php if ($product_exists) : ?>
                            <div class="col-lg-6">
                                <div class="border rounded position-relative">
                                    <?php
                                    $is_promotion = isset($product['promotion_percent']) && $product['promotion_percent'] > 0;
                                    $display_percent = $is_promotion ? round($product['promotion_percent'] * 100) : 0;
                                    ?>
                                    <div class="position-absolute rounded-circle text-white 
                                        <?php echo $is_promotion ? 'bg-danger' : 'bg-secondary'; ?>"
                                        style="width: 60px; height: 60px; top: 10px; left: 10px; 
                                            display: flex; align-items: center; justify-content: center; 
                                            font-size: 0.9rem; font-weight: bold;">
                                        -<?php echo $display_percent; ?>%
                                    </div>
                                    <img src="../../Admin/uploads/<?php echo htmlspecialchars($product['img']); ?>"
                                        class="img-fluid rounded"
                                        alt="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <h4 class="fw-bold mb-3 change-font">
                                    <?php echo htmlspecialchars($product['tensanpham']); ?>
                                </h4>
                                <p class="mb-3">
                                    Danh mục:
                                    <strong><?php echo htmlspecialchars($product['tendanhmuc']); ?></strong>
                                </p>

                                <?php if ($is_promotion): ?>
                                    <h6 class="text-secondary fw-normal mb-1" style="text-decoration: line-through;">
                                        <?php echo number_format($product['original_price'], 0, ',', '.'); ?> VNĐ
                                    </h6>
                                    <h4 class="fw-bold mb-3 change-font text-danger">
                                        <?php echo number_format($product['gia'], 0, ',', '.'); ?> VNĐ
                                    </h4>
                                    <p class="mb-3 text-success fw-bold">
                                        <i class="fa fa-tag me-1"></i>
                                        Giảm: <?php echo htmlspecialchars($product['display_discount']); ?>
                                        (<?php echo htmlspecialchars($product['promotion_description']); ?>)
                                    </p>
                                <?php else: ?>
                                    <h4 class="fw-bold mb-3 change-font text-dark">
                                        <?php echo number_format($product['gia'], 0, ',', '.'); ?> VNĐ
                                    </h4>
                                <?php endif; ?>

                                <div class="d-flex mb-4">
                                    <?php
                                    // Hiển thị đánh giá trung bình
                                    $avg_rating = round($average_rating);
                                    for ($i = 1; $i <= 5; $i++) {
                                        $star_class = ($i <= $avg_rating) ? 'text-secondary' : '';
                                        echo '<i class="fa fa-star ' . $star_class . '"></i>';
                                    }
                                    ?>
                                    <span class="ms-2">(<?php echo $review_count; ?> nhận xét)</span>
                                </div>

                                <p class="mb-4">
                                    <?php echo nl2br(htmlspecialchars($product['mota'])); ?>
                                </p>
                                <div class="mb-5">
                                    <p class="mb-1 text-muted">
                                        Số lượng còn lại:
                                        <strong><?php echo htmlspecialchars($ton_kho); ?></strong>
                                    </p>
                                </div>

                                <form action="CartController.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="add_to_cart">
                                    <input type="hidden" name="product_id"
                                        value="<?php echo htmlspecialchars($product_id); ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                        class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary"
                                        <?php echo $ton_kho <= 0 ? 'disabled' : ''; ?>>
                                        <i class="fa fa-shopping-bag me-2 text-primary"></i>
                                        <?php echo $ton_kho > 0 ? 'Thêm vào giỏ hàng' : 'Hết hàng'; ?>
                                    </button>
                                </form>
                            </div>
                            <?php else : ?>
                            <div class="col-12 text-center py-5">
                                <h3 class="text-danger mb-3">Sản phẩm không tồn tại hoặc không được tìm thấy.</h3>
                                <p class="fs-5 text-muted">Vui lòng quay lại trang cửa hàng để chọn sản phẩm.</p>
                                <a href="ShopController.php" class="btn btn-primary mt-3">Quay lại trang sản phẩm</a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($product_exists) : ?>
                        <div class="col-lg-12 mt-5">
                            <nav>
                                <div class="nav nav-tabs mb-3">
                                    <button class="nav-link border-white border-bottom-0 active" type="button" role="tab"
                                        id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission"
                                        aria-controls="nav-mission" aria-selected="true">
                                        Đánh giá (<?php echo $review_count; ?>)
                                    </button>
                                </div>
                            </nav>

                            <div class="tab-content mb-5">
                                <div class="tab-pane fade show active" id="nav-mission" role="tabpanel"
                                    aria-labelledby="nav-mission-tab">
                                    <?php if ($review_count > 0) : ?>
                                        <h5 class="mb-4">
                                            Tổng cộng <?php echo $review_count; ?> nhận xét
                                            (Điểm TB: <strong><?php echo number_format($average_rating, 1); ?></strong>/5)
                                        </h5>

                                        <?php foreach ($reviews as $review) : ?>
                                            <div class="d-flex mb-4 border-bottom pb-3">
                                                <div class="w-100">
                                                    <div class="d-flex justify-content-between">
                                                        <h5><?php echo htmlspecialchars($review['hoten'] ?? 'Khách hàng'); ?></h5>
                                                        <div class="d-flex mb-3">
                                                            <?php
                                                            // Hiển thị sao đánh giá
                                                            $rating = intval($review['danhgia'] ?? 0);
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                $star_class = ($i <= $rating) ? 'text-secondary' : '';
                                                                echo '<i class="fa fa-star ' . $star_class . '"></i>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                    <p class="mb-2" style="font-size: 14px; color: #6c757d;">
                                                        Ngày: <?php echo date('d/m/Y', strtotime($review['ngaytao'] ?? 'now')); ?>
                                                    </p>
                                                    <p class="text-dark">
                                                        <?php echo nl2br(htmlspecialchars($review['binhluan'] ?? '')); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <p>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên nhận xét! 📝</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <form action="ReviewController.php" method="POST" id="reviewForm">
                            <h4 class="mb-3 fw-bold">Nhận xét của bạn</h4>
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                            <input type="hidden" name="action" value="add_review">

                            <?php if (!$is_logged_in_check): ?>
                                <div class="alert alert-danger" role="alert">
                                    Vui lòng <a href="../View/Entry.php" class="alert-link fw-bold">Đăng nhập</a> để đăng nhận xét.
                                </div>
                            <?php endif; ?>
                            
                            <div id="review-alert-container" class="mb-4">
                                <?php if ($session_success): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo htmlspecialchars($session_success); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if ($session_error): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo htmlspecialchars($session_error); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="row g-4" <?php echo $is_logged_in_check ? '' : 'style="filter: blur(2px); pointer-events: none;"'; ?>>
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="text" class="form-control border-0 me-4"
                                            placeholder="Tên *" name="user_name_review"
                                            value="<?php echo $user_name_display; ?>" required
                                            <?php echo $is_logged_in_check ? 'readonly' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border-bottom rounded">
                                        <input type="email" class="form-control border-0"
                                            placeholder="Email *" name="user_email_review"
                                            value="<?php echo $user_email_display; ?>" required
                                            <?php echo $is_logged_in_check ? 'readonly' : ''; ?>>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="border-bottom rounded my-4">
                                        <textarea name="comment" class="form-control border-0"
                                            cols="30" rows="8" placeholder="Nhận xét của bạn *"
                                            spellcheck="false" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="d-flex justify-content-between py-3 mb-5">
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-3">Đánh giá:</p>
                                            <div class="d-flex align-items-center rating-stars" style="font-size: 18px;">
                                                <i class="fa fa-star star-input text-muted" data-rating="1"></i>
                                                <i class="fa fa-star star-input text-muted" data-rating="2"></i>
                                                <i class="fa fa-star star-input text-muted" data-rating="3"></i>
                                                <i class="fa fa-star star-input text-muted" data-rating="4"></i>
                                                <i class="fa fa-star star-input text-muted" data-rating="5"></i>
                                                <input type="hidden" name="rating" id="ratingInput" value="0">
                                            </div>
                                        </div>
                                        <button type="submit"
                                            class="btn border border-secondary text-primary rounded-pill px-4 py-3">
                                            Đăng nhận xét
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="../js/main.js"></script> 
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Kiểm tra xem có sản phẩm để hiển thị form đánh giá không
            <?php if ($product_exists) : ?>
            
            const stars = document.querySelectorAll('.rating-stars .star-input');
            const ratingInput = document.getElementById('ratingInput');
            const form = document.getElementById('reviewForm');
            const reviewAlertContainer = document.getElementById('review-alert-container');
            const is_logged_in = <?php echo $is_logged_in_check ? 'true' : 'false'; ?>;
            const activeClass = 'text-secondary'; 
            const mutedClass = 'text-muted';

            // --- XỬ LÝ ĐÁNH GIÁ SAO (Visual feedback) ---
            function updateStars(value, is_hover = false) {
                stars.forEach(s => {
                    const starRating = parseInt(s.getAttribute('data-rating'));
                    if (starRating <= value) {
                        s.classList.remove(mutedClass);
                        s.classList.add(activeClass);
                    } else {
                        // Chỉ cập nhật sao đã chọn khi không hover hoặc sao hiện tại lớn hơn sao đã chọn
                        if (!is_hover || starRating > parseInt(ratingInput.value)) {
                            s.classList.remove(activeClass);
                            s.classList.add(mutedClass);
                        }
                    }
                });
            }

            stars.forEach(star => {
                star.addEventListener('click', function () {
                    const ratingValue = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = ratingValue;
                    updateStars(ratingValue);
                    // Xóa cảnh báo lỗi sao khi đã chọn, nhưng giữ lại thông báo từ Controller
                    const alertDiv = reviewAlertContainer.querySelector('.alert-warning');
                    if (alertDiv) {
                        alertDiv.remove();
                    }
                });
                star.addEventListener('mouseover', function () {
                    const hoverValue = parseInt(this.getAttribute('data-rating'));
                    updateStars(hoverValue, true);
                });
                star.addEventListener('mouseout', function () {
                    const selectedValue = parseInt(ratingInput.value);
                    updateStars(selectedValue);
                });
            });

            // Khởi tạo trạng thái ban đầu
            updateStars(parseInt(ratingInput.value));
            
            // --- XỬ LÝ SỰ KIỆN SUBMIT FORM ---
            form.addEventListener('submit', function(e) {
                
                // 1. Kiểm tra Đăng nhập
                if (!is_logged_in) {
                    e.preventDefault();
                    // Hiển thị lại cảnh báo nếu có người dùng cố tình gửi
                    const loginAlert = `<div class="alert alert-danger" role="alert">Vui lòng <a href="../View/Entry.php" class="alert-link fw-bold">Đăng nhập</a> để đăng nhận xét.</div>`;
                    if (!reviewAlertContainer.innerHTML.includes('Đăng nhập')) {
                        reviewAlertContainer.innerHTML = loginAlert + reviewAlertContainer.innerHTML;
                    }
                    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return; 
                }

                // 2. Kiểm tra Số sao đánh giá (Frontend validation)
                if (parseInt(ratingInput.value) === 0) {
                    e.preventDefault();
                    
                    // Xóa tất cả cảnh báo cũ
                    reviewAlertContainer.innerHTML = '';
                    
                    reviewAlertContainer.innerHTML = `
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            Vui lòng chọn số **sao đánh giá** (1-5) để hoàn tất nhận xét.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    // Cuộn đến form để người dùng thấy cảnh báo
                    form.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                
                // Nếu vượt qua cả 2 điều kiện, form sẽ được gửi đi
            });
            <?php endif; ?>
        });
    </script>
</body>
</html>