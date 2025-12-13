<?php
/**
 * Đăng xuất
 * File: /logout.php
 */

session_start();

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
$_SESSION['success'] = 'Đã đăng xuất thành công';

// Redirect về trang login
header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . './HDTG_Project/login.php');
exit;