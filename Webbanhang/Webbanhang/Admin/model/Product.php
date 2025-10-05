<?php
// File: Admin/model/Product.php
require_once __DIR__ . '/../../Database/database.php'; // Đảm bảo lớp kết nối Database tồn tại

class Product {
    // 1. THUỘC TÍNH (Properties)
    // Lưu ý: $product_id là public để Controller có thể dễ dàng set ID khi update.
    public $product_id; 
    private $tensanpham;
    private $category_id;
    private $gia;
    private $tonkho;
    private $mota;
    private $img;
    private $ngaytao; // Thuộc tính này không được sử dụng trong INSERT/UPDATE, nên có thể bỏ qua

    private $conn;
    private $table_name = "sanpham";

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS (Đọc dữ liệu) ---
    public function getId() { return $this->product_id; }
    public function getTenSanPham() { return $this->tensanpham; }
    public function getCategoryId() { return $this->category_id; }
    public function getGia() { return $this->gia; }
    public function getTonKho() { return $this->tonkho; }
    public function getMoTa() { return $this->mota; }
    public function getImg() { return $this->img; }
    public function getNgayTao() { return $this->ngaytao; }


    // --- SETTERS (Ghi dữ liệu) ---
    // Giữ private, chỉ dùng nội bộ hoặc trong save(). Controller có thể set trực tiếp $product_id.
    private function setId($id) { $this->product_id = (int)$id; } 

    public function setTenSanPham($tensanpham) {
        if (empty(trim($tensanpham))) {
            throw new Exception("Tên sản phẩm không được để trống.");
        }
        $this->tensanpham = trim($tensanpham);
    }
    
    public function setCategoryId($category_id) {
        $this->category_id = (int)$category_id;
    }

    public function setGia($gia) {
        if ($gia < 0) {
            throw new Exception("Giá phải lớn hơn hoặc bằng 0.");
        }
        $this->gia = (float)$gia;
    }

    public function setTonKho($tonkho) {
        if ($tonkho < 0) {
            throw new Exception("Tồn kho không thể là số âm.");
        }
        $this->tonkho = (int)$tonkho;
    }

    public function setMoTa($mota) {
        $this->mota = $mota;
    }
    
    public function setImg($img) {
        $this->img = $img;
    }
    
    private function setNgayTao($ngaytao) {
        $this->ngaytao = $ngaytao;
    }


    // --- ACTIVE RECORD METHODS ---

    // 1. TÌM KIẾM THEO ID (Find)
    public function find($id) {
        // ... (Giữ nguyên)
        $sql = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return $row; // Trả về mảng dữ liệu
        }
        return null; // Trả về null nếu không tìm thấy
    }

    // 2. LƯU (SAVE) - Xử lý cả INSERT và UPDATE
    public function save() {
        if (empty($this->product_id)) {
            // THỰC HIỆN CREATE (INSERT)
            $sql = "INSERT INTO " . $this->table_name . " 
                     (tensanpham, category_id, gia, tonkho, mota, img) 
                     VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param(
                "sdiiss", 
                $this->tensanpham, 
                $this->category_id, 
                $this->gia, 
                $this->tonkho, 
                $this->mota, 
                $this->img
            );

            if ($stmt->execute()) {
                $this->setId($this->conn->insert_id); 
                return true;
            }
            return false;

        } else {
            // THỰC HIỆN UPDATE (Nếu $product_id đã tồn tại)
            $sql = "UPDATE " . $this->table_name . " 
                     SET tensanpham = ?, category_id = ?, gia = ?, tonkho = ?, mota = ?, img = ? 
                     WHERE product_id = ?";
             
            $stmt = $this->conn->prepare($sql);
            
            // LƯU Ý: Phải có đủ 7 tham số. Thêm product_id vào chuỗi bind_param ('sdiissi') và tham số cuối cùng.
            $stmt->bind_param(
                "sdiissi", 
                $this->tensanpham, 
                $this->category_id, 
                $this->gia, 
                $this->tonkho, 
                $this->mota, 
                $this->img, 
                $this->product_id
            );

            // Ghi log lỗi nếu prepare thất bại
            if ($stmt === false) {
                 error_log("Prepare failed (UPDATE): " . $this->conn->error);
                 return false; 
            }
            
            return $stmt->execute();
        }
    }

    // 3. PHƯƠNG THỨC UPDATE RIÊNG ĐƯỢC XÓA BỎ VÌ ĐÃ TÍCH HỢP VÀO save()
    // Để giữ Controller hoạt động, nếu bạn muốn dùng update() riêng, hãy đổi tên phương thức save() thành create().
    // Nhưng do Controller của bạn gọi $this->productModel->update(), tôi sẽ tạo phương thức update()
    // VÀ giữ nguyên logic UPDATE ở đây để Controller chạy được mà không cần sửa.

    /**
     * Phương thức update riêng biệt để tương thích với ProductController::edit()
     * Thực chất là phần UPDATE của phương thức save()
     * @return bool
     */
    public function update() {
        if (empty($this->product_id)) {
            error_log("Cannot update: Product ID is missing.");
            return false;
        }
        
        $sql = "UPDATE " . $this->table_name . " 
                 SET tensanpham = ?, category_id = ?, gia = ?, tonkho = ?, mota = ?, img = ? 
                 WHERE product_id = ?";
        
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             error_log("Prepare failed (UPDATE): " . $this->conn->error);
             return false; 
        }

        // Tên các cột: tensanpham(s), category_id(i), gia(d), tonkho(i), mota(s), img(s), product_id(i)
        $stmt->bind_param(
            "sdiissi", 
            $this->tensanpham, 
            $this->category_id, 
            $this->gia, 
            $this->tonkho, 
            $this->mota, 
            $this->img, 
            $this->product_id
        );
        
        if ($stmt->execute()) {
             // Trả về true nếu execute thành công (không quan trọng affected_rows là 0 hay lớn hơn 0)
             return true; 
        } else {
             error_log("Execute failed (UPDATE): " . $stmt->error);
             return false;
        }
    }


    // 4. XÓA (DELETE) 
    public function delete($product_id) {
        // ... (Giữ nguyên)
        if (empty($product_id)) {
            return false; 
        }

        $sql = "DELETE FROM " . $this->table_name . " WHERE product_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if ($stmt === false) {
             error_log("Prepare failed (DELETE): " . $this->conn->error);
             return false;
        }
        
        $stmt->bind_param("i", $product_id); 
        
        if ($stmt->execute()) {
            return $stmt->affected_rows > 0;
        } else {
             error_log("Execute failed (DELETE): " . $stmt->error);
             return false;
        }
    }
    
    // 5. Lấy tổng số bản ghi (Count)
    public function getTotalRecords() {
        // ... (Giữ nguyên)
        $sql = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $result = $this->conn->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return (int)$row['total'];
        }
        return 0;
    }

    // 6. Đọc tất cả sản phẩm (kèm phân trang và lấy thêm Tên Danh Mục)
    public function readAll($limit, $offset) {
        // ... (Giữ nguyên)
        $sql = "SELECT p.*, c.tendanhmuc 
                FROM " . $this->table_name . " p
                JOIN danhmucsanpham c ON p.category_id = c.category_id
                ORDER BY p.product_id DESC
                LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
             error_log("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
             return null;
        }

        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result(); 
    }

}
// KHÔNG ĐƯỢC CÓ KHOẢNG TRẮNG, DÒNG TRẮNG HOẶC THẺ ĐÓNG PHP NÀO SAU DÂY