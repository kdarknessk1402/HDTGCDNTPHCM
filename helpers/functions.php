<?php
/**
 * Helper Functions
 * File: /helpers/functions.php
 */

/**
 * Kiểm tra user đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Lấy thông tin user hiện tại từ session
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? '',
        'full_name' => $_SESSION['full_name'] ?? '',
        'email' => $_SESSION['email'] ?? '',
        'phone' => $_SESSION['phone'] ?? '',
        'role_id' => $_SESSION['role_id'] ?? null,
        'role_name' => $_SESSION['role_name'] ?? '',
        'role_display_name' => $_SESSION['role_display_name'] ?? '',
        'khoa_id' => $_SESSION['khoa_id'] ?? null,
        'ten_khoa' => $_SESSION['ten_khoa'] ?? null
    ];
}

/**
 * Kiểm tra user có role cụ thể không
 * @param string|array $roles - Tên role hoặc mảng các role
 */
function hasRole($roles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = $_SESSION['role_name'] ?? '';
    
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    
    return $userRole === $roles;
}

/**
 * Kiểm tra user có phải Admin không
 */
function isAdmin() {
    return hasRole('Admin');
}

/**
 * Kiểm tra user có phải Phòng Đào tạo không
 */
function isPhongDaoTao() {
    return hasRole('Phong_Dao_Tao');
}

/**
 * Kiểm tra user có phải Trưởng Khoa không
 */
function isTruongKhoa() {
    return hasRole('Truong_Khoa');
}

/**
 * Kiểm tra user có phải Giáo vụ không
 */
function isGiaoVu() {
    return hasRole('Giao_Vu');
}

/**
 * Set flash message
 */
function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = [
        'message' => $message,
        'type' => $type // success, danger, warning, info
    ];
}

/**
 * Get và xóa flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    // Kiểm tra cả success và error từ session cũ
    if (isset($_SESSION['success'])) {
        $message = ['message' => $_SESSION['success'], 'type' => 'success'];
        unset($_SESSION['success']);
        return $message;
    }
    
    if (isset($_SESSION['error'])) {
        $message = ['message' => $_SESSION['error'], 'type' => 'danger'];
        unset($_SESSION['error']);
        return $message;
    }
    
    return null;
}

/**
 * Redirect
 */
function redirect($url) {
    if (strpos($url, 'http') !== 0) {
        $url = BASE_URL . '/' . ltrim($url, '/');
    }
    header('Location: ' . $url);
    exit;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Format số tiền VND
 */
function formatMoney($amount) {
    return number_format($amount, 0, ',', '.') . 'đ';
}

/**
 * Format ngày tháng
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) {
        return '';
    }
    
    try {
        $dt = new DateTime($date);
        return $dt->format($format);
    } catch (Exception $e) {
        return $date;
    }
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return formatDate($datetime, $format);
}

/**
 * Chuyển số thành chữ tiếng Việt
 */
function numberToVietnameseWords($number) {
    $number = (int)$number;
    
    if ($number == 0) {
        return 'Không đồng';
    }
    
    $units = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
    $levels = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ', 'triệu tỷ'];
    
    $result = [];
    $level = 0;
    
    while ($number > 0) {
        $group = $number % 1000;
        
        if ($group > 0) {
            $groupWords = convertGroupToWords($group, $units);
            if ($level > 0) {
                $groupWords .= ' ' . $levels[$level];
            }
            array_unshift($result, $groupWords);
        }
        
        $number = (int)($number / 1000);
        $level++;
    }
    
    $text = implode(' ', $result);
    $text = ucfirst(trim($text));
    
    return $text . ' đồng';
}

/**
 * Chuyển nhóm 3 chữ số thành chữ
 */
function convertGroupToWords($number, $units) {
    $result = '';
    
    $hundreds = (int)($number / 100);
    $tens = (int)(($number % 100) / 10);
    $ones = $number % 10;
    
    if ($hundreds > 0) {
        $result .= $units[$hundreds] . ' trăm';
        if ($tens == 0 && $ones > 0) {
            $result .= ' lẻ';
        }
    }
    
    if ($tens > 1) {
        $result .= ' ' . $units[$tens] . ' mươi';
        if ($ones == 1) {
            $result .= ' mốt';
        } else if ($ones > 1) {
            $result .= ' ' . $units[$ones];
        }
    } else if ($tens == 1) {
        $result .= ' mười';
        if ($ones > 0) {
            $result .= ' ' . $units[$ones];
        }
    } else if ($ones > 0) {
        $result .= ' ' . $units[$ones];
    }
    
    return trim($result);
}

/**
 * Generate random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (VN)
 */
function isValidPhone($phone) {
    // Remove spaces and special characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if starts with 0 and has 10 digits
    return preg_match('/^0[0-9]{9}$/', $phone);
}

/**
 * Get current school year
 */
function getCurrentSchoolYear() {
    $currentMonth = (int)date('n');
    $currentYear = (int)date('Y');
    
    if ($currentMonth >= 8) {
        // Từ tháng 8 trở đi: năm học mới
        return $currentYear . '-' . ($currentYear + 1);
    } else {
        // Từ tháng 1-7: năm học trước
        return ($currentYear - 1) . '-' . $currentYear;
    }
}

/**
 * Get academic semester
 */
function getCurrentSemester() {
    $currentMonth = (int)date('n');
    
    if ($currentMonth >= 8 || $currentMonth <= 12) {
        return 1; // Học kỳ 1
    } else {
        return 2; // Học kỳ 2
    }
}

/**
 * Debug helper - Dump and die
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    die();
}

/**
 * Debug helper - Dump
 */
function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Get file extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function isImage($filename) {
    $ext = getFileExtension($filename);
    return in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

/**
 * Check if file is PDF
 */
function isPDF($filename) {
    return getFileExtension($filename) === 'pdf';
}

/**
 * Check if file is Excel
 */
function isExcel($filename) {
    $ext = getFileExtension($filename);
    return in_array($ext, ['xls', 'xlsx', 'csv']);
}

/**
 * Check if file is Word
 */
function isWord($filename) {
    $ext = getFileExtension($filename);
    return in_array($ext, ['doc', 'docx']);
}

/**
 * Generate unique filename
 */
function generateUniqueFilename($originalFilename) {
    $ext = getFileExtension($originalFilename);
    $basename = pathinfo($originalFilename, PATHINFO_FILENAME);
    $basename = sanitizeFilename($basename);
    
    return $basename . '_' . time() . '_' . generateRandomString(6) . '.' . $ext;
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    // Remove Vietnamese characters
    $filename = removeVietnamese($filename);
    
    // Remove special characters
    $filename = preg_replace('/[^a-zA-Z0-9_-]/', '_', $filename);
    
    // Remove multiple underscores
    $filename = preg_replace('/_+/', '_', $filename);
    
    return trim($filename, '_');
}

/**
 * Remove Vietnamese characters
 */
function removeVietnamese($str) {
    $vietnamese = [
        'á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ',
        'đ',
        'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ',
        'í', 'ì', 'ỉ', 'ĩ', 'ị',
        'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ',
        'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự',
        'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ',
        'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ',
        'Đ',
        'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ',
        'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị',
        'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ',
        'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự',
        'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ'
    ];
    
    $latin = [
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'd',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'i', 'i', 'i', 'i', 'i',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y',
        'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A',
        'D',
        'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E',
        'I', 'I', 'I', 'I', 'I',
        'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O',
        'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U',
        'Y', 'Y', 'Y', 'Y', 'Y'
    ];
    
    return str_replace($vietnamese, $latin, $str);
}

/**
 * Get file size in human readable format
 */
function humanFileSize($bytes, $decimals = 2) {
    $size = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
}

/**
 * Truncate string
 */
function truncate($string, $length = 100, $append = '...') {
    $string = trim($string);
    
    if (strlen($string) > $length) {
        $string = wordwrap($string, $length);
        $string = explode("\n", $string, 2);
        $string = $string[0] . $append;
    }
    
    return $string;
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $string = removeVietnamese($string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = trim($string, '-');
    
    return $string;
}

/**
 * Get client IP address
 */
function getClientIP() {
    $ipaddress = '';
    
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    
    return $ipaddress;
}

/**
 * Get user agent
 */
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
}

/**
 * Log activity
 */
function logActivity($action, $table_name, $record_id, $old_data = null, $new_data = null) {
    try {
        $db = Database::getInstance()->getConnection();
        
        $sql = "INSERT INTO activity_logs (
                    user_id, 
                    table_name, 
                    record_id, 
                    action, 
                    old_data, 
                    new_data, 
                    ip_address, 
                    user_agent
                ) VALUES (
                    :user_id,
                    :table_name,
                    :record_id,
                    :action,
                    :old_data,
                    :new_data,
                    :ip_address,
                    :user_agent
                )";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':user_id' => $_SESSION['user_id'] ?? null,
            ':table_name' => $table_name,
            ':record_id' => $record_id,
            ':action' => $action,
            ':old_data' => $old_data ? json_encode($old_data) : null,
            ':new_data' => $new_data ? json_encode($new_data) : null,
            ':ip_address' => getClientIP(),
            ':user_agent' => getUserAgent()
        ]);
        
        return true;
    } catch (Exception $e) {
        error_log('Log activity error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Check if request is AJAX
 */
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get months in Vietnamese
 */
function getVietnameseMonths() {
    return [
        1 => 'Tháng 1',
        2 => 'Tháng 2',
        3 => 'Tháng 3',
        4 => 'Tháng 4',
        5 => 'Tháng 5',
        6 => 'Tháng 6',
        7 => 'Tháng 7',
        8 => 'Tháng 8',
        9 => 'Tháng 9',
        10 => 'Tháng 10',
        11 => 'Tháng 11',
        12 => 'Tháng 12'
    ];
}

/**
 * Get years for dropdown (current year ± 5)
 */
function getYears($range = 5) {
    $currentYear = (int)date('Y');
    $years = [];
    
    for ($i = -$range; $i <= $range; $i++) {
        $year = $currentYear + $i;
        $years[$year] = $year;
    }
    
    return $years;
}