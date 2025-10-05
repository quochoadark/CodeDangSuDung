<?php
ob_start(); // GIẢI QUYẾT LỖI HEADER
session_start(); // Gọi session_start() ngay từ đầu file
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Trang quản trị</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/Template/styles.min.css" />
  <link rel="stylesheet" href="./assets/css/Template/errors.css" />
  <link rel="stylesheet" href="./assets/css/style.css" />
  <link rel="stylesheet" href="./assets/fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <?php include './view/Sidebar.php'; ?>
      <div class="col-10 p-0">
        <?php include './view/Navbar.php'; ?>
        <!-- Nội dung chính -->
        <!-- THAY class="content" thành id="content" -->
        <div id="content" class="content p-3">
        <?php
        if(isset($_GET['page'])){
            $page = $_GET['page'];
            switch($page){
                case 'nhanvien':
                      include './view/Staff.php';
                    break;
                case 'danhmuc':
                    include './view/Category.php';
                    break;
                case 'sanpham':
                    include './view/Product.php';
                    break;
                case 'danhgia':
                    include './Controller/DanhGiaController.php';
                    break;
                case 'doanhthu':
                    include './Controller/DoanhThuController.php';
                    break;
                case 'donhang':
                    include './Controller/DonHangController.php';
                    break;
                case 'khachhang':
                    include './view/User.php';
                    break;
                case 'khuyenmai':
                    include './Controller/KhuyenMaiController.php';
                    break;
                case 'voucher':
                    include './Controller/VoucherController.php';
                    break;
                default:
                    echo "Trang không tồn tại";
            }
        } else {
            include  './view/Staff.php'; // mặc định
        }
        ?>
        </div><!-- end #content -->
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/js/sidebarmenu.js"></script>
  <script src="./assets/js/app.min.js"></script>
  <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="./assets/js/dashboard.js"></script>
  <script src="./assets/js/ajax-load.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

  <!-- JS bạn copy hôm trước để load AJAX -->
  <script src="./assets/js/ajax-load.js"></script>
</body>
</html>
