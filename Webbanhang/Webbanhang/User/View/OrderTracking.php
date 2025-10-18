<?php

$status_flow = $status_flow ?? []; 
$order_info = $order_info ?? [];
$current_status_text = $order_info['ten_trangthai'] ?? 'N/A';
$current_status_id = (int)($order_info['trangthai'] ?? 0);


if (!function_exists('getTrackingIcon')) {
    function getTrackingIcon($status_name) {
        switch (mb_strtolower($status_name, 'UTF-8')) {
            case 'chờ xác nhận': return 'fas fa-file-invoice';
            case 'đã xác nhận': return 'fas fa-check-circle';
            case 'đang giao hàng': return 'fas fa-truck';
            case 'đã giao hàng': return 'fas fa-box-open'; // Đã sửa lại icon cho "Đã giao hàng"
            case 'đã hủy': return 'fas fa-times-circle';
            case 'hoàn hàng': return 'fas fa-undo';
            case 'thành công': return 'fas fa-box-open';
            default: return 'fas fa-dot-circle';
        }
    }
}

// Logic tính toán chiều rộng cho thanh màu xanh (đã hoàn thành)
$step_count = count($status_flow);
$current_index = -1; 

foreach ($status_flow as $i => $step) {
    if ($step['status'] === 'current' || $step['status'] === 'done') {
        $current_index = $i;
    }
}

$progress_width_percent = 0;
if ($step_count > 1 && $current_index >= 0) {
    $progress_width_percent = ($current_index) / ($step_count - 1) * 100;
}

// Xử lý các trạng thái kết thúc (ID 5: Đã hủy, ID 6: Hoàn hàng)
$is_canceled = $current_status_id == 5;
$is_returned = $current_status_id == 6;

if ($is_canceled || $is_returned) {
    $progress_width_percent = ($current_index >= 0) ? ($current_index) / ($step_count - 1) * 100 : 0;
}

$line_color = '#4CAF50'; // Mặc định là xanh lá cây
if ($is_canceled) $line_color = '#dc3545'; // Đỏ nếu đã hủy
else if ($is_returned) $line_color = '#ffc107'; // Vàng nếu hoàn hàng
?>

<div class="card mb-4 mt-3">
    <div class="card-header bg-success text-white" style="background-color: <?= $is_canceled ? '#dc3545!important' : ($is_returned ? '#ffc107!important' : '#198754!important') ?>;">
        <h5 class="mb-0"><i class="fas fa-route me-2"></i>Tiến Trình Đơn Hàng</h5>
    </div>
    <div class="card-body">
        
        <div class="d-flex justify-content-end mb-4">
            <span class="badge bg-warning text-dark p-2 fw-bold">Trạng thái hiện tại: <?= htmlspecialchars($current_status_text) ?></span>
        </div>

        <div class="progress-tracker d-flex justify-content-between position-relative py-3">
            
            <div style="position: absolute; top: 40px; left: 5%; right: 5%; height: 3px; background-color: #ddd; z-index: 1;"></div>
            
            <div style="position: absolute; top: 40px; left: 5%; width: calc(<?= $progress_width_percent ?>% - 30px); height: 3px; background-color: <?= $line_color ?>; z-index: 2; transition: width 0.5s ease-in-out;"></div>
            
            <?php foreach ($status_flow as $step): 
                $icon_class = getTrackingIcon($step['name']);
                $color_style = '#ddd;';
                $text_color = '#888;';
                $is_active = $step['status'] === 'done' || $step['status'] === 'current';

                if ($is_active) {
                    $color_style = $line_color; // Sử dụng màu của tiến trình
                    if ($step['status'] === 'current' && !$is_canceled && !$is_returned) {
                         $color_style = '#1a73e8'; // Màu xanh dương nếu đang ở trạng thái bình thường
                    }
                    $text_color = '#333;';
                }
                
                // Hiển thị tên
                $display_name = htmlspecialchars($step['name']);
                
                // ĐÃ BỎ LOGIC ĐỔI TỪ "Đã giao hàng" SANG "Thành công" 
                // -> Tên sẽ hiển thị theo tên gốc trong DB (Đã giao hàng)
                if (mb_strtolower($display_name, 'UTF-8') === 'thành công') {
                    $display_name = 'Đã giao hàng';
                }
            ?>
                <div class="step text-center position-relative" style="flex: 1; z-index: 3;">
                    <div class="step-icon-wrap mx-auto mb-2" 
                         style="width: 50px; height: 50px; line-height: 50px; border-radius: 50%; border: 3px solid <?= $color_style ?>; background-color: <?= $is_active ? $color_style : '#fff' ?>; color: <?= $is_active ? '#fff' : $color_style ?>; font-size: 20px;">
                        <i class="<?= htmlspecialchars($icon_class) ?>"></i>
                    </div>
                    <div class="step-name fw-bold" style="color: <?= $text_color ?>; font-size: 0.9em; position: static; transform: none; min-height: 40px;">
                        <?= $display_name ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</div>