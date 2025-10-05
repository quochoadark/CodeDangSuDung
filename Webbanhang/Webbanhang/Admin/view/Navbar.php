<?php
// BƯỚC 1: Đặt session_start() có điều kiện ở ĐẦU TỆP
// Điều này khởi động session nếu nó chưa hoạt động và ngăn lỗi "Ignoring session_start()" 
// nếu session đã được khởi động ở file khác.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg"
    style="background-color: #fff; min-height:40px; border-bottom:1px solid #e5e5e5;">
    <div class="container-fluid">
        <button id="toggleSidebar" class="btn btn-link text-dark" style="font-size: 1.4rem; margin-left: 8px;">
            <i class="fas fa-bars"></i>
        </button>
        <div class="d-flex align-items-center ms-auto">
            <div class="dropdown">
                <button class="btn d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false" style="background: none; border: none;">
                    <img src="./assets/images/profile/user-1.jpg" alt="Admin"
                        style="width:32px; height:32px; border-radius:50%; margin-right:8px; margin-left:-4px;">
                    <span style="font-size: 1.15rem; font-weight: 500;">
                        <?php
                        // BƯỚC 2: Kiểm tra và hiển thị tên người dùng (sử dụng session đã được khởi động ở trên)
                        if (isset($_SESSION['hoten'])) {
                            echo htmlspecialchars($_SESSION['hoten']); // Hiển thị tên người dùng
                        } else {
                            echo "Khách"; // Hoặc một tên mặc định nếu chưa đăng nhập
                        }
                        ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="min-width: 200px;">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                            <i class="fas fa-user"></i> Thông tin của tôi
                        </a>    
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                            <i class="fas fa-envelope"></i> Thông tin tài khoản
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="#">
                            <i class="fas fa-tasks"></i> Nhiệm vụ
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-center btn btn-outline-primary"
                            href="/Webbanhang/Admin/view/Logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>