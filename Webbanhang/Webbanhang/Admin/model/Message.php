<?php
// File: Admin/model/Message.php
require_once __DIR__ . '/../../Database/Database.php';

class Message {
    // 1. THUỘC TÍNH (Properties) - Mapping to tinnhan table
    private $message_id; 
    private $user_id; 
    private $staff_id;
    private $noidung;
    private $ngaygui;

    private $conn;
    private $table_name = "tinnhan"; 

    // 2. CONSTRUCTOR
    public function __construct($db) {
        $this->conn = $db;
    }

    // --- GETTERS ---
    public function getMessageId() { return $this->message_id; }
    public function getUserId() { return $this->user_id; }
    public function getStaffId() { return $this->staff_id; }
    public function getNoidung() { return $this->noidung; }
    public function getNgaygui() { return $this->ngaygui; }

    // --- SETTERS ---
    // Setter cho message_id không cần thiết nếu nó là auto-increment
    public function setUserId($user_id) { $this->user_id = (int)$user_id; }
    public function setStaffId($staff_id) { $this->staff_id = (int)$staff_id; }
    public function setNoidung($noidung) { $this->noidung = $noidung; }


    public function create() {
        // Kiểm tra dữ liệu cần thiết đã được gán chưa
        if (empty($this->user_id) || empty($this->staff_id) || empty($this->noidung)) {
            error_log("Thiếu thuộc tính bắt buộc để tạo Tin nhắn.");
            return false;
        }

        $sql = "INSERT INTO " . $this->table_name . " (user_id, staff_id, noidung, ngaygui) VALUES (?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);

        if ($stmt === false) {
            error_log("Chuẩn bị thất bại (Message::create): " . $this->conn->error);
            return false;
        }

        // message_id, user_id, staff_id, noidung, ngaygui
        $stmt->bind_param("iis", $this->user_id, $this->staff_id, $this->noidung);

        if ($stmt->execute()) {
            $this->message_id = $this->conn->insert_id;
            return true;
        } else {
            error_log("Thực thi thất bại (Message::create): " . $stmt->error);
            return false;
        }
    }
}