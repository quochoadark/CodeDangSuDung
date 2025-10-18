<?php
// File: Admin/Controller/ReviewController.php

// Kiểm tra Session (nếu đây là Admin Controller, bạn nên có Auth Check ở file khác)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/Testimonial.php';
require_once __DIR__ . '/../../Database/Database.php'; 

class TestimonialController
{
    private $reviewModel;
    private $conn;
    private $limit = 8; // Số bản ghi mỗi trang

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->conn;

        if ($this->conn->connect_error) {
            error_log("Lỗi kết nối CSDL: " . $this->conn->connect_error);
            // Có thể chuyển hướng đến trang lỗi
        }

        $this->reviewModel = new Testimonial($this->conn);
    }

    /**
     * Xử lý request chung: Lấy dữ liệu hoặc thực hiện hành động (Xóa)
     */
    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'index';
        $p = (int)($_GET['p'] ?? 1); // Trang hiện tại
        $product_id = (int)($_GET['product_id'] ?? 0); // ID Sản phẩm để lọc

        // Xử lý các hành động POST/GET cần chuyển hướng
        switch ($action) {
            case 'delete':
                $this->deleteReview($_GET['id'] ?? null, $p, $product_id);
                break;
            // Nếu có action 'edit' hoặc 'update' thì thêm vào đây
        }

        // Mặc định là action 'index' (Hiển thị danh sách)
        return $this->index($p, $product_id);
    }
    private function index($current_page, $product_id_filter)
    {
        $offset = ($current_page - 1) * $this->limit;

        // 1. Lấy tổng số bản ghi
        $total_records = $this->reviewModel->getTotalRecords($product_id_filter);
        $total_pages = ceil($total_records / $this->limit);
        
        // Đảm bảo trang hiện tại hợp lệ
        if ($current_page < 1) $current_page = 1;
        if ($current_page > $total_pages && $total_pages > 0) $current_page = $total_pages;
        if ($total_records == 0) $current_page = 1;


        // 2. Lấy dữ liệu đánh giá
        $reviews = $this->reviewModel->readAll($this->limit, $offset, $product_id_filter);
        
        // 3. Lấy danh sách sản phẩm (để tạo bộ lọc)
        $product_list_result = $this->reviewModel->getAllProducts();
        $product_list = [];
        if ($product_list_result) {
            while ($row = $product_list_result->fetch_assoc()) {
                $product_list[] = $row;
            }
        }

        // Trả về dữ liệu cho View
        return [
            'reviews' => $reviews,
            'total_pages' => $total_pages,
            'current_page' => $current_page,
            'product_list' => $product_list,
            'current_product_id' => $product_id_filter,
            // Thêm các biến khác nếu cần
        ];
    }
    
    /**
     * Xử lý xóa đánh giá
     */
    private function deleteReview($review_id, $current_page, $product_id)
    {
        if (!$review_id) {
            // Chuyển hướng kèm thông báo lỗi
            $_SESSION['review_msg'] = ['type' => 'danger', 'text' => 'Lỗi: ID đánh giá không hợp lệ.'];
        } else {
            if ($this->reviewModel->delete($review_id)) {
                $_SESSION['review_msg'] = ['type' => 'success', 'text' => 'Xóa đánh giá thành công!'];
            } else {
                $_SESSION['review_msg'] = ['type' => 'danger', 'text' => 'Lỗi: Không thể xóa đánh giá này.'];
            }
        }
        
        // Chuyển hướng về trang danh sách (giữ nguyên trang và bộ lọc)
        $location = 'index.php?page=danhgia';
        if ($product_id > 0) $location .= '&product_id=' . $product_id;
        if ($current_page > 1) $location .= '&p=' . $current_page;
        
        header("Location: {$location}");
        exit();
    }
}
?>