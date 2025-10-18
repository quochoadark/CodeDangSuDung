<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------------------------

session_start();

// 1. NẠP Database
require_once '../../Database/Database.php';

// 2. NẠP REPOSITORY VÀ SERVICE (CHỈ CẦN 2 FILE NÀY)
require_once '../Repository/ShopDetailRepository.php';
require_once '../Service/ShopDetailService.php';

// 3. KHỞI TẠO Database, Repository VÀ Service
$db = new Database();
$conn = $db->conn;

// Chỉ khởi tạo một Repository
$productRepo = new ShopDetailRepository($conn); 
$productDetailService = new ShopDetailService($productRepo); 



$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Gọi Service để lấy toàn bộ thông tin chi tiết, khuyến mãi và đánh giá
$product_data = $productDetailService->getProductDetailWithAllInfo($product_id); 

$product = null;
$reviews = [];
$average_rating = 0;
$review_count = 0;
$ton_kho = 0;
$quantity = 1;

if ($product_data) {
    // Phân tách dữ liệu nhận được cho View
    $product = $product_data;
    $reviews = $product_data['reviews'];
    $average_rating = $product_data['average_rating'];
    $review_count = $product_data['review_count'];
    $ton_kho = $product_data['tonkho'];
}


require_once '../View/ShopDetail.php'; 
$db->conn->close();
?>