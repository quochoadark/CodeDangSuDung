<?php 
require_once __DIR__ . '/../Controller/StaffController.php'; 

$controller = new StaffController();
$data = $controller->handleRequest(); 

$staffs        = $data['staffs'] ?? null; 
$total_pages   = $data['total_pages'] ?? 1;
$current_page  = $data['current_page'] ?? 1;
$page_param    = 'nhanvien'; 
$ID_NV_BAN_HANG = 1; // ID chức vụ Nhân viên bán hàng
?>

<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="font-family: 'Arial', sans-serif;">
        <span class="mb-2 mb-sm-0"><b>Danh Sách Nhân Viên</b></span>
        <a href="crud/Staff/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Họ Tên</th>
                        <th class="d-none d-md-table-cell">Email</th>
                        <th class="d-none d-lg-table-cell">Điện Thoại</th>
                        <th>Chức vụ</th>
                        <th class="d-none d-sm-table-cell">Trạng thái</th>
                        <th>HĐ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($staffs && $staffs->num_rows > 0): ?>
                        <?php while ($row = $staffs->fetch_assoc()): 
                            $is_active  = (int)($row['trangthai'] ?? 0) === 1;
                            $staff_id   = $row['staff_id'];
                            $chucvu_id  = (int)($row['id_chucvu'] ?? 0);
                            $is_nvbh    = $chucvu_id === $ID_NV_BAN_HANG; 
                            $staff_name = htmlspecialchars($row['hoten']);
                        ?> 
                        <tr>
                            <td><?= $staff_name ?></td>
                            <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['email']) ?></td>
                            <td class="d-none d-lg-table-cell"><?= htmlspecialchars($row['dienthoai'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($row['ten_chucvu'] ?? 'N/A') ?></td>
                            <td class="d-none d-sm-table-cell">
                                <?= $is_active 
                                    ? '<span class="badge bg-success">Hoạt động</span>' 
                                    : '<span class="badge bg-danger">Ngừng hoạt động</span>'; ?>
                            </td>
                            <td>
                                <div class="d-flex flex-nowrap align-items-center">
                                    <a href="crud/Staff/Detail.php?id=<?= $staff_id ?>" 
                                       class="btn btn-primary btn-sm me-1" 
                                       title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <?php if ($is_nvbh): ?>
                                        <?php if ($is_active): ?>
                                            <a href="#" 
                                               class="btn btn-warning btn-sm block-link"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#blockModal"
                                               data-url="?page=<?= $page_param ?>&action=block&id=<?= $staff_id ?>"
                                               data-staff="<?= $staff_name ?>"
                                               data-action="Khóa"
                                               title="Khóa tài khoản">
                                                <i class="fas fa-lock"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="#" 
                                               class="btn btn-success btn-sm block-link"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#blockModal"
                                               data-url="?page=<?= $page_param ?>&action=unblock&id=<?= $staff_id ?>"
                                               data-staff="<?= $staff_name ?>"
                                               data-action="Mở khóa"
                                               title="Mở khóa tài khoản">
                                                <i class="fas fa-lock-open"></i>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted d-none d-md-block" style="font-size: 0.8em;">
                                            (Không thể quản lý)
                                        </span>
                                    <?php endif; ?>
                                </div>
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
</div>

<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $current_page - 1 ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : 'd-none d-sm-block' ?>">
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page_param ?>&p=<?= $current_page + 1 ?>">&raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- MODAL XÁC NHẬN -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận Hành động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
document.addEventListener('DOMContentLoaded', function () {
    const blockModal = document.getElementById('blockModal');
    const confirmBlockButton = document.getElementById('confirmBlock');
    let currentBlockUrl = '';

    blockModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-url');
        const staffName = button.getAttribute('data-staff');
        const action = button.getAttribute('data-action');
        const modalBody = blockModal.querySelector('#blockModalBody');

        modalBody.innerHTML = `Bạn có chắc chắn muốn <strong>${action}</strong> tài khoản của nhân viên <strong>${staffName}</strong> không?`;
        currentBlockUrl = url;

        confirmBlockButton.classList.remove('btn-success', 'btn-warning', 'btn-danger');
        if (action === "Khóa") {
            confirmBlockButton.classList.add('btn-warning');
        } else if (action === "Mở khóa") {
            confirmBlockButton.classList.add('btn-success');
        } else {
            confirmBlockButton.classList.add('btn-danger');
        }
    });

    confirmBlockButton.addEventListener('click', function () {
        if (currentBlockUrl) {
            window.location.href = currentBlockUrl;
        }
    });
});
</script>
