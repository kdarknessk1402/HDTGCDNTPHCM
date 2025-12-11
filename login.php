<?php
/**
 * Trang đăng nhập
 * File: /login.php
 */

session_start();
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/helpers/functions.php';

// Nếu đã đăng nhập, chuyển về trang chủ
if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/');
    exit;
}

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $db = Database::getInstance()->getConnection();
        $userModel = new User($db);
        
        // Kiểm tra đăng nhập - TÊN CỘT: username, password
        $user = $userModel->login($username, $password);
        
        if ($user) {
            // Lưu thông tin vào session - TÊN CỘT CHÍNH XÁC
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
            
            // Cập nhật last_login
            $userModel->updateLastLogin($user['user_id']);
            
            // Thông báo thành công
            $_SESSION['success'] = 'Đăng nhập thành công! Xin chào ' . htmlspecialchars($user['full_name']);
            
            // Redirect về trang chủ
            header('Location: ' . BASE_URL . '/');
            exit;
        } else {
            $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng';
        }
    }
}

// Hiển thị trang đăng nhập
include __DIR__ . '/views/auth/login.php';