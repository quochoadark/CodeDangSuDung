<?php
// File: Admin/view/category/Add.php (hoặc Create.php)

// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/CategoryController.php';

// Khởi tạo Controller và gọi phương thức create()
$controller = new CategoryController();

// Phương thức create() trong Controller sẽ:
// 1. Nếu là GET request: Chỉ hiển thị form.
// 2. Nếu là POST request: Xử lý thêm, sau đó chuyển hướng (header location) nếu thành công
//    hoặc trả về mảng chứa thông báo lỗi nếu thất bại.
$data = $controller->create();

$error_message = $data['error_message'] ?? null; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Danh Mục Mới</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* CSS được giữ nguyên từ file sản phẩm */
        body {
            background: #f5f7fa;
        }
        .add-category-card {
            max-width: 400px; /* Nhỏ hơn vì ít trường hơn */
            margin: 80px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            padding: 32px 28px;
        }
        .add-category-card h2 {
            font-weight: 600;
            color: #5a6a85;
            text-align: center;
            margin-bottom: 28px;
        }
        .btn-success {
            background: #16a34a;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 10px 0;
            width: 100%;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-success:hover {
            background: #15803d;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: #5a6a85;
            text-decoration: none;
            font-size: 0.98rem;
        }
        .back-link:hover {
            text-decoration: underline;
            color: #16a34a;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        .form-group p {
            width: 130px; 
            margin-right: 10px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0;
        }
        .form-group input {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .error-message {
            color: #dc2626;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="add-category-card">
        <h2>Thêm Danh Mục Mới</h2>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>

        <form method="POST" action="Add.php"> 
            
            <div class="form-group">
                <p><strong>Tên danh mục:</strong></p>
                <input type="text" id="tendanhmuc" name="tendanhmuc" placeholder="Nhập tên danh mục" required>
            </div>

            <button type="submit" class="btn btn-success">Thêm Danh Mục</button>
        </form>

        <a href="../../index.php?page=danhmuc" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng danh mục
        </a>
    </div>

</body>
</html>