<?php
// 📁 File: register.php
require_once '../../Database/database.php';
require_once '../Controller/RegisterUserController.php';

// Khởi tạo Controller
$db = new database();
$conn = $db->conn;

$controller = new RegisterUserController($conn);
$controller->handleRequest();

// Đóng kết nối sau khi xử lý xong
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng ký tài khoản</title>

  <!-- Bootstrap & FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- jQuery & Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // ✅ VALIDATE CLIENT (hiển thị lỗi bằng Modal)
    function validateForm() {
      const form = document.forms["registerForm"];
      const hoten = form["hoten"].value.trim();
      const email = form["email"].value.trim();
      const matkhau = form["matkhau"].value.trim();
      const dienthoai = form["dienthoai"].value.trim();
      const diachi = form["diachi"].value.trim();
      let errorMessage = "";

      // Kiểm tra các trường bắt buộc
      if (!hoten || !email || !matkhau || !dienthoai || !diachi) {
        errorMessage = "Vui lòng điền đầy đủ tất cả các trường!";
      }
      // Kiểm tra email hợp lệ
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errorMessage = "Địa chỉ Email không hợp lệ!";
      }
      // Kiểm tra độ dài mật khẩu
      else if (matkhau.length < 6) {
        errorMessage = "Mật khẩu phải ít nhất 6 ký tự!";
      }
      // Kiểm tra định dạng số điện thoại
      else if (!/^(0[0-9]{9,10})$/.test(dienthoai)) {
        errorMessage = "Số điện thoại không hợp lệ (ví dụ: 0901234567)!";
      }

      if (errorMessage) {
        $('#clientErrorDetails').text(errorMessage);
        $('#clientErrorModal').modal('show');
        return false;
      }

      return true;
    }
  </script>
</head>

<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg border-0">
          <div class="card-body p-4">
            <h3 class="text-center mb-4 text-primary">Đăng ký tài khoản</h3>

            <form name="registerForm" method="POST" action="" onsubmit="return validateForm();" novalidate>
              <div class="mb-3">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="hoten" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="matkhau" class="form-control" required minlength="6">
              </div>

              <div class="mb-3">
                <label class="form-label">Điện thoại</label>
                <input type="text" name="dienthoai" class="form-control" required>
              </div>

              <div class="mb-3">
                <label class="form-label">Địa chỉ</label>
                <input type="text" name="diachi" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-primary w-100">Đăng ký</button>
            </form>

            <p class="text-center mt-3">
              Đã có tài khoản? <a href="Login.php">Đăng nhập ngay</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Modal Thành công -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-success" id="successModalLabel">Thông báo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
          <h4 class="mt-3">Đăng ký thành công!</h4>
        </div>
        <div class="modal-footer justify-content-center">
          <a href="Login.php" class="btn btn-primary">Đăng nhập ngay</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ❌ Modal Lỗi Server -->
  <div class="modal fade" id="serverErrorModal" tabindex="-1" aria-labelledby="serverErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-danger" id="serverErrorModalLabel">Lỗi Đăng ký</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <i class="fas fa-times-circle text-danger" style="font-size: 3rem;"></i>
          <h4 class="mt-3">Đăng ký thất bại!</h4>
          <p id="serverErrorDetails" class="mt-2 text-danger">
            <?php echo htmlspecialchars($controller->error_message ?? ''); ?>
          </p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
        </div>
      </div>
    </div>
  </div>

  <!-- ⚠️ Modal Lỗi Client -->
  <div class="modal fade" id="clientErrorModal" tabindex="-1" aria-labelledby="clientErrorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-warning" id="clientErrorModalLabel">Xác thực Dữ liệu</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
          <h4 class="mt-3">Dữ liệu nhập không hợp lệ!</h4>
          <p id="clientErrorDetails" class="mt-2 text-warning"></p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sửa lại</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      <?php if ($controller->registration_success): ?>
        $('#successModal').modal('show');
      <?php elseif (!empty($controller->error_message)): ?>
        $('#serverErrorModal').modal('show');
      <?php endif; ?>
    });
  </script>
</body>
</html>
