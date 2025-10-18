<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <!-- Icon Fonts -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/style1.css" rel="stylesheet">
</head>

<body>
    <!-- Spinner -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed top-50 start-50 translate-middle d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>

    <!-- Navbar -->
    <?php require_once 'View/navbar.php'; ?>

    <!-- Hero Header -->
    <div class="container-fluid py-5 mb-5 hero-header fix-navbar-overlap">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <!-- Text -->
                <div class="col-md-12 col-lg-7 adjusted-heading">
                    <h4 class="mb-3 text-secondary fw-semibold">100% Sản phẩm chính hãng</h4>
                    <h1 class="mb-5 display-3 fw-bold color-text-header-hero">Laptop cấu hình cao & Đa dạng mẫu mã</h1>
                </div>

                <!-- Carousel -->
                <div class="col-md-12 col-lg-5">
                    <div id="carouselId" class="carousel slide position-relative bg-dark p-3 rounded shadow"
                        data-bs-ride="carousel">
                        <div class="carousel-inner text-center">
                            <div class="carousel-item active">
                                <img src="img/laptop/caocap/Microsoft_surface_Laptop_Studio.png"
                                    class="img-fluid w-100 h-100 rounded" alt="Laptop cao cấp">
                            </div>
                            <div class="carousel-item">
                                <img src="img/laptop/gaming/Asus_ROG_Strix_Scar_18.png"
                                    class="img-fluid w-100 h-100 rounded" alt="Laptop gaming">
                            </div>
                            <div class="carousel-item">
                                <img src="img/laptop/hoctap/Asus_Vivobook_Go_15.png"
                                    class="img-fluid w-100 h-100 rounded" alt="Laptop học tập">
                            </div>
                            <div class="carousel-item">
                                <img src="img/laptop/vanphong/Apple_MacBook_Pro_14_M3.png"
                                    class="img-fluid w-100 h-100 rounded" alt="Laptop văn phòng">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselId"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Trước</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselId"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sau</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Counter Section -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded shadow-sm">
                <div class="row g-4 justify-content-center text-center align-items-stretch">
                    <div class="col-md-6 col-lg-3">
                        <div class="counter bg-white rounded p-4 h-100 shadow-sm d-flex flex-column align-items-center justify-content-between">
                            <div>
                                <i class="fa fa-smile text-secondary mb-3 fs-3"></i>
                                <h4 class="counter-title">Khách hàng hài lòng</h4>
                            </div>
                            <h1 class="change-font-product counter-number">1963</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="counter bg-white rounded p-4 h-100 shadow-sm d-flex flex-column align-items-center justify-content-between">
                            <div>
                                <i class="fa fa-cogs text-secondary mb-3 fs-3"></i>
                                <h4 class="counter-title">Chất lượng dịch vụ</h4>
                            </div>
                            <h1 class="change-font-product counter-number">99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="counter bg-white rounded p-4 h-100 shadow-sm d-flex flex-column align-items-center justify-content-between">
                            <div>
                                <i class="fa fa-certificate text-secondary mb-3 fs-3"></i>
                                <h4 class="counter-title">Chứng nhận chất lượng</h4>
                            </div>
                            <h1 class="change-font-product counter-number">33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="counter bg-white rounded p-4 h-100 shadow-sm d-flex flex-column align-items-center justify-content-between">
                            <div>
                                <i class="fa fa-laptop text-secondary mb-3 fs-3"></i>
                                <h4 class="counter-title">Sản phẩm có sẵn</h4>
                            </div>
                            <h1 class="change-font-product counter-number">789</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'View/footer.php'; ?>

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>

    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>

    <!-- ✅ CSS chỉnh sửa hoàn chỉnh -->
    <style>
        /* === HERO HEADER === */
        .fix-navbar-overlap {
            margin-top: 100px;
        }

        .adjusted-heading {
            padding-top: 40px;
        }

        .hero-header h4 {
            color: #f5b400;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .hero-header h1 {
            color: #004aad;
            font-weight: 800;
            line-height: 1.3;
        }

        /* === COUNTER BOX === */
        .counter {
            background-color: #ffffff;
            border-radius: 16px;
            padding: 30px 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            min-height: 220px;
        }

        .counter:hover {
            transform: translateY(-6px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .counter-title {
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            font-size: 15px;
            line-height: 1.4;
            min-height: 40px;
        }

        .counter-number {
            font-size: 34px;
            font-weight: 800;
            color: #37474f;
            line-height: 1;
        }

        /* === RESPONSIVE === */
        @media (max-width: 991.98px) {
            .adjusted-heading {
                padding-top: 100px !important;
                text-align: center;
            }

            .hero-header h1 {
                font-size: 2rem;
            }

            .counter {
                margin-top: 15px;
                margin-bottom: 15px;
            }
        }

        @media (max-width: 575.98px) {
            .hero-header {
                padding-top: 80px;
                padding-bottom: 50px;
            }

            .hero-header h1 {
                font-size: 1.7rem;
            }

            .hero-header h4 {
                font-size: 0.95rem;
            }
        }
    </style>
</body>
</html>
