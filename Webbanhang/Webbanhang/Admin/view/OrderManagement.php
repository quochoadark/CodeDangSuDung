<?php 
// File: Admin/crud/Order/Index.php (Danh sách Đơn hàng)

// Đảm bảo đường dẫn đến Controller là chính xác
require_once __DIR__ . '/../Controller/OrderManagementController.php'; 

$controller = new OrderManagementController();
$data = $controller->handleRequest(); 

$orders_result = $data['orders'] ?? null;
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;
$message = $data['message'] ?? null;

// Hàm hỗ trợ hiển thị badge màu theo trạng thái
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'chờ xác nhận': return 'bg-warning text-dark';
        case 'đang chuẩn bị': 
        case 'xác nhận đơn hàng': return 'bg-info';
        case 'đang giao hàng': return 'bg-primary';
        case 'đã giao hàng': 
        case 'hoàn thành': 
        case 'thành công': return 'bg-success';
        case 'đã hủy': return 'bg-danger';
        case 'đã hoàn về': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lý Đơn Hàng</title>
    <link rel="stylesheet" href="../../assets/css/style.css"> 
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        .order-table th { background-color: #f8f9fa; color: #343a40; }
        .order-actions a { margin-right: 5px; }
        .badge { font-weight: 600; padding: 0.5em 0.75em; border-radius: 0.375rem; color: #fff; }
        .pagination .page-link { color: #007bff; }
        .pagination .page-item.active .page-link { background-color: #007bff; border-color: #007bff; color: white; }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Quản Lý Đơn Hàng</h2>

    <?php if ($message == 'success_update'): ?>
        <div class="alert alert-success">✅ Cập nhật trạng thái đơn hàng thành công!</div>
    <?php elseif ($message == 'notfound'): ?>
        <div class="alert alert-danger">❌ Lỗi: Không tìm thấy đơn hàng.</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped order-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Tổng tiền</th>
                <th>Ngày tạo</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($orders_result && $orders_result->num_rows > 0):
                while ($order = $orders_result->fetch_assoc()): 
            ?>
            <tr>
                <td><?= htmlspecialchars($order['order_id']) ?></td>
                <td><?= htmlspecialchars($order['user_id']) ?></td>
                <td><?= number_format($order['tongtien'], 0, ',', '.') ?> VNĐ</td>
                <td><?= htmlspecialchars($order['ngaytao']) ?></td>
                <td>
                    <span class="badge <?= getStatusBadgeClass($order['ten_trangthai']) ?>">
                        <?= htmlspecialchars($order['ten_trangthai']) ?>
                    </span>
                </td>
                <td class="order-actions">
                    <a href="crud/OrderManagement/UpdateStatus.php?id=<?= $order['order_id'] ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6" class="text-center">Không có đơn hàng nào.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($total_pages > 0 && $orders_result && $orders_result->num_rows > 0): ?>
    <nav aria-label="Order Pagination">
        <ul class="pagination justify-content-center">
            
            <?php if ($total_pages > 1): ?>
            <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=donhang&p=<?= $current_page - 1 ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                <a class="page-link" href="?page=donhang&p=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
            
            <?php if ($total_pages > 1): ?>
            <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=donhang&p=<?= $current_page + 1 ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

</body>
</html>