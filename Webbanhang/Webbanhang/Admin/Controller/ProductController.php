<?php
// File: Admin/Controllers/ProductController.php

// Đường dẫn này phải đúng, nếu không sẽ bị lỗi Not Found Class
require_once __DIR__ . '/../../Database/database.php'; 
require_once __DIR__ . '/../model/Product.php'; 

class ProductController {
    private $db;
    private $productModel;
    private $uploadDir; // Thêm thuộc tính để lưu thư mục upload

    public function __construct() {
    // Tạo kết nối Database (đã định nghĩa trong database.php)
    $database = new Database();
    $this->db = $database->conn;

    // Khởi tạo Product Model
    $this->productModel = new Product($this->db); 
    
    // Sửa lỗi đường dẫn: 
    // Giả định thư mục 'uploads' nằm ở thư mục gốc của dự án (cao hơn một cấp)
    // dirname(__DIR__) sẽ đi lên một cấp.
    $uploadPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR;
    
    // Gán đường dẫn đã chuẩn hóa
    $this->uploadDir = $uploadPath;

    // Logic kiểm tra và tạo thư mục vẫn ĐÚNG (chỉ tạo nếu chưa tồn tại)
    // Nếu thư mục đã có, nó sẽ bỏ qua bước này.
    if (!is_dir($this->uploadDir)) { 
        mkdir($this->uploadDir, 0777, true); 
    }
} 

    // --- Hỗ trợ: Lấy danh sách danh mục (cho Form Add/Update) ---
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
            // Không có file mới hoặc lỗi upload (chấp nhận nếu đang sửa và không up ảnh mới)
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
                // Gán dữ liệu từ form vào model
                $this->productModel->setTenSanPham($_POST["tensanpham"] ?? '');
                $this->productModel->setCategoryId(intval($_POST["category_id"] ?? 0));
                $this->productModel->setGia(floatval($_POST["gia"] ?? 0));
                $this->productModel->setTonKho(intval($_POST["tonkho"] ?? 0));
                $this->productModel->setMoTa($_POST["mota"] ?? '');
                
                // Kiểm tra và Upload Ảnh (currentImg = null vì là thêm mới)
                $imgPath = $this->handleImageUpload(null);

                if (empty($imgPath)) {
                    throw new Exception("Vui lòng chọn ảnh cho sản phẩm.");
                }

                $this->productModel->setImg($imgPath);
                
                // Gọi Model để lưu vào DB
                if ($this->productModel->save()) {
                    header("Location: /Webbanhang/Admin/index.php?page=sanpham&add_success=1"); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi lưu dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['product_data'] = $_POST; // Giữ lại dữ liệu đã nhập
            }   
        }
        
        return $data;
    }
    
    // --- 2. Hiển thị form Sửa và xử lý Sửa (UPDATE) ---
    public function edit() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $product_data = null;
        $error_message = null;
        
        // --- A. Lấy dữ liệu sản phẩm hiện tại (GET request) ---
        if ($id > 0) {
            $product_data = $this->productModel->find($id); 
            if (!$product_data) {
                $error_message = "Không tìm thấy sản phẩm cần chỉnh sửa.";
            }
        } else {
            $error_message = "Thiếu ID sản phẩm.";
        }
        
        // --- B. Xử lý khi Submit form (POST request) ---
        if ($_SERVER["REQUEST_METHOD"] == "POST" && $id > 0) {
            try {
                // Lấy ảnh cũ trước khi gán dữ liệu POST
                $currentImg = $product_data['img'] ?? null; 

                // Set lại ID cho model (QUAN TRỌNG CHO UPDATE)
                $this->productModel->product_id = $id; 

                // Gán dữ liệu mới từ form
                $this->productModel->setTenSanPham($_POST["tensanpham"] ?? '');
                $this->productModel->setCategoryId(intval($_POST["category_id"] ?? 0));
                $this->productModel->setGia(floatval($_POST["gia"] ?? 0));
                $this->productModel->setTonKho(intval($_POST["tonkho"] ?? 0));
                $this->productModel->setMoTa($_POST["mota"] ?? '');
                
                // Xử lý Upload Ảnh (truyền ảnh cũ để hàm xử lý xóa)
                // Hàm handleImageUpload sẽ trả về tên ảnh mới hoặc ảnh cũ.
                $imgPath = $this->handleImageUpload($currentImg);
                $this->productModel->setImg($imgPath);

                // Gán lại dữ liệu đã POST vào $product_data để hiển thị lại form nếu có lỗi
                $product_data = array_merge($product_data ?? [], $_POST); 
                $product_data['img'] = $imgPath; // Cập nhật tên ảnh trong dữ liệu hiển thị

                // Gọi Model để update
                // LƯU Ý: Đảm bảo đã định nghĩa Product::update()
                if ($this->productModel->update()) { 
                    header("Location: /Webbanhang/Admin/index.php?page=sanpham&update_success=1"); 
                    exit(); 
                } else {
                    throw new Exception("Lỗi khi cập nhật dữ liệu vào Database.");
                }

            } catch (Exception $e) {
                $error_message = $e->getMessage();
                // Giữ lại dữ liệu đã POST để điền vào form
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
        // 1. Cấu hình Phân trang
        $limit = 8;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $offset = ($page - 1) * $limit;

        // 2. Lấy tổng số bản ghi (từ Model)
        $total_records = $this->productModel->getTotalRecords();
        $total_pages = ceil($total_records / $limit);

        // 3. Lấy dữ liệu sản phẩm (từ Model)
        $products_result = $this->productModel->readAll($limit, $offset); 

        // 4. Trả về dữ liệu cho View
        return [
            'products' => $products_result, 
            'total_pages' => $total_pages,
            'current_page' => $page,
            'error_message' => null
        ];
    }
    
    // --- 4. Xử lý Yêu cầu (Handle Request) ---
    public function handleRequest() {
        
        // --- A. XỬ LÝ YÊU CẦU XÓA (DELETE) ---
        if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
            $product_id = (int)$_GET['id'];
            
            // LƯU Ý: Hàm delete() phải được định nghĩa trong Model (Product.php)
            if ($this->productModel->delete($product_id)) {
                // Xóa thành công
                header("Location: /Webbanhang/Admin/index.php?page=sanpham&delete_success=1"); 
                exit(); 
            } else {
                 // Xóa thất bại
                
                // 1. Xử lý URL (Đơn giản hóa)
                $redirect_url = "/Webbanhang/Admin/index.php?page=sanpham" . 
                                "&error_message=" . urlencode("Không thể xóa sản phẩm. Có thể sản phẩm đang có trong đơn hàng.");
                
                // 2. Chuyển hướng
                header("Location: " . $redirect_url);
                exit(); 
            }
        }
        
        // --- B. HIỂN THỊ DANH SÁCH (READ/INDEX) ---
        return $this->index(); 
    }
}
?>