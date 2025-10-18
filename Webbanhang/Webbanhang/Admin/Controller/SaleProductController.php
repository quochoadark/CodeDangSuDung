<?php
// File: Admin/Controller/SaleProductController.php
require_once __DIR__ . '/../Model/SaleProduct.php'; 
require_once __DIR__ . '/../../Database/Database.php'; 

class SaleProductController {
    private $db; 
    private $saleProductModel; 

    public function __construct() {
        $Database = new Database();
        $this->db = $Database->conn;
        $this->saleProductModel = new SaleProduct($this->db); 
    }
    
    private function getProductsData() {
        return $this->saleProductModel->getProducts();
    }

    /**
     * Lấy danh sách khuyến mãi sản phẩm cho trang index
     * @return array
     */
    public function index() {
        $result = SaleProduct::readAllStatic($this->db); 
        $sales = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $sales[] = $row;
            }
            $result->free();
        }
        return ['sales' => $sales];
    }
    
    /**
     * Xử lý thêm mới Khuyến mãi Sản phẩm (GET/POST)
     * @return array
     */
    public function create() {
        $data = [
            'error_message' => null,
            'promo_data' => [],
            'products' => $this->getProductsData()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_data = $_POST;
            $new_promo = new SaleProduct($this->db); 

            try {
                // 1. Gán dữ liệu bằng SETTERS (Validation)
                $new_promo->setProductId($post_data['product_id'] ?? 0);
                $new_promo->setMoTa($post_data['mota'] ?? null);
                $new_promo->setGiam($post_data['giam'] ?? 0);
                $new_promo->setNgayBatDau($post_data['ngaybatdau'] ?? null);
                $new_promo->setNgayKetThuc($post_data['ngayketthuc'] ?? null);
                
                // 2. Xử lý lưu dữ liệu
                if ($new_promo->save()) {
                    header("Location: ../../index.php?page=");
                    exit();
                } else {
                    throw new Exception("Lỗi hệ thống: Không thể thêm khuyến mãi sản phẩm.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['promo_data'] = $post_data;
            }
        }
        return $data;
    }

    /**
     * Xử lý cập nhật Khuyến mãi Sản phẩm (GET/POST)
     * @return array
     */
    public function edit() {
        $promo_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $current_promo = new SaleProduct($this->db); 
        
        $data = [
            'error_message' => null,
            'promo_data' => [],
            'products' => $this->getProductsData()
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_data = $_POST;

            try {
                // 1. Tải dữ liệu cũ và gán ID
                if (!$current_promo->find($promo_id)) {
                    throw new Exception("Không tìm thấy khuyến mãi để cập nhật.");
                }
                
                // 2. Gán dữ liệu mới bằng SETTERS (Validation)
                $current_promo->setProductId($post_data['product_id'] ?? 0);
                $current_promo->setMoTa($post_data['mota'] ?? null);
                $current_promo->setGiam($post_data['giam'] ?? 0);
                $current_promo->setNgayBatDau($post_data['ngaybatdau'] ?? null);
                $current_promo->setNgayKetThuc($post_data['ngayketthuc'] ?? null);
                
                // 3. Xử lý cập nhật
                if ($current_promo->save()) {
                    header("Location: ../../index.php?page=khuyenmai");
                    exit();
                } else {
                    throw new Exception("Lỗi hệ thống: Không thể cập nhật khuyến mãi.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                // Merge dữ liệu POST vào data, kèm theo promo_id
                $data['promo_data'] = array_merge($post_data, ['promo_id' => $promo_id]);
            }

        } else { // GET request: Lấy dữ liệu cũ
            if ($current_promo->find($promo_id)) {
                $data['promo_data'] = [
                    'promo_id' => $current_promo->getId(),
                    'product_id' => $current_promo->getProductId(),
                    'mota' => $current_promo->getMoTa(),
                    'giam' => $current_promo->getGiam(),
                    'ngaybatdau' => $current_promo->getNgayBatDau(),
                    'ngayketthuc' => $current_promo->getNgayKetThuc(),
                ];
            } else {
                $data['error_message'] = "Thiếu ID khuyến mãi sản phẩm hoặc không tìm thấy.";
            }
        }
        return $data;
    }
    
    /**
     * Xử lý xóa Khuyến mãi Sản phẩm
     * @param int $promo_id ID của khuyến mãi cần xóa
     * @return void
     */
    public function delete(int $promo_id) {
        $promo_to_delete = new SaleProduct($this->db);
        $promo_to_delete->promo_id = $promo_id;
        
        if ($promo_to_delete->delete()) {
            header("Location: ../../index.php?page=khuyenmai");
            exit();
        } else {
            // Có thể dùng session để báo lỗi chi tiết hơn
            header("Location: ../../index.php?page=khuyenmai&error=delete_failed");
            exit();
        }
    }
}