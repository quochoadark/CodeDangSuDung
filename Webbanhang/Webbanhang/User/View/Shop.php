<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Sản phẩm - LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/style1.css" rel="stylesheet">
</head>

<body>

    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    
    <?php 
    // Include navbar.php (nằm cùng thư mục View/), nó sẽ định nghĩa BASE_URL
    require_once 'navbar.php'; 
    ?>

    <script>
    // JS để làm active navbar (giữ nguyên)
    document.addEventListener("DOMContentLoaded", function () {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

        navLinks.forEach(link => {
            // Lấy phần cuối của đường dẫn href (ví dụ: index.php)
            const linkPath = link.getAttribute('href').split('/').pop(); 
            
            // Xử lý Active cho trang hiện tại (Controller)
            if (currentPath.includes('ShopController.php') && link.getAttribute('href').includes('ShopController.php')) {
                link.classList.add('active');
            }
            // Xử lý Active cho các trang View khác
            else if (currentPath.endsWith(linkPath) && linkPath !== '') {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }

            const parentDropdown = link.closest('.dropdown');
            if (parentDropdown && currentPath.includes(linkPath)) {
                parentDropdown.querySelector('.nav-link.dropdown-toggle').classList.add('active');
            }
        });

        const homeLink = document.querySelector('.navbar-nav .nav-link[href="<?php echo BASE_URL; ?>index.php"]');
        if (homeLink) {
            homeLink.classList.remove('active');
        }
    });
    </script>
    
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Sản phẩm</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php" style = "color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Sản phẩm</li>
        </ol>
    </div>
    
    <div class="container-fluid fruite py-5">
        <div class="container py-5">
            <h1 class="mb-4">
                <?php
                // HIỂN THỊ DỮ LIỆU TỪ CONTROLLER
                if (isset($search_query) && $search_query) {
                    echo "Kết quả tìm kiếm cho: " . htmlspecialchars($search_query);
                } else {
                    echo "Tất cả sản phẩm";
                }
                ?>
            </h1>
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="row g-4">
                        
                        <div class="col-xl-3">
                            <div class="input-group w-100 mx-auto d-flex">
                                <form action="ShopController.php" method="GET" class="w-100 d-flex">
                                    <input type="search" name="search_query" class="form-control p-3" placeholder="Từ khóa" aria-describedby="search-icon-1" value="<?php echo htmlspecialchars($search_query ?? ''); ?>">
                                    <button type="submit" id="search-icon-1" class="input-group-text p-3">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="col-6"></div>
                        
                        <div class="col-xl-3">
                            <div class="bg-light ps-3 py-3 rounded d-flex justify-content-between mb-4">
                                <label for="category_select">Danh mục</label>
                                <select id="category_select" class="border-0 form-select-sm bg-light me-3" onchange="window.location.href='ShopController.php?category_id=' + this.value">
                                    <option value="">Tất cả</option>
                                    <?php 
                                    // HIỂN THỊ DỮ LIỆU TỪ CONTROLLER
                                    if (isset($danhmucsanpham)): ?>
                                        <?php foreach ($danhmucsanpham as $dm): ?>
                                            <option value="<?php echo htmlspecialchars($dm['category_id']); ?>" <?php if (isset($category_id) && $category_id == $dm['category_id']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($dm['tendanhmuc']); ?> (<?php echo htmlspecialchars($dm['total_products']); ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                   <div class="row g-4 justify-content-center">
                        <?php 
                        // HIỂN THỊ DỮ LIỆU SẢN PHẨM TỪ CONTROLLER
                        if (!empty($products)): ?>
                            <?php foreach ($products as $row): 
                                // Lấy giá trị khuyến mãi để hiển thị
                                $is_promotion = isset($row['promotion_percent']) && $row['promotion_percent'] > 0;
                                $display_percent = round($row['promotion_percent'] * 100);
                                $original_price = $row['original_price'];
                                $new_price = $row['gia'];
                            ?>
                                <div class="col-md-6 col-lg-6 col-xl-4">
                                    <div class="rounded position-relative fruite-item d-flex flex-column h-100" 
                                        onclick="window.location.href='<?php echo BASE_URL; ?>Controller/ShopDetailController.php?product_id=<?php echo htmlspecialchars($row['product_id']); ?>'" 
                                        style="cursor: pointer;">
                                        
                                        <div class="position-absolute rounded-circle text-white <?php echo $is_promotion ? 'bg-danger' : 'bg-secondary'; ?>" 
                                            style="width: 60px; height: 60px; top: 10px; left: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; font-weight: bold;">
                                            -<?php echo $display_percent; ?>%
                                        </div>
                                        
                                        <div class="fruite-img">
                                            <img src="../../Admin/uploads/<?php echo htmlspecialchars($row['img']); ?>" class="img-fluid w-100 rounded-top" alt="<?php echo htmlspecialchars($row['tensanpham']); ?>">
                                        </div>
                                        <div class="p-4 border border-secondary border-top-0 rounded-bottom d-flex flex-column h-100">
                                            <h4 class="text-center change-font-product"><?php echo htmlspecialchars($row['tensanpham']); ?></h4>
                                            <div class="flex-grow-1 mb-2">
                                                <p><?php echo htmlspecialchars($row['mota']); ?></p>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <div class="text-dark mb-1 change-font-product d-flex flex-column justify-content-center">
                                                    <?php if ($is_promotion): ?>
                                                        <p class="text-secondary fw-normal mb-0" style="text-decoration: line-through; font-size: 0.9rem; height: 1.2em; line-height: 1.2em; overflow: hidden;">
                                                            <?php echo number_format($original_price, 0, ',', '.'); ?> VNĐ
                                                        </p>
                                                        <p class="fs-5 fw-bold mb-0 text-danger" style="line-height: 1.2em;">
                                                            <?php echo number_format($new_price, 0, ',', '.'); ?> VNĐ
                                                        </p>
                                                    <?php else: ?>
                                                        <p class="text-secondary fw-normal mb-0 invisible" style="text-decoration: line-through; font-size: 0.9rem; height: 1.2em; line-height: 1.2em;">&nbsp;</p>
                                                        <p class="fs-5 fw-bold mb-0 text-dark" style="line-height: 1.2em;">
                                                            <?php echo number_format($new_price, 0, ',', '.'); ?> VNĐ
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <form action="<?php echo BASE_URL; ?>Controller/CartController.php" method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="add_to_cart">
                                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>"> 
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn border border-secondary rounded-pill px-3 text-primary mt-5" onclick="event.stopPropagation();">
                                                        <i class="fa fa-shopping-bag me-2 text-primary"></i> Thêm vào giỏ
                                                    </button>
                                                </form>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class='text-center'>Không tìm thấy sản phẩm nào.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php 
            // HIỂN THỊ PHÂN TRANG (Trỏ về Controller hiện tại)
            if (!isset($search_query) && isset($total_pages) && $total_pages > 1): ?>
                <nav aria-label="Page navigation example" class="mt-4">
                    <ul class="pagination">
                        <?php 
                        for($i = 1; $i <= $total_pages; $i++): 
                            $query_params['page'] = $i;
                            $query_string = http_build_query($query_params);
                        ?>
                        <li class="page-item <?php if(isset($page) && $i == $page) echo 'active'; ?>">
                            <a class="page-link" href="ShopController.php?<?php echo htmlspecialchars($query_string); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
    
    <?php include 'footer.php'; // Footer nằm trong View/ ?>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="../js/main.js"></script>
</body>

</html>