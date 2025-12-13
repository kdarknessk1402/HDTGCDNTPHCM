<?php
/**
 * Controller: Authentication
 * File: controllers/AuthController.php
 * Xử lý đăng nhập, đăng xuất
 */

class AuthController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Hiển thị form login
     */
    public function showLogin() {
        if (isLoggedIn()) {
            redirect('/dashboard');
            return;
        }
        
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/login');
            return;
        }
        
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        
        if (empty($username) || empty($password)) {
            setFlashMessage('error', 'Vui lòng nhập đầy đủ thông tin!');
            redirect('/login');
            return;
        }
        
        // Lấy thông tin user
        $query = "SELECT u.*, r.role_name 
                  FROM users u 
                  LEFT JOIN roles r ON u.role_id = r.role_id
                  WHERE u.username = :username AND u.is_active = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            setFlashMessage('error', 'Tài khoản không tồn tại hoặc đã bị khóa!');
            redirect('/login');
            return;
        }
        
        // Verify password (bcrypt)
        if (!password_verify($password, $user['password'])) {
            setFlashMessage('error', 'Mật khẩu không chính xác!');
            redirect('/login');
            return;
        }
        
        // Lưu session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['khoa_id'] = $user['khoa_id'];
        
        // Update last_login
        $updateQuery = "UPDATE users SET last_login = NOW() WHERE user_id = :user_id";
        $updateStmt = $this->db->prepare($updateQuery);
        $updateStmt->execute([':user_id' => $user['user_id']]);
        
        // Log activity
        logActivity($user['user_id'], 'Đăng nhập', 'users', $user['user_id']);
        
        setFlashMessage('success', 'Đăng nhập thành công!');
        redirect('/dashboard');
    }
    
    /**
     * Đăng xuất
     */
    public function logout() {
        if (isLoggedIn()) {
            logActivity(getUserId(), 'Đăng xuất', 'users', getUserId());
        }
        
        session_destroy();
        setFlashMessage('success', 'Đã đăng xuất!');
        redirect('/login');
    }
    
    /**
     * Đổi mật khẩu
     */
    public function changePassword() {
        if (!isLoggedIn()) {
            redirect('/login');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_password = trim($_POST['old_password'] ?? '');
            $new_password = trim($_POST['new_password'] ?? '');
            $confirm_password = trim($_POST['confirm_password'] ?? '');
            
            if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                setFlashMessage('error', 'Vui lòng nhập đầy đủ thông tin!');
                redirect('/change-password');
                return;
            }
            
            if ($new_password !== $confirm_password) {
                setFlashMessage('error', 'Mật khẩu mới không khớp!');
                redirect('/change-password');
                return;
            }
            
            if (strlen($new_password) < 6) {
                setFlashMessage('error', 'Mật khẩu mới phải ít nhất 6 ký tự!');
                redirect('/change-password');
                return;
            }
            
            // Lấy mật khẩu hiện tại
            $query = "SELECT password FROM users WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':user_id' => getUserId()]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!password_verify($old_password, $user['password'])) {
                setFlashMessage('error', 'Mật khẩu cũ không đúng!');
                redirect('/change-password');
                return;
            }
            
            // Cập nhật mật khẩu mới
            $hashed = password_hash($new_password, PASSWORD_BCRYPT);
            $updateQuery = "UPDATE users SET password = :password WHERE user_id = :user_id";
            $updateStmt = $this->db->prepare($updateQuery);
            
            if ($updateStmt->execute([':password' => $hashed, ':user_id' => getUserId()])) {
                logActivity(getUserId(), 'Đổi mật khẩu', 'users', getUserId());
                setFlashMessage('success', 'Đổi mật khẩu thành công!');
                redirect('/dashboard');
            } else {
                setFlashMessage('error', 'Có lỗi xảy ra!');
                redirect('/change-password');
            }
        }
        
        $pageTitle = 'Đổi mật khẩu';
        require_once __DIR__ . '/../views/auth/change_password.php';
    }
}