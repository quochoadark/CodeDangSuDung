<?php
// Tên tệp: User/Service/ShopDetailService.php
require_once '../Repository/ShopDetailRepository.php';

class ShopDetailService {
    private $productRepo;

    public function __construct(ShopDetailRepository $productRepo) {
        $this->productRepo = $productRepo;
    }


    private function calculateDiscount(array $product, $promotion) {
        // ... (Logic tính toán khuyến mãi giữ nguyên)
        $product['original_price'] = floatval($product['gia']);
        $product['promotion_discount'] = 0; 
        $product['promotion_percent'] = 0; 
        $product['display_discount'] = null;
        $product['promotion_description'] = null;

        if ($promotion) {
            $original_price = $product['original_price'];
            $discount_raw = floatval($promotion['giam']);
            $discount_value = 0; 
            $percent_off = 0;

            if ($discount_raw < 1 && $discount_raw > 0) {
                // Giảm giá theo TỶ LỆ PHẦN TRĂM
                $discount_value = $original_price * $discount_raw;
                $percent_off = $discount_raw;
                $product['display_discount'] = round($percent_off * 100) . "%";
            } else {
                // Giảm giá theo GIÁ TRỊ TIỀN MẶT
                $discount_value = $discount_raw;
                $percent_off = $original_price > 0 ? ($discount_value / $original_price) : 0;
                $product['display_discount'] = number_format($discount_value, 0, ',', '.') . ' VNĐ';
            }
            
            if ($discount_value > $original_price) {
                $discount_value = $original_price;
                $percent_off = 1;
            }
            
            $new_price = $original_price - $discount_value;

            $product['gia'] = max(0, $new_price);
            $product['promotion_discount'] = $discount_value;
            $product['promotion_percent'] = $percent_off;
            $product['promotion_description'] = $promotion['mota'];
        }

        return $product;
    }

    private function calculateAverageRating(array $reviews) {
        $review_count = count($reviews);
        if ($review_count == 0) {
            return 0;
        }
        
        $total_rating = array_sum(array_column($reviews, 'danhgia'));
        return round($total_rating / $review_count, 1);
    }


    public function getProductDetailWithAllInfo($product_id) {
        $product = $this->productRepo->findProductDetail($product_id); 

        if (!$product) {
            return null;
        }

        // 1. Áp dụng Khuyến mãi
        $promotion = $this->productRepo->findActivePromotionByProductId($product_id);
        $product = $this->calculateDiscount($product, $promotion);
        
        // 2. Lấy Đánh giá
        $reviews = $this->productRepo->findReviewsByProductId($product_id);
        $average_rating = $this->calculateAverageRating($reviews);

        // 3. Đóng gói kết quả
        $product['reviews'] = $reviews;
        $product['review_count'] = count($reviews);
        $product['average_rating'] = $average_rating;

        // 🔥 BỔ SUNG: Thêm 'tonkho' vào mảng trả về cho Controller sử dụng
        $product['tonkho'] = isset($product['tonkho']) ? (int)$product['tonkho'] : 0;


        return $product;
    }
}
?>