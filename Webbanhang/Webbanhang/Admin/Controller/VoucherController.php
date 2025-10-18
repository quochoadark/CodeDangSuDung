<?php
// Admin/Controller/VoucherController.php
require_once __DIR__ . '/../Model/Voucher.php'; 
require_once __DIR__ . '/../../Database/Database.php'; // Cần import Database để lấy connection

class VoucherController {
    // Thuộc tính để lưu kết nối CSDL
    private $db; 
    // Thuộc tính để lưu đối tượng Model
    private $voucherModel; 

    public function __construct() {
        // Tạo kết nối Database
        $Database = new Database(); // Giả định class Database tồn tại
        $this->db = $Database->conn; // Lấy đối tượng kết nối mysqli

        // Khởi tạo Voucher Model VÀ TRUYỀN KẾT NỐI VÀO
        $this->voucherModel = new Voucher($this->db); 
    }

    // Xử lý tạo mới (Add.php)
    public function create() {
        $data = [
            'error_message' => null,
            'voucher_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_data = $_POST;
            // Dùng đối tượng model đã khởi tạo trong constructor
            $new_voucher = $this->voucherModel; 
            // Lưu ý: Phải tạo một đối tượng mới nếu bạn muốn tránh ghi đè dữ liệu
            // Tốt nhất là tạo đối tượng mới trong hàm để giữ sạch $this->voucherModel
            $new_voucher = new Voucher($this->db); 

            try {
                // 1. Gán dữ liệu vào đối tượng Voucher bằng SETTERS
                $new_voucher->setMaKhuyenMai($post_data['makhuyenmai'] ?? null);
                $new_voucher->setGiam($post_data['giam'] ?? 0);
                $new_voucher->setNgayHetHan($post_data['ngayhethan'] ?? null);
                $new_voucher->setSoLuong($post_data['soluong'] ?? null);
                $new_voucher->setLuotSuDung(0);
                
                // 2. Kiểm tra trùng mã (Dùng phương thức từ đối tượng Model)
                if ($new_voucher->checkDuplicateCode($new_voucher->getMaKhuyenMai())) {
                     throw new Exception("Mã khuyến mãi này đã tồn tại. Vui lòng chọn mã khác.");
                }

                // 3. Xử lý lưu dữ liệu (Gọi save() trên đối tượng)
                if ($new_voucher->save()) {
                    header("Location: ../../index.php?page=voucher");
                    exit();
                } else {
                    throw new Exception("Lỗi hệ thống: Không thể thêm mã khuyến mãi.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['voucher_data'] = $post_data;
            }
        }
        return $data;
    }

    // Xử lý chỉnh sửa (Update.php)
    public function edit() {
        $data = [
            'error_message' => null,
            'voucher_data' => []
        ];
        
        $voucher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $current_voucher = new Voucher($this->db); // Tạo đối tượng mới

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $post_data = $_POST;

            try {
                // 1. Tải dữ liệu cũ vào đối tượng và gán ID
                if (!$current_voucher->find($voucher_id)) {
                    throw new Exception("Không tìm thấy mã khuyến mãi để cập nhật.");
                }
                
                // 2. Gán dữ liệu mới vào đối tượng bằng SETTERS
                $current_voucher->setMaKhuyenMai($post_data['makhuyenmai'] ?? null);
                $current_voucher->setGiam($post_data['giam'] ?? 0);
                $current_voucher->setNgayHetHan($post_data['ngayhethan'] ?? null);
                $current_voucher->setSoLuong($post_data['soluong'] ?? null);
                
                // 3. Kiểm tra trùng mã
                if ($current_voucher->checkDuplicateCode($current_voucher->getMaKhuyenMai(), $voucher_id)) {
                     throw new Exception("Mã khuyến mãi này đã tồn tại. Vui lòng chọn mã khác.");
                }

                // 4. Xử lý cập nhật (Gọi save() trên đối tượng đã có ID)
                if ($current_voucher->save()) {
                    header("Location: ../../index.php?page=voucher");
                    exit();
                } else {
                    throw new Exception("Lỗi hệ thống: Không thể cập nhật mã khuyến mãi.");
                }

            } catch (Exception $e) {
                $data['error_message'] = $e->getMessage();
                $data['voucher_data'] = array_merge($post_data, ['voucher_id' => $voucher_id]);
            }

        } else { // GET request: Lấy dữ liệu cũ
            if ($voucher_id > 0) {
                if ($current_voucher->find($voucher_id)) {
                    // Lấy dữ liệu từ Getters sau khi find thành công
                    $data['voucher_data'] = [
                        'voucher_id' => $current_voucher->getId(),
                        'makhuyenmai' => $current_voucher->getMaKhuyenMai(),
                        'giam' => $current_voucher->getGiam(),
                        'ngayhethan' => $current_voucher->getNgayHetHan(),
                        'soluong' => $current_voucher->getSoLuong(),
                        'luotsudung' => $current_voucher->getLuotSuDung(),
                    ];
                } else {
                    $data['error_message'] = "Không tìm thấy mã khuyến mãi.";
                }
            } else {
                 $data['error_message'] = "Thiếu ID mã khuyến mãi.";
            }
        }
        return $data;
    }
    
    // Xử lý xóa
    public function delete(int $voucher_id) {
        $voucher_to_delete = new Voucher($this->db);
        
        // Gán ID cho đối tượng (vì delete cần ID)
        $voucher_to_delete->voucher_id = $voucher_id;
        
        if ($voucher_to_delete->delete()) {
            header("Location: index.php?page=voucher");
            exit();
        } else {
            header("Location: index.php?page=voucher&error=delete_failed");
            exit();
        }
    }
    
    // Lấy danh sách (List.php)
    public function index() {
        // Dùng phương thức STATIC readAllStatic và truyền kết nối CSDL
        $result = Voucher::readAllStatic($this->db); 
        
        // Chuyển kết quả mysqli_result thành mảng cho View
        $vouchers = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $vouchers[] = $row;
            }
            $result->free();
        }
        
        return [
            'vouchers' => $vouchers
        ];
    }
}