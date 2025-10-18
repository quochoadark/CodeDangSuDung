<?php 
// File: Admin/Controller/CategoryController.php
require_once __DIR__ . '/../../Database/Database.php'; 
require_once __DIR__ . '/../model/Category.php'; 

class CategoryController {
    private $db;
    private $categoryModel;

    public function __construct() {
        $Database = new Database();
        $this->db = $Database->conn;
        $this->categoryModel = new Category($this->db);
    }
    
    // HIỂN THỊ TẤT CẢ (READ/INDEX)
    public function index() {
        $stmt = $this->categoryModel->readAll();
        
        $categories = [];
        if ($stmt) {
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return ['categories' => $categories]; 
    }

    // THÊM MỚI (CREATE)
    public function create() {
        $data = ['error_message' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $tendanhmuc = $_POST['tendanhmuc'] ?? '';
                
                $this->categoryModel->setTenDanhMuc($tendanhmuc);
                
                if ($this->categoryModel->save()) {
                    // Chuyển hướng về trang danh sách sau khi thêm thành công
                    header("Location: /Webbanhang/Admin/index.php?page=danhmuc&add_success=1"); 
                    exit;
                }
            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
            }
        }
        return $data; 
    }
}
?>