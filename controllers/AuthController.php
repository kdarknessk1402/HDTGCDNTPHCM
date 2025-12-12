<?php
require_once __DIR__ . '/Controller.php';

/**
 * AuthController - Xử lý đăng nhập/đăng xuất
 * File: /controllers/AuthController.php
 */
class AuthController extends Controller {
    
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    /**
     * Hiển thị trang đăng nhập
     */
    public function login() {
        // Nếu đã đăng nhập, chuyển về trang chủ
        if (isLoggedIn()) {
            $this->redirect('index.php');
        }
        
        $this->view('auth/login', [
            'title' => 'Đăng nhập'
        ]);
    }
    
    /**
     * Xử lý đăng nhập
     */
    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login.php');
        }
        
        $username = sanitize($this->post('username'));
        $password = $this->post('password');
        
        // Validate
        if (empty($username) || empty($password)) {
            $this->setFlash('Vui lòng nhập đầy đủ thông tin', 'danger');
            $this->redirect('login.php');
        }
        
        // Attempt login
        $user = $this->userModel->login($username, $password);
        
        if ($user) {
            // Set session - TÊN CỘT CHÍNH XÁC
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['role_display_name'] = $user['role_display_name'];
            $_SESSION['khoa_id'] = $user['khoa_id'];
            $_SESSION['ten_khoa'] = $user['ten_khoa'] ?? null;
            
            // Update last login
            $this->userModel->updateLastLogin($user['user_id']);
            
            $this->setFlash('Đăng nhập thành công! Xin chào ' . $user['full_name'], 'success');
            $this->redirect('index.php');
        } else {
            $this->setFlash('Tên đăng nhập hoặc mật khẩu không đúng', 'danger');
            $this->redirect('login.php');
        }
    }
    
    /**
     * Đăng xuất
     */
    public function logout() {
        // Xóa tất cả session
        $_SESSION = array();
        
        // Hủy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        
        // Hủy session
        session_destroy();
        
        // Khởi động lại session để hiển thị thông báo
        session_start();
        $this->setFlash('Đã đăng xuất thành công', 'success');
        $this->redirect('login.php');
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $this->post('current_password');
            $newPassword = $this->post('new_password');
            $confirmPassword = $this->post('confirm_password');
            
            // Validate
            $errors = [];
            
            if (empty($currentPassword)) {
                $errors[] = 'Vui lòng nhập mật khẩu hiện tại';
            }
            
            if (empty($newPassword)) {
                $errors[] = 'Vui lòng nhập mật khẩu mới';
            }
            
            if (strlen($newPassword) < MIN_PASSWORD_LENGTH) {
                $errors[] = 'Mật khẩu mới phải có ít nhất ' . MIN_PASSWORD_LENGTH . ' ký tự';
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp';
            }
            
            if (empty($errors)) {
                // Check current password
                $user = $this->userModel->getById($_SESSION['user_id']);
                
                if ($user['password'] !== $currentPassword) {
                    $this->setFlash('Mật khẩu hiện tại không đúng', 'danger');
                } else {
                    // Update password
                    $updated = $this->userModel->update($_SESSION['user_id'], [
                        'password' => $newPassword
                    ]);
                    
                    if ($updated) {
                        $this->setFlash('Đổi mật khẩu thành công', 'success');
                        $this->redirect('change-password.php');
                    } else {
                        $this->setFlash('Lỗi khi đổi mật khẩu', 'danger');
                    }
                }
            } else {
                $this->setFlash(implode('<br>', $errors), 'danger');
            }
        }
        
        $this->view('auth/change-password', [
            'title' => 'Đổi mật khẩu'
        ]);
    }
}