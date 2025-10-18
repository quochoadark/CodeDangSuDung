<?php
// File: /Webbanhang/User/Repository/LoginRepository.php (ĐÃ HỢP LÝ HÓA TRẢ VỀ)

require_once __DIR__ . '/../Model/LoginModel.php'; 

class LoginRepository 
{
    private $conn;
    private $table_name = "nguoidung"; 

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    // ⭐ PHƯƠNG THỨC MỚI: CẬP NHẬT PASSWORD
    public function updatePassword(int $user_id, string $hashed_password): bool
    {
        $sql = "UPDATE {$this->table_name} SET matkhau = ? WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("LỖI DB: Prepare updatePassword thất bại: " . $this->conn->error . " (user_id: " . $user_id . ")");
            return false;
        }

        $stmt->bind_param("si", $hashed_password, $user_id);
        
        if (!$stmt->execute()) {
            error_log("LỖI DB: Execute updatePassword thất bại: " . $stmt->error . " (user_id: " . $user_id . ")");
            return false;
        }
        
        return $stmt->affected_rows > 0;
    }

    // ⭐ PHƯƠNG THỨC MỚI: LƯU RESET TOKEN VÀO DB
    public function setResetToken(int $user_id, ?string $token_hash, ?string $expiry_time): bool
    {
        $sql = "
            UPDATE {$this->table_name}
            SET reset_token_hash = ?, reset_token_expiry = ?
            WHERE user_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("LỖI DB QUAN TRỌNG: Prepare setResetToken thất bại: " . $this->conn->error . " | user_id: " . $user_id);
            return false;
        }

        $stmt->bind_param("ssi", $token_hash, $expiry_time, $user_id);
        
        if (!$stmt->execute()) {
            error_log("LỖI DB QUAN TRỌNG: Execute setResetToken thất bại: " . $stmt->error . " | user_id: " . $user_id);
            return false;
        }
        
        // Trả về TRUE nếu execute thành công (ngay cả khi không có dòng nào bị ảnh hưởng, tức là update cùng giá trị)
        return true; 
    }
    
    // ⭐ PHƯƠNG THỨC MỚI: TÌM USER BẰNG RESET TOKEN
    public function findByResetToken(string $token_hash): ?LoginModel
    {
        $sql = "
            SELECT user_id, hoten, email, matkhau, trangthai,
                   remember_token_hash, token_expiry,
                   reset_token_hash, reset_token_expiry
            FROM {$this->table_name}
            WHERE reset_token_hash = ? AND trangthai = 1
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("LỖI DB: Prepare findByResetToken thất bại: " . $this->conn->error);
            return null;
        }

        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new LoginModel(
                $row['user_id'],
                $row['hoten'],
                $row['email'],
                $row['matkhau'],
                $row['trangthai'],
                $row['remember_token_hash'] ?? null,
                $row['token_expiry'] ?? null,
                $row['reset_token_hash'] ?? null, 
                $row['reset_token_expiry'] ?? null 
            );
        }
        return null;
    }

    public function findActiveByEmail(string $email)
    {
        $sql = "
            SELECT user_id, hoten, email, matkhau, trangthai,
                   remember_token_hash, token_expiry,
                   reset_token_hash, reset_token_expiry
            FROM {$this->table_name}
            WHERE email = ? AND trangthai = 1
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("LỖI DB: Prepare findActiveByEmail thất bại: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
             error_log("LỖI DB: Execute findActiveByEmail thất bại: " . $stmt->error);
             return null; 
        }

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            return new LoginModel(
                $row['user_id'],
                $row['hoten'],
                $row['email'],
                $row['matkhau'],
                $row['trangthai'],
                $row['remember_token_hash'] ?? null,
                $row['token_expiry'] ?? null,
                $row['reset_token_hash'] ?? null,
                $row['reset_token_expiry'] ?? null
            );
        }

        return null;
    }

    public function findActiveByRememberToken(string $token_hash)
    {
        $sql = "
            SELECT user_id, hoten, email, matkhau, trangthai, token_expiry,
                   remember_token_hash,
                   reset_token_hash, reset_token_expiry
            FROM {$this->table_name}
            WHERE remember_token_hash = ? AND trangthai = 1
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
            error_log("LỖI DB: Prepare findActiveByRememberToken thất bại: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("s", $token_hash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Kiểm tra hạn token
            if (strtotime($row['token_expiry']) < time()) {
                $this->setRememberToken($row['user_id'], null);
                return null;
            }

            return new LoginModel(
                $row['user_id'],
                $row['hoten'],
                $row['email'],
                $row['matkhau'],
                $row['trangthai'],
                $row['remember_token_hash'] ?? null,
                $row['token_expiry'] ?? null,
                $row['reset_token_hash'] ?? null,
                $row['reset_token_expiry'] ?? null
            );
        }

        return null;
    }

    /**
     * Cập nhật Remember Token Hash và thời gian hết hạn vào CSDL.
     */
    public function setRememberToken(int $user_id, ?string $token_hash)
    {
        $sql = "
            UPDATE {$this->table_name}
            SET remember_token_hash = ?, token_expiry = ?
            WHERE user_id = ?
        ";

        $expiry = $token_hash
            ? date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60))
            : null;

        $stmt = $this->conn->prepare($sql);
        
        if (!$stmt) {
             error_log("LỖI DB: Prepare setRememberToken thất bại: " . $this->conn->error . " | user_id: " . $user_id);
             return false; 
        }

        $stmt->bind_param("ssi", $token_hash, $expiry, $user_id);
        
        if (!$stmt->execute()) {
             error_log("LỖI DB: Execute setRememberToken thất bại: " . $stmt->error . " | user_id: " . $user_id);
             return false; // Trả về false nếu execute thất bại
        }

        return true; // Trả về true nếu execute thành công
    }
}
