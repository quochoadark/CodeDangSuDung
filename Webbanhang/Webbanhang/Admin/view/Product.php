<?php 
// Giả định file này nằm trong thư mục Admin/view/

// --- 1. NẠP CONTROLLER ---
// (Kiểm tra lại đường dẫn này nếu bị lỗi Not Found hoặc Class ProductController không tìm thấy)
require_once __DIR__ . '/../Controller/ProductController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM XỬ LÝ YÊU CẦU (Bao gồm Xóa và Lấy dữ liệu) ---
$controller = new ProductController();
$data = $controller->handleRequest(); 
// LƯU Ý: Nếu hành động xóa thành công, code PHP sẽ DỪNG TẠI ĐÂY (do exit() trong Controller)

// --- 3. GÁN BIẾN DỮ LIỆU TỪ KẾT QUẢ CỦA handleRequest() ---
// handleRequest() sẽ trả về mảng dữ liệu của hàm index() nếu không phải là hành động xóa.
$products = $data['products'] ?? null; 
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;
$error_message = $data['error_message'] ?? null; 

// --- 4. KHÔNG CẦN BIẾN $xoaDuLieu NỮA ---
// Biến $xoaDuLieu không cần thiết vì hành động đã được Controller xử lý và chuyển hướng.

// ----------------------------------------------------------------------------------
// Giữ lại phần xử lý thông báo thành công/thất bại dựa trên $_GET như cũ
// ----------------------------------------------------------------------------------

$delete_success = isset($_GET['delete_success']) && $_GET['delete_success'] == 1;
if ($delete_success): ?>
<?php endif; ?>

<?php 
// Kiểm tra thông báo thêm/sửa thành công (từ URL)
$add_success = isset($_GET['add_success']) && $_GET['add_success'] == 1;
$update_success = isset($_GET['update_success']) && $_GET['update_success'] == 1;

if ($add_success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Thêm sản phẩm thành công!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php elseif ($update_success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    Cập nhật sản phẩm thành công!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<?php if (isset($error_message) && $error_message): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= htmlspecialchars($error_message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>


<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="font-family: 'Arial', sans-serif">
        <span><b>Danh Sách Sản Phẩm</b></span>
        <a href="crud/Product/Add.php" class="btn btn-success">+ Thêm</a>
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="font-family: 'Arial', sans-serif">Tên sản phẩm</th>
                    <th style="font-family: 'Arial', sans-serif">Danh mục</th>
                    <th style="font-family: 'Arial', sans-serif">Giá</th>
                    <th style="font-family: 'Arial', sans-serif">Số lượng tồn</th>
                    <th style="font-family: 'Arial', sans-serif">Mô tả</th>
                    <th style="font-family: 'Arial', sans-serif">Ngày tạo</th>
                    <th style="font-family: 'Arial', sans-serif">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if ($products && $products->num_rows > 0): 
            ?>
                <?php while($row = $products->fetch_assoc()): ?> 
                <tr>
                    <td><?= htmlspecialchars($row['tensanpham']) ?></td>
                    
                    <td><?= htmlspecialchars($row['tendanhmuc'] ?? 'N/A') ?></td> 
                    
                    <td><?= number_format($row['gia'], 0, ',', '.') ?> VNĐ</td>
                    
                    <td><?= htmlspecialchars($row['tonkho']) ?></td>
                    
                    <td>
                        <?php
                            $mota = htmlspecialchars($row['mota']);
                            // Cắt chuỗi Mô tả
                            echo (mb_strlen($mota) > 50) ? mb_substr($mota, 0, 50) . '...' : $mota;
                        ?>
                    </td>
                    
                    <td><?= htmlspecialchars($row['ngaytao']) ?></td>
                    
                    <td>
                        <a href="crud/Product/Update.php?id=<?= $row['product_id'] ?>" class="btn btn-info btn-sm" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <a href="#" class="btn btn-danger btn-sm delete-link" title="Xóa"
                            data-url="?page=sanpham&action=delete&id=<?= $row['product_id'] ?>">
                            <i class="fas fa-times"></i>
                        </a>
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

<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination">
            <?php 
            if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=sanpham&p=<?= $current_page - 1 ?>" aria-label="Previous">
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
                    <a class="page-link" href="?page=sanpham&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php
            // Nút "Trang sau" (Next)
            if ($current_page < $total_pages): ?>
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
            <div class="modal-body">Bạn có chắc chắn muốn xóa sản phẩm này không?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Không</button>
                <button type="button" class="btn btn-danger" id="confirmYes">Có</button>
            </div>
        </div>
    </div>
</div>
<script>
    // **LƯU Ý: Phải đảm bảo thư viện Bootstrap và jQuery (nếu dùng) đã được load**
    let deleteUrl = '';
    const confirmYes = document.getElementById('confirmYes');

    document.querySelectorAll('.delete-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            // Lấy URL xóa từ data-url
            deleteUrl = link.dataset.url;
            
            // Mở Modal
            const myModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            myModal.show();
        });
    });

    confirmYes.addEventListener('click', () => {
        // Chuyển hướng khi người dùng xác nhận xóa
        window.location.href = deleteUrl;
    });
</script>