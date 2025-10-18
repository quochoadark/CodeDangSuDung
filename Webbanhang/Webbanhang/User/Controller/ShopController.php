<?php
// Tên tệp: User/Controller/ShopController.php

// -----------------------------------------------------------
// BẬT HIỂN THỊ LỖI (DEBUG)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------------------------

session_start();

// 1. NẠP Database
require_once '../../Database/Database.php';

// 2. NẠP REPOSITORY VÀ SERVICE
require_once '../Repository/ShopRepository.php';
require_once '../Service/ShopService.php';

// 3. KHỞI TẠO Database, Repository VÀ Service
$db = new Database();
$conn = $db->conn;
$productRepository = new ShopRepository($conn);
$productService = new ShopService($productRepository);


// Lấy tham số đầu vào
$search_query = isset($_GET['search_query']) && !empty(trim($_GET['search_query']))
    ? trim($_GET['search_query']) : null;

$category_id = isset($_GET['category_id']) && $_GET['category_id'] !== ''
    ? intval($_GET['category_id']) : null;

$page = isset($_GET['page']) && intval($_GET['page']) > 0
    ? intval($_GET['page']) : 1;

$limit = 6;
$offset = ($page - 1) * $limit;

$products = [];
$total_pages = 1;

// Xử lý logic tìm kiếm hoặc phân trang/phân loại
if ($search_query) {
    // 1. Tìm kiếm
    $products = $productService->searchProductsAndApplyPromotion($search_query);
    $total_pages = 1; // Không phân trang khi tìm kiếm
} else {
    // 2. Phân loại + Phân trang
    $total_products = $productService->countProducts($category_id);
    $total_pages = ceil($total_products / $limit);

    // Kiểm tra và điều chỉnh trang hợp lệ
    if ($page > $total_pages && $total_products > 0) {
        $page = $total_pages;
        $offset = ($page - 1) * $limit;
    } elseif ($total_products == 0) {
        $page = 1;
        $offset = 0;
    }

    // Lấy sản phẩm có áp dụng khuyến mãi
    $products = $productService->getProductsForShop($limit, $offset, $category_id);
}

// LẤY DANH MỤC (luôn cần cho Sidebar/Menu)
$danhmucsanpham = $productService->getCategories();

// BIẾN PHỤ CHO VIEW
$query_params = $_GET;

require_once '../View/Shop.php';

// ĐÓNG KẾT NỐI
$db->conn->close();
?>