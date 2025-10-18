<?php
ob_start();
require_once __DIR__ . '/AuthCheck.php';
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trang quản trị</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/Template/styles.min.css" />
    <link rel="stylesheet" href="assets/css/Template/errors.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />

    <style>
        /* ------------------------------------------------------------------- */
        /* CSS Responsive cho Layout Admin */
        /* ------------------------------------------------------------------- */
        
        /* 1. Thiết lập cho màn hình PC/Tablet lớn (min-width: 992px) */
        .sidebar-col {
            /* Giữ nguyên cho PC */
            transition: margin-left 0.3s ease;
        }
        
        /* 2. Thiết lập cho màn hình Mobile/Tablet nhỏ (max-width: 991px) */
        @media (max-width: 991px) {
            
            /* ⭐ QUAN TRỌNG: Ghi đè class Bootstrap d-none/d-lg-block khi Mobile Sidebar Active */
            .sidebar-active .sidebar-col {
                display: block !important; /* Bắt buộc hiện khi active */
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1050; 
                width: 250px; 
                margin-left: 0; /* Hiện Sidebar */
                background-color: #fff;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5); 
            }
            
            /* Nếu không có .sidebar-active, nó bị ẩn bởi class d-none */
            
            .content-col {
                width: 100% !important;
                margin-left: 0 !important;
            }

            /* Overlay khi Sidebar mở */
            #overlay {
                display: none;
            }
            .sidebar-active #overlay {
                display: block;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040; 
            }
        }
        
        /* 3. Tinh chỉnh Content padding */
        #content.content {
            min-height: calc(100vh - 40px); 
            background-color: #f7f9fc; 
        }
    </style>
</head>

<body>
    <div id="overlay"></div> 
    
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-lg-2 p-0 sidebar-col d-none d-lg-block">
                <?php include './view/Sidebar.php'; ?>
            </div>

            <div class="col-12 col-lg-10 p-0 content-col" id="main-content-wrapper">
                <?php include './view/Navbar.php'; ?>

                <div id="content" class="content p-3">
                    <?php
                    if (isset($_GET['page'])) {
                        $page = $_GET['page'];
                        // ... (Giữ nguyên logic switch case) ...
                        switch ($page) {
                            case 'nhanvien': include './view/Staff.php'; break;
                            case 'danhmuc': include './view/Category.php'; break;
                            case 'sanpham': include './view/Product.php'; break;
                            case 'danhgia': include './view/Testimonial.php'; break;
                            case 'doanhthu': include './view/Statistic.php'; break;
                            case 'donhang': include './view/OrderManagement.php'; break;
                            case 'khachhang': include './view/User.php'; break;
                            case 'khuyenmai': include './view/SaleProduct.php'; break;
                            case 'voucher': include './view/Voucher.php'; break;
                            default: echo "<p class='text-danger'>Trang không tồn tại.</p>";
                        }
                    } else {
                        // Mặc định vào trang nhân viên
                        include './view/Staff.php';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>

    <script>
        // Tái sử dụng logic Modal của bạn (Giữ nguyên)
        let deleteUrl = '';
        let blockUrl = '';

        // Mở modal xác nhận xóa
        $(document).on('click', '.delete-link', function(e) {
            e.preventDefault();
            deleteUrl = $(this).data('url');
            $('#confirmModal').modal('show');
        });

        // Xác nhận xóa
        $('#confirmYes').on('click', function() {
            window.location.href = deleteUrl;
        });

        // Mở modal xác nhận khóa/mở khóa
        $(document).on('click', '.block-link', function(e) {
            e.preventDefault();
            blockUrl = $(this).data('url');
            const staffName = $(this).data('staff');
            const actionType = $(this).data('action');

            $('#blockModalBody').html(
                `Bạn có chắc chắn muốn <b>${actionType}</b> nhân viên <b>${staffName}</b> không?`
            );

            const confirmBtn = $('#confirmBlock');
            confirmBtn.removeClass('btn-success btn-danger');
            confirmBtn.addClass(actionType === 'Khóa' ? 'btn-danger' : 'btn-success');

            $('#blockModal').modal('show');
        });

        // Xác nhận khóa/mở khóa
        $('#confirmBlock').on('click', function() {
            window.location.href = blockUrl;
        });
        
        // -------------------------------------------------------------------
        // LOGIC JAVASCRIPT CHO SIDEBAR RESPONSIVE
        // -------------------------------------------------------------------

        // Hàm chuyển đổi trạng thái Sidebar (thêm/xóa class sidebar-active trên thẻ body)
        function toggleMobileSidebar() {
            // Chỉ thực hiện trên mobile/tablet (<= 991px)
            if ($(window).width() <= 991) {
                $('body').toggleClass('sidebar-active');
            }
        }

        // Bắt sự kiện click vào nút toggle (Navbar)
        $(document).on('click', '#toggleSidebar', function() {
            toggleMobileSidebar();
        });
        
        // Bắt sự kiện click vào nút 'X' (sidebarCollapse) trong Sidebar
        $(document).on('click', '#sidebarCollapse', function() {
            toggleMobileSidebar();
        });

        // Đóng Sidebar khi click ra ngoài Overlay trên Mobile
        $('#overlay').on('click', function() {
            toggleMobileSidebar();
        });

        // Tự động đóng Sidebar khi một liên kết trong Sidebar được click
        $(document).on('click', '.sidebar-link', function() {
            if ($(window).width() <= 991) {
                toggleMobileSidebar();
            }
        });

        // Tự động điều chỉnh trên PC
        $(window).resize(function() {
            if ($(window).width() > 991) {
                // Đảm bảo không có lớp active trên PC
                $('body').removeClass('sidebar-active');
            }
        });
    </script>
    
    </body>
</html>