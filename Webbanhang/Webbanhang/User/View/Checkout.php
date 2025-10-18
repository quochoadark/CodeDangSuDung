<?php
// Tên tệp: View/Checkout.php
// FILE NÀY KHÔNG CHẠY TRỰC TIẾP. NÓ ĐƯỢC LOAD BỞI CONTROLLER

// Các biến sau đây được giả định đã được định nghĩa trong CheckoutController.php:
// $cart_items (Mảng chi tiết sản phẩm trong giỏ hàng - bao gồm original_price, item_discount)
// $sub_total (Tổng tiền tạm tính - đã trừ giảm giá SP)
// $shipping_fee (Phí vận chuyển)
// $discount (Số tiền giảm giá Voucher/Toàn đơn)
// $grand_total (Tổng tiền cuối cùng)
// $default_address (Thông tin người dùng mặc định: name, phone, address)
// $error_message (Thông báo lỗi nếu có)
// $voucher_code (Mã voucher đã áp dụng)


// Khắc phục lỗi Undefined Variable nếu Controller không định nghĩa
if (!isset($cart_items)) $cart_items = [];
if (!isset($sub_total)) $sub_total = 0;
if (!isset($shipping_fee)) $shipping_fee = 0;
if (!isset($discount)) $discount = 0;
if (!isset($grand_total)) $grand_total = 0;
if (!isset($default_address)) $default_address = ['name' => '', 'phone' => '', 'address' => ''];
if (!isset($error_message)) $error_message = '';
if (!isset($voucher_code)) $voucher_code = ''; // Biến mới

if (empty($cart_items)) {
    echo '<div class="container py-5"><h4 class="text-center text-danger">Giỏ hàng trống. Vui lòng thêm sản phẩm để thanh toán.</h4></div>';
    include 'footer.php'; 
    exit();
}

