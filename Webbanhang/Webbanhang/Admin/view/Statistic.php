<?php 
// File: View/Statistic.php

// 🚨 BƯỚC MỚI: VIEW GỌI CONTROLLER 🚨
// 1. GỌI FILE CLASS DATABASE VÀ CONTROLLER
// Bạn cần đảm bảo đường dẫn này là chính xác từ vị trí của file View/Statistic.php
require_once __DIR__ . '/../../Database/Database.php'; 
require_once __DIR__ . '/../controller/StatisticController.php'; 
// Lưu ý: StatisticController đã require StatisticModel, nên không cần require lại.

// 2. KHỞI TẠO KẾT NỐI
$db_instance = new Database();
$conn = $db_instance->conn;

if (!$conn || $conn->connect_error) {
    // Xử lý lỗi kết nối
    die("Lỗi: Không thể tải thống kê. Lỗi kết nối Database.");
}

// 3. KHỞI TẠO CONTROLLER & LẤY DỮ LIỆU
$controller = new StatisticController($conn); // Truyền kết nối vào Controller
$data = $controller->index();

// 4. TRÍCH XUẤT DỮ LIỆU
extract($data);

// Đóng kết nối sau khi đã lấy xong dữ liệu
// $db_instance->closeConnection(); // Nếu bạn không muốn đóng, bạn có thể bỏ qua

// Các biến đã có sẵn sau extract: $report_type, $report_value, $dailyStats, $totalRevenue, $topSelling, $leastSelling, $totalDays

// Chỉnh sửa hiển thị tiêu đề (Giữ nguyên)
$title = "Doanh Thu";
if ($report_type === 'week') {
    $title = "Doanh Thu Tuần " . date('W', strtotime($report_value)) . " (" . date('Y', strtotime($report_value)) . ")";
} elseif ($report_type === 'month') {
    $title = "Doanh Thu Tháng " . date('m/Y', strtotime($report_value));
} elseif ($report_type === 'year') {
    $title = "Doanh Thu Năm " . $report_value;
}

?>

