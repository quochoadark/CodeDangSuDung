<?php
// Tên tệp: User/Repository/ShopDetailRepository.php

class ShopDetailRepository {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }


    public function findProductDetail($product_id) {
        $product = null;
        
        $sql_product_detail = "SELECT sp.*, dm.tendanhmuc 
                             FROM sanpham AS sp 
                             JOIN danhmucsanpham AS dm ON sp.category_id = dm.category_id 
                             WHERE sp.product_id = ?";
        
        if ($stmt = $this->conn->prepare($sql_product_detail)) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();
            }
            $stmt->close();
        } else {
            error_log("SQL Prepare Error (findProductDetail): " . $this->conn->error);
        }
        
        return $product;
    }

    public function findActivePromotionByProductId($product_id) {
        $promo = null;
        
        $sql = "SELECT promo_id, giam, mota
                FROM khuyenmai_sanpham 
                WHERE product_id = ? 
                AND ngaybatdau <= NOW() AND ngayketthuc >= NOW()
                ORDER BY giam DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { 
            error_log("SQL Prepare Error (findActivePromotionByProductId): " . $this->conn->error);
            return null; 
        }

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $promo = $result->fetch_assoc();
        }
        $stmt->close();

        return $promo;
    }
    
    public function findReviewsByProductId($product_id) {
        $reviews = [];
        
        // 🔥 SỬA: bảng từ 'danhgia' -> 'danhgiasanpham' và cột từ 'tennguoidung' -> 'hoten'
        $sql = "SELECT dg.*, nd.hoten 
                FROM danhgiasanpham AS dg 
                JOIN nguoidung AS nd ON dg.user_id = nd.user_id 
                WHERE dg.product_id = ? 
                ORDER BY dg.ngaytao DESC";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { 
            error_log("SQL Prepare Error (findReviewsByProductId): " . $this->conn->error);
            return $reviews; 
        }

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $reviews;
    }
}
?>