<?php
// Tên tệp: User/Repository/ReviewRepository.php

// Nạp Model (đã cập nhật)
require_once __DIR__ . '/../Model/ReviewProductModel.php'; 

class ReviewRepository {
    private mysqli $conn;

    public function __construct(mysqli $db_connection) {
        $this->conn = $db_connection; 
    }

    public function getAllReviewsForTestimonial(): array {
        $reviews = [];
        $sql = "SELECT dg.binhluan, dg.danhgia, dg.ngaytao, nd.hoten, sp.tensanpham
                FROM danhgiasanpham AS dg
                JOIN nguoidung AS nd ON dg.user_id = nd.user_id 
                JOIN sanpham AS sp ON dg.product_id = sp.product_id  
                ORDER BY dg.ngaytao DESC"; 
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            $stmt->close();
        } else {
            error_log("SQL Prepare Error (getAllReviewsForTestimonial): " . $this->conn->error);
        }
        return $reviews;
    }

    public function getReviewsByProductId(int $product_id): array {
        $reviews = [];
        $sql = "SELECT dg.*, nd.hoten, nd.email 
                FROM danhgiasanpham AS dg
                JOIN nguoidung AS nd ON dg.user_id = nd.user_id 
                WHERE dg.product_id = ?
                ORDER BY dg.ngaytao DESC"; 
        
        if ($stmt = $this->conn->prepare($sql)) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row; 
            }
            $stmt->close();
        } else {
            error_log("SQL Prepare Error (getReviewsByProductId): " . $this->conn->error);
        }
        return $reviews;
    }

    public function saveReview(ReviewProductModel $reviewObject): bool {
        $ngaytao = date('Y-m-d H:i:s');
        $sql = "INSERT INTO danhgiasanpham (user_id, product_id, danhgia, binhluan, ngaytao) 
                VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $this->conn->prepare($sql)) {
            $user_id = $reviewObject->getUserId();
            $product_id = $reviewObject->getProductId();
            $rating = $reviewObject->getDanhGia();
            $comment = $reviewObject->getBinhLuan();
            
            // 🔥 DEBUG 2: GHI LOG KIỂU DỮ LIỆU ĐƯỢC TRUYỀN VÀO BIND PARAM
            error_log("REVIEW DEBUG - Repo Input Types: U=" . gettype($user_id) . 
                      ", P=" . gettype($product_id) . 
                      ", R=" . gettype($rating) . 
                      ", C=" . gettype($comment));

            // i: user_id, i: product_id, i: danhgia, s: binhluan, s: ngaytao
            $bind_result = $stmt->bind_param("iiiss", $user_id, $product_id, $rating, $comment, $ngaytao);
            
            // KIỂM TRA LỖI BIND PARAM
            if ($bind_result === false) {
                 error_log("BIND PARAM FAILED - Error: " . $stmt->error);
                 $stmt->close();
                 return false;
            }

            $success = $stmt->execute();
            
            if ($success) {
                // SỬA LỖI: Kiểm tra số hàng bị ảnh hưởng để xác nhận thành công
                $rows_affected = $stmt->affected_rows;
                $stmt->close();
                
                if ($rows_affected === 1) {
                    return true;
                } else {
                    error_log("SQL Execute Success but 0 rows affected (saveReview).");
                    return false; 
                }
            } else {
                // Execute thất bại (ghi log lỗi MySQL chi tiết)
                error_log("SQL Execute FAILED (saveReview) - MySQL error: " . $stmt->error); 
                error_log("SQL Execute FAILED (saveReview) - Input: U=" . $user_id . ", P=" . $product_id . ", R=" . $rating . ", Comment=" . $comment);
                $stmt->close();
                return false;
            }
        } else {
            // Lỗi prepare (sai cú pháp SQL)
            error_log("SQL Prepare Error (saveReview): " . $this->conn->error);
            return false;
        }
    }
}
?>