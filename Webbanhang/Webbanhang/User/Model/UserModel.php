<?php

class UserModel
{
    // 1. THUỘC TÍNH (Properties) - Private
    private ?int $user_id = null;
    private string $hoten;
    private string $email;
    private string $matkhau; // Chứa hashed password
    private ?string $dienthoai = null;
    private ?string $diachi = null;
    private int $tier_id;
    private int $trangthai;
    
    // Các trường token (nên là private hoặc protected)
    private ?string $reset_token_hash = null;
    private ?string $reset_token_expiry = null;

    public function __construct() {}

    // 2. SETTERS (Có validation tương tự Staff Model)

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
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

    /**
     * Set mật khẩu. Nên đảm bảo mật khẩu được HASH trước khi lưu vào Model 
     * nếu hàm này được gọi từ Service/Repository.
     */
    public function setMatKhau(string $matkhau_hashed): void
    {
        // Giả định đây là mật khẩu đã được HASH.
        // Nếu là mật khẩu thô, cần thêm validation độ dài trước khi hash.
        $this->matkhau = $matkhau_hashed;
    }

    public function setDienThoai(?string $dienthoai): void
    {
        // Có thể thêm regex validation cho số điện thoại ở đây
        $this->dienthoai = trim($dienthoai);
    }
    
    public function setDiaChi(?string $diachi): void
    {
        $this->diachi = trim($diachi);
    }

    public function setTierId(int $tier_id): void
    {
        if ($tier_id <= 0) {
            throw new Exception("Tier ID không hợp lệ.");
        }
        $this->tier_id = $tier_id;
    }
    
    public function setTrangThai(int $trangthai): void
    {
        // Thêm validation để đảm bảo chỉ nhận giá trị 0 hoặc 1, v.v.
        $this->trangthai = $trangthai;
    }
    
    public function setResetTokenHash(?string $hash): void
    {
        $this->reset_token_hash = $hash;
    }
    
    public function setResetTokenExpiry(?string $expiry): void
    {
        $this->reset_token_expiry = $expiry;
    }

    // 3. GETTERS (Truy xuất dữ liệu)

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getHoTen(): string
    {
        return $this->hoten;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMatKhau(): string
    {
        return $this->matkhau;
    }
    
    public function getDienThoai(): ?string
    {
        return $this->dienthoai;
    }

    public function getDiaChi(): ?string
    {
        return $this->diachi;
    }

    public function getTierId(): int
    {
        return $this->tier_id;
    }
    
    public function getTrangThai(): int
    {
        return $this->trangthai;
    }
    
    public function getResetTokenHash(): ?string
    {
        return $this->reset_token_hash;
    }
    
    public function getResetTokenExpiry(): ?string
    {
        return $this->reset_token_expiry;
    }
}