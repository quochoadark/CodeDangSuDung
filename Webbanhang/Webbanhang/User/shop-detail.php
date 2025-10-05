<?php
session_start();
// Bắt đầu PHP
require_once '../Database/Database.php'; // Đảm bảo đường dẫn này chính xác

// Khởi tạo biến $product là null
$product = null;

// KHỞI TẠO CÁC BIẾN SẼ DÙNG TRONG FORM VÀ HTML
$product_id = 0;
$quantity = 1; // Mặc định số lượng thêm vào giỏ hàng là 1

// Kiểm tra xem có product_id được truyền qua URL không và nó có phải là số không
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = intval($_GET['product_id']);

    // Sử dụng Prepared Statement để ngăn chặn SQL Injection
    $sql_product_detail = "SELECT sp.*, dm.tendanhmuc 
                             FROM sanpham AS sp 
                             JOIN danhmucsanpham AS dm ON sp.category_id = dm.category_id 
                             WHERE sp.product_id = ?";
    
    // Chuẩn bị câu lệnh
    if ($stmt = $conn->prepare($sql_product_detail)) {
        // Gắn tham số vào câu lệnh
        $stmt->bind_param("i", $product_id);
        
        // Thực thi câu lệnh
        $stmt->execute();
        
        // Lấy kết quả
        $result = $stmt->get_result();
        
        // Lấy dòng dữ liệu đầu tiên
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
        }
        
        // Đóng câu lệnh
        $stmt->close();
    }
}
// Nếu không tìm thấy sản phẩm, $product sẽ là null và $product_id vẫn là 0 (hoặc ID truyền vào)

// Đóng kết nối cơ sở dữ liệu sau khi hoàn thành truy vấn
if (isset($conn) && $conn) {
    $conn->close();
}

// Lấy ra số lượng tồn kho trong table sanpham
// Dùng $product_id đã được gán để đảm bảo nó có giá trị nếu tìm thấy sản phẩm
$ton_kho = isset($product['tonkho']) ? $product['tonkho'] : 'Đang cập nhật'; 
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
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <link href="css/bootstrap.min.css" rel="stylesheet">

    <link href="css/style.css" rel="stylesheet">
</head>

<body>

    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50  d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
       <?php include 'navbar.php'; ?>
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Chi tiết sản phẩm</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="./index.php" style = "color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Chi tiết sản phẩm</li>
        </ol>
    </div>
<div class="container-fluid py-5">
    <div class="container py-5">
        <!-- Thêm justify-content-center để căn giữa -->
        <div class="row g-4 mb-5 justify-content-center">
            <!-- Thêm mx-auto để căn giữa cột -->
            <div class="col-lg-8 col-xl-9 mx-auto">
                <div class="row g-4">
                    <?php if ($product) : ?>
                        <div class="col-lg-6">
                            <div class="border rounded">
                                <img src="../Admin/uploads/<?php echo htmlspecialchars($product['img']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['tensanpham']); ?>">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="fw-bold mb-3 change-font"><?php echo htmlspecialchars($product['tensanpham']); ?></h4>
                            <p class="mb-3">Danh mục: <?php echo htmlspecialchars($product['tendanhmuc']); ?></p>
                            <h5 class="fw-bold mb-3 change-font"><?php echo number_format($product['gia']); ?> đ</h5>
                            <div class="d-flex mb-4">
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star text-secondary"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p class="mb-4"><?php echo nl2br(htmlspecialchars($product['mota'])); ?></p>
                            <div class="mb-5">
                                <p class="mb-1 text-muted">
                                    Số lượng còn lại: <?php echo htmlspecialchars($ton_kho); ?>
                                </p>
                            </div>
                            <form action="cart.php" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="add_to_cart">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                                <input type="hidden" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>"> 
                                <button type="submit" class="btn border border-secondary rounded-pill px-4 py-2 mb-4 text-primary">
                                    <i class="fa fa-shopping-bag me-2 text-primary"></i> Thêm vào giỏ hàng
                                </button>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="col-12 text-center">
                            <h3>Sản phẩm không tồn tại hoặc đã bị xóa.</h3>
                            <a href="shop.php" class="btn btn-primary mt-3">Quay lại trang sản phẩm</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-12">
                    <nav>
                        <div class="nav nav-tabs mb-3">
                            <button class="nav-link border-white border-bottom-0" type="button" role="tab" id="nav-mission-tab" data-bs-toggle="tab" data-bs-target="#nav-mission" aria-controls="nav-mission" aria-selected="false">Đánh giá</button>
                        </div>
                    </nav>
                    <div class="tab-content mb-5">
                        <div class="tab-pane fade" id="nav-mission" role="tabpanel" aria-labelledby="nav-mission-tab">
                            <div class="d-flex mb-4">
                                <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                <div class="w-100">
                                    <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                    <div class="d-flex justify-content-between">
                                        <h5>Jason Smith</h5>
                                        <div class="d-flex mb-3">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </div>
                                    <p>The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic words etc. Susp endisse ultricies nisi vel quam suscipit</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <img src="img/avatar.jpg" class="img-fluid rounded-circle p-3" style="width: 100px; height: 100px;" alt="">
                                <div class="w-100">
                                    <p class="mb-2" style="font-size: 14px;">April 12, 2024</p>
                                    <div class="d-flex justify-content-between">
                                        <h5>Sam Peters</h5>
                                        <div class="d-flex mb-3">
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star text-secondary"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                        </div>
                                    </div>
                                    <p class="text-dark">The generated Lorem Ipsum is therefore always free from repetition injected humour, or non-characteristic words etc. Susp endisse ultricies nisi vel quam suscipit</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="#">
                    <h4 class="mb-5 fw-bold">Nhận xét</h4>
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="border-bottom rounded">
                                <input type="text" class="form-control border-0 me-4" placeholder="Tên *">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border-bottom rounded">
                                <input type="email" class="form-control border-0" placeholder="Email *">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="border-bottom rounded my-4">
                                <textarea name="" id="" class="form-control border-0" cols="30" rows="8" placeholder="Nhận xét của bạn *" spellcheck="false"></textarea>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="d-flex justify-content-between py-3 mb-5">
                                <div class="d-flex align-items-center">
                                    <p class="mb-0 me-3">Đánh giá:</p>
                                    <div class="d-flex align-items-center" style="font-size: 12px;">
                                        <i class="fa fa-star text-muted"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                        <i class="fa fa-star"></i>
                                    </div>
                                </div>
                                <a href="#" class="btn border border-secondary text-primary rounded-pill px-4 py-3">
                                    Đăng nhận xét
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
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
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/lightbox/js/lightbox.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="js/main.js"></script>
</body>

</html>