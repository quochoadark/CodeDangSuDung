<?php
// Tên tệp: Service/CheckoutService.php

require_once __DIR__ . '/../Repository/CheckoutRepository.php'; 
require_once __DIR__ . '/../Repository/CartRepository.php'; 

// Cần đảm bảo đường dẫn này là chính xác
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/SMTP.php';
require_once __DIR__ . '/../../Admin/PHPMailer-master/PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class CheckoutService
{
    private $orderRepository;
    private $cartRepository;
    
    // Cấu hình mail 
    private $mailConfig = [
        'Host' => 'smtp.gmail.com', 
        'Username' => 'tranquochoan349@gmail.com', 
        'Password' => 'dzzszqovfnwayiqf', 
        'Port' => 587, 
        'SMTPSecure' => PHPMailer::ENCRYPTION_STARTTLS, 
        'SenderName' => 'Website Bán Hàng'
    ];

    public function __construct(CheckoutRepository $orderRepository, CartRepository $cartRepository)
    {
        $this->orderRepository = $orderRepository;
        $this->cartRepository = $cartRepository;
    }

    // -----------------------------------------------------------
    // PHƯƠNG THỨC GỬI EMAIL HÓA ĐƠN VỚI THIẾT KẾ RESPONSIVE
    // -----------------------------------------------------------
    private function sendInvoiceEmail(int $order_id, array $order_details, string $user_email)
    {
        $mail = new PHPMailer(true);
        $recipientName = $order_details['ten_nguoi_nhan'] ?? 'Khách hàng';
        
        try {
            // Cấu hình PHPMailer
            $mail->SMTPDebug = 0; 
            $mail->isSMTP();
            $mail->Host       = $this->mailConfig['Host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->mailConfig['Username'];
            $mail->Password   = $this->mailConfig['Password'];
            $mail->SMTPSecure = $this->mailConfig['SMTPSecure'];
            $mail->Port       = $this->mailConfig['Port'];
            
            $mail->setFrom($this->mailConfig['Username'], $this->mailConfig['SenderName']);
            $mail->addAddress($user_email, $recipientName);
            
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "[Xác nhận Đơn hàng] Đơn hàng #$order_id của bạn đã được đặt thành công";
            
            // --- BẮT ĐẦU THIẾT KẾ HTML (Responsive cho Email) ---
            
            $sub_total = 0;
            $items_html = '';
            
            // Tạo HTML cho các sản phẩm
            foreach ($order_details['items'] as $item) {
                $thanh_tien = $item['thanh_tien'];
                $sub_total += $thanh_tien;
                
                // Thiết kế bảng đơn giản hóa: Tích hợp SL x ĐG vào cột Tên sản phẩm
                $items_html .= "
                    <tr>
                        <td style='padding: 10px; border: 1px solid #ddd; vertical-align: top;'>
                            <strong style='display: block; font-size: 14px;'>".htmlspecialchars($item['ten_san_pham'])."</strong>
                            <span style='font-size: 12px; color: #666;'>SL: {$item['so_luong']} x ".formatVND($item['don_gia_da_giam'])." VND</span>
                        </td>
                        <td style='padding: 10px; border: 1px solid #ddd; text-align: right; font-weight: bold; color: #007bff; vertical-align: top;'>".formatVND($thanh_tien)." VND</td>
                    </tr>
                ";
            }

            $shipping_fee = 50000;
            $discount_voucher = $order_details['giam_gia_voucher'] ?? 0;
            $grand_total = $sub_total + $shipping_fee - $discount_voucher;
            if ($grand_total < 0) $grand_total = 0;
            
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; width: 100%; margin: auto; border: 1px solid #eee; border-radius: 8px; overflow: hidden; background-color: #ffffff;'>
                    
                    <div style='background-color: #007bff; color: #ffffff; padding: 20px 25px; text-align: center;'>
                        <h1 style='margin: 0; font-size: 24px;'>XÁC NHẬN ĐƠN HÀNG #$order_id</h1>
                        <p style='margin-top: 5px; font-size: 14px;'>Cảm ơn bạn đã đặt hàng từ {$this->mailConfig['SenderName']}!</p>
                    </div>
                    
                    <div style='padding: 25px;'>
                        <p style='font-size: 16px; margin-bottom: 20px;'>Xin chào <strong>{$recipientName}</strong>,</p>
                        <p style='font-size: 14px;'>Đơn hàng của bạn đã được tiếp nhận. Chi tiết đơn hàng:</p>

                        <h3 style='border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333;'>Chi tiết Sản phẩm</h3>
                        
                        <!-- BẢNG ĐƯỢC ĐƠN GIẢN HÓA CHO ĐIỆN THOẠI (Chỉ còn 2 cột) -->
                        <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px;'>
                            <thead>
                                <tr style='background-color: #f8f8f8;'>
                                    <th style='padding: 10px; border: 1px solid #ddd; text-align: left; width: 60%;'>Sản phẩm</th>
                                    <th style='padding: 10px; border: 1px solid #ddd; text-align: right; width: 40%;'>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                {$items_html}
                            </tbody>
                        </table>
                        
                        <!-- BẢNG TỔNG KẾT -->
                        <h3 style='border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333;'>Tóm tắt Thanh toán</h3>
                        <table style='width: 100%; font-size: 14px; margin-bottom: 20px;'>
                            <tr>
                                <td style='padding: 5px 0;'>Tạm tính:</td>
                                <td style='padding: 5px 0; text-align: right;'>".formatVND($sub_total)." VND</td>
                            </tr>
                            <tr>
                                <td style='padding: 5px 0;'>Phí vận chuyển:</td>
                                <td style='padding: 5px 0; text-align: right;'>".formatVND($shipping_fee)." VND</td>
                            </tr>
                            <tr>
                                <td style='padding: 5px 0; color: #dc3545;'>Giảm giá Voucher:</td>
                                <td style='padding: 5px 0; text-align: right; color: #dc3545;'>- ".formatVND($discount_voucher)." VND</td>
                            </tr>
                            <tr style='border-top: 2px solid #333;'>
                                <td style='padding: 10px 0; font-weight: bold;'>TỔNG THANH TOÁN:</td>
                                <td style='padding: 10px 0; text-align: right; font-weight: bold; color: #28a745; font-size: 18px;'>".formatVND($grand_total)." VND</td>
                            </tr>
                        </table>

                        <h3 style='border-bottom: 1px solid #eee; padding-bottom: 10px; color: #333;'>Thông tin Giao hàng</h3>
                        <p style='font-size: 14px; margin: 5px 0;'><strong>Người nhận:</strong> ".htmlspecialchars($recipientName)."</p>
                        <p style='font-size: 14px; margin: 5px 0;'><strong>Điện thoại:</strong> ".htmlspecialchars($order_details['sdt_nguoi_nhan'])."</p>
                        <p style='font-size: 14px; margin: 5px 0;'><strong>Địa chỉ:</strong> ".htmlspecialchars($order_details['dia_chi_nhan'])."</p>
                        <p style='font-size: 14px; margin: 5px 0;'><strong>Thanh toán:</strong> ".htmlspecialchars($order_details['phuong_thuc_tt'])."</p>
                    </div>
                    
                    <div style='background-color: #f8f8f8; padding: 15px 25px; text-align: center; border-top: 1px solid #eee;'>
                        <p style='margin: 0; font-size: 12px; color: #777;'>Mọi thắc mắc, vui lòng liên hệ bộ phận hỗ trợ của chúng tôi.</p>
                    </div>
                </div>
            ";
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("LỖI GỬI HÓA ĐƠN EMAIL cho Order ID {$order_id}: Lỗi PHPMailer: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    // -----------------------------------------------------------
    // PHƯƠNG THỨC XỬ LÝ ĐẶT HÀNG 
    // -----------------------------------------------------------
    public function placeOrder(
        $user_id, $cart_items, $grand_total, $voucher_id, $discount, 
        $recipient_name, $phone_number, $address, $payment_method, $note
    ) {
        if (empty($cart_items)) {
            return [
                'success' => false,
                'message' => 'Giỏ hàng trống. Không thể tạo đơn hàng.',
                'order_id' => 0
            ];
        }

        try {
            // 1. Thực thi Transaction trong Repository
            $order_id = $this->orderRepository->executePlaceOrderTransaction(
                $user_id, $grand_total, $voucher_id, $discount, $cart_items, 
                $recipient_name, $phone_number, $address, $payment_method, $note
            );

            // 2. Xóa giỏ hàng (Sau khi Transaction thành công)
            $this->cartRepository->syncCartToDatabase($user_id, []); 
            
            // 3. GỬI EMAIL HÓA ĐƠN
            // Dòng gây lỗi trước đó giờ đã được khắc phục do đã thêm getUserEmailById vào Repository
            $user_email_data = $this->orderRepository->getUserEmailById($user_id); 
            
            if ($user_email_data && !empty($user_email_data['email'])) {
                 $order_details = $this->orderRepository->getOrderDetailsForConfirmation($order_id, $user_id);
                 if ($order_details) {
                     $this->sendInvoiceEmail($order_id, $order_details, $user_email_data['email']);
                 }
            } else {
                 error_log("Không tìm thấy email người dùng để gửi hóa đơn cho Order ID: $order_id");
            }

            return [
                'success' => true,
                'message' => 'Đặt hàng thành công! Hóa đơn xác nhận đã được gửi qua email.',
                'order_id' => $order_id
            ];
        } catch (Exception $e) {
            error_log("Checkout Service Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Lỗi đặt hàng: " . $e->getMessage(),
                'order_id' => 0
            ];
        }
    }
    
    public function getUserProfile($user_id)
    {
        return $this->orderRepository->getUserProfileById($user_id);
    }
    
    public function getOrderDetailsForConfirmation($order_id, $user_id)
    {
        return $this->orderRepository->getOrderDetailsForConfirmation($order_id, $user_id);
    }
} 

// ĐỊNH NGHĨA HÀM FORMAT TIỀN TỆ ĐỂ ĐẢM BẢO CÓ THỂ GỌI ĐƯỢC TRONG sendInvoiceEmail
if (!function_exists('formatVND')) {
    function formatVND($number) {
        $num = intval($number);
        return number_format($num, 0, ',', '.');
    }
}
