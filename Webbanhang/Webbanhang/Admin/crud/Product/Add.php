<?php
// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../../Controller/ProductController.php';

// Khởi tạo Controller
$controller = new ProductController();
$data = $controller->create();
$categories = $data['categories']; 

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thêm Sản Phẩm</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        body {
            background: #f5f7fa;
        }
        .add-category-card {
            max-width: 400px;
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
            width: 130px; /* Cùng kích thước với label cũ */
            margin-right: 10px;
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 0; /* Loại bỏ margin dưới của p */
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            flex: 1;
            padding: 10px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }
        .form-group textarea {
            resize: vertical;
        }
    </style>
</head>
<body>
    <div class="add-category-card">
        <h2>Thêm Sản Phẩm</h2>

        <form method="POST" action="Add.php" enctype="multipart/form-data">
            <div class="form-group">
                <p><strong>Tên sản phẩm:</strong></p>
                <input type="text" id="tensanpham" name="tensanpham" placeholder="Nhập tên sản phẩm" required>
            </div>

           <div class="form-group">
                <p><strong>Danh mục:</strong></p>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php
                    // SỬA TỪ ĐÂY:
                    if (!empty($categories)):
                        // Lặp qua mảng PHP đã được Controller chuẩn bị
                        foreach ($categories as $row): ?>
                            <option value="<?= $row['category_id'] ?>">
                                <?= htmlspecialchars($row['tendanhmuc']) ?>
                            </option>
                        <?php endforeach;
                    endif; 
                    // KẾT THÚC SỬA
                    ?>
                </select>
            </div>

            <div class="form-group">
                <p><strong>Giá (VNĐ):</strong></p>
                <input type="number" id="gia" name="gia" placeholder="Nhập giá sản phẩm" step="1000" min="0" required>
            </div>

            <div class="form-group">
                <p><strong>Số lượng tồn kho:</strong></p>
                <input type="number" id="tonkho" name="tonkho" placeholder="Nhập số lượng tồn kho" min="0" required>
            </div>

            <div class="form-group">
                <p><strong>Ngày tạo:</strong></p>
                <input type="date" id="ngaytao" name="ngaytao" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <p><strong>Mô tả:</strong></p>
                <textarea id="mota" name="mota" rows="3" placeholder="Nhập mô tả sản phẩm"></textarea>
            </div>

            <div class="form-group">
                <p><strong>Ảnh sản phẩm:</strong></p>
                <input type="file" id="img" name="img" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-success">Thêm</button>
        </form>

        <a href="../../index.php?page=sanpham" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại bảng sản phẩm
        </a>
    </div>

</body>
</html>