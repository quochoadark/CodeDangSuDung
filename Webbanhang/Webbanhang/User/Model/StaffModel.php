<?php
// File: App/Models/Staff.php (Model/Entity - KHÔNG SQL)

// Model đại diện cho bảng 'nhanvien'
class StaffModel
{
    // 1. THUỘC TÍNH (Properties) - Tất cả đều là private để đảm bảo tính đóng gói (Encapsulation)
    private ?int $staff_id = null;
    private string $hoten;
    private string $email;
    private string $matkhau; // Chứa hashed password
    private ?string $dienthoai = null;
    private int $trangthai;
    private int $id_chucvu;
    private ?string $remember_token_hash = null;
    private ?string $token_expiry = null;

    public function __construct() {}

    // 2. SETTERS (Chứa logic Validation và kiểm soát dữ liệu)

    public function setStaffId(int $staff_id): void
    {
        $this->staff_id = $staff_id;
    }
    
    public function setHoTen(string $hoten): void
    {
        if (empty(trim($hoten))) {
            throw new Exception("Họ tên không được để trống.");
        }
        $this->hoten = trim($hoten);
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email không hợp lệ.");
        }
        $this->email = trim($email);
    }

    public function setMatKhau(string $matkhau): void
    {
        // Lưu ý: Nếu tham số $matkhau là mật khẩu thô, nên hash nó ở đây hoặc ở Service.
        // Giữ lại validation độ dài cơ bản nếu nó nhận mật khẩu thô:
        if (strlen($matkhau) < 6) {
             throw new Exception("Mật khẩu phải có ít nhất 6 ký tự.");
        }
        $this->matkhau = $matkhau; 
    }

    public function setDienThoai(?string $dienthoai): void
    {
        $this->dienthoai = trim($dienthoai);
    }

    public function setTrangThai(int $trangthai): void
    {
        $this->trangthai = $trangthai;
    }

    public function setIdChucVu(int $id_chucvu): void
    {
        if ($id_chucvu <= 0) {
             throw new Exception("Vui lòng chọn chức vụ.");
        }
        $this->id_chucvu = $id_chucvu;
    }
    
    public function setRememberTokenHash(?string $hash): void
    {
        $this->remember_token_hash = $hash;
    }
    
    public function setTokenExpiry(?string $expiry): void
    {
        $this->token_expiry = $expiry;
    }


    // 3. GETTERS (Để truy xuất dữ liệu từ Entity)
    
    public function getStaffId(): ?int { return $this->staff_id; }
    public function getHoten(): string { return $this->hoten; }
    public function getEmail(): string { return $this->email; }
    public function getMatKhau(): string { return $this->matkhau; }
    public function getDienThoai(): ?string { return $this->dienthoai; }
    public function getTrangThai(): int { return $this->trangthai; }
    public function getIdChucVu(): int { return $this->id_chucvu; }
    public function getRememberTokenHash(): ?string { return $this->remember_token_hash; }
    public function getTokenExpiry(): ?string { return $this->token_expiry; }
}
