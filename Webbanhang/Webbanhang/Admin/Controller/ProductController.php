<?php
// File: Admin/Controllers/ProductController.php

// Đường dẫn này phải đúng, nếu không sẽ bị lỗi Not Found Class
require_once __DIR__ . '/../../Database/Database.php'; 
require_once __DIR__ . '/../model/Product.php'; 

class ProductController {
    private $db;
    private $productModel;
    private $uploadDir; 

    public function __construct() {
    // Tạo kết nối Database (đã định nghĩa trong Database.php)
    $Database = new Database();
    $this->db = $Database->conn;

    // Khởi tạo Product Model
    $this->productModel = new Product($this->db); 
    
    // Đường dẫn thư mục uploads
    $uploadPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    $this->uploadDir = $uploadPath;

    if (!is_dir($this->uploadDir)) { 
        mkdir($this->uploadDir, 0777, true); 
    }
} 

    // --- Hỗ trợ: Lấy danh sách danh mục ---
    private function getCategories() {
        $danhmuc_result = $this->db->query("SELECT category_id, tendanhmuc FROM danhmucsanpham");
        
        $categories = [];
        if ($danhmuc_result) {
            $categories = $danhmuc_result->fetch_all(MYSQLI_ASSOC);
        }
        return $categories;
    }

    // --- Hỗ trợ: Xử lý Upload và kiểm tra file ---
    private function handleImageUpload($currentImg = null) {
        if (!isset($_FILES['img']) || $_FILES['img']['error'] !== UPLOAD_ERR_OK) {
            return $currentImg;
        }

        $imgName = basename($_FILES["img"]["name"]);
        $targetFile = $this->uploadDir . $imgName;

        if (getimagesize($_FILES["img"]["tmp_name"]) === false) {
            throw new Exception("File không phải ảnh.");
        }
        if ($_FILES["img"]["size"] > 5000000) {
            throw new Exception("Ảnh quá lớn (tối đa 5MB).");
        }

        // Xóa ảnh cũ (chỉ khi có ảnh mới và ảnh cũ tồn tại)
        if ($currentImg && file_exists($this->uploadDir . $currentImg)) {
            unlink($this->uploadDir . $currentImg);
        }
        
        // Upload ảnh mới
        if (move_uploaded_file($_FILES["img"]["tmp_name"], $targetFile)) {
            return $imgName; 
        } else {
            throw new Exception("Lỗi khi upload ảnh.");
        }
    }
    
    // --- 1. Xử lý Thêm mới Sản phẩm (CREATE) ---
    public function create() {
        $data = [
            'categories' => $this->getCategories(),
            'error_message' => null,
            'product_data' => [] 
        ];

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            try {
                $this->productModel->setTenSanPham($_POST["tensanpham"] ?? '');
                $this->productModel->setCategoryId(intval($_POST["category_id"] ?? 0));
                $this->productModel->setGia(floatval($_POST["gia"] ?? 0));
                $this->productModel->setTonKho(intval($_POST["tonkho"] ?? 0));
                $this->productModel->setMoTa($_POST["mota"] ?? '');
                
                $imgPath = $this->handleImageUpload(null);

                if (empty($imgPath)) {
                    throw new Exception("Vui lòng chọn ảnh cho sản phẩm.");
                }

                $this->productModel->setImg($imgPath);
                
                if ($this->productModel->save()) {
                    header("Location: /Webbanhang/Admin/index.php?page=sanpham&add_success=1"); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi lưu dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['product_data'] = $_POST; 
            } 
        }
        
        return $data;
    }
    
    // --- 2. Hiển thị form Sửa và xử lý Sửa (UPDATE) ---
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product_data = null;
        $error_message = null;
        
        if ($id > 0) {
            $product_data = $this->productModel->find($id); 
            if (!$product_data) {
                $error_message = "Không tìm thấy sản phẩm cần chỉnh sửa.";
            }
        } else {
            $error_message = "Thiếu ID sản phẩm.";
        }
        
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
            try {
                $currentImg = $product_data['img'] ?? null; 

                $this->productModel->product_id = $id; 

                $this->productModel->setTenSanPham($_POST["tensanpham"] ?? '');
                $this->productModel->setCategoryId(intval($_POST["category_id"] ?? 0));
                $this->productModel->setGia(floatval($_POST["gia"] ?? 0));
                $this->productModel->setTonKho(intval($_POST["tonkho"] ?? 0));
                $this->productModel->setMoTa($_POST["mota"] ?? '');
                
                $imgPath = $this->handleImageUpload($currentImg);
                $this->productModel->setImg($imgPath);

                $product_data = array_merge($product_data ?? [], $_POST); 
                $product_data['img'] = $imgPath; 

                if ($this->productModel->update()) { 
                    header("Location: /Webbanhang/Admin/index.php?page=sanpham&update_success=1"); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi cập nhật dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $error_message = $e->getMessage();
                $product_data = array_merge($product_data ?? [], $_POST);
            }
        }
        
        return [
            'categories' => $this->getCategories(),
            'product_data' => $product_data,
            'error_message' => $error_message,
        ];
    }
    
    // --- 3. Hiển thị Danh sách (READ/INDEX) ---
    public function index() {
        $limit = 8;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        $total_records = $this->productModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        $products_result = $this->productModel->readAll($limit, $offset); 

        return [
            'products' => $products_result, 
            'total_pages' => $total_pages,
            'current_page' => $page,
            'error_message' => null
        ];
    }
    
    // --- 4. Xử lý Yêu cầu (Handle Request) - ĐÃ SỬA LOGIC XÓA ---
    public function handleRequest() {
        
        // --- A. XỬ LÝ YÊU CẦU XÓA (DELETE) ---
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $product_id = (int)$_GET['id'];
            $delete_success = false;

            // KIỂM TRA LOGIC NGHIỆP VỤ: Sản phẩm đã có trong đơn hàng nào chưa?
            if ($this->productModel->hasRelatedOrders($product_id)) {
                // CÓ đơn hàng -> BẮT BUỘC XÓA MỀM (Soft Delete)
                $delete_success = $this->productModel->delete($product_id); 
                $message = "Sản phẩm đã được ẩn (Soft Delete) do có dữ liệu đơn hàng liên quan.";

            } else {
                // CHƯA CÓ đơn hàng -> XÓA CỨNG (Hard Delete)
                $delete_success = $this->productModel->hardDelete($product_id); 
                $message = "Sản phẩm đã được xóa vĩnh viễn khỏi Database.";
            }

            if ($delete_success) {
                // Chuyển hướng với thông báo chi tiết
                header("Location: /Webbanhang/Admin/index.php?page=sanpham&delete_success=1&msg=" . urlencode($message)); 
                exit(); 
            } else {
                 $redirect_url = "/Webbanhang/Admin/index.php?page=sanpham" . 
                                 "&error_message=" . urlencode("Không thể xóa sản phẩm. Lỗi hệ thống hoặc sản phẩm không tồn tại.");
                 header("Location: " . $redirect_url);
                 exit(); 
            }
        }
        
        // --- B. HIỂN THỊ DANH SÁCH (READ/INDEX) / Các action khác ---
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'create') { return $this->create(); }
            if ($_GET['action'] == 'edit') { return $this->edit(); }
        }
        
        return $this->index(); 
    }
}