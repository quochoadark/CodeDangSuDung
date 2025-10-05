<?php 
// File: Admin/view/nhanvien.php (Danh sách nhân viên)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../Controller/StaffController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM XỬ LÝ YÊU CẦU ---
$controller = new StaffController();
$data = $controller->handleRequest(); 

// --- 3. GÁN BIẾN DỮ LIỆU ---
$staffs = $data['staffs'] ?? null; 
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;

// Base URL cho các hành động
$page_param = 'nhanvien'; 

// ID CHỨC VỤ CỦA NHÂN VIÊN BÁN HÀNG (PHẢI KHỚP VỚI ID ĐÃ DÙNG TRONG CONTROLLER)
$ID_NV_BAN_HANG = 1; 
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="font-family: 'Arial', sans-serif">
        <span><b>Danh Sách Nhân Viên</b></span>
        <a href="crud/Staff/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="font-family: 'Arial', sans-serif">Họ Tên</th>
                    <th style="font-family: 'Arial', sans-serif">Email</th>
                    <th style="font-family: 'Arial', sans-serif">Điện Thoại</th>
                    <th style="font-family: 'Arial', sans-serif">Chức vụ</th>
                    <th style="font-family: 'Arial', sans-serif">Trạng thái</th>
                    <th style="font-family: 'Arial', sans-serif">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if ($staffs && $staffs->num_rows > 0): 
            ?>
                <?php while($row = $staffs->fetch_assoc()): 
                    $is_active = (int)($row['trangthai'] ?? 0) === 1;
                    $staff_id = $row['staff_id'];
                    $chucvu_id = (int)($row['id_chucvu'] ?? 0);
                    
                    // Kiểm tra xem nhân viên này có phải là Nhân viên Bán hàng không
                    $is_nvbh = $chucvu_id === $ID_NV_BAN_HANG;
                    $staff_name = htmlspecialchars($row['hoten']);
                ?> 
                <tr>
                    <td><?= $staff_name ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['dienthoai'] ?? 'N/A') ?></td>
                    
                    <td><?= htmlspecialchars($row['ten_chucvu'] ?? 'N/A') ?></td> 
                    
                    <td>
                        <?php
                            echo $is_active ? '<span class="badge bg-success">Hoạt động</span>' : '<span class="badge bg-danger">Ngừng hoạt động</span>';
                        ?>
                    </td>
                    
                    <td>
                        <a href="crud/Staff/Detail.php?id=<?= $staff_id ?>" class="btn btn-primary btn-sm" title="Chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="crud/Staff/Update.php?id=<?= $staff_id ?>" class="btn btn-info btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <?php 
                        // CHỈ HIỂN THỊ NÚT KHÓA/MỞ KHÓA VÀ XÓA CHO NHÂN VIÊN BÁN HÀNG
                        if ($is_nvbh): 
                            if ($is_active): 
                        ?>
                                <a href="#" class="btn btn-warning btn-sm block-link" title="Khóa nhân viên"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#blockModal"
                                    data-url="?page=<?= $page_param ?>&action=block&id=<?= $staff_id ?>"
                                    data-staff="<?= $staff_name ?>"
                                    data-action="Khóa">
                                    <i class="fas fa-lock"></i> 
                                </a>
                            <?php else: ?>
                                <a href="#" class="btn btn-success btn-sm block-link" title="Mở khóa nhân viên" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#blockModal"
                                    data-url="?page=<?= $page_param ?>&action=unblock&id=<?= $staff_id ?>"
                                    data-staff="<?= $staff_name ?>"
                                    data-action="Mở khóa">
                                    <i class="fas fa-lock-open"></i> 
                                </a>
                            <?php 
                            endif;
                            ?>
                            
                            <a href="#" class="btn btn-danger btn-sm delete-link" title="Xóa"
                                data-bs-toggle="modal" 
                                data-bs-target="#confirmModal"
                                data-url="?page=<?= $page_param ?>&action=delete&id=<?= $staff_id ?>">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php 
                        // END IF $is_nvbh
                        else:
                        ?>
                            <span class="text-muted" style="font-size: 0.8em;">(Không thể Khóa/Xóa)</span>
                        <?php
                        endif;
                        ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center">Không có nhân viên nào.</td>
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
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $current_page - 1 ?>" aria-label="Previous">
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
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php
            // Nút "Trang sau" (Next)
            if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $current_page + 1 ?>" aria-label="Next">
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
                <h5 class="modal-title" id="confirmModalLabel">Xác nhận Xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Bạn có chắc chắn muốn xóa nhân viên này không?</div>
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

    // --- LOGIC XỬ LÝ MODAL XÓA ---
    document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', e => {
            deleteUrl = link.dataset.url;
        });
    });

    confirmYes.addEventListener('click', () => {
        window.location.href = deleteUrl;
    });

    // --- LOGIC XỬ LÝ MODAL KHÓA/MỞ KHÓA (Điều chỉnh cho Nhân viên) ---
    document.querySelectorAll('.block-link').forEach(link => {
        link.addEventListener('click', e => {
            blockUrl = link.dataset.url;
            // Lấy tên nhân viên (từ data-staff thay vì data-user)
            const staffName = link.dataset.staff; 
            const actionType = link.dataset.action; // 'Khóa' hoặc 'Mở khóa'
            
            // Cập nhật nội dung Modal
            const modalBody = document.getElementById('blockModalBody');
            modalBody.innerHTML = `Bạn có chắc chắn muốn **${actionType}** nhân viên **${staffName}** không?`;
            
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
        window.location.href = blockUrl;
    });
</script>