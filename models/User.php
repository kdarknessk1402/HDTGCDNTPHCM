<?php
require_once __DIR__ . '/Model.php';

/**
 * User Model
 * TÊN CỘT DATABASE: user_id, username, password, full_name, email, phone, role_id, khoa_id, is_active, last_login
 */
class User extends Model {
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    /**
     * Login user - TÊN CỘT CHÍNH XÁC với database
     * Columns: user_id, username, password, full_name, email, phone, role_id, khoa_id, is_active
     */
    public function login($username, $password) {
        $sql = "SELECT 
                    u.user_id,
                    u.username,
                    u.password,
                    u.full_name,
                    u.email,
                    u.phone,
                    u.role_id,
                    u.khoa_id,
                    u.is_active,
                    r.role_name,
                    r.role_display_name,
                    k.ten_khoa
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                LEFT JOIN khoa k ON u.khoa_id = k.khoa_id
                WHERE u.username = :username AND u.is_active = 1
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        // Simple password check (no hash as per requirements)
        if ($user && $user['password'] === $password) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Update last login - TÊN CỘT: last_login
     */
    public function updateLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Get user with role
     */
    public function getUserWithRole($userId) {
        $sql = "SELECT u.*, r.role_name, r.role_display_name, k.ten_khoa
                FROM users u 
                LEFT JOIN roles r ON u.role_id = r.role_id 
                LEFT JOIN khoa k ON u.khoa_id = k.khoa_id
                WHERE u.user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get users by role
     */
    public function getUsersByRole($roleId) {
        return $this->getAll(['role_id' => $roleId, 'is_active' => 1], 'full_name ASC');
    }
    
    /**
     * Get users by khoa
     */
    public function getUsersByKhoa($khoaId) {
        return $this->getAll(['khoa_id' => $khoaId, 'is_active' => 1], 'full_name ASC');
    }
}