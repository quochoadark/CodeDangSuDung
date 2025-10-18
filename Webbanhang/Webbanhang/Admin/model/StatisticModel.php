<?php
// File: models/StatisticModel.php

class StatisticModel { 
    private $conn;
    // SỬA: Thêm trạng thái 1 (Chờ xác nhận) vào danh sách đơn hàng được tính
    private $valid_revenue_statuses = [1, 3, 4]; // 1: Chờ xác nhận, 3: Đang giao, 4: Đã giao/Thành công

    public function __construct($dbConnection) {
        $this->conn = $dbConnection; 
    }

    public function saveRevenueReport($thoi_gian, $doanh_thu_moi, $report_type) {
        // 1. Kiểm tra xem báo cáo đã tồn tại chưa
        $sql_check = "SELECT report_id FROM baocaodoanhthu WHERE thoigian = ? AND report_type = ?";
        $stmt_check = $this->conn->prepare($sql_check);
        if (!$stmt_check) return false;
        
        $stmt_check->bind_param('ss', $thoi_gian, $report_type);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $existing_report = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($existing_report) {
            // 2. Nếu đã tồn tại -> UPDATE
            $sql = "UPDATE baocaodoanhthu SET tongdoanhthu = ?, ngaytao = NOW() WHERE report_id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return false;
            $stmt->bind_param('di', $doanh_thu_moi, $existing_report['report_id']);
        } else {
            // 3. Nếu chưa tồn tại -> INSERT
            $sql = "INSERT INTO baocaodoanhthu (thoigian, tongdoanhthu, ngaytao, report_type) VALUES (?, ?, NOW(), ?)";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return false;
            $stmt->bind_param('sds', $thoi_gian, $doanh_thu_moi, $report_type);
        }

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }
    
    /**
     * Lấy tổng doanh thu theo tháng (YYYY-MM).
     */
    public function getRevenueByPeriod($month) {
        $sql = "SELECT SUM(tongtien) AS total_revenue
                FROM donhang
                WHERE DATE_FORMAT(ngaytao, '%Y-%m') = ?
                AND trangthai IN (1, 3, 4)"; 
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return ['total_revenue' => 0];
        $stmt->bind_param('s', $month); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) { $stmt->close(); return ['total_revenue' => 0]; }
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }
    
    /**
     * Lấy tổng doanh thu theo phạm vi ngày (dùng cho Tuần và Năm).
     */
    public function getRevenueByDateRange($startDate, $endDate) {
        $sql = "SELECT SUM(tongtien) AS total_revenue
                FROM donhang
                WHERE ngaytao BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                AND trangthai IN (1, 3, 4)"; 
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return ['total_revenue' => 0];
        
        // Sử dụng $endDate làm tham số thứ hai cho DATE_ADD
        $stmt->bind_param('ss', $startDate, $endDate); 
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) { $stmt->close(); return ['total_revenue' => 0]; }
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    /**
     * Lấy số lượng đơn hàng và tổng doanh thu theo từng ngày trong phạm vi YYYY-MM.
     */
    public function getDailyStatistics($monthYear) {
        $sql = "SELECT DATE(ngaytao) AS date, COUNT(order_id) AS total_orders, SUM(tongtien) AS total_revenue
                FROM donhang
                WHERE ngaytao LIKE ? AND trangthai IN (1, 3, 4)
                GROUP BY DATE(ngaytao) ORDER BY date ASC";
        $pattern = $monthYear . '-%'; 
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('s', $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) { $stmt->close(); return []; }
        $data = [];
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        $stmt->close();
        return $data;
    }

    /**
     * Lấy số lượng đơn hàng và tổng doanh thu theo từng ngày trong phạm vi ngày tùy ý (dùng cho Tuần và Năm).
     */
    public function getDailyStatisticsByDateRange($startDate, $endDate) {
         $sql = "SELECT DATE(ngaytao) AS date, COUNT(order_id) AS total_orders, SUM(tongtien) AS total_revenue
                FROM donhang
                WHERE ngaytao BETWEEN ? AND DATE_ADD(?, INTERVAL 1 DAY)
                AND trangthai IN (1, 3, 4)
                GROUP BY DATE(ngaytao) ORDER BY date ASC";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];
        
        // Sử dụng $endDate làm tham số thứ hai cho DATE_ADD
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (!$result) { $stmt->close(); return []; }
        $data = [];
        while ($row = $result->fetch_assoc()) { $data[] = $row; }
        $stmt->close();
        return $data;
    }

    /**
     * Lấy sản phẩm bán chạy nhất/bán ế nhất (Top N).
     */
    public function getTopSellingProducts($order = 'DESC', $limit = 5) {
        $sql = "SELECT IFNULL(p.tensanpham, CONCAT('ID: ', ct.product_id, ' (Lỗi tên)')) AS tensanpham, 
                        SUM(ct.soluong) AS total_sold
                FROM chitietdonhang ct
                JOIN donhang dh ON ct.order_id = dh.order_id
                LEFT JOIN sanpham p ON ct.product_id = p.product_id
                WHERE dh.trangthai IN (1, 3, 4)"; // SỬA ĐIỀU KIỆN
        
        $sql .= " GROUP BY ct.product_id, p.tensanpham ORDER BY total_sold " . ($order == 'DESC' ? 'DESC' : 'ASC') . " LIMIT ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return []; 
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if (!$result) { $stmt->close(); return []; }
        
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $stmt->close();
        return $data;
    }
}