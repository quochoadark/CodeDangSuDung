<?php
// File: Admin/view/Navbar.php 

// BƯỚC 1: Đặt session_start() có điều kiện ở ĐẦU TỆP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg"
    style="background-color: #fff; min-height:40px; border-bottom:1px solid #e5e5e5;">
    <div class="container-fluid">
        <button id="toggleSidebar" class="btn btn-link text-dark d-block d-lg-none" style="font-size: 1.4rem; margin-left: 8px;">
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
                        // CHỈ KIỂM TRA VÀ HIỂN THỊ TÊN KHI CÓ SESSION
                        if (isset($_SESSION['hoten'])) {
                            echo htmlspecialchars($_SESSION['hoten']); // Hiển thị tên người dùng
                        }
                        ?>
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="min-width: 200px;">
                    <li>
                        <a class="dropdown-item text-center btn btn-outline-primary"
                            href="/Webbanhang/Admin/view/Logout.php">Đăng xuất</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>