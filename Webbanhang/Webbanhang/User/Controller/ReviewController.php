<?php
// Tên tệp: User/Controller/ReviewController.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 1. Nạp Database và Service
require_once __DIR__ . '/../../Database/Database.php';
require_once __DIR__ . '/../Repository/ReviewRepository.php'; 
require_once __DIR__ . '/../Service/ReviewService.php'; 

// Khởi tạo các biến cần thiết
$db = null;
$target_redirect = 'ShopController.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_review') {
    
    // 🔥 Lấy và ÉP KIỂU product_id ngay lập tức
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0; 

    // Thiết lập chuyển hướng
    if ($product_id > 0) {
         $target_redirect = 'ShopDetailController.php?product_id=' . $product_id;
    }

    // QUAN TRỌNG: Đảm bảo xóa cả hai thông báo Session cũ
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);

    try {
        // 1. Kiểm tra Đăng nhập
        if (!isset($_SESSION['kh_user_id']) || !is_numeric($_SESSION['kh_user_id']) || $_SESSION['kh_user_id'] <= 0) {
            $_SESSION['error_message'] = 'Vui lòng đăng nhập để gửi nhận xét. 🚫';
            header('Location: ' . $target_redirect); 
            exit;
        }

        // 🔥 Lấy và ÉP KIỂU dữ liệu nghiêm ngặt
        $user_id = (int)$_SESSION['kh_user_id']; 
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
        $comment = $_POST['comment'] ?? '';
        
        // 🔥 DEBUG 1: GHI LOG GIÁ TRỊ VÀ KIỂU DỮ LIỆU ĐƯỢC TRUYỀN TỪ CONTROLLER
        error_log("REVIEW DEBUG - Controller Input: U=" . $user_id . " (" . gettype($user_id) . 
                  "), P=" . $product_id . " (" . gettype($product_id) . 
                  "), R=" . $rating . " (" . gettype($rating) . ")");

        // 2. Kết nối CSDL và Khởi tạo Dependency
        $db = new Database();
        $conn = $db->conn;
        
        if ($conn->connect_error) {
            throw new Exception("Lỗi kết nối CSDL."); 
        }
        
        $reviewRepository = new ReviewRepository($conn);
        $reviewService = new ReviewService($reviewRepository);

        
        // 3. Xử lý nghiệp vụ
        $result = $reviewService->processAndSaveReview($product_id, $user_id, $rating, $comment); 
        
        // 4. Phản hồi và Chuyển hướng
        if ($result === true) {
            $_SESSION['success_message'] = 'Nhận xét của bạn đã được gửi thành công! ✅';
        } else {
            $_SESSION['error_message'] = 'Lỗi: ' . $result; 
        }
        
    } catch (Exception $e) {
        // Lỗi hệ thống/Lỗi kết nối nghiêm trọng
        $_SESSION['error_message'] = 'Lỗi hệ thống khi xử lý: ' . $e->getMessage() . ' 🛠️';
    } finally {
        // 5. Đóng kết nối an toàn
        if ($db !== null && isset($db->conn) && $db->conn instanceof mysqli) {
            $db->conn->close();
        }
        
        // 6. Chuyển hướng cuối cùng
        header('Location: ' . $target_redirect); 
        exit;
    }
}

// Nếu truy cập trực tiếp file này mà không phải POST, chuyển hướng về Shop
header('Location: ShopController.php');
exit;
?>