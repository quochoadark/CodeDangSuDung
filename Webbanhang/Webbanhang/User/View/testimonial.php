<?php 
// Tên tệp: User/View/testimonial.php (ĐÃ CHỈNH SỬA - BỎ AVATAR)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Biến $all_reviews được giả định đã được truyền từ ReviewListController.php
$all_reviews = isset($all_reviews) ? $all_reviews : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Đánh giá - LaptopShop</title>
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
        /* Đảm bảo sao được tô màu vàng (thường là màu 'primary' hoặc 'secondary' trong theme) */
        .testimonial-item .fa-star.text-primary,
        .testimonial-item .fa-star, /* Thêm rule này để tô màu cho tất cả các sao */
        .testimonial-item .fa-star.text-secondary {
            color: #ccc; /* Màu xám cho các sao chưa được đánh giá */
        }
        .testimonial-item .fa-star.text-secondary {
            color: #FFC107 !important; /* Màu vàng cho các sao đã được đánh giá */
        }
    </style>
</head>

<body>

    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <?php include 'navbar.php'; ?>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            // Lấy tên file từ href của mỗi đường dẫn
            const linkPath = link.getAttribute('href').split('/').pop();

            // Kiểm tra xem đường dẫn hiện tại có khớp với href của link không
            if (currentPath.endsWith(linkPath) && linkPath !== '') {
                // Nếu khớp, thêm lớp 'active'
                link.classList.add('active');
            } else {
                // Nếu không, đảm bảo là không có lớp 'active'
                link.classList.remove('active');
            }

            // Xử lý riêng cho dropdown để tránh xung đột
            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown && currentPath.includes(linkPath)) {
                parentDropdown.querySelector('.nav-link.dropdown-toggle').classList.add('active');
            }
        });

        // Xóa lớp active ban đầu khỏi trang chủ, vì nó được thêm cứng trong HTML
        const homeLink = document.querySelector('.navbar-nav .nav-link[href="index.php"]');
        if (homeLink) {
            homeLink.classList.remove('active');
        }
    });
    </script>
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Đánh giá</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="./index.php" style = "color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Đánh giá</li>
        </ol>
    </div>
    <div class="container-fluid testimonial py-5">
        <div class="container py-5">
            <div class="testimonial-header text-center">
                <h1 class="display-5 mb-5 text-dark">Đánh giá của khách hàng!</h1>
                <?php $review_count = count($all_reviews); ?>
                <p class="fs-4 text-muted">Tổng cộng có **<?php echo $review_count; ?>** đánh giá từ khách hàng.</p>
                <?php if ($review_count == 0): ?>
                    <p class="fs-5 text-muted">Chưa có đánh giá nào được tìm thấy. Vui lòng thử lại sau. 📝</p>
                <?php endif; ?>
            </div>
            
            <?php if ($review_count > 0): ?>
            <div class="owl-carousel testimonial-carousel">
                <?php 
                    foreach ($all_reviews as $review) : 
                        // Lấy avatar mặc định (không dùng)
                        $default_avatar_bg = '#ffc107'; // Màu vàng cho khối avatar
                        
                        $rating = intval($review['danhgia'] ?? 0);
                        $review_content = htmlspecialchars($review['binhluan'] ?? '');
                        $reviewer_name = htmlspecialchars($review['hoten'] ?? 'Khách hàng ẩn danh');
                        $product_name = htmlspecialchars($review['tensanpham'] ?? 'N/A');
                        $review_date = date('d/m/Y', strtotime($review['ngaytao'] ?? 'now'));
                ?>
                <div class="testimonial-item img-border-radius bg-light rounded p-4">
                    <div class="position-relative">
                        <i class="fa fa-quote-right fa-2x text-secondary position-absolute"
                            style="bottom: 30px; right: 0;"></i>
                        <div class="mb-4 pb-4 border-bottom border-secondary">
                            <p class="mb-1 text-primary small">Về sản phẩm: **<?php echo $product_name; ?>**</p>
                            
                            <?php if (!empty($review_content)) : ?>
                                <p class="mb-0">
                                    <?php echo nl2br($review_content); ?>
                                </p>
                            <?php else: ?>
                                <p class="mb-0 text-muted fst-italic">
                                    (Không có nội dung bình luận chi tiết.)
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex align-items-center flex-nowrap">
                            <div class="rounded" style="width: 100px; height: 100px; background-color: <?php echo $default_avatar_bg; ?>; display: flex; align-items: center; justify-content: center;">
                                <span style="color: white; font-size: 24px;">👤</span>
                            </div>
                            <div class="ms-4 d-block">
                                <h4 class="text-dark"><?php echo $reviewer_name; ?></h4>
                                <p class="m-0 pb-1">Ngày: <?php echo $review_date; ?></p>
                                <div class="d-flex pe-5">
                                    <?php 
                                        // Hiển thị 5 sao
                                        for ($i = 1; $i <= 5; $i++) {
                                            $star_class = ($i <= $rating) ? 'text-secondary' : ''; 
                                            echo '<i class="fas fa-star ' . $star_class . '"></i>';
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach; 
                ?>
            </div>
            <?php endif; // End if $review_count > 0 ?>

        </div>
    </div>
    <?php include 'footer.php'; ?>
    
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="../js/main.js"></script>
    <script>
    $(document).ready(function () {
        // Chỉ khởi tạo carousel nếu có đánh giá
        if ($('.testimonial-carousel').children().length > 0) {
            $(".testimonial-carousel").owlCarousel({
                autoplay: true,
                smartSpeed: 2000,
                center: false,
                dots: true,
                loop: true,
                margin: 25,
                nav : true,
                navText : [
                    '<i class="bi bi-arrow-left"></i>',
                    '<i class="bi bi-arrow-right"></i>'
                ],
                responsive: {
                    0:{
                        items:1
                    },
                    576:{
                        items:1
                    },
                    768:{
                        items:2
                    },
                    992:{
                        items:2
                    },
                    1200:{
                        items:3
                    }
                }
            });
        }
    });
    </script>
</body>

</html>