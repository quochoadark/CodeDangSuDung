<?php
// File: StatisticController.php

// 1. GỌI FILE CLASS DATABASE VÀ MODEL
require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../model/StatisticModel.php';

// 2. KHỞI TẠO KẾT NỐI (Vẫn cần để Controller có thể truy cập DB)
$db_instance = new Database();
$conn = $db_instance->conn;

if (!$conn || $conn->connect_error) {
    // Để lỗi này, nhưng trong thực tế, nên xử lý lỗi một cách duyên dáng hơn
    die("Lỗi: Không thể khởi tạo kết nối Database."); 
}

class StatisticController
{
    private $model;
    private $db_instance;

    public function __construct($conn) // Bỏ $db_instance ở đây để tránh trùng lặp, sẽ đóng kết nối ở điểm vào (View)
    {
        $this->model = new StatisticModel($conn);
        // Lưu conn để đóng nếu cần, nhưng thường được đóng ở điểm vào chính
        $this->conn = $conn; 
    }

    public function index()
    {
        // 🚨 NHẬN THAM SỐ MỚI 🚨
        // Lưu ý: View/Điểm vào mới sẽ phải truyền tham số qua $_GET nếu cần
        $report_type = $_GET['report_type'] ?? 'month';
        $report_value = $_GET['report_value'] ?? date('Y-m'); 

        // 1. Logic Doanh thu và thống kê hàng ngày
        $dailyStats = [];
        $revenue = ['total_revenue' => 0];

        switch ($report_type) {
            case 'week':
                $date_obj = new DateTime($report_value);
                $date_obj->setISODate($date_obj->format('Y'), $date_obj->format('W'), 1);
                $startDate = $date_obj->format('Y-m-d');
                $date_obj->modify('+6 days');
                $endDate = $date_obj->format('Y-m-d');

                $dailyStats = $this->model->getDailyStatisticsByDateRange($startDate, $endDate);
                $revenue = $this->model->getRevenueByDateRange($startDate, $endDate);
                break;

            case 'year':
                $startDate = $report_value . '-01-01';
                $endDate = $report_value . '-12-31';
                
                $dailyStats = $this->model->getDailyStatisticsByDateRange($startDate, $endDate);
                $revenue = $this->model->getRevenueByDateRange($startDate, $endDate);
                
                $this->model->saveRevenueReport($report_value, $revenue['total_revenue'] ?? 0, 'year');
                break;

            case 'month':
            default:
                $dailyStats = $this->model->getDailyStatistics($report_value);
                $revenue = $this->model->getRevenueByPeriod($report_value);
                
                $this->model->saveRevenueReport($report_value, $revenue['total_revenue'] ?? 0, 'month');
                break;
        }

        // 2. Logic Sản phẩm
        $topSelling = $this->model->getTopSellingProducts('DESC', 5);
        $leastSelling = $this->model->getTopSellingProducts('ASC', 5);

        // Tính tổng số ngày có giao dịch
        $total_days_with_transactions = count($dailyStats);

        $data = [
            'report_type'             => $report_type,
            'report_value'            => $report_value,
            'dailyStats'              => $dailyStats,
            'totalRevenue'            => $revenue['total_revenue'] ?? 0,
            'totalDays'               => $total_days_with_transactions,
            'topSelling'              => $topSelling,
            'leastSelling'            => $leastSelling
        ];

        // TRẢ VỀ MẢNG DỮ LIỆU
        return $data; 
    }

    public function getDailyDataJson()
    {
        $month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
        $dailyStats = $this->model->getDailyStatistics($month);

        header('Content-Type: application/json');
        echo json_encode($dailyStats);
        
        // Không đóng kết nối ở đây, để điểm vào chính (index/view) xử lý
    }
}

// Bỏ đoạn routing ở cuối file này theo yêu cầu của bạn.