<div class="container mt-5">
    <h1 class="mb-4">Bảng Thống Kê Doanh Thu</h1>

    <div class="row mb-3">
        <div class="col-md-6">
            <form method="GET" action="" id="report-form">
                <?php if (isset($_GET['page'])): ?>
                    <input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
                <?php endif; ?>
                
                <div class="d-flex align-items-end">
                    <div class="me-3">
                        <label for="report_type" class="form-label">Loại báo cáo:</label>
                        <select id="report_type" name="report_type" class="form-select">
                            <option value="month" <?php echo ($report_type === 'month') ? 'selected' : ''; ?>>Tháng</option>
                            <option value="week" <?php echo ($report_type === 'week') ? 'selected' : ''; ?>>Tuần</option>
                            <option value="year" <?php echo ($report_type === 'year') ? 'selected' : ''; ?>>Năm</option>
                        </select>
                    </div>

                    <div class="me-3 flex-grow-1">
                        <label for="report_value" class="form-label">Chọn thời gian:</label>
                        <input type="text" id="report_value" name="report_value" class="form-control" value="<?php echo htmlspecialchars($report_value); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Xem Thống Kê</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card card-stats bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Tổng <?php echo $title; ?></h5>
                    <p class="card-text fs-3"><?php echo number_format($totalRevenue, 0, ',', '.') . ' VNĐ'; ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stats bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Tổng Ngày Có Giao Dịch</h5>
                    <p class="card-text fs-3"><?php echo number_format($totalDays); ?> ngày</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Biểu đồ Doanh thu và Số đơn hàng hàng ngày</div>
        <div class="card-body">
            <div class="chart-container" style="height: 400px;"><canvas id="dailyStatsChart"></canvas></div> 
        </div>
    </div>

    <h2 class="mt-5">Chi tiết Doanh Thu Hàng Ngày</h2>
    <table class="table table-striped table-hover">
        <thead>
            <tr><th>Ngày</th><th>Số Đơn Hàng</th><th>Doanh Thu (VNĐ)</th></tr>
        </thead>
        <tbody>
            <?php if (!empty($dailyStats)): ?>
                <?php foreach ($dailyStats as $stat): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($stat['date'])); ?></td>
                    <td><?php echo number_format($stat['total_orders']); ?></td>
                    <td><?php echo number_format($stat['total_revenue'], 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">Không có dữ liệu đơn hàng trong thời gian này.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2 class="mt-5">Thống Kê Sản Phẩm Bán Chạy / Bán Ế (Top 5)</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">Sản Phẩm Bán Chạy Nhất</div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($topSelling)): ?>
                        <?php foreach ($topSelling as $index => $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><?php echo ($index + 1) . ". " . htmlspecialchars($item['tensanpham']); ?></strong>
                                <span class="badge bg-primary rounded-pill"><?php echo number_format($item['total_sold']); ?> đơn vị</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?><li class="list-group-item text-center">Không có dữ liệu bán chạy.</li><?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-danger text-white">Sản Phẩm Bán Ế Nhất</div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($leastSelling)): ?>
                        <?php foreach ($leastSelling as $index => $item): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><?php echo ($index + 1) . ". " . htmlspecialchars($item['tensanpham']); ?></strong>
                                <span class="badge bg-danger rounded-pill"><?php echo number_format($item['total_sold']); ?> đơn vị</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?><li class="list-group-item text-center">Không có dữ liệu bán ế.</li><?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // --- JS CHO CHỌN THỜI GIAN ---
    document.addEventListener('DOMContentLoaded', function() {
        const reportTypeSelect = document.getElementById('report_type');
        const reportValueInput = document.getElementById('report_value');

        function updateInputType() {
            const type = reportTypeSelect.value;
            const oldValue = reportValueInput.value; 

            if (type === 'month') {
                reportValueInput.type = 'month';
                reportValueInput.value = oldValue.substring(0, 7) || '<?php echo date('Y-m'); ?>';
            } else if (type === 'week') {
                reportValueInput.type = 'date'; 
                reportValueInput.placeholder = 'Chọn một ngày bất kỳ trong tuần';
                reportValueInput.value = oldValue || '<?php echo date('Y-m-d'); ?>';
            } else if (type === 'year') {
                reportValueInput.type = 'number';
                reportValueInput.placeholder = 'Nhập năm (YYYY)';
                reportValueInput.value = oldValue.substring(0, 4) || '<?php echo date('Y'); ?>';
            }
        }

        reportTypeSelect.addEventListener('change', updateInputType);
        
        // Khởi tạo lần đầu
        updateInputType();
    });


    // --- JS CHO BIỂU ĐỒ ---
    const dailyStatsData = <?php echo json_encode($dailyStats); ?>;
    
    const labels = dailyStatsData.map(item => item.date);
    const revenueData = dailyStatsData.map(item => item.total_revenue);
    const orderData = dailyStatsData.map(item => item.total_orders);

    const ctx = document.getElementById('dailyStatsChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar', data: { labels: labels, datasets: [{
            label: 'Doanh Thu (VNĐ)', data: revenueData, backgroundColor: 'rgba(54, 162, 235, 0.5)', yAxisID: 'yRevenue'
        }, {
            label: 'Số Đơn Hàng', data: orderData, type: 'line', fill: false, borderColor: 'rgba(255, 99, 132, 1)', yAxisID: 'yOrders'
        }]},
        options: {
            responsive: true, maintainAspectRatio: false,
            scales: {
                yRevenue: { type: 'linear', position: 'left', title: { display: true, text: 'Doanh Thu' }, 
                            ticks: { callback: function(value) { return value.toLocaleString('vi-VN'); } } },
                yOrders: { type: 'linear', position: 'right', title: { display: true, text: 'Số Đơn Hàng' }, 
                            grid: { drawOnChartArea: false, }, ticks: { beginAtZero: true, precision: 0 } }
            }
        }
    }); 
</script>