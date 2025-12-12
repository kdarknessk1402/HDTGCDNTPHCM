<?php
/**
 * Configuration File
 * File: /config/config.php
 */

// Môi trường
define('ENVIRONMENT', 'development'); // development | production

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'hdtg_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Hệ thống Quản lý Hợp đồng Thỉnh giảng');
define('APP_VERSION', '2.0.0');
define('APP_AUTHOR', 'Cao đẳng Nghề TP.HCM');

// URL Configuration
// QUAN TRỌNG: Thay đổi BASE_URL cho phù hợp với môi trường của bạn
define('BASE_URL', 'http://localhost/hdtg_project');

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

// Upload Directories
define('UPLOAD_GIANG_VIEN', UPLOAD_PATH . '/giang_vien');
define('UPLOAD_HOP_DONG', UPLOAD_PATH . '/hop_dong');
define('UPLOAD_IMPORT', UPLOAD_PATH . '/imports');
define('UPLOAD_TEMP', UPLOAD_PATH . '/temp');

// Session Configuration
ini_set('session.gc_maxlifetime', 86400); // 24 hours
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'domain' => '',
    'secure' => false, // Set true nếu dùng HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error Reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . '/logs/error.log');
}

// File Upload Configuration
define('MAX_FILE_SIZE', 10485760); // 10MB in bytes
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
define('ALLOWED_ALL_TYPES', array_merge(ALLOWED_IMAGE_TYPES, ALLOWED_DOC_TYPES));

// Pagination
define('ITEMS_PER_PAGE', 20);

// Password Policy (nếu muốn thêm sau này)
define('MIN_PASSWORD_LENGTH', 6);

// Application Constants
define('CONTRACT_NUMBER_PREFIX', 'HĐ-CĐN');
define('SCHOOL_YEAR_START_MONTH', 8); // Tháng 8
define('TEACHER_CODE_PREFIX', 'GV');

// Format
define('DATE_FORMAT', 'd/m/Y');
define('DATETIME_FORMAT', 'd/m/Y H:i');
define('MYSQL_DATE_FORMAT', 'Y-m-d');
define('MYSQL_DATETIME_FORMAT', 'Y-m-d H:i:s');

// Status
define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);

// Role IDs (từ database)
define('ROLE_ADMIN', 1);
define('ROLE_PHONG_DAO_TAO', 2);
define('ROLE_TRUONG_KHOA', 3);
define('ROLE_GIAO_VU', 4);

// Contract Status
define('CONTRACT_STATUS_NEW', 'Mới tạo');
define('CONTRACT_STATUS_APPROVED', 'Đã duyệt');
define('CONTRACT_STATUS_IN_PROGRESS', 'Đang thực hiện');
define('CONTRACT_STATUS_COMPLETED', 'Hoàn thành');
define('CONTRACT_STATUS_CANCELLED', 'Hủy');

// Email Configuration (nếu cần gửi email)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM_EMAIL', 'noreply@cdnhcm.edu.vn');
define('SMTP_FROM_NAME', APP_NAME);

// Create upload directories if not exist
$uploadDirs = [
    UPLOAD_PATH,
    UPLOAD_GIANG_VIEN,
    UPLOAD_HOP_DONG,
    UPLOAD_IMPORT,
    UPLOAD_TEMP
];

foreach ($uploadDirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Auto-load helpers
require_once ROOT_PATH . '/helpers/functions.php';