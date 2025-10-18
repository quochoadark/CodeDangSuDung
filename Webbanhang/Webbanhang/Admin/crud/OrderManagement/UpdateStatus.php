<?php
// File: Admin/crud/Order/UpdateStatus.php (Phiên bản Hoàn Chỉnh & UX)

// Giả định đường dẫn tới Controller đã đúng
require_once __DIR__ . '/../../Controller/OrderManagementController.php'; 

$controller = new OrderManagementController();
// Phương thức editStatus() xử lý cả GET (lấy data) và POST (cập nhật)
$data = $controller->editStatus(); 

$order_data = $data['order_data'] ?? [];
$status_list = $data['status_list'] ?? []; // Danh sách các trạng thái từ DB
$error_message = $data['error_message'] ?? null;
// Thêm biến để nhận thông báo thành công sau khi redirect (nếu có)
$success_message = $data['success_message'] ?? (isset($_GET['success']) ? 'Cập nhật trạng thái đơn hàng thành công!' : null);

$order_id = $order_data['order_id'] ?? (isset($_GET['id']) ? (int)$_GET['id'] : 0);

// Lấy trạng thái hiện tại (Đảm bảo cột 'trangthai' là ID và 'ten_trangthai' là tên đã được JOIN)
$current_status_id = $order_data['trangthai'] ?? 0;
$current_status_text = $order_data['ten_trangthai'] ?? 'Không xác định';

