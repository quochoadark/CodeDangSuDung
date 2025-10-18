<?php 
// File: Admin/view/khuyenmai.php

require_once __DIR__ . '/../Controller/SaleProductController.php';
$controller = new SaleProductController();

// --- Xử lý hành động XÓA (nếu có) ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $controller->delete((int) $_GET['id']); 
}

// --- Lấy danh sách Khuyến mãi ---
$data = $controller->index(); 
$sales = $data['sales'] ?? []; 
?>

<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="font-family: 'Arial', sans-serif;">
        <span class="mb-2 mb-sm-0"><b>Danh Sách Khuyến Mãi Sản Phẩm</b></span>
        <a href="crud/SaleProduct/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-light">
                    <tr>
                        <th class="d-none d-md-table-cell">ID</th> <th>Sản Phẩm</th>
                        <th class="d-none d-lg-table-cell">Mô Tả</th> <th>Giảm</th>
                        <th class="d-none d-sm-table-cell">Giá Gốc</th> <th>Giá Sale</th>
                        <th class="d-none d-md-table-cell">Thời Gian</th> <th class="d-none d-sm-table-cell">Trạng Thái</th> <th>HĐ</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($sales): ?>
                        <?php foreach ($sales as $row): ?>
                            <?php
                                // --- Xử lý logic hiển thị ---
                                $giam_value   = $row['giam'];
                                $gia_goc      = $row['gia'];
                                
                                if ($giam_value < 1.0) {
                                    // Giảm theo tỷ lệ % (ví dụ: 0.15 -> 15%)
                                    $giam_text    = ($giam_value * 100) . '%';
                                    $giam_tien    = $gia_goc * $giam_value;
                                } else {
                                    // Giảm theo số tiền cố định (ví dụ: 50000)
                                    $giam_text    = number_format($giam_value, 0, ',', '.') . ' VND';
                                    $giam_tien    = $giam_value;
                                }
                                
                                $gia_sale = $gia_goc - $giam_tien;
                                $ngay_start = strtotime($row['ngaybatdau']);
                                $ngay_end   = strtotime($row['ngayketthuc']);
                                $current_time = time();

                                if ($current_time < $ngay_start) {
                                    $status_text = 'Sắp diễn ra';
                                    $status_class = 'bg-warning text-dark';
                                } elseif ($current_time > $ngay_end) {
                                    $status_text = 'Đã kết thúc';
                                    $status_class = 'bg-danger';
                                } else {
                                    $status_text = 'Đang diễn ra';
                                    $status_class = 'bg-success';
                                }
                            ?>
                            <tr>
                                <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['promo_id']) ?></td>
                                <td class="text-start">
                                    <b><?= htmlspecialchars($row['tensanpham']) ?></b> 
                                </td>
                                <td class="d-none d-lg-table-cell text-truncate" style="max-width: 150px;">
                                    <?= htmlspecialchars($row['mota']) ?>
                                </td>
                                <td><?= $giam_text ?></td>
                                <td class="d-none d-sm-table-cell">
                                    <?= number_format($gia_goc, 0, ',', '.') ?>
                                </td>
                                <td class="fw-bold text-danger"><?= number_format($gia_sale, 0, ',', '.') ?></td>
                                <td class="d-none d-md-table-cell" style="min-width: 130px;">
                                    <?= date('d/m/Y', $ngay_start) . '<br> - ' . date('d/m/Y', $ngay_end) ?>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <span class="badge <?= $status_class ?>"><?= $status_text ?></span>
                                </td>
                                <td>
                                    <div class="d-flex flex-nowrap justify-content-center">
                                        <a href="crud/SaleProduct/Update.php?id=<?= $row['promo_id'] ?>" 
                                            class="btn btn-info btn-sm me-1" title="Sửa"><i class="fas fa-edit"></i></a>
                                        <a href="#" 
                                            class="btn btn-danger btn-sm delete-link" 
                                            title="Xóa"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#confirmModal"
                                            data-url="?page=khuyenmai&action=delete&id=<?= $row['promo_id'] ?>">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted">Không có khuyến mãi sản phẩm nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div> </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa khuyến mãi này không? Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a id="confirmDeleteButton" href="#" class="btn btn-danger">Xóa</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var confirmModal = document.getElementById('confirmModal');
        
        // Kiểm tra xem Modal có tồn tại không trước khi thêm listener
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget; 
                var deleteUrl = button.getAttribute('data-url');
                
                var confirmDeleteButton = confirmModal.querySelector('#confirmDeleteButton');
                confirmDeleteButton.href = deleteUrl;
            });
        }
    });
</script>