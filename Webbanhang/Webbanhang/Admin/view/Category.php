<?php
// File: Admin/view/category/index.php 
// --- NẠP CONTROLLER ---
require_once __DIR__ . '/../Controller/CategoryController.php'; 

// Khởi tạo Controller và lấy dữ liệu
$controller = new CategoryController();
// Gọi hàm index() của Controller, giả định nó trả về mảng ['categories' => ...]
$data = $controller->index(); 

$categories = $data['categories'] ?? []; 


?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Danh Sách Danh Mục</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../fontawesome-free-6.7.2-web/fontawesome-free-6.7.2-web/css/all.min.css">
    <style>
        /* Đảm bảo tiêu đề bên trái, nút bên phải */
        .card-header span {
            margin-right: auto;
        }
        
        /* Căn giữa nội dung cột Tên Danh Mục */
        .card-body table th:nth-child(1),
        .card-body table td:nth-child(1) {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center" style="font-family: 'Arial', sans-serif">
        <span><b>Danh Sách Danh Mục</b></span> 
        <a href="crud/Category/Add.php" class="btn btn-success">+ Thêm</a> 
    </div>

    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="font-family: 'Arial', sans-serif">Tên Danh Mục</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            if (!empty($categories)): 
                foreach ($categories as $row): 
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['tendanhmuc']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="1" class="text-center">Không có danh mục nào.</td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>