// Hàm helper để xác định lớp CSS và Icon dựa trên TÊN trạng thái (Server-side rendering)
function getStatusBadgeClassAndIcon($status_name) {
    // Chuyển về chữ thường để đảm bảo khớp chính xác
    switch (mb_strtolower($status_name, 'UTF-8')) {
        case 'chờ xác nhận': 
            return ['class' => 'badge-pending', 'icon' => 'fas fa-hourglass-half'];
        
        case 'đã xác nhận': 
            return ['class' => 'badge-confirmed', 'icon' => 'fas fa-check-circle'];
        
        case 'đang giao hàng': 
            return ['class' => 'badge-shipping', 'icon' => 'fas fa-truck'];
        
        case 'đã giao hàng': 
            return ['class' => 'badge-success', 'icon' => 'fas fa-clipboard-check']; 
        
        case 'đã hủy': 
            return ['class' => 'badge-danger', 'icon' => 'fas fa-times-circle'];
        
        case 'hoàn hàng': 
            return ['class' => 'badge-return', 'icon' => 'fas fa-undo-alt'];      
        default: 
            return ['class' => 'bg-light text-dark', 'icon' => 'fas fa-question-circle'];
    }
}
$badge_info = getStatusBadgeClassAndIcon($current_status_text);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cập Nhật Trạng Thái Đơn Hàng #<?= htmlspecialchars($order_id) ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css"> 
    <style>
        body { 
            background: #f0f2f5; 
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .update-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08); 
            padding: 35px 30px;
            max-width: 480px;
            width: 95%; 
            text-align: center;
        }
        .update-card h2 {
            font-weight: 700; 
            color: #343a40;
            margin-bottom: 10px;
            font-size: 1.8rem;
        }
        .update-card h3 {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 25px;
            font-size: 1.2rem;
        }
        .current-status-group {
            margin-bottom: 30px;
            font-size: 1.1rem;
            color: #495057;
        }
        .current-status-label {
            font-weight: 500;
            display: block; 
            margin-bottom: 8px;
        }
        .badge-status {
            padding: 8px 15px;
            border-radius: 20px; 
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        /* Màu sắc tùy chỉnh theo logic PHP */
        .badge-pending { background-color: #ffc107; color: #343a40; } 
        .badge-confirmed { background-color: #17a2b8; color: white; } 
        .badge-shipping { background-color: #007bff; color: white; } 
        .badge-success { background-color: #28a745; color: white; } 
        .badge-danger { background-color: #dc3545; color: white; } 
        .badge-return { background-color: #ff8c00; color: white; } 
        /* --- */

        .form-group {
            margin-bottom: 25px;
        }
        .form-label {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 10px; 
            display: block;
            text-align: left; 
        }
        .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
            font-size: 1rem;
            width: 100%;
            -webkit-appearance: none; 
            -moz-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        .btn-primary {
            background-color: #28a745; 
            border-color: #28a745;
            border-radius: 8px;
            font-weight: 600;
            padding: 12px 25px;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.2s ease-in-out;
            /* Thêm CSS cho trạng thái tải */
            position: relative;
        }
        .btn-primary:hover:not(:disabled) {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        /* Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
            margin-right: 0.5rem;
            border-width: 0.15em;
        }
        .back-link {
            display: block;
            margin-top: 25px;
            color: #007bff;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
        .alert-danger, .alert-success {
            margin-bottom: 20px;
            text-align: left;
        }
    </style>
    </head>
<body>
    <div class="update-card">
        <h2>Cập Nhật Trạng Thái Đơn Hàng</h2>
        <h3>Mã đơn hàng: #<?= htmlspecialchars($order_id) ?></h3>

        <?php if ($error_message): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>
        
        <div class="current-status-group">
            <span class="current-status-label">Trạng thái hiện tại:</span>
            <span class="badge badge-status <?= htmlspecialchars($badge_info['class']) ?>" data-current-status-id="<?= htmlspecialchars($current_status_id) ?>" id="current-status-badge">
                <i class="<?= htmlspecialchars($badge_info['icon']) ?>"></i> 
                <?= htmlspecialchars($current_status_text) ?>
            </span>
        </div>

        <form method="POST" action="UpdateStatus.php?id=<?= htmlspecialchars($order_id) ?>" id="updateStatusForm">
            
            <div class="form-group">
                <label for="trangthai_id" class="form-label">Chọn Trạng thái Mới:</label>
                <select id="trangthai_id" name="trangthai_id" class="form-select" required>
                    <option value="">-- Chọn trạng thái --</option>
                    <?php 
                    // Duyệt qua danh sách trạng thái từ Database
                    foreach ($status_list as $status): 
                        // Đặt selected cho trạng thái hiện tại
                        $selected = ((int)$status['trangthai_id'] === (int)$current_status_id) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($status['trangthai_id']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($status['ten_trangthai']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-save" id="submitIcon"></i> 
                <span id="submitText">Cập Nhật Trạng Thái</span>
            </button>
        </form>

        <a href="../../index.php?page=donhang" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng đơn hàng
        </a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('updateStatusForm');
            const selectElement = document.getElementById('trangthai_id');
            const submitBtn = document.getElementById('submitBtn');
            const currentStatusId = parseInt('<?= $current_status_id ?>');

            // 1. Logic kiểm tra trạng thái cũ và mới
            form.addEventListener('submit', function(e) {
                const newStatusId = parseInt(selectElement.value);

                // Kiểm tra xem người dùng có chọn trạng thái nào không
                if (!newStatusId) {
                    return; // Nếu chưa chọn thì để HTML required tự xử lý
                }

                // Kiểm tra xem trạng thái mới có trùng với trạng thái cũ không
                if (newStatusId === currentStatusId) {
                    e.preventDefault(); // Ngăn chặn submit form
                    alert('Lỗi: Trạng thái mới phải khác trạng thái hiện tại!');
                    return false;
                }

                // 2. Thêm hiệu ứng loading khi submit thành công
                // Nếu vượt qua kiểm tra, bắt đầu hiệu ứng loading
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Đang xử lý...
                `;
            });
            
            // 3. Khôi phục nút bấm khi người dùng thay đổi lựa chọn (nếu chưa submit)
            selectElement.addEventListener('change', function() {
                if (submitBtn.disabled) {
                    // Nếu đã disable (do submit hoặc kiểm tra fail), re-enable nó
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save" id="submitIcon"></i> <span id="submitText">Cập Nhật Trạng Thái</span>';
                }
            });
        });
    </script>
</body>
</html>