// Hàm format tiền tệ (Dùng chung) - Có thể chuyển hàm này vào một file helper
if (!function_exists('formatVND')) {
    function formatVND($number) {
        $num = intval($number);
        return number_format($num, 0, ',', '.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Thanh toán - LaptopShop</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">
    <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        .price-original {
            text-decoration: line-through;
            color: #888;
            font-size: 0.9em;
            display: block;
        }
        .price-discount {
            color: #d9534f; /* Màu đỏ cho giá giảm */
            font-weight: bold;
        }
        /* Style cho trường lỗi */
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .invalid-feedback {
            display: none; /* Mặc định ẩn, sẽ hiện bằng JS */
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }
        .form-group.has-error .invalid-feedback {
            display: block;
        }
    </style>
</head>

<body>

    <div id="spinner"
        class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    
    <?php include 'navbar.php'; ?>
    
    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Thanh toán</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="index.php" style="color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Thanh toán</li>
        </ol>
    </div>
    
    <div class="container-fluid py-5">
        <div class="container py-5">
            <h1 class="mb-4">Chi tiết hóa đơn</h1>

            <div id="alert-container">
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger" role="alert" id="server-error-alert">
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="checkout-form-container">
                
                <div class="row g-5">
                    
                    <div class="col-md-12 col-lg-6 col-xl-7">
                        <h3>Thông tin nhận hàng</h3>
                        
                        <form id="place-order-ajax-form"> 
                            <div class="row">
                                <div class="col-md-12 form-group" id="group-recipient_name">
                                    <div class="form-item w-100">
                                        <label class="form-label my-3">Họ và Tên người nhận <sup>*</sup></label>
                                        <input type="text" class="form-control" name="recipient_name" id="recipient_name" 
                                            value="<?php echo htmlspecialchars($default_address['name'] ?? ''); ?>">
                                        <div class="invalid-feedback">Vui lòng nhập họ và tên người nhận.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-item form-group" id="group-address">
                                <label class="form-label my-3">Địa chỉ nhận hàng <sup>*</sup></label>
                                <input type="text" class="form-control" name="address" id="address" 
                                    value="<?php echo htmlspecialchars($default_address['address'] ?? ''); ?>">
                                <div class="invalid-feedback">Vui lòng nhập địa chỉ nhận hàng.</div>
                            </div>
                            
                            <div class="form-item form-group" id="group-phone_number">
                                <label class="form-label my-3">Điện thoại <sup>*</sup></label>
                                <input type="tel" class="form-control" name="phone_number" id="phone_number" 
                                    value="<?php echo htmlspecialchars($default_address['phone'] ?? ''); ?>">
                                <div class="invalid-feedback">Vui lòng nhập số điện thoại hợp lệ.</div>
                            </div>
                            
                            <hr>
                            
                            <div class="form-item">
                                <label class="form-label my-3">Ghi chú (Tùy chọn)</label>
                            </div>
                            <div class="form-item">
                                <textarea name="note" id="note" class="form-control" rows="5"
                                    placeholder="Ghi chú về đơn hàng, ví dụ: thời gian giao hàng, yêu cầu đặc biệt..."></textarea>
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-md-12 col-lg-6 col-xl-5">
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">SP</th>
                                        <th scope="col">Tên</th>
                                        <th scope="col">Giá/SP</th>
                                        <th scope="col">SL</th>
                                        <th scope="col">Tổng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_item_discount = 0;
                                    foreach ($cart_items as $item): 
                                        $original_price = $item['original_price'] ?? $item['price'];
                                        $item_discount_amount = $item['item_discount'] ?? 0;
                                        $total_item_discount += $item_discount_amount * $item['quantity'];
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <div class="d-flex align-items-center mt-2">
                                                <img src="../../Admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid"
                                                   style="width: 100px; height: 60px; object-fit: cover;" alt="">
                                            </div>
                                        </th>
                                        <td class="py-4"><?php echo htmlspecialchars($item['name']); ?></td>
                                        <td class="py-4">
                                            <?php if ($item_discount_amount > 0): ?>
                                                <span class="price-original"><?php echo formatVND($original_price); ?></span>
                                                <span class="price-discount"><?php echo formatVND($item['price']); ?></span>
                                            <?php else: ?>
                                                <span><?php echo formatVND($item['price']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="py-4"><?php echo $item['quantity']; ?></td>
                                        <td class="py-4 text-primary"><?php echo formatVND($item['total']); ?></td>
                                    </tr>
                                    <?php endforeach; ?>

                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark">Tạm tính (Tổng giá đã giảm SP)</p>
                                        </td>
                                        <td class="py-3 text-primary">
                                            <p class="mb-0 sub-total-display"><?php echo formatVND($sub_total); ?></p>
                                        </td>
                                    </tr>                
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark text-uppercase">Phí Vận chuyển</p>
                                        </td>
                                        <td class="py-3 text-primary">
                                            <p class="mb-0 shipping-fee-display"><?php echo formatVND($shipping_fee); ?></p>
                                        </td>
                                    </tr>
                                    
                                    <?php // Dùng ID để JS dễ dàng ẩn/hiện và cập nhật nếu có thay đổi ?>
                                    <tr id="voucher-discount-row" style="display: <?php echo ($discount > 0) ? 'table-row' : 'none'; ?>;"> 
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-success text-uppercase">Giảm giá Voucher</p>
                                        </td>
                                        <td class="py-3 text-success">
                                            <p class="mb-0 discount-display">- <?php echo formatVND($discount); ?></p>
                                            <p class="mb-0 small text-muted">(<?php echo htmlspecialchars($voucher_code); ?>)</p>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <th scope="row"></th>
                                        <td class="py-3" colspan="3">
                                            <p class="mb-0 text-dark text-uppercase">TỔNG CỘNG</p>
                                        </td>
                                        <td class="py-3 text-success h5">
                                            <p class="mb-0 grand-total-display" style="font-family: 'Arial';"><?php echo formatVND($grand_total); ?></p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="py-4">
                            <h4>Phương thức thanh toán</h4>
                            
                            <div class="row g-4 text-center align-items-center justify-content-center border-bottom py-3">
                                <div class="col-12">
                                    <div class="form-check text-start my-3">
                                        <input type="radio" class="form-check-input bg-primary border-0" id="Delivery-1"
                                            name="payment_method" value="Tiền mặt" checked>
                                        <label class="form-check-label" for="Delivery-1">Thanh toán khi nhận hàng (Tiền mặt)</label>
                                    </div>
                                    <p class="text-start text-dark">Thanh toán bằng tiền mặt khi nhận hàng.</p>
                                </div>
                            </div>
                            
                            </div>

                        <div class="row g-4 text-center align-items-center justify-content-center pt-4">
                            <button type="button" id="place-order-btn"
                                class="btn border-secondary py-3 px-4 text-uppercase w-100 text-primary">
                                Đặt hàng
                            </button>
                            <div id="loading-spinner" class="mt-3" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang xử lý...</span>
                                </div>
                                <p class="text-muted small">Đang xử lý đơn hàng...</p>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top"><i
            class="fa fa-arrow-up"></i></a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../lib/easing/easing.min.js"></script>
    <script src="../lib/waypoints/waypoints.min.js"></script>
    <script src="../lib/lightbox/js/lightbox.min.js"></script>
    <script src="../lib/owlcarousel/owl.carousel.min.js"></script>

    <script src="../js/main.js"></script>
    
    <script>
    $(document).ready(function() {
        
        // Hàm hiển thị thông báo Alert
        function showAlert(message, type = 'danger') {
            $('#alert-container').html(
                '<div class="alert alert-' + type + '" role="alert">' +
                    message +
                '</div>'
            );
        }

        // Hàm xóa trạng thái lỗi (CSS và thông báo)
        function clearErrorState(fieldId) {
            $('#' + fieldId).removeClass('is-invalid');
            $('#group-' + fieldId).find('.invalid-feedback').hide();
        }

        // Hàm thiết lập trạng thái lỗi
        function setErrorState(fieldId, message) {
            $('#' + fieldId).addClass('is-invalid');
            $('#group-' + fieldId).find('.invalid-feedback').text(message).show();
        }

        // Hàm Validate Client-side
        function validateForm() {
            var isValid = true;

            // Xóa hết trạng thái lỗi cũ
            clearErrorState('recipient_name');
            clearErrorState('address');
            clearErrorState('phone_number');
            
            // Regex kiểm tra nếu chuỗi CHỈ chứa số (0-9)
            var onlyNumbersRegex = /^\d+$/; 
            
            // 1. Validate Họ và Tên
            var recipientName = $('#recipient_name').val().trim();
            if (recipientName === '') {
                setErrorState('recipient_name', 'Vui lòng nhập họ và tên người nhận.');
                isValid = false;
            } else if (onlyNumbersRegex.test(recipientName)) { // 🔥 ĐIỀU KIỆN BỔ SUNG 🔥
                setErrorState('recipient_name', 'Họ và tên không được chỉ chứa toàn số.');
                isValid = false;
            }

            // 2. Validate Địa chỉ
            var address = $('#address').val().trim();
            
            if (address === '') {
                setErrorState('address', 'Vui lòng nhập địa chỉ nhận hàng.');
                isValid = false;
            } else if (address.length < 10) { 
                // Kiểm tra độ dài tối thiểu để đảm bảo chi tiết
                setErrorState('address', 'Địa chỉ quá ngắn (tối thiểu 10 ký tự). Vui lòng nhập chi tiết.');
                isValid = false;
            } else if (onlyNumbersRegex.test(address)) {
                // Ngăn chặn chỉ nhập toàn số
                setErrorState('address', 'Địa chỉ phải bao gồm thông tin chi tiết (số nhà, tên đường, khu vực).');
                isValid = false;
            }

            // 3. Validate Điện thoại (Kiểm tra rỗng và định dạng cơ bản)
            var phoneNumber = $('#phone_number').val().trim();
            // Regex cơ bản cho số điện thoại Việt Nam (10 hoặc 11 số, bắt đầu bằng 0)
            var phoneRegex = /^(0|\+84)\d{9,10}$/; 
            
            if (phoneNumber === '') {
                setErrorState('phone_number', 'Vui lòng nhập số điện thoại.');
                isValid = false;
            } else if (!phoneRegex.test(phoneNumber)) {
                setErrorState('phone_number', 'Số điện thoại không hợp lệ. Vui lòng kiểm tra lại.');
                isValid = false;
            }

            return isValid;
        }

        // Bắt sự kiện click nút "Đặt hàng"
        $('#place-order-btn').on('click', function(e) {
            e.preventDefault(); 
            
            // Xóa thông báo Server/AJAX cũ
            $('#alert-container').empty();
            
            // 1. Thực hiện Client-side Validation
            if (!validateForm()) {
                showAlert('Vui lòng điền đầy đủ và chính xác các thông tin bắt buộc.', 'danger');
                // Cuộn lên đầu form để người dùng thấy lỗi
                $('html, body').animate({
                    scrollTop: $('#checkout-form-container').offset().top - 100
                }, 500);
                return;
            }

            // 2. Thu thập dữ liệu
            var formData = {
                action: 'place_order_ajax', // Hành động mới cho Controller xử lý AJAX
                recipient_name: $('#recipient_name').val().trim(),
                address: $('#address').val().trim(),
                phone_number: $('#phone_number').val().trim(),
                note: $('#note').val().trim(),
                payment_method: $('input[name="payment_method"]:checked').val(),
                // Các dữ liệu khác như total, discount đã được lưu trong Session, 
                // Server sẽ tự tính toán lại để tránh gian lận.
            };

            // 3. Hiển thị Loading và vô hiệu hóa nút
            $('#place-order-btn').prop('disabled', true).text('Đang xử lý...');
            $('#loading-spinner').show();

            // 4. Gửi AJAX
            $.ajax({
                url: 'CheckoutController.php', // Đảm bảo đúng đường dẫn tới Controller
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    $('#place-order-btn').prop('disabled', false).text('Đặt hàng');
                    $('#loading-spinner').hide();

                    if (response.success) {
                        // Đặt hàng thành công
                        showAlert(response.message || 'Đặt hàng thành công! Cảm ơn quý khách.', 'success');
                        
                        // Chuyển hướng hoặc hiển thị trang Order Confirmation
                        setTimeout(function() {
                            window.location.href = '../Controller/CheckoutController.php?order_id=' + response.order_id; 
                        }, 2000); // Chuyển sau 2 giây
                        
                    } else {
                        // Đặt hàng thất bại (do lỗi server, tồn kho, hay lỗi logic khác)
                        showAlert(response.message || 'Lỗi: Không thể đặt hàng. Vui lòng thử lại.', 'danger');
                        
                        // Nếu có lỗi tồn kho, có thể cuộn đến đầu để người dùng dễ thấy
                         $('html, body').animate({
                            scrollTop: $('#checkout-form-container').offset().top - 100
                        }, 500);
                    }
                },
                error: function(xhr, status, error) {
                    $('#place-order-btn').prop('disabled', false).text('Đặt hàng');
                    $('#loading-spinner').hide();
                    
                    showAlert('Lỗi kết nối hoặc Server không phản hồi. Vui lòng thử lại sau.', 'danger');
                    console.error("AJAX Error:", status, error);
                }
            });
        });
        
        // Xóa thông báo lỗi khi người dùng bắt đầu nhập lại
        $('#recipient_name, #address, #phone_number').on('input', function() {
            clearErrorState(this.id);
        });
    });
    </script>
</body>

</html>