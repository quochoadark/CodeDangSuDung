<?php 
// File: Admin/view/khachhang.php

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../Controller/UserController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM XỬ LÝ YÊU CẦU (Bao gồm Xóa, Block và Lấy dữ liệu) ---
$controller = new UserController();
$data = $controller->handleRequest(); 
// LƯU Ý: Nếu hành động xóa/block/unblock thành công, code PHP sẽ DỪNG TẠI ĐÂY (do exit() trong Controller)

// --- 3. GÁN BIẾN DỮ LIỆU TỪ KẾT QUẢ CỦA handleRequest() ---
$users = $data['users'] ?? null; 
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;

// ----------------------------------------------------------------------------------
// Đã loại bỏ logic hiển thị thông báo (Alerts) theo yêu cầu.
// ----------------------------------------------------------------------------------

?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="font-family: 'Arial', sans-serif">
        <span><b>Danh Sách Khách Hàng</b></span>
        <a href="crud/User/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="font-family: 'Arial', sans-serif">Họ Tên</th>
                    <th style="font-family: 'Arial', sans-serif">Email</th>
                    <th style="font-family: 'Arial', sans-serif">Điện Thoại</th>
                    <th style="font-family: 'Arial', sans-serif">Địa Chỉ</th>
                    <th style="font-family: 'Arial', sans-serif">Tier/Vai trò</th>
                    <th style="font-family: 'Arial', sans-serif">Trạng thái</th>
                    <th style="font-family: 'Arial', sans-serif">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if ($users && $users->num_rows > 0): 
            ?>
                <?php while($row = $users->fetch_assoc()): 
                    // Kiểm tra trạng thái hiện tại
                    $is_active = (int)($row['trangthai'] ?? 0) === 1;
                ?> 
                <tr>
                    <td><?= htmlspecialchars($row['hoten']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['dienthoai'] ?? 'N/A') ?></td>
                    
                    <td>
                        <?php
                            $diachi = htmlspecialchars($row['diachi'] ?? '');
                            // Cắt chuỗi Địa chỉ
                            echo (mb_strlen($diachi) > 30) ? mb_substr($diachi, 0, 30) . '...' : $diachi;
                        ?>
                    </td>
                    
                    <td><?= htmlspecialchars($row['tenhang'] ?? 'N/A') ?></td> 
                    
                    <td>
                        <?php
                            echo $is_active ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Khóa</span>';
                        ?>
                    </td>
                    
                    <td>
                        <a href="crud/User/Detail.php?id=<?= $row['user_id'] ?>" class="btn btn-primary btn-sm" title="Chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="crud/User/Update.php?id=<?= $row['user_id'] ?>" class="btn btn-info btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <?php if ($is_active): ?>
                            <a href="#" class="btn btn-warning btn-sm block-link" title="Khóa khách hàng"
                                data-bs-toggle="modal" 
                                data-bs-target="#blockModal"
                                data-url="?page=khachhang&action=block&id=<?= $row['user_id'] ?>"
                                data-user="<?= htmlspecialchars($row['hoten']) ?>"
                                data-action="Khóa">
                                <i class="fas fa-lock"></i>
                            </a>
                        <?php else: ?>
                            <a href="#" class="btn btn-success btn-sm block-link" title="Mở khóa khách hàng" 
                                data-bs-toggle="modal" 
                                data-bs-target="#blockModal"
                                data-url="?page=khachhang&action=unblock&id=<?= $row['user_id'] ?>"
                                data-user="<?= htmlspecialchars($row['hoten']) ?>"
                                data-action="Mở khóa">
                                <i class="fas fa-unlock-alt"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="#" class="btn btn-danger btn-sm delete-link" title="Xóa"
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmModal"
                            data-url="?page=khachhang&action=delete&id=<?= $row['user_id'] ?>">
                            <i class="fas fa-times"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="7" class="text-center">Không có người dùng nào.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination">
            <?php 
            if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=khachhang&p=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            // Vòng lặp hiển thị các số trang
            for ($i = 1; $i <= $total_pages; $i++):
                $active_class = ($i == $current_page) ? 'active' : '';
            ?>
                <li class="page-item <?= $active_class ?>">
                    <a class="page-link" href="?page=khachhang&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php
            // Nút "Trang sau" (Next)
            if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=khachhang&p=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác nhận</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Bạn có chắc chắn muốn xóa người dùng này không?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger" id="confirmYes">Có</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="blockModalLabel">Xác nhận Hành động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="blockModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmBlock">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Khai báo biến toàn cục
    let deleteUrl = '';
    let blockUrl = '';
    
    const confirmYes = document.getElementById('confirmYes');
    const confirmBlock = document.getElementById('confirmBlock');

    // --- LOGIC XỬ LÝ MODAL XÓA (Giữ nguyên) ---
    document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', e => {
            // Lấy URL xóa từ data-url
            deleteUrl = link.dataset.url;
        });
    });

    confirmYes.addEventListener('click', () => {
        // Chuyển hướng khi người dùng xác nhận xóa
        window.location.href = deleteUrl;
    });

    // --- LOGIC XỬ LÝ MODAL KHÓA/MỞ KHÓA (MỚI) ---
    document.querySelectorAll('.block-link').forEach(link => {
        link.addEventListener('click', e => {
            // Lấy URL Khóa/Mở khóa từ data-url
            blockUrl = link.dataset.url;
            const userName = link.dataset.user;
            const actionType = link.dataset.action; // 'Khóa' hoặc 'Mở khóa'
            
            // Cập nhật nội dung Modal
            const modalBody = document.getElementById('blockModalBody');
            modalBody.innerHTML = `Bạn có chắc chắn muốn **${actionType}** tài khoản **${userName}** không?`;
            
            // Cập nhật nút xác nhận (đổi màu nếu là hành động Khóa)
            if (actionType === 'Khóa') {
                confirmBlock.classList.remove('btn-success');
                confirmBlock.classList.add('btn-danger');
            } else {
                confirmBlock.classList.remove('btn-danger');
                confirmBlock.classList.add('btn-success');
            }
        });
    });

    confirmBlock.addEventListener('click', () => {
        // Chuyển hướng khi người dùng xác nhận Khóa/Mở khóa
        window.location.href = blockUrl;
    });
</script>