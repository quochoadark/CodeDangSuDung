<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Giỏ hàng - LaptopShop</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Raleway:wght@600;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <link href="../css/bootstrap.min.css" rel="stylesheet"> 
    <link href="../css/style.css" rel="stylesheet">
    
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .text-danger {
            font-weight: bold;
        }
        /* Style cho giá gốc bị gạch ngang */
        .original-price {
            text-decoration: line-through;
            color: #999;
            font-size: 0.9em;
        }
        /* Style cho giá đã giảm (giá bán) */
        .current-price {
            font-weight: bold;
            color: #198754; /* Màu xanh lá cho giá đã giảm */
        }
        /* Style cho cột giảm giá */
        .product-discount {
            color: #dc3545; /* Màu đỏ cho số tiền giảm */
            font-weight: bold;
        }

        /* --- CSS THUẦN CHO MODAL TỰ TẠO (Không dùng class Bootstrap Modal) --- */
        .custom-modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Nền mờ */
            display: none; /* Mặc định ẩn */
            justify-content: center;
            align-items: center;
            z-index: 1050; /* Cao hơn các thành phần khác */
        }

        .custom-modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 500px;
            transform: scale(0.7); /* Thu nhỏ ban đầu */
            transition: transform 0.3s ease-out;
        }
        
        .custom-modal-backdrop.show .custom-modal-content {
            transform: scale(1); /* Kích hoạt hiệu ứng phóng to */
        }

        .custom-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        .custom-modal-title {
            margin: 0;
            font-size: 1.25rem;
        }

        .custom-modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            color: #000;
            opacity: 0.5;
            cursor: pointer;
            padding: 0;
        }

        .custom-modal-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
            margin-top: 15px;
        }

        .custom-modal-footer button {
            margin-left: 10px;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        /* Style cho nút Hủy và Xóa (đã cố định màu) */
        #cancelRemoveBtn {
            background-color: #ffc107; /* Vàng (Tương đương btn-warning) */
            color: #212529;
            border: 1px solid #ffc107;
        }
        #confirmRemoveBtn {
            background-color: #dc3545; /* Đỏ (Tương đương btn-danger) */
            color: white;
            border: 1px solid #dc3545;
        }
        /* --- KẾT THÚC CSS MODAL --- */
        
        /* CSS cho dòng giảm giá */
        /* Giữ nguyên để PHP khởi tạo lần đầu */
        #voucher-discount-row {
            display: <?php echo ($discount > 0) ? 'flex' : 'none'; ?>; 
        }
        /* CSS cho thông báo lỗi */
        .cart-error-message {
            color: #dc3545;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    
    <div id="spinner" class="show w-100 vh-100 bg-white position-fixed translate-middle top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    
    <?php include 'navbar.php'; ?>
    
    <?php 
    /**
     * KHẮC PHỤC LỖI UNDEFINED VARIABLE
     * Đảm bảo các biến sử dụng trong View được khởi tạo để tránh lỗi PHP Warning.
     */
    if (!isset($cart_items)) { $cart_items = []; }
    // Khởi tạo các biến tính toán nếu chưa có
    if (!isset($sub_total)) { $sub_total = 0; }
    if (!isset($shipping_fee)) { $shipping_fee = 0; } 
    if (!isset($discount)) { $discount = 0; }
    if (!isset($grand_total)) { $grand_total = 0; }
    
    // Lấy thông báo từ Controller
    $cart_message = $_SESSION['cart_message'] ?? '';
    unset($_SESSION['cart_message']);
    
    // Lấy thông tin voucher (đảm bảo nó có giá trị hoặc chuỗi rỗng)
    $voucher_message = $voucher_message ?? '';
    $voucher_code = $voucher_code ?? '';
    ?>

    <div class="container-fluid page-header py-5">
        <h1 class="text-center text-white display-6">Giỏ hàng</h1>
        <ol class="breadcrumb justify-content-center mb-0">
            <li class="breadcrumb-item"><a href="index.php" style="color: #7CFC00;">Trang chủ</a></li>
            <li class="breadcrumb-item active text-white">Giỏ hàng</li>
        </ol>
    </div>

    <div class="container-fluid py-5">
        <div class="container py-5">
            <?php if (!empty($cart_message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($cart_message); ?>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Sản phẩm</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Giá</th>
                            <th scope="col">Giảm giá SP</th> 
                            <th scope="col">Số lượng</th>
                            <th scope="col">Tổng cộng</th>
                            <th scope="col">Xóa</th>
                        </tr>
                    </thead>
                    <tbody id="cart-table-body">
                        <?php if (!empty($cart_items)): ?>
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-product-id="<?php echo $item['id']; ?>">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../../Admin/uploads/<?php echo htmlspecialchars($item['image']); ?>" class="img-fluid me-5" style="width: 100px; height: 80px;" alt="">
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4 product-name"><?php echo htmlspecialchars($item['name']); ?></p>
                                    </td>
                                    <td>
                                        <div class="mt-4">
                                            <?php if ($item['item_discount'] > 0): ?>
                                                <p class="mb-0 original-price"><?php echo formatVND($item['original_price']); ?> đ</p>
                                                <p class="mb-0 current-price item-price" data-price="<?php echo htmlspecialchars($item['price']); ?>"><?php echo formatVND($item['price']); ?> đ</p>
                                            <?php else: ?>
                                                <p class="mb-0 item-price" data-price="<?php echo htmlspecialchars($item['price']); ?>"><?php echo formatVND($item['price']); ?> đ</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="mt-4">
                                            <?php if ($item['item_discount'] > 0): ?>
                                                <p class="mb-0 product-discount">- <?php echo formatVND($item['item_discount']); ?> đ</p>
                                                <p class="mb-0 small text-muted">(<?php echo htmlspecialchars($item['promo_description']); ?>)</p>
                                            <?php else: ?>
                                                <p class="mb-0">-</p>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group quantity mt-4" style="width: 100px;">
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-minus rounded-circle bg-light border" data-id="<?php echo $item['id']; ?>">
                                                    <i class="fa fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="text" class="form-control form-control-sm text-center border-0 quantity-input" 
                                                     value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                                                     data-id="<?php echo $item['id']; ?>" 
                                                     data-tonkho="<?php echo htmlspecialchars($item['tonkho']); ?>"
                                                     readonly>
                                            <div class="input-group-btn">
                                                <button class="btn btn-sm btn-plus rounded-circle bg-light border" data-id="<?php echo $item['id']; ?>">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <?php 
                                            // Kiểm tra và hiển thị cảnh báo tồn kho ban đầu
                                            $tonkho_warning_style = ($item['quantity'] > $item['tonkho']) ? '' : 'style="display: none;"'; 
                                            $tonkho_warning_text = ($item['quantity'] > $item['tonkho']) ? 'Chỉ còn ' . htmlspecialchars($item['tonkho']) . ' SP' : '';
                                        ?>
                                        <p class="text-danger small mt-1 tonkho-warning-<?php echo $item['id']; ?>" <?php echo $tonkho_warning_style; ?>>
                                            <?php echo $tonkho_warning_text; ?>
                                        </p>
                                        <p class="cart-error-message small mt-1" id="error-message-<?php echo $item['id']; ?>"></p>
                                    </td>
                                    <td>
                                        <p class="mb-0 mt-4 text-secondary product-total-display" data-product-id="<?php echo $item['id']; ?>"><?php echo formatVND($item['total']); ?> đ</p>
                                    </td>
                                    <td>
                                        <button class="btn btn-md rounded-circle bg-light border mt-4 btn-remove-item" data-id="<?php echo $item['id']; ?>">
                                            <i class="fa fa-times text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="empty-cart-row">
                                <td colspan="7" class="text-center py-5"> <h4 class="text-muted">Giỏ hàng của bạn đang trống!</h4>
                                    <a href="../Controller/ShopController.php" class="btn btn-primary mt-3">Tiếp tục mua sắm</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($cart_items)): ?>
            <div class="row g-4 justify-content-between d-flex align-items-start" id="cart-summary-section">

                <div class="col-sm-12 col-md-6 col-lg-5">
                    <div class="bg-light rounded p-4 h-100">
                        <h4 class="mb-4">Mã khuyến mãi</h4>
                        <p class="text-success small fw-bold" id="voucher-message-display">
                            <?php 
                                // Nếu có voucher code, hiển thị nó (giả sử voucher_message chứa thông báo thành công)
                                if (!empty($voucher_code)) {
                                    echo htmlspecialchars($voucher_message) . ' (Mã: ' . htmlspecialchars($voucher_code) . ')';
                                } else {
                                    echo htmlspecialchars($voucher_message);
                                }
                            ?>
                        </p>
                        
                        <div class="d-flex">
                            <input type="text" id="voucher-input" 
                                class="form-control p-3 me-3 border-0 rounded-pill" 
                                placeholder="Mã voucher" 
                                value="<?php echo htmlspecialchars($voucher_code); ?>">
                            <button type="button" 
                                         id="apply-voucher-btn" 
                                         class="btn border-secondary rounded-pill px-4 py-2 text-primary">
                                Áp dụng
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-5">
                    <div class="bg-light rounded p-4 h-100">
                        <h4 class="mb-4">Tổng giỏ hàng</h4>

                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Tổng tiền (Tạm tính):</h5>
                            <p class="mb-0 fw-bold sub-total-display" data-value="<?php echo htmlspecialchars($sub_total); ?>">
                                <?php echo formatVND($sub_total); ?> đ
                            </p>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <h5 class="mb-0 me-4">Phí vận chuyển:</h5>
                            <p class="mb-0"><?php echo formatVND($shipping_fee); ?> đ</p>
                        </div>

                        <div class="d-flex justify-content-between mb-4 text-success voucher-discount-row" id="voucher-discount-row">
                            <h5 class="mb-0 me-4">
                                Giảm giá <span id="voucher-code-display">
                                    <?php echo (!empty($voucher_code) ? '('.htmlspecialchars($voucher_code).')' : ''); ?>
                                </span>:
                            </h5>
                            <p class="mb-0 fw-bold discount-display" data-value="<?php echo htmlspecialchars($discount); ?>">- <?php echo formatVND($discount); ?> đ</p>
                        </div>

                        <div class="py-4 mb-4 border-top border-bottom d-flex justify-content-between">
                            <h5 class="mb-0 me-4">TỔNG CỘNG:</h5>
                            <p class="mb-0 fw-bold grand-total-display" data-value="<?php echo htmlspecialchars($grand_total); ?>">
                                <?php echo formatVND($grand_total); ?> đ
                            </p>
                        </div>

                        <form action="CheckoutController.php" method="GET">
                            <button type="submit" 
                                class="btn border-secondary rounded-pill px-4 py-3 text-primary text-uppercase mb-4 w-100">
                                Tiến hành thanh toán
                            </button>
                        </form>
                    </div>
                </div>

            </div>
            <?php endif; ?>
        </div>
    </div>
    <div id="removeItemModal" class="custom-modal-backdrop">
        <div class="custom-modal-content">
            <div class="custom-modal-header">
                <h5 class="custom-modal-title" id="removeItemModalLabel">Xác nhận Xóa Sản phẩm</h5>
                <button type="button" class="custom-modal-close" id="closeRemoveModal" aria-label="Close">&times;</button>
            </div>
            <div class="custom-modal-body">
                Bạn có chắc chắn muốn xóa sản phẩm "<strong id="product-name-to-remove"></strong>" khỏi giỏ hàng không?
            </div>
            <div class="custom-modal-footer">
                <button type="button" id="cancelRemoveBtn">Hủy</button>
                <button type="button" id="confirmRemoveBtn">Xóa</button>
            </div>
        </div>
    </div>

    <a href="#" class="btn btn-primary border-3 border-primary rounded-circle back-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            
            // Biến toàn cục để lưu trữ ID và Tên sản phẩm cần xóa
            var productIdToRemove = null;
            var productNameToRemove = '';
            
            // --- HÀM XỬ LÝ MODAL THUẦN DÙNG JQUERY ---
            function showCustomModal() {
                $('#removeItemModal').css('display', 'flex'); 
                setTimeout(function() {
                    $('#removeItemModal').addClass('show'); 
                }, 10);
            }

            function hideCustomModal() {
                $('#removeItemModal').removeClass('show'); 
                setTimeout(function() {
                    $('#removeItemModal').css('display', 'none'); 
                }, 300);
            }
            // ------------------------------------------

            // Hàm CẬP NHẬT TỔNG GIỎ HÀNG sau khi AJAX thành công
            function updateCartSummary(response) {
                // Cập nhật giá trị
                $('.sub-total-display').text(response.sub_total_text);
                $('.grand-total-display').text(response.grand_total_text);
                $('.discount-display').text(response.discount_text);
                
                // Cập nhật ẩn/hiện dòng giảm giá
                if (response.discount_value > 0) {
                    $('#voucher-discount-row').css('display', 'flex');
                    // Cập nhật mã voucher hiển thị
                    if (response.voucher_code !== undefined && response.voucher_code !== '') {
                        // SỬA JS: Cập nhật thẻ SPAN có ID voucher-code-display
                        $('#voucher-code-display').text(' (' + response.voucher_code + ')'); 
                    } else {
                        $('#voucher-code-display').text('');
                    }
                } else {
                    $('#voucher-discount-row').css('display', 'none');
                    $('#voucher-code-display').text('');
                }
                
                // Nếu là kết quả từ voucher, cập nhật input (vì input có thể bị xóa nếu voucher không hợp lệ)
                if (response.voucher_code !== undefined) {
                    $('#voucher-input').val(response.voucher_code);
                }
            }
            
            // Hàm xử lý trường hợp giỏ hàng trống
            function handleEmptyCart(response) {
                if (response.is_cart_empty) {
                    var emptyHtml = '<tr id="empty-cart-row"><td colspan="7" class="text-center py-5"><h4 class="text-muted">Giỏ hàng của bạn đang trống!</h4><a href="../Controller/ShopController.php" class="btn btn-primary mt-3">Tiếp tục mua sắm</a></td></tr>';
                    $('#cart-table-body').empty().append(emptyHtml); // Xóa hết và thêm thông báo trống
                    $('#cart-summary-section').remove(); 
                    // Kiểm tra và xóa cả phần tử cha nếu cần, tùy thuộc vào cấu trúc của bạn
                    // Ví dụ: $('div.container-fluid.py-5 > div.container.py-5 > div.row.g-4').remove();
                }
            }

            // Hàm AJAX chung để cập nhật/xóa giỏ hàng
            function updateCart(action, productId, newValue = 0) {
                var row = $('tr[data-product-id="' + productId + '"]');
                var input = row.find('.quantity-input');
                var totalDisplay = row.find('.product-total-display');
                // Element hiển thị thông báo lỗi/thành công cho dòng sản phẩm
                var errorDisplay = $('#error-message-' + productId);
                var tonkhoWarning = $('.tonkho-warning-' + productId); // Cảnh báo tồn kho gốc (nếu có)
                
                // Ẩn thông báo cũ
                errorDisplay.text('').removeClass('text-success text-danger');

                // Hiển thị trạng thái xử lý tạm thời trên nút hoặc nơi khác nếu cần
                // Ví dụ: row.css('opacity', '0.5'); 
                
                // Lưu lại giá trị ban đầu trước khi gửi
                var originalQuantity = parseInt(input.val());

                $.ajax({
                    url: 'CartController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: action,
                        product_id: productId,
                        new_quantity: newValue,
                    },
                    success: function(response) {
                        
                        // 1. Cập nhật giao diện TỔNG GIỎ HÀNG
                        updateCartSummary(response);
                        
                        // XỬ LÝ KẾT QUẢ CẬP NHẬT SỐ LƯỢNG (ajax_update_quantity)
                        if (action === 'ajax_update_quantity') {
                            
                            // 1. Cập nhật lại số lượng thực tế từ server (có thể bị giới hạn/thay đổi)
                            var actualQuantity = response.new_quantity;
                            input.val(actualQuantity);
                            
                            // 2. Xử lý trường hợp sản phẩm bị xóa (action='removed') hoặc lỗi nghiêm trọng
                            if (response.action === 'removed' || actualQuantity == 0) {
                                row.remove();
                                alert(response.message || 'Sản phẩm đã bị loại bỏ do lỗi hoặc hết hàng.');
                                handleEmptyCart(response);
                                return; // Dừng xử lý tiếp
                            }
                            
                            // 3. Xử lý thành công/cảnh báo
                            if (response.success) {
                                // Cập nhật tổng tiền dòng sản phẩm
                                totalDisplay.text(response.new_item_total_text);
                                
                                // Hiển thị cảnh báo tồn kho (nếu số lượng đạt giới hạn tồn kho)
                                var tonkho = parseInt(input.data('tonkho'));
                                if (actualQuantity >= tonkho) {
                                    tonkhoWarning.text('Chỉ còn ' + tonkho + ' SP').show();
                                } else {
                                    tonkhoWarning.hide();
                                }
                                
                                // Hiển thị thông báo thành công (có thể là cảnh báo điều chỉnh số lượng)
                                if (response.message) {
                                     errorDisplay.addClass('text-success').text(response.message).show();
                                     // Tự ẩn thông báo thành công sau 2 giây
                                     setTimeout(function() {
                                         errorDisplay.fadeOut(300, function() { 
                                             $(this).text('').show().removeClass('text-success text-danger'); 
                                         });
                                     }, 2000);
                                } else {
                                    errorDisplay.text('').hide(); // Ẩn nếu không có tin nhắn
                                }

                            } else {
                                // 4. Xử lý lỗi (success=false) nhưng sản phẩm vẫn còn
                                errorDisplay.addClass('text-danger').text(response.message || 'Lỗi: Không thể cập nhật.').show();
                                // Không khôi phục lại giá trị input vì đã được Controller gửi lại actualQuantity
                            }
                        } 
                        
                        // XỬ LÝ KẾT QUẢ XÓA SẢN PHẨM (remove_item)
                        else if (action === 'remove_item') {
                            if (response.success) {
                                row.remove();
                                handleEmptyCart(response);
                                errorDisplay.text('');
                            } else {
                                // Lỗi khi xóa (hiển thị alert vì row đã bị xóa khỏi DOM)
                                alert(response.message || 'Không thể xóa sản phẩm.');
                            }
                        }

                    },
                    error: function(xhr, status, error) {
                        // Lỗi kết nối
                        // Khôi phục số lượng
                        if (action === 'ajax_update_quantity') {
                           input.val(originalQuantity); 
                        }
                        errorDisplay.addClass('text-danger').text('Lỗi kết nối server: ' + error).show();
                        // alert('Lỗi kết nối server: ' + error); // Có thể ẩn alert để tránh làm gián đoạn UX
                    }
                });
            }

            // Xử lý nút Tăng/Giảm số lượng 
            $('.btn-plus, .btn-minus').on('click', function() {
                var productId = $(this).data('id');
                var row = $('tr[data-product-id="' + productId + '"]');
                var input = row.find('.quantity-input');
                var currentQuantity = parseInt(input.val());
                var newQuantity;

                if ($(this).hasClass('btn-plus')) {
                    newQuantity = currentQuantity + 1;
                } else {
                    newQuantity = currentQuantity - 1;
                    if (newQuantity < 1) { 
                        // Nếu số lượng muốn giảm xuống 0, thay vì gửi 0 (có thể gây xóa), ta gửi 1 
                        // và để Controller xử lý việc xóa nếu người dùng muốn xóa hẳn (dùng nút Xóa)
                        newQuantity = 1; 
                    }
                }
                
                // Chỉ gửi AJAX nếu số lượng thay đổi
                if (newQuantity !== currentQuantity) {
                    updateCart('ajax_update_quantity', productId, newQuantity);
                }
            });
            
            // Xử lý sự kiện thay đổi số lượng bằng tay (trên input)
            $('.quantity-input').on('change', function() {
                var productId = $(this).closest('tr').data('product-id');
                var newQuantity = parseInt($(this).val());
                var currentQuantity = parseInt($(this).data('original-quantity') || $(this).val());
                var errorDisplay = $('#error-message-' + productId);
                
                // Kiểm tra đầu vào
                if (isNaN(newQuantity) || newQuantity < 1) {
                    errorDisplay.addClass('text-danger').text('Số lượng không hợp lệ (phải >= 1).').show();
                    $(this).val(currentQuantity); // Khôi phục về giá trị hiện tại
                    return;
                }
                
                // Cập nhật
                updateCart('ajax_update_quantity', productId, newQuantity);
            });

            // Xử lý nút Xóa sản phẩm (MỞ CUSTOM MODAL)
            $('.btn-remove-item').on('click', function() {
                productIdToRemove = $(this).data('id');
                var row = $(this).closest('tr');
                productNameToRemove = row.find('.product-name').text().trim();
                
                $('#product-name-to-remove').text(productNameToRemove);
                
                showCustomModal();
            });

            // Xử lý nút "Hủy" và "X" (Đóng Custom Modal)
            $('#cancelRemoveBtn, #closeRemoveModal').on('click', function() {
                hideCustomModal();
            });

            // Xử lý nút "Xóa" trong Custom Modal 
            $('#confirmRemoveBtn').on('click', function() {
                if (productIdToRemove) {
                    hideCustomModal();
                    updateCart('remove_item', productIdToRemove);
                    
                    productIdToRemove = null; 
                    productNameToRemove = '';
                }
            });

            // Đóng Modal khi click ra ngoài nền mờ
            $('#removeItemModal').on('click', function(e) {
                if ($(e.target).is('#removeItemModal')) { 
                    hideCustomModal();
                }
            });
            
            // =======================================================
            // LOGIC AJAX CHO VOUCHER 
            // =======================================================
            $('#apply-voucher-btn').on('click', function() {
                var voucherCode = $('#voucher-input').val().trim();
                var voucherMessageDisplay = $('#voucher-message-display');
                
                voucherMessageDisplay.removeClass('text-success text-danger').text('Đang áp dụng...');

                $.ajax({
                    url: 'CartController.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'apply_voucher_ajax', 
                        voucher_code: voucherCode
                    },
                    success: function(response) {
                        // 1. Cập nhật tổng giỏ hàng (Áp dụng discount và Grand Total)
                        updateCartSummary(response);

                        // 2. Cập nhật thông báo
                        if (response.success) {
                            // Hiển thị mã voucher trong thông báo chính
                            var displayMessage = response.message;
                            if (response.voucher_code) {
                                displayMessage += ' (Mã: ' + response.voucher_code + ')';
                            }
                            voucherMessageDisplay.addClass('text-success').text(displayMessage);
                        } else {
                            voucherMessageDisplay.addClass('text-danger').text(response.message || 'Lỗi không xác định khi áp dụng voucher.');
                        }
                    },
                    error: function() {
                        voucherMessageDisplay.addClass('text-danger').text('Lỗi kết nối server khi áp dụng voucher.');
                    }
                });
            });
            // =======================================================
            
        });
    </script>
    
    <?php include 'footer.php'; ?>
    <script src="../js/main.js"></script>
</body>

</html>