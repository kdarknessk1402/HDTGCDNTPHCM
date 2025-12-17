<?php
/**
 * File: config/functions.php
 * Helper functions cho toàn bộ hệ thống
 */

// ==================== SESSION & AUTH ====================

/**
 * Kiểm tra đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Lấy user ID hiện tại
 */
function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Lấy username hiện tại
 */
function getUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Lấy họ tên user
 */
function getFullName() {
    return $_SESSION['full_name'] ?? '';
}

/**
 * Lấy vai trò user
 */
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Lấy Khoa ID của user (cho Trưởng Khoa)
 */
function getUserKhoaId() {
    return $_SESSION['khoa_id'] ?? null;
}

/**
 * Kiểm tra quyền Admin
 */
function isAdmin() {
    return getUserRole() === 'Admin';
}

/**
 * Kiểm tra quyền Phòng Đào tạo
 */
function isPhongDaoTao() {
    return getUserRole() === 'Phong_Dao_Tao';
}

/**
 * Kiểm tra quyền Trưởng Khoa
 */
function isTruongKhoa() {
    return getUserRole() === 'Truong_Khoa';
}

/**
 * Kiểm tra quyền Giáo vụ
 */
function isGiaoVu() {
    return getUserRole() === 'Giao_Vu';
}

// ==================== REDIRECT & FLASH MESSAGE ====================

/**
 * Redirect đến URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_type'] = $type; // success, error, warning, info
    $_SESSION['flash_message'] = $message;
}

/**
 * Hiển thị flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $type = $_SESSION['flash_type'] ?? 'info';
        $message = $_SESSION['flash_message'];
        
        $alert_class = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        $class = $alert_class[$type] ?? 'alert-info';
        
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    }
}

// ==================== DATA FORMATTING ====================

/**
 * Format số thành tiền VNĐ
 */
function formatMoney($number) {
    return number_format($number, 0, ',', '.') . ' đ';
}

/**
 * Format ngày DD/MM/YYYY
 */
function formatDate($date) {
    if (empty($date)) return '';
    return date('d/m/Y', strtotime($date));
}

/**
 * Format ngày giờ DD/MM/YYYY HH:mm
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Làm sạch chuỗi
 */
function cleanString($str) {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

/**
 * Cắt chuỗi với dấu ...
 */
function truncate($str, $length = 100) {
    if (mb_strlen($str) > $length) {
        return mb_substr($str, 0, $length) . '...';
    }
    return $str;
}

// ==================== VALIDATION ====================

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate số điện thoại VN
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^[0-9]{10,11}$/', $phone);
}

/**
 * Clean số điện thoại (chỉ giữ số)
 */
function cleanPhone($phone) {
    return preg_replace('/[^0-9]/', '', $phone);
}

/**
 * Validate CCCD (12 số)
 */
function validateCCCD($cccd) {
    return preg_match('/^[0-9]{12}$/', $cccd);
}

// ==================== FILE HANDLING ====================

/**
 * Upload file
 */
function uploadFile($file, $path, $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'], $max_size = 5242880) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Lỗi upload file'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_ext)) {
        return ['success' => false, 'message' => 'File không đúng định dạng'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File quá lớn'];
    }
    
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $filepath = $path . $filename;
    
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Không thể lưu file'];
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get file size readable
 */
function getFileSize($filepath) {
    if (!file_exists($filepath)) return 'N/A';
    
    $bytes = filesize($filepath);
    $units = ['B', 'KB', 'MB', 'GB'];
    
    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

// ==================== VIETNAMESE TEXT ====================

/**
 * Chuyển tiếng Việt có dấu thành không dấu
 */
function removeVietnameseTones($str) {
    $vietnamese = [
        'à', 'á', 'ạ', 'ả', 'ã', 'â', 'ầ', 'ấ', 'ậ', 'ẩ', 'ẫ', 'ă', 'ằ', 'ắ', 'ặ', 'ẳ', 'ẵ',
        'è', 'é', 'ẹ', 'ẻ', 'ẽ', 'ê', 'ề', 'ế', 'ệ', 'ể', 'ễ',
        'ì', 'í', 'ị', 'ỉ', 'ĩ',
        'ò', 'ó', 'ọ', 'ỏ', 'õ', 'ô', 'ồ', 'ố', 'ộ', 'ổ', 'ỗ', 'ơ', 'ờ', 'ớ', 'ợ', 'ở', 'ỡ',
        'ù', 'ú', 'ụ', 'ủ', 'ũ', 'ư', 'ừ', 'ứ', 'ự', 'ử', 'ữ',
        'ỳ', 'ý', 'ỵ', 'ỷ', 'ỹ',
        'đ'
    ];
    
    $english = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'd'
    ];
    
    return str_replace($vietnamese, $english, $str);
}

/**
 * Tạo slug từ tiếng Việt
 */
function createSlug($str) {
    $str = removeVietnameseTones($str);
    $str = strtolower($str);
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return trim($str, '-');
}

// ==================== ACTIVITY LOG ====================

/**
 * Ghi log hoạt động
 */
function logActivity($user_id, $action, $table_name = null, $record_id = null, $description = null) {
    global $db;
    
    try {
        $query = "INSERT INTO activity_logs (user_id, action, table_name, record_id, description, ip_address, user_agent) 
                  VALUES (:user_id, :action, :table_name, :record_id, :description, :ip_address, :user_agent)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':action' => $action,
            ':table_name' => $table_name,
            ':record_id' => $record_id,
            ':description' => $description,
            ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (Exception $e) {
        // Silent fail - không làm gián đoạn chương trình
    }
}

// ==================== ARRAY & STRING ====================

/**
 * Lấy phần tử đầu tiên của array
 */
function arrayFirst($array) {
    return reset($array);
}

/**
 * Lấy phần tử cuối của array
 */
function arrayLast($array) {
    return end($array);
}

/**
 * Kiểm tra chuỗi bắt đầu bằng
 */
function startsWith($haystack, $needle) {
    return strpos($haystack, $needle) === 0;
}

/**
 * Kiểm tra chuỗi kết thúc bằng
 */
function endsWith($haystack, $needle) {
    return substr($haystack, -strlen($needle)) === $needle;
}

// ==================== DEBUG ====================

/**
 * Debug - dump and die
 */
function dd($var) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    die();
}

/**
 * Debug - print_r and die
 */
function dump($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}