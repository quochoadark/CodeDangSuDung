<?php
// Bật hiển thị lỗi để dễ dàng gỡ lỗi
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Bao gồm file kết nối cơ sở dữ liệu
include __DIR__ . '/../Database/Database.php';

$registration_success = false;

// Kiểm tra nếu form đã được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form và gán vào biến
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $matkhau_raw = $_POST['matkhau'];
    $dienthoai = trim($_POST['dienthoai']);
    $diachi = trim($_POST['diachi']);

    // Xác thực dữ liệu cơ bản
    if (empty($hoten) || empty($email) || empty($matkhau_raw) || empty($dienthoai) || empty($diachi)) {
        echo "<script>alert('Vui lòng điền đầy đủ tất cả các trường.');</script>";
    } else {
        // Mã hóa mật khẩu
        $hashed_matkhau = password_hash($matkhau_raw, PASSWORD_DEFAULT);

        // Gán giá trị mặc định cho các cột không có trong form
        $tier_id = 4;
        $trangthai = 'hoạt động';

        // Chuẩn bị câu lệnh SQL
        $sql = "INSERT INTO nguoidung (hoten, email, matkhau, dienthoai, diachi, tier_id, trangthai) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Kiểm tra xem câu lệnh SQL đã được chuẩn bị thành công chưa
        if ($stmt === false) {
            error_log("Lỗi SQL: " . $conn->error);
            echo "<script>alert('Lỗi hệ thống, vui lòng thử lại sau.');</script>";
        } else {
            // Gán các giá trị vào câu lệnh SQL
            $stmt->bind_param("sssssis", $hoten, $email, $hashed_matkhau, $dienthoai, $diachi, $tier_id, $trangthai);

            // Thực thi câu lệnh và kiểm tra kết quả
            if ($stmt->execute()) {
                $registration_success = true;
            } else {
                echo "<script>alert('Lỗi: " . $stmt->error . "');</script>";
            }

            // Đóng câu lệnh
            $stmt->close();
        }
    }
    // Đóng kết nối
    $conn->close();
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng ký</title>
  <link rel="shortcut icon" type="image/png" href="../Admin/assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../Admin/assets/css/Template/styles.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="../Admin/assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../Admin/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="./index.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="./assets/images/logos/logo.svg" alt="">
                </a>
                <p class="text-center fw-bold" style="margin-top:-30px; font-size: 20px;">Đăng ký</p>
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                  <div class="mb-3">
                    <label for="hoten" class="form-label">Họ và tên</label>
                    <input type="text" class="form-control" id="hoten" name="hoten" aria-describedby="textHelp" required>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Địa chỉ Email</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" required>
                  </div>
                  <div class="mb-4">
                    <label for="matkhau" class="form-label">Mật khẩu</label>
                    <input type="password" class="form-control" id="matkhau" name="matkhau" required>
                  </div>
                  <div class="mb-3">
                    <label for="dienthoai" class="form-label">Số điện thoại</label>
                    <input type="tel" class="form-control" id="dienthoai" name="dienthoai" required>
                  </div>
                  <div class="mb-3">
                    <label for="diachi" class="form-label">Địa chỉ</label>
                    <input type="text" class="form-control" id="diachi" name="diachi" required>
                  </div>
                  <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Đăng ký</button>
                  <div class="d-flex align-items-center justify-content-center">
                    <p class="fs-4 mb-0 fw-bold">Bạn đã có tài khoản?</p>
                    <a class="text-primary fw-bold ms-2" href="./Login.php">Đăng nhập</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="Database fade" id="successDatabase" tabindex="-1" aria-labelledby="successDatabaseLabel" aria-hidden="true">
    <div class="Database-dialog Database-dialog-centered">
      <div class="Database-content">
        <div class="Database-header">
          <h5 class="Database-title" id="successDatabaseLabel">Thông báo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="Database" aria-label="Close"></button>
        </div>
        <div class="Database-body text-center">
          <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
          <h4 class="mt-3">Đăng ký thành công!</h4>
        </div>
        <div class="Database-footer justify-content-center">
          <a href="Login.php" class="btn btn-primary">Đăng nhập ngay</a>
        </div>
      </div>
    </div>
  </div>
  <?php if ($registration_success): ?>
  <script>
    $(document).ready(function(){
      $('#successDatabase').Database('show');
    });
  </script>
  <?php endif; ?>
</body>
</html>