<?php
// File: /Webbanhang/User/Model/LoginModel.php

class LoginModel
{
    private $user_id;
    private $hoten;
    private $email;
    private $matkhau;
    private $trangthai;
    
    // ⭐ BỔ SUNG CHO CHỨC NĂNG REMEMBER ME
    private $remember_token_hash;
    private $token_expiry;
    
    // ⭐ BỔ SUNG CHO CHỨC NĂNG QUÊN MẬT KHẨU
    private $reset_token_hash;
    private $reset_token_expiry;

    public function __construct(
        $user_id = null, 
        $hoten = null, 
        $email = null, 
        $matkhau = null, 
        $trangthai = null,
        $remember_token_hash = null,
        $token_expiry = null, 
        $reset_token_hash = null, 
        $reset_token_expiry = null 
    ) {
        $this->user_id = $user_id;
        $this->hoten = $hoten;
        $this->email = $email;
        $this->matkhau = $matkhau;
        $this->trangthai = $trangthai;
        $this->remember_token_hash = $remember_token_hash;
        $this->token_expiry = $token_expiry;
        $this->reset_token_hash = $reset_token_hash;
        $this->reset_token_expiry = $reset_token_expiry;
    }

    // ================= GETTERS =================
    public function getId() { return $this->user_id; }
    public function getHoTen() { return $this->hoten; }
    public function getEmail() { return $this->email; }
    public function getMatKhau() { return $this->matkhau; }
    public function getTrangThai() { return $this->trangthai; }
    
    // ⭐ GETTERS CHO REMEMBER ME
    public function getRememberTokenHash() { return $this->remember_token_hash; }
    public function getTokenExpiry() { return $this->token_expiry; }

    // ⭐ GETTERS CHO RESET PASSWORD
    public function getResetTokenHash() { return $this->reset_token_hash; }
    public function getResetTokenExpiry() { return $this->reset_token_expiry; }

    // ================= SETTERS =================
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setEmail($email) { $this->email = trim($email); }
    public function setMatKhau($matkhau) { $this->matkhau = $matkhau; }
    public function setHoTen($hoten) { $this->hoten = $hoten; }
    public function setTrangThai($trangthai) { $this->trangthai = $trangthai; }
    
    // ⭐ SETTERS CHO REMEMBER ME
    public function setRememberTokenHash($hash) { $this->remember_token_hash = $hash; }
    public function setTokenExpiry($expiry) { $this->token_expiry = $expiry; }

    // ⭐ SETTERS CHO RESET PASSWORD
    public function setResetTokenHash($hash) { $this->reset_token_hash = $hash; }
    public function setResetTokenExpiry($expiry) { $this->reset_token_expiry = $expiry; }
}