<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
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

    <!-- Spinner Start -->
    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->


    <!-- Navbar start -->
    <?php include 'View/navbar.php'; ?>
    <!-- Navbar End -->


    <!-- Database Search Start -->
    <div class="Database fade" id="searchDatabase" tabindex="-1" aria-labelledby="exampleDatabaseLabel" aria-hidden="true">
        <div class="Database-dialog Database-fullscreen">
            <div class="Database-content rounded-0">
                <div class="Database-header">
                    <h5 class="Database-title" id="exampleDatabaseLabel">Tìm kiếm sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="Database" aria-label="Close"></button>
                </div>
                <div class="Database-body d-flex align-items-center">
                    <div class="input-group w-75 mx-auto d-flex">
                        <input type="search" class="form-control p-3" placeholder="Từ khóa"
                            aria-describedby="search-icon-1">
                        <span id="search-icon-1" class="input-group-text p-3"><i class="fa fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Database Search End -->


    <!-- Hero Start -->
    <div class="container-fluid py-5 mb-5 hero-header">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-md-12 col-lg-7 adjusted-heading">
                    <h4 class="mb-3 text-secondary">100% Sản phẩm chính hãng</h4>
                    <h1 class="mb-5 display-3 color-text-header-hero">Laptop cấu hình cao & Đa dạng mẫu mã</h1>
                </div>
            <div class="col-md-12 col-lg-5">
            <div id="carouselId" class="carousel slide position-relative bg-dark p-4 rounded" data-bs-ride="carousel">
                <div class="carousel-inner" role="listbox">
                <div class="carousel-item active rounded text-center">
                    <img src="img/laptop/caocap/Microsoft_surface_Laptop_Studio.png" class="img-fluid w-100 h-100 rounded bg-transparent"
                    alt="First slide">
                </div>
                <div class="carousel-item rounded text-center">
                    <img src="img/laptop/gaming/Asus_ROG_Strix_Scar_18.png" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                </div>
                <div class="carousel-item rounded text-center">
                    <img src="img/laptop/hoctap/Asus_Vivobook_Go_15.png" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                </div>
                <div class="carousel-item rounded text-center">
                    <img src="img/laptop/vanphong/Apple_MacBook_Pro_14_M3.png" class="img-fluid w-100 h-100 rounded" alt="Second slide">
                </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselId" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Trước</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselId" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Sau</span>
                </button>
            </div>
            </div>

            </div>
        </div>
    </div>
    <!-- Hero End -->

    <!-- Fruits Shop Start-->
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <div class="tab-class text-center">
                <div class="row g-4">
                    <div class="col-lg-4 text-start">
                        <h1>Sản phẩm chính hãng</h1>
                    </div>
                    <div class="col-lg-8 text-end">
                        <ul class="nav nav-pills d-inline-flex text-center mb-5">
                            <li class="nav-item">
                                <a class="d-flex m-2 py-2 bg-light rounded-pill active" data-bs-toggle="pill"
                                    href="#tab-1">
                                    <span class="text-dark" style="width: 130px;">Tất cả sản phẩm</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-content">
                    <div id="tab-1" class="tab-pane fade show p-0 active">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/HP15s.png" class="card-img-top rounded-top" alt="Acer Aspire 3">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">HP 15s</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1135G7 (thế hệ 11) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 256GB SSD NVMe <br>
                                                    Card đồ họa: Intel Iris Xe Graphics <br>
                                                    Màn hình: 15.6 inch Full HD IPS <br>
                                                    M.Hình: 16" Full HD 165Hz
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    8.500.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Acer_Aspire_3.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Acer Aspire 3</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 256GB SSD NVMe <br>
                                                    Card đồ họa: Intel Iris Xe Graphics <br>
                                                    Màn hình: 15.6 inch Full HD  <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    9.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Asus_Vivobook_Go_15.png" class="card-img-top rounded-top" alt="Acer Aspire 3">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus Vivobook Go 15 </h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i3-N305 <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD PCIe 3.0 <br>
                                                    Card đồ họa: Intel UHD Graphics <br>
                                                    Màn hình: 15.6 inch Full HD <br>
                                                    Hệ điều hành: Windows 11 Pro
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    10.000.000 đ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Dell_Inspiron_15_3000_series.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell Inspiron 15 3000 series </h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1135G7 (thế hệ 11) <br>
                                                    RAM: 16GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD <br>
                                                    Card đồ họa: Intel Iris Xe Graphics <br>
                                                    Màn hình: 15.6 inch Full HD <br>
                                                    Hệ điều hành: Windows 10
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    11.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Lenovo_IdeaPad_Slim_3.png" class="card-img-top rounded-top" alt="Acer Aspire 3">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo IdeaPad Slim 3 </h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe <br>
                                                    Card đồ họa: Intel Iris Xe Graphics <br>
                                                    Màn hình: 15.6 inch Full HD <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    12.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Acer_Swift_Go_14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Acer Swift Go 14</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core Ultra 5 125H <br>
                                                    RAM: 8GB LPDDR5 <br>
                                                    Ổ cứng: 512GB SSD <br>
                                                    Card đồ họa: Intel Arc Graphics <br>
                                                    Màn hình: 14 inch OLED 2880 x 1800 <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    20.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Lenovo_Yoga_Slim_7.png" class="card-img-top rounded-top" alt="Acer Aspire 3">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo Yoga Slim 7</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1135G7 (thế hệ 11) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe <br>
                                                    Card đồ họa: Intel Iris Xe Graphics <br>
                                                    Màn hình: 14 inch Full HD cảm ứng <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    25.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/hoctap/Apple_MacBook_Air_M2.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Học tập</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Apple MacBook Air M2</h5>
                                                <p class="card-text text-center">
                                                    CPU: Apple M2 (8-core CPU, 10-core GPU) <br>
                                                    RAM: 8GB Unified Memory <br>
                                                    Ổ cứng: 512GB SSD<br>
                                                    Card đồ họa: 10-core GPU tích hợp <br>
                                                    Màn hình: 13.6 inch Liquid Retina <br>
                                                    Hệ điều hành: macOS
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    29.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Dell_Vostro_3000_series.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell Vostro 3000 series</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (10 nhân, thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 15.6 inch Full HD (1920×1080) <br>
                                                    Hệ điều hành: Windows 11 Pro
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    12.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Lenovo_ThinkBook_14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo ThinkBook 14</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1335U (thế hệ 13) <br>
                                                    RAM: 16GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch Full HD+ (1920×1200) <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    14.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/HP_Pavilion_14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">HP Pavilion 14</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch Full HD+ (1920×1200) <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    16.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Dell_Latitude_3000_series.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell Latitude 3000 series </h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 256GB SSD NVMe<br>
                                                    Card đồ họa: Intel UHD/Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 15.6 inch Full HD (1920×1080) <br>
                                                    Hệ điều hành: Windows 11 Pro
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    20.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Asus_Zenbook_14_OLED.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus Zenbook 14 OLED</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1340P (thế hệ 13) <br>
                                                    RAM: 16GB LPDDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch OLED 2.8K (2880×1800) <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    22.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Microsoft_Surface_Laptop_Go_3.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Microsoft Surface Laptop Go 3</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1235U (thế hệ 12) <br>
                                                    RAM: 8GB LPDDR5<br>
                                                    Ổ cứng: 256GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 12.4 inch PixelSense (1536×1024) cảm ứng <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    25.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/LG_Gram_14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">LG Gram 14</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-1340P (thế hệ 13) <br>
                                                    RAM: 16GB LPDDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch WUXGA (1920×1200) <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    35.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/vanphong/Lenovo_ThinkPad_X1_Carbon_Gen_10.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Văn phòng</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo ThinkPad X1 Carbon Gen 10</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-1260P (thế hệ 12) <br>
                                                    RAM: 16GB LPDDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch 2.2K (2240×1400) IPS <br>
                                                    Hệ điều hành: Windows 11 Pro
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    45.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/MSI_Thin_GF63.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">MSI Thin GF63</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i5-12450H (thế hệ 12) <br>
                                                    RAM: 8GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4050 6GB rời <br>
                                                    Màn hình: 15.6 inch FHD (1920×1080) 144Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    17.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Asus_TUF_Gaming_F15.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus TUF Gaming F15</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-12700H (thế hệ 12) <br>
                                                    RAM: 16GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 3060 6GB rời <br>
                                                    Màn hình: 15.6 inch FHD (1920×1080) 144Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    20.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Acer_Nitro_5.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Acer Nitro 5</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-12700H (thế hệ 12) <br>
                                                    RAM: 16GB DDR4 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 3050Ti 4GB rời <br>
                                                    Màn hình: 15.6 inch FHD (1920×1080) 144Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    22.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Dell_G15.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell G15</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-12700H (thế hệ 12) <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 3060 6GB rời <br>
                                                    Màn hình: 15.6 inch FHD (1920×1080) 165Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    25.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Lenovo_LOQ_15.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo LOQ 15</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-13620H (thế hệ 13) <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4050 6GB rời <br>
                                                    Màn hình: 15.6 inch FHD (1920×1080) 144Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    26.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Lenovo_Legion_Slim_5.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo Legion Slim 5</h5>
                                                <p class="card-text text-center">
                                                    CPU: AMD Ryzen 7 7840HS <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4060 8GB rời <br>
                                                    Màn hình: 15.6 inch WQHD+ (2560×1600) 165Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    30.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/Asus_ROG_Zephyrus_G14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus ROG Zephyrus G14</h5>
                                                <p class="card-text text-center">
                                                    CPU: AMD Ryzen 9 7940HS <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4060 8GB rời <br>
                                                    Màn hình: 14 inch QHD+ (2560×1600) 165Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    40.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/gaming/MSI_Stealth_16.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Gaming</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">MSI Stealth 16</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-13700H (thế hệ 13) <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4060 8GB rời <br>
                                                    Màn hình: 16 inch QHD+ (2560×1600) 240Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    45.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Lenovo_Yoga_Slim_9i.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Lenovo Yoga Slim 9i</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-1280P (thế hệ 12) <br>
                                                    RAM: 16GB LPDDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: Intel Iris Xe Graphics tích hợp <br>
                                                    Màn hình: 14 inch 4K OLED (3840×2400) cảm ứng <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    50.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Razer_Blade_14.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Razer Blade 14</h5>
                                                <p class="card-text text-center">
                                                    CPU: AMD Ryzen 9 7940HS <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4070 8GB rời <br>
                                                    Màn hình: 14 inch QHD+ (2560×1600) 240Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    55.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                      <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Microsoft_Surface_Laptop_Studio.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Microsoft Surface Laptop Studio</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-11370H (thế hệ 11) <br>
                                                    RAM: 16GB LPDDR4x <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 3050 Ti rời <br>
                                                    Màn hình: 14.4 inch PixelSense Flow 2400×1600 120Hz cảm ứng <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    60.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Dell_XPS_17.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell XPS 17</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i7-12700H (thế hệ 12) <br>
                                                    RAM: 16GB DDR5 <br>
                                                    Ổ cứng: 512GB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 3050 4GB rời <br>
                                                    Màn hình: 17 inch UHD+ (3840×2400) cảm ứng <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    65.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                      <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Apple_MacBook_Pro_16_M2_Pro.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Apple MacBook Pro 16 M2 Pro</h5>
                                                <p class="card-text text-center">
                                                    CPU: Apple M2 Pro (12-core CPU, 19-core GPU) <br>
                                                    RAM: 16GB Unified Memory <br>
                                                    Ổ cứng: 512GB SSD<br>
                                                    Card đồ họa: 19-core GPU tích hợp <br>
                                                    Màn hình: 16.2 inch Liquid Retina XDR (3456×2234) <br>
                                                    Hệ điều hành: macOS Ventura
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    70.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/Asus_ROG_Zephyrus_Duo_16.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus ROG Zephyrus Duo 16s</h5>
                                                <p class="card-text text-center">
                                                    CPU: AMD Ryzen 9 7945HX <br>
                                                    RAM: 32GB DDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4090 16GB rời <br>
                                                    Màn hình: 16 inch Mini-LED QHD+ (2560×1600) 240Hz + màn hình phụ ScreenPad Plus 14″ 4K <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    85.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                      <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/1Dell_Alienware_m18.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Dell Alienware m18</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i9-13980HX (thế hệ 13) <br>
                                                    RAM: 32GB DDR5 <br>
                                                    Ổ cứng: 1TB SSD NVMe<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 4090 16GB rời <br>
                                                    Màn hình: 18 inch QHD+ (2560×1600) 165Hz IPS <br>
                                                    Hệ điều hành: Windows 11 Home
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    90.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="card h-100 border border-secondary">
                                            <img src="img/laptop/caocap/180_trieu.png" class="card-img-top rounded-top" alt="MacBook Air">
                                            <div class="position-absolute top-0 start-0 m-3 px-3 py-1 bg-secondary text-white rounded">Cao cấp</div>
                                            <div class="card-body">
                                                <h5 class="card-title text-center change-font-product">Asus ROG Mothership GZ700</h5>
                                                <p class="card-text text-center">
                                                    CPU: Intel Core i9-9980HK (thế hệ 9) <br>
                                                    RAM: 64GB DDR4 <br>
                                                    Ổ cứng: 2×512GB SSD NVMe RAID0<br>
                                                    Card đồ họa: NVIDIA GeForce RTX 2080 8GB rời <br>
                                                    Màn hình: 17.3 inch FHD (1920×1080) 144Hz IPS <br>
                                                    Hệ điều hành: Windows 10 Pro
                                                </p>
                                                <div class="price-container text-center mt-auto">
                                                    99.000.000 đ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tab-5" class="tab-pane fade show p-0">
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <div class="row g-4">
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-3.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Banana</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do
                                                    eiusmod te incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-2.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Raspberries</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do
                                                    eiusmod te incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-xl-3">
                                        <div class="rounded position-relative fruite-item">
                                            <div class="fruite-img">
                                                <img src="img/fruite-item-1.jpg" class="img-fluid w-100 rounded-top"
                                                    alt="">
                                            </div>
                                            <div class="text-white bg-secondary px-3 py-1 rounded position-absolute"
                                                style="top: 10px; left: 10px;">Fruits</div>
                                            <div class="p-4 border border-secondary border-top-0 rounded-bottom">
                                                <h4>Oranges</h4>
                                                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit sed do
                                                    eiusmod te incididunt</p>
                                                <div class="d-flex justify-content-between flex-lg-wrap">
                                                    <p class="text-dark fs-5 fw-bold mb-0">$4.99 / kg</p>
                                                    <a href="#"
                                                        class="btn border border-secondary rounded-pill px-3 text-primary"><i
                                                            class="fa fa-shopping-bag me-2 text-primary"></i> Add to
                                                        cart</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fruits Shop End-->

    <!-- Fact Start -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="bg-light p-5 rounded">
                <div class="row g-4 justify-content-center">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Khách hàng hài lòng</h4>
                            <h1 class = "change-font-product">1963</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Chất lượng dịch vụ</h4>
                            <h1 class = "change-font-product">99%</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Chứng nhận chất lượng</h4>
                            <h1 class = "change-font-product">33</h1>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="counter bg-white rounded p-5">
                            <i class="fa fa-users text-secondary"></i>
                            <h4>Sản phẩm có sẵn</h4>
                            <h1 class = "change-font-product">789</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fact Start -->



    <!-- Footer Start -->
    <?php include 'footer.php'; ?>
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>


    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>


<script>
document.addEventListener('click', function(e){
  const link = e.target.closest('.pagination-link');
  if(link){
    e.preventDefault();
    let url = link.dataset.url;
    fetch(url)
      .then(res => res.text())
      .then(html => {
        document.getElementById('content').innerHTML = html;
      });
  }
});
</script>