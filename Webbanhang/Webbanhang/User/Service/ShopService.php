<?php

require_once '../Repository/ShopRepository.php';

class ShopService {
    private $productRepository;

    public function __construct(ShopRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    private function applyPromotionToProducts(array $products) {
        foreach ($products as &$product) {
            // Lấy khuyến mãi cho từng sản phẩm
            $promotion = $this->productRepository->findActivePromotionByProductId($product['product_id']);

            // Khởi tạo các giá trị mặc định/gốc
            $product['original_price'] = floatval($product['gia']);
            $product['promotion_discount'] = 0;
            $product['promotion_percent'] = 0;
            $product['display_discount'] = null; // Chuỗi hiển thị mức giảm

            if ($promotion) {
                $original_price = $product['original_price'];
                $discount_raw = floatval($promotion['giam']);
                $discount_value = 0;
                $percent_off = 0;

                // KIỂM TRA LOẠI GIẢM GIÁ
                if ($discount_raw < 1 && $discount_raw > 0) {
                    // 1. Giảm giá theo TỶ LỆ PHẦN TRĂM
                    $discount_value = $original_price * $discount_raw;
                    $percent_off = $discount_raw;
                    $product['display_discount'] = round($percent_off * 100) . "%";
                } else {
                    // 2. Giảm giá theo GIÁ TRỊ TIỀN MẶT
                    $discount_value = $discount_raw;
                    $percent_off = $original_price > 0 ? ($discount_value / $original_price) : 0;
                    $product['display_discount'] = number_format($discount_value, 0, ',', '.') . ' VNĐ';
                }

                $new_price = $original_price - $discount_value;

                // Cập nhật giá bán sau khi giảm
                $product['gia'] = max(0, $new_price);
                $product['promotion_discount'] = $discount_value;
                $product['promotion_percent'] = $percent_off;
            }
        }
        return $products;
    }

    public function getProductsForShop($limit, $offset, $category_id) {
        $products = $this->productRepository->findProductsByCriteria($limit, $offset, $category_id);
        return $this->applyPromotionToProducts($products);
    }

    public function searchProductsAndApplyPromotion($search_query) {
        $products = $this->productRepository->searchProductsByName($search_query);
        return $this->applyPromotionToProducts($products);
    }
    

    public function countProducts($category_id) {
        return $this->productRepository->countTotalProducts($category_id);
    }
    

    public function getCategories() {
        return $this->productRepository->findAllCategoriesWithProductCount();
    }
}