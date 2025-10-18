<?php
// File: Admin/view/khachhang.php

require_once __DIR__ . '/../Controller/UserController.php';

$controller = new UserController();
$data = $controller->handleRequest();

$users = $data['users'] ?? null;
$total_pages = $data['total_pages'] ?? 1;
$current_page = $data['current_page'] ?? 1;
$tiers = $data['tiers'] ?? [];
$current_tier_id = $data['current_tier_id'] ?? 0;

$tier_query_string = $current_tier_id > 0 ? '&tier_id=' . $current_tier_id : '';
$current_page_query = '&p=' . $current_page;
?>

<style>
/* -------------------- BẢNG KHÁCH HÀNG RESPONSIVE -------------------- */
.table-responsive-pc {
    overflow-x: auto;
}

@media (max-width: 767px) {
    .card-header .d-flex.align-items-center {
        flex-direction: column;
        width: 100%;
        margin-top: 10px;
    }

    .card-header .form-select {
        margin-bottom: 10px;
        width: 100%;
    }

    .table.user-table thead {
        display: none;
    }

    .table.user-table tbody,
    .table.user-table tr,
    .table.user-table td {
        display: block;
        width: 100%;
        border: none;
    }

    .table.user-table tr {
        margin-bottom: 15px;
        border: 1px solid #dee2e6;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.05);
        padding: 10px;
        border-radius: .25rem;
    }

    .table.user-table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
        border-bottom: 1px solid #eee;
        word-wrap: break-word;
    }

    .table.user-table td:last-child {
        border-bottom: none;
    }

    .table.user-table td::before {
        content: attr(data-label);
        position: absolute;
        left: 10px;
        width: calc(50% - 20px);
        padding-right: 10px;
        white-space: nowrap;
        text-align: left;
        font-weight: bold;
        color: #495057;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-actions-td {
        text-align: center !important;
        padding-left: 10px !important;
        padding-top: 10px !important;
    }

    .user-actions-td::before {
        content: none;
    }

    .user-actions-td a {
        margin: 5px;
    }
}
</style>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center" style="font-family: 'Arial', sans-serif;">
        <span><b>Danh Sách Khách Hàng</b></span>

        <div class="d-flex align-items-center flex-wrap mt-2 mt-md-0">
            <form method="GET" class="me-3 w-100 w-md-auto">
                <input type="hidden" name="page" value="khachhang">
                <?php if ($current_page > 1): ?>
                    <input type="hidden" name="p" value="1">
                <?php endif; ?>

                <select name="tier_id" class="form-select" onchange="this.form.submit()">
                    <option value="0" <?= $current_tier_id == 0 ? 'selected' : '' ?>>--- Tất cả Tier ---</option>
                    <?php foreach ($tiers as $tier): ?>
                        <option value="<?= $tier['tier_id'] ?>" <?= $current_tier_id == $tier['tier_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tier['tenhang']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive-pc">
            <table class="table table-bordered user-table">
                <thead>
                    <tr>
                        <th>Họ Tên</th>
                        <th>Email</th>
                        <th>Điện Thoại</th>
                        <th>Địa Chỉ</th>
                        <th>Tier</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users && $users->num_rows > 0): ?>
                        <?php while ($row = $users->fetch_assoc()):
                            $is_active = (int)($row['trangthai'] ?? 0) === 1;
                            $base_link_params = "?page=khachhang{$tier_query_string}{$current_page_query}";
                            ?>
                            <tr>
                                <td data-label="Họ Tên"><?= htmlspecialchars($row['hoten']) ?></td>
                                <td data-label="Email"><?= htmlspecialchars($row['email']) ?></td>
                                <td data-label="Điện Thoại"><?= htmlspecialchars($row['dienthoai'] ?? 'N/A') ?></td>
                                <td data-label="Địa Chỉ">
                                    <?php
                                    $diachi = htmlspecialchars($row['diachi'] ?? '');
                                    echo (mb_strlen($diachi) > 30) ? mb_substr($diachi, 0, 30) . '...' : $diachi;
                                    ?>
                                </td>
                                <td data-label="Tier"><?= htmlspecialchars($row['tenhang'] ?? 'N/A') ?></td>
                                <td data-label="Trạng thái">
                                    <?= $is_active
                                        ? '<span class="badge bg-success">Hoạt động</span>'
                                        : '<span class="badge bg-danger">Khóa</span>' ?>
                                </td>
                                <td class="user-actions-td" data-label="Hành động">
                                    <a href="crud/User/Detail.php?id=<?= $row['user_id'] ?>" class="btn btn-primary btn-sm" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="crud/User/SendEmail.php?id=<?= $row['user_id'] ?>" class="btn btn-secondary btn-sm" title="Gửi Email">
                                        <i class="fas fa-envelope"></i>
                                    </a>

                                    <?php if ($is_active): ?>
                                        <a href="#" class="btn btn-warning btn-sm block-link"
                                           title="Khóa khách hàng"
                                           data-bs-toggle="modal"
                                           data-bs-target="#blockModal"
                                           data-url="<?= $base_link_params ?>&action=block&id=<?= $row['user_id'] ?>"
                                           data-user="<?= htmlspecialchars($row['hoten']) ?>"
                                           data-action="Khóa">
                                            <i class="fas fa-lock"></i> Khóa
                                        </a>
                                    <?php else: ?>
                                        <a href="#" class="btn btn-success btn-sm block-link"
                                           title="Mở khóa khách hàng"
                                           data-bs-toggle="modal"
                                           data-bs-target="#blockModal"
                                           data-url="<?= $base_link_params ?>&action=unblock&id=<?= $row['user_id'] ?>"
                                           data-user="<?= htmlspecialchars($row['hoten']) ?>"
                                           data-action="Mở khóa">
                                            <i class="fas fa-unlock-alt"></i> Mở khóa
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Không có khách hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- PHÂN TRANG -->
<div class="d-flex justify-content-center mt-4">
    <nav>
        <ul class="pagination">
            <?php
            $base_link = "?page=khachhang{$tier_query_string}";

            if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $base_link ?>&p=<?= $current_page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif;

            for ($i = 1; $i <= $total_pages; $i++):
                $active = $i === (int)$current_page ? 'active' : ''; ?>
                <li class="page-item <?= $active ?>">
                    <a class="page-link" href="<?= $base_link ?>&p=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor;

            if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="<?= $base_link ?>&p=<?= $current_page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- MODAL XÁC NHẬN -->
<div class="modal fade" id="blockModal" tabindex="-1" aria-labelledby="blockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xác nhận Hành động</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="blockModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirmBlock">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const blockModal = document.getElementById("blockModal");
    const confirmBtn = document.getElementById("confirmBlock");
    let currentUrl = "";

    if (blockModal) {
        blockModal.addEventListener("show.bs.modal", (e) => {
            const button = e.relatedTarget;
            const url = button.getAttribute("data-url");
            const user = button.getAttribute("data-user");
            const action = button.getAttribute("data-action");

            document.getElementById("blockModalBody").innerHTML =
                `Bạn có chắc chắn muốn <strong>${action}</strong> tài khoản của khách hàng <strong>${user}</strong> không?`;

            currentUrl = url;

            confirmBtn.className = "btn";
            confirmBtn.classList.add(action === "Khóa" ? "btn-warning" : "btn-success");
        });

        confirmBtn.addEventListener("click", () => {
            if (currentUrl) window.location.href = currentUrl;
        });
    }
});
</script>
