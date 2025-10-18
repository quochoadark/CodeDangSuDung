<?php
// Tên tệp: User/Service/ReviewService.php

// Nạp Repository và Model
require_once __DIR__ . '/../Repository/ReviewRepository.php'; 
require_once __DIR__ . '/../Model/ReviewProductModel.php'; 

class ReviewService {
    private ReviewRepository $reviewRepository;

    public function __construct(ReviewRepository $reviewRepository) {
        $this->reviewRepository = $reviewRepository;
    }
    
    public function getAllReviewsForTestimonial(): array {
        return $this->reviewRepository->getAllReviewsForTestimonial();
    }
    
    public function getReviewsByProductId(int $product_id): array {
        if ($product_id <= 0) {
            return [];
        }
        return $this->reviewRepository->getReviewsByProductId($product_id);
    }

    public function processAndSaveReview(int $product_id, int $user_id, int $rating, string $comment): bool|string {
        
        // 1. Validation Logic
        if ($product_id <= 0) {
            return 'ID sản phẩm không hợp lệ.';
        }
        if ($user_id <= 0) {
            return 'Lỗi phiên người dùng. Vui lòng đăng nhập lại.';
        }
        if ($rating < 1 || $rating > 5) {
            return 'Đánh giá số sao không hợp lệ (1-5).';
        }
        if (empty(trim($comment))) {
            return 'Nội dung nhận xét không được để trống.';
        }
        
        // 2. TẠO ĐỐI TƯỢNG MODEL VÀ SỬ DỤNG SETTER ĐỂ ĐẢM BẢO ĐÚNG THUỘC TÍNH
        $reviewObject = new ReviewProductModel();
        $reviewObject->setUserId($user_id);
        $reviewObject->setProductId($product_id);
        $reviewObject->setDanhGia($rating);
        $reviewObject->setBinhLuan(trim($comment));
        
        // 3. Gọi Repository để lưu vào CSDL
        $success = $this->reviewRepository->saveReview($reviewObject);
        
        if ($success) {
            return true;
        } else {
            // Lỗi CSDL đã được log trong Repository.
            return 'Đã xảy ra lỗi khi lưu nhận xét vào hệ thống (lỗi CSDL).';
        }
    }
}