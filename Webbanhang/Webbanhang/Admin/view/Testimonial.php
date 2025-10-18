<?php 
// File: Admin/view/danhgia.php (ĐÃ ĐIỀU CHỈNH RESPONSIVE)

// --- 1. NẠP CONTROLLER ---
require_once __DIR__ . '/../Controller/TestimonialController.php'; 

// --- 2. KHỞI TẠO VÀ GỌI HÀM XỬ LÝ YÊU CẦU ---
$controller = new TestimonialController();
$data = $controller->handleRequest(); 

// --- 3. GÁN BIẾN DỮ LIỆU ---
$reviews = $data['reviews'] ?? null; 
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;
$product_list = $data['product_list'] ?? []; 
$current_product_id = $data['current_product_id'] ?? 0; 

// Tạo chuỗi truy vấn
$product_query_string = $current_product_id > 0 ? '&product_id=' . $current_product_id : '';
$current_page_query = '&p=' . $current_page;

// Xử lý thông báo (từ session)
$alert_msg = $_SESSION['review_msg'] ?? null;
unset($_SESSION['review_msg']);

// Độ dài tối đa của Bình luận trước khi cắt (ví dụ: 50 ký tự)
$MAX_COMMENT_LENGTH = 50; 

?>

<div class="card">
    <div class="card-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center" style="font-family: 'Arial', sans-serif">
        <span class="mb-2 mb-sm-0"><b>Quản Lý Đánh Giá Sản Phẩm</b></span>
        
        <div class="d-flex align-items-center">
            <form method="GET" class="me-3">
                <input type="hidden" name="page" value="danhgia">
                <?php if ($current_page > 1): ?>
                    <input type="hidden" name="p" value="1"> 
                <?php endif; ?>
                
                <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="0" <?= $current_product_id == 0 ? 'selected' : '' ?>>--- Tất cả Sản phẩm ---</option>
                    <?php foreach ($product_list as $product): ?>
                        <option 
                            value="<?= $product['product_id'] ?>" 
                            <?= $current_product_id == $product['product_id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($product['tensanpham']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="card-body">
        <?php if ($alert_msg): // Hiển thị thông báo ?>
            <div class="alert alert-<?= htmlspecialchars($alert_msg['type']) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($alert_msg['text']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th style="font-family: 'Arial', sans-serif">Sản Phẩm</th>
                        <th style="font-family: 'Arial', sans-serif" class="d-none d-sm-table-cell">Người Đánh Giá</th>
                        <th style="font-family: 'Arial', sans-serif">Điểm</th>
                        <th style="font-family: 'Arial', sans-serif" class="d-none d-md-table-cell">Bình Luận</th>
                        <th style="font-family: 'Arial', sans-serif" class="d-none d-lg-table-cell">Ngày Tạo</th>
                        <th style="font-family: 'Arial', sans-serif">HĐ</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if ($reviews && $reviews->num_rows > 0): 
                ?>
                    <?php while($row = $reviews->fetch_assoc()): 
                        $full_comment = htmlspecialchars($row['binhluan'] ?? '');
                    ?> 
                    <tr>
                        <td><?= htmlspecialchars($row['tensanpham'] ?? 'Sản phẩm đã xóa') ?></td>
                        <td class="d-none d-sm-table-cell"><?= htmlspecialchars($row['hoten'] ?? 'User đã xóa') ?></td>
                        <td><span class="badge bg-primary"><?= htmlspecialchars($row['danhgia']) ?>/5</span></td>
                        
                        <td class="d-none d-md-table-cell text-truncate" style="max-width: 250px;">
                            <?php
                                // Cắt chuỗi Bình luận và hiển thị ...
                                echo (mb_strlen($full_comment) > $MAX_COMMENT_LENGTH) ? 
                                    mb_substr($full_comment, 0, $MAX_COMMENT_LENGTH) . '...' : $full_comment;
                            ?>
                        </td>
                        
                        <td class="d-none d-lg-table-cell"><?= htmlspecialchars($row['ngaytao']) ?></td>
                        
                        <td>
                            <?php $base_link_params = "?page=danhgia" . $product_query_string . $current_page_query; ?>
                            
                            <div class="d-flex flex-column flex-md-row justify-content-start">
                                <a href="#" class="btn btn-primary btn-sm view-link mb-1 mb-md-0 me-md-1" title="Xem chi tiết bình luận"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewModal"
                                    data-product="<?= htmlspecialchars($row['tensanpham'] ?? 'N/A') ?>"
                                    data-user="<?= htmlspecialchars($row['hoten'] ?? 'N/A') ?>"
                                    data-rating="<?= htmlspecialchars($row['danhgia']) ?>"
                                    data-comment="<?= $full_comment // Truyền toàn bộ bình luận vào đây ?>"
                                >
                                    <i class="fas fa-eye"></i> <span class="d-none d-md-inline">Xem</span>
                                </a>
                                
                                <a href="#" class="btn btn-danger btn-sm delete-link" title="Xóa đánh giá"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#confirmModal"
                                    data-url="<?= $base_link_params . "&action=delete&id=" . $row['review_id'] ?>">
                                    <i class="fas fa-times"></i> <span class="d-none d-md-inline">Xóa</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Không có đánh giá nào được tìm thấy.</td>
                </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div> </div>
</div>

<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination">
            <?php 
            $base_pagination_link = "?page=danhgia" . $product_query_string;
            
            if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $base_pagination_link ?>&p=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php
            for ($i = 1; $i <= $total_pages; $i++):
                $active_class = ($i == $current_page) ? 'active' : '';
            ?>
                <li class="page-item <?= $active_class ?> d-none d-sm-block <?= $i == $current_page || abs($i - $current_page) <= 1 ? 'd-block' : '' ?>">
                    <a class="page-link" href="<?= $base_pagination_link ?>&p=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php if ($i == $current_page && $total_pages > 2 && $i > 1 && $i < $total_pages): ?>
                    <li class="page-item active d-sm-none">
                        <a class="page-link" href="<?= $base_pagination_link ?>&p=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endif; ?>
            <?php endfor; ?>

            <?php
            if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $base_pagination_link ?>&p=<?= $current_page + 1 ?>" aria-label="Next">
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
                <h5 class="modal-title" id="confirmModalLabel">Xác nhận Xóa Đánh giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Bạn có chắc chắn muốn xóa đánh giá này không? Hành động này không thể hoàn tác.</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmYes">Xóa</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Chi Tiết Đánh Giá</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Sản phẩm:</strong> <span id="modalProduct"></span></p>
                <p><strong>Người đánh giá:</strong> <span id="modalUser"></span></p>
                <p><strong>Điểm đánh giá:</strong> <span id="modalRating"></span></p>
                <hr>
                <p><strong>Nội dung Bình luận:</strong></p>
                <div id="modalComment" class="p-3 border rounded bg-light" style="white-space: pre-wrap;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Logic cho Modal XÓA ---
        const deleteLinks = document.querySelectorAll('.delete-link');
        const confirmYesButton = document.getElementById('confirmYes');
        let deleteUrl = '';

        deleteLinks.forEach(link => {
            link.addEventListener('click', function() {
                deleteUrl = this.getAttribute('data-url');
                // Gán hành động xóa vào nút xác nhận
                confirmYesButton.onclick = function() {
                    window.location.href = deleteUrl;
                };
            });
        });

        // --- Logic cho Modal XEM CHI TIẾT ---
        const viewLinks = document.querySelectorAll('.view-link');
        const modalProduct = document.getElementById('modalProduct');
        const modalUser = document.getElementById('modalUser');
        const modalRating = document.getElementById('modalRating');
        const modalComment = document.getElementById('modalComment');

        viewLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Lấy dữ liệu từ data-* attributes
                const product = this.getAttribute('data-product');
                const user = this.getAttribute('data-user');
                const rating = this.getAttribute('data-rating');
                const comment = this.getAttribute('data-comment');

                // Hiển thị dữ liệu lên Modal
                modalProduct.textContent = product;
                modalUser.textContent = user;
                modalRating.innerHTML = `<span class="badge bg-primary">${rating}/5</span>`;
                modalComment.textContent = comment;
            });
        });
    });
</script>