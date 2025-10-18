<?php

require_once '../Model/ProductModel.php';
require_once '../Model/SaleProductModel.php';

class ShopRepository {
    private $conn;

    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    public function findProductsByCriteria($limit, $offset, $category_id = null) {
        $products = [];

        $sql = "SELECT * FROM sanpham";
        $params = [];
        $types = "";

        if ($category_id !== null) {
            $sql .= " WHERE category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }

        $sql .= " ORDER BY product_id DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();
        // Trả về mảng kết hợp để Service có thể dễ dàng xử lý
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    public function findActivePromotionByProductId($product_id) {
        $promo = null;
        $current_date = date('Y-m-d H:i:s');
        
        // SỬ DỤNG TABLE khuyenmai_sanpham đã lưu
        $sql = "SELECT promo_id, giam, mota
                FROM khuyenmai_sanpham 
                WHERE (product_id = ? OR product_id IS NULL)
                AND ngaybatdau <= ? AND ngayketthuc >= ?
                ORDER BY product_id DESC, giam DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) { return null; }

        $stmt->bind_param("iss", $product_id, $current_date, $current_date);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $promo = $result->fetch_assoc();
        }
        $stmt->close();

        return $promo;
    }
    
    public function searchProductsByName($search_query) {
        $sql_search = "SELECT * FROM sanpham WHERE tensanpham LIKE ?";
        $stmt = $this->conn->prepare($sql_search);
        if (!$stmt) return [];

        $search_param = "%" . $search_query . "%";
        $stmt->bind_param("s", $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $products;
    }

    public function countTotalProducts($category_id = null) {
        $sql = "SELECT COUNT(*) AS total FROM sanpham";
        $params = [];
        $types = "";
        $total = 0;

        if ($category_id !== null) {
            $sql .= " WHERE category_id = ?";
            $params[] = $category_id;
            $types .= "i";
        }

        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) return 0;

            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $total_row = $result->fetch_assoc();
            $total = $total_row['total'];
            $stmt->close();
        } else {
            $total_result = $this->conn->query($sql);
            if ($total_result) {
                $total_row = $total_result->fetch_assoc();
                $total = $total_row['total'];
            }
        }

        return $total;
    }


    public function findAllCategoriesWithProductCount() {
        $sql_danhmuc = "SELECT dm.category_id, dm.tendanhmuc, COUNT(sp.product_id) AS total_products 
                         FROM danhmucsanpham AS dm 
                         LEFT JOIN sanpham AS sp ON dm.category_id = sp.category_id 
                         GROUP BY dm.category_id, dm.tendanhmuc 
                         ORDER BY dm.tendanhmuc ASC";

        $result_danhmuc = $this->conn->query($sql_danhmuc);
        if (!$result_danhmuc) return [];

        return $result_danhmuc->fetch_all(MYSQLI_ASSOC);
    }
}