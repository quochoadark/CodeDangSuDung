<?php
// File: Admin/Model/Testimonial.php
require_once __DIR__ . '/../../Database/Database.php';

class Testimonial
{
    private $conn;
    private $table_name = "danhgiasanpham"; 
    private $user_table = "nguoidung";
    private $product_table = "sanpham";

    // Thuộc tính để lưu trữ dữ liệu của 1 đánh giá khi tìm kiếm
    public $review_id;
    public $user_id;
    public $product_id;
    public $danhgia;
    public $binhluan;
    public $ngaytao;
    
    // Thêm các thuộc tính cho chức năng Đăng nhập nếu cần (nhưng không cần ở đây)

    public function __construct($db) 
    {
        $this->conn = $db;
    }

    // 1. Lấy tất cả đánh giá (kèm phân trang và lọc theo Sản phẩm)
    public function readAll($limit, $offset, $product_id = null) 
    {
        $sql = "
            SELECT 
                r.*, 
                u.hoten, 
                p.tensanpham 
            FROM " . $this->table_name . " r
            LEFT JOIN " . $this->user_table . " u ON r.user_id = u.user_id
            LEFT JOIN " . $this->product_table . " p ON r.product_id = p.product_id
        ";
        
        $where_params = [];
        $param_types = "";
        
        // Thêm điều kiện lọc theo Sản phẩm (nếu product_id > 0)
        if ($product_id !== null && $product_id > 0) {
            $sql .= " WHERE r.product_id = ?";
            $param_types .= "i";
            $where_params[] = $product_id;
        }

        $sql .= " ORDER BY r.ngaytao DESC LIMIT ? OFFSET ?";
        $param_types .= "ii";
        $where_params[] = $limit;
        $where_params[] = $offset;

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed (readAll): (" . $this->conn->errno . ") " . $this->conn->error);
            return null;
        }

        // Gắn tham số (bind_param)
        if (!empty($where_params)) {
            $params = array_merge([$param_types], $where_params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
        }

        $stmt->execute();
        return $stmt->get_result(); 
    }

    // 2. Lấy tổng số bản ghi (Count) - Đã cập nhật để hỗ trợ lọc
    public function getTotalRecords($product_id = null) 
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name . " r";
        $param_types = "";
        $where_params = [];
        
        if ($product_id !== null && $product_id > 0) {
            $sql .= " WHERE r.product_id = ?";
            $param_types .= "i";
            $where_params[] = $product_id;
        }
        
        if (!empty($where_params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return 0;
            
            $params = array_merge([$param_types], $where_params);
            call_user_func_array([$stmt, 'bind_param'], $this->refValues($params));
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->conn->query($sql);
        }
        
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }
    
    // 3. XÓA (DELETE)
    public function delete($review_id) 
    {
        if (empty($review_id)) {
            return false; 
        }

        $sql = "DELETE FROM " . $this->table_name . " WHERE review_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
            error_log("Prepare failed (DELETE): " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $review_id); 
        
        if ($stmt->execute()) {
            // *** ĐIỀU CHỈNH: Chỉ cần execute thành công là trả về TRUE ***
            // Dù affected_rows là 0 (ID không tồn tại) hay > 0 (xóa thành công)
            return true;
        } else {
            error_log("Execute failed (DELETE): " . $stmt->error);
            return false;
        }
    }
    
    // 4. Lấy danh sách sản phẩm để lọc (Giả định bạn có một Model Sản phẩm riêng, nhưng có thể gọi trực tiếp ở đây cho đơn giản)
    public function getAllProducts()
    {
        $sql = "SELECT product_id, tensanpham FROM " . $this->product_table . " ORDER BY tensanpham ASC";
        $result = $this->conn->query($sql);
        return $result;
    }

    // Phương thức hỗ trợ để truyền tham chiếu cho bind_param
    private function refValues($arr) {
        if (strnatcmp(phpversion(),'5.3') >= 0) {
            $refs = array();
            foreach($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }
}
?>