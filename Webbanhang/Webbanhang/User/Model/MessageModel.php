<?php
// File: Admin/model/Message.php (Hoặc dùng trong User/model)

class MessageModel {
    // 1. THUỘC TÍNH (Properties)
    // Tên cột: message_id, user_id, staff_id, noidung, ngaygui
    
    // Khóa chính
    private $message_id; 
    
    // Khóa ngoại, người gửi (User)
    private $user_id; 
    
    // Khóa ngoại, người nhận/xử lý (Admin/Staff)
    private $staff_id; 
    
    // Nội dung (Tiêu đề và chi tiết)
    private $noidung; 
    
    // Thời gian gửi
    private $ngaygui; 
    

    // 2. CONSTRUCTOR (Tùy chọn, để dễ dàng tạo đối tượng)
    public function __construct(
        $message_id = null, 
        $user_id = null, 
        $staff_id = null, 
        $noidung = '', 
        $ngaygui = null
    ) {
        $this->message_id = $message_id;
        $this->user_id = $user_id;
        $this->staff_id = $staff_id;
        $this->noidung = $noidung;
        $this->ngaygui = $ngaygui;
    }


    // --- 3. GETTERS (Đọc dữ liệu) ---
    public function getMessageId() { return $this->message_id; }
    public function getUserId() { return $this->user_id; }
    public function getStaffId() { return $this->staff_id; }
    public function getNoidung() { return $this->noidung; }
    public function getNgayGui() { return $this->ngaygui; }


    // --- 4. SETTERS (Ghi dữ liệu và Validation cơ bản) ---
    
    // Setter cho ID (Thường chỉ dùng nội bộ hoặc khi lấy từ DB)
    public function setMessageId($id) { 
        $this->message_id = (int)$id; 
    } 

    public function setUserId($user_id) {
        // Có thể thêm validation: if ($user_id <= 0) throw new Exception("...");
        $this->user_id = (int)$user_id;
    }
    
    public function setStaffId($staff_id) {
        // staff_id có thể NULL, nên kiểm tra
        $this->staff_id = $staff_id === null ? null : (int)$staff_id;
    }

    public function setNoidung($noidung) {
        if (empty(trim($noidung))) {
            throw new Exception("Nội dung tin nhắn không được để trống.");
        }
        $this->noidung = trim($noidung);
    }

    // Ngày gửi thường được Database hoặc Service tự động set, 
    // nhưng vẫn cần setter nếu muốn set thủ công.
    public function setNgayGui($ngaygui) {
        $this->ngaygui = $ngaygui;
    }
}
?>