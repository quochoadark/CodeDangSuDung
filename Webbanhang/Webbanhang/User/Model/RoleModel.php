<?php
// File: App/Models/Role.php (Model/Entity - KHÔNG SQL)

// Model đại diện cho bảng 'chucvu'
class Role
{
    // 1. THUỘC TÍNH (Properties) - Mapping với các cột CSDL
    public $id_chucvu;
    public $ten_chucvu;

    public function __construct() {}

    // 2. GETTERS
    public function getIdChucVu() { return $this->id_chucvu; }
    public function getTenChucVu() { return $this->ten_chucvu; }

    // 3. SETTERS (Không cần validation phức tạp vì đây là bảng tham chiếu)
    public function setIdChucVu($id) { $this->id_chucvu = (int)$id; }
    public function setTenChucVu($name) { $this->ten_chucvu = trim($name); }
}