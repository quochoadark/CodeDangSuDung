<?php 
// File: Admin/view/sanpham.php (ĐÃ CẬP NHẬT CHO CẤU TRÚC CSR)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../Controller/ProductController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM XỬ LÝ YÊU CẦU (INDEX) ---
$controller = new ProductController();

// Gọi hàm index() trong Controller mới. Hàm này sẽ tự xử lý DELETE
// hoặc trả về dữ liệu danh sách sản phẩm.
$data = $controller->index(); 

// --- 3. GÁN BIẾN DỮ LIỆU ---
$products       = $data['products'] ?? null; 
$total_pages    = $data['total_pages'] ?? 1;
$current_page   = $data['current_page'] ?? 1;
$error_message  = $data['error_message'] ?? null;
?>

<?php 
// ================================
// HIỂN THỊ THÔNG BÁO THÀNH CÔNG / LỖI
// ================================
$delete_success = (isset($_GET['delete_success']) && $_GET['delete_success'] == 1);
$add_success    = isset($_GET['add_success']) && $_GET['add_success'] == 1;
$update_success = isset($_GET['update_success']) && $_GET['update_success'] == 1;

if ($delete_success || $add_success || $update_success): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $delete_success ? 'Xóa sản phẩm thành công! 👍' : ($add_success ? 'Thêm sản phẩm thành công! ✨' : 'Cập nhật sản phẩm thành công! ✅') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error_message) ?> ⚠️
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="font-family: 'Arial', sans-serif">
        <span class="mb-2 mb-sm-0"><b>Danh Sách Sản Phẩm</b></span> 
        <a href="crud/Product/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Tên SP</th>
                        <th class="d-none d-lg-table-cell">Danh mục</th> 
                        <th>Giá</th>
                        <th class="d-none d-sm-table-cell">Tồn</th> 
                        <th class="d-none d-xl-table-cell">Mô tả</th>
                        <th class="d-none d-md-table-cell">Ngày tạo</th> 
                        <th>HĐ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products && $products->num_rows > 0): ?>
                        <?php while ($row = $products->fetch_assoc()): ?> 
                            <tr>
                                <td><?= htmlspecialchars($row['tensanpham']) ?></td>
                                <td class="d-none d-lg-table-cell"><?= htmlspecialchars($row['tendanhmuc'] ?? 'N/A') ?></td> 
                                <td><?= number_format($row['gia'], 0, ',', '.') ?> VNĐ</td>
                                <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['tonkho']) ?></td>
                                <td class="d-none d-xl-table-cell text-truncate" style="max-width: 150px;">
                                    <?php
                                         $mota = htmlspecialchars($row['mota']);
                                         echo (mb_strlen($mota) > 30) ? mb_substr($mota, 0, 30) . '...' : $mota;
                                    ?>
                                </td>
                                <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['ngaytao']) ?></td>
                                <td>
                                    <div class="d-flex flex-nowrap justify-content-center"> 
                                        <a href="crud/Product/Update.php?id=<?= $row['product_id'] ?>" class="btn btn-info btn-sm me-1" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" 
                                           class="btn btn-danger btn-sm delete-link" 
                                           title="Xóa"
                                           data-bs-toggle="modal"
                                           data-bs-target="#confirmModal"
                                           data-url="?page=sanpham&action=delete&id=<?= $row['product_id'] ?>">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có sản phẩm nào.</td>
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
                    <a class="page-link" href="?page=sanpham&p=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=sanpham&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=sanpham&p=<?= $current_page + 1 ?>" aria-label="Next">
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
            <div class="modal-body">Bạn có chắc chắn muốn thực hiện hành động này không?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger" id="confirmYes">Có</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmYesButton = document.getElementById('confirmYes');
    const confirmModalElement = document.getElementById('confirmModal');
    const modalBody = confirmModalElement.querySelector('.modal-body');
    const modalTitle = confirmModalElement.querySelector('.modal-title');
    let actionUrl = '';

    if (confirmModalElement) {
        confirmModalElement.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            if (button && button.hasAttribute('data-url')) {
                actionUrl = button.getAttribute('data-url');

                if (button.classList.contains('delete-link')) {
                    modalBody.textContent = 'Bạn có chắc chắn muốn xóa sản phẩm này không?';
                    modalTitle.textContent = 'Xác nhận Xóa';
                }
            }
        });

        confirmYesButton.addEventListener('click', () => {
            if (actionUrl) {
                window.location.href = actionUrl;
            }
        });
    }
});
</script>