<?php 
// File: Admin/view/voucher.php (ĐÃ ĐIỀU CHỈNH RESPONSIVE)

require_once __DIR__ . '/../Controller/VoucherController.php';

// --- 1. Khởi tạo Controller ---
$controller = new VoucherController();

// --- 2. Xử lý hành động XÓA (nếu có) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete((int) $_GET['id']); 
    // Nếu xóa thành công, Controller sẽ redirect và exit()
}

// --- 3. Lấy danh sách Voucher ---
$data = $controller->index(); 
$vouchers = $data['vouchers'] ?? []; 
?>

<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="font-family: 'Arial', sans-serif;">
        <span class="mb-2 mb-sm-0"><b>Danh Sách Mã Khuyến Mãi</b></span>
        <a href="crud/Voucher/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th style="font-family: 'Arial', sans-serif;">Mã Voucher</th>
                        <th style="font-family: 'Arial', sans-serif;">Giá Trị Giảm</th>
                        <th style="font-family: 'Arial', sans-serif;" class="d-none d-lg-table-cell">Ngày Hết Hạn</th>
                        <th style="font-family: 'Arial', sans-serif;" class="d-none d-md-table-cell">Số Lượng</th>
                        <th style="font-family: 'Arial', sans-serif;" class="d-none d-md-table-cell">Đã Dùng</th>
                        <th style="font-family: 'Arial', sans-serif;">Trạng Thái</th>
                        <th style="font-family: 'Arial', sans-serif;">HĐ</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($vouchers): ?>
                        <?php foreach ($vouchers as $row): ?>
                            <?php
                                // --- Kiểm tra trạng thái ---
                                $ngayhethan   = strtotime($row['ngayhethan']);
                                $soluong      = $row['soluong'];
                                $luotsudung   = $row['luotsudung'];
                                $is_expired   = $ngayhethan < time();

                                if ($is_expired) {
                                    $status_text  = 'Hết hạn';
                                    $status_class = 'bg-danger';
                                } elseif ($soluong !== null && $luotsudung >= $soluong) {
                                    $status_text  = 'Hết SL';
                                    $status_class = 'bg-secondary';
                                } else {
                                    $status_text  = 'Còn hiệu lực';
                                    $status_class = 'bg-success';
                                }
                            ?>
                            <tr>
                                <td><span class="fw-bold"><?= htmlspecialchars($row['makhuyenmai']) ?></span></td>
                                <td><?= htmlspecialchars($row['giam']) ?></td>
                                <td class="d-none d-lg-table-cell"><?= htmlspecialchars(date('d/m/Y', $ngayhethan)) ?></td>
                                <td class="d-none d-md-table-cell"><?= $soluong === null ? 'Vô hạn' : htmlspecialchars($soluong) ?></td>
                                <td class="d-none d-md-table-cell"><?= htmlspecialchars($luotsudung) ?></td>

                                <td>
                                    <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                </td>

                                <td>
                                    <div class="d-flex flex-nowrap justify-content-center">
                                        <a href="crud/Voucher/Update.php?id=<?= $row['voucher_id'] ?>" 
                                            class="btn btn-info btn-sm me-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="#" 
                                            class="btn btn-danger btn-sm delete-link" 
                                            title="Xóa"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmModal"
                                            data-url="?page=voucher&action=delete&id=<?= $row['voucher_id'] ?>">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có mã khuyến mãi nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body text-center">
                <p>Bạn có chắc chắn muốn xóa mã khuyến mãi này không?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger px-4" id="confirmYes">Có</button>
            </div>
        </div>
    </div>
</div>

<script>
// Quản lý modal xác nhận xóa
document.addEventListener('DOMContentLoaded', function() {
    const confirmModal = document.getElementById('confirmModal');
    const confirmYesButton = document.getElementById('confirmYes');

    if (confirmModal) {
        confirmModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget; // nút mở modal
            const url = button.getAttribute('data-url'); // link xóa

            // Khi nhấn nút "Có", chuyển hướng xóa
            confirmYesButton.onclick = function() {
                window.location.href = url;
            };
        });
    }
});
</script>