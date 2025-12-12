<?php
/**
 * Base Controller Class
 * File: /controllers/Controller.php
 */

class Controller {
    
    /**
     * Load model
     */
    protected function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }
    
    /**
     * Load view
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Check if view file exists
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("View not found: $view");
        }
    }
    
    /**
     * Get POST data
     */
    protected function post($key = null, $default = null) {
        if ($key === null) {
            return $_POST;
        }
        
        return $_POST[$key] ?? $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key = null, $default = null) {
        if ($key === null) {
            return $_GET;
        }
        
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Redirect
     */
    protected function redirect($url) {
        if (strpos($url, 'http') !== 0) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        header('Location: ' . $url);
        exit;
    }
    
    /**
     * Set flash message
     */
    protected function setFlash($message, $type = 'success') {
        setFlashMessage($message, $type);
    }
    
    /**
     * Check if user is logged in
     */
    protected function requireAuth() {
        if (!isLoggedIn()) {
            $this->setFlash('Vui lòng đăng nhập để tiếp tục', 'danger');
            $this->redirect('login.php');
        }
    }
    
    /**
     * Check user role
     */
    protected function requireRole($roles) {
        $this->requireAuth();
        
        if (!hasRole($roles)) {
            $this->setFlash('Bạn không có quyền truy cập chức năng này', 'danger');
            $this->redirect('index.php');
        }
    }
    
    /**
     * Validate required fields
     */
    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $ruleArray = explode('|', $rule);
            
            foreach ($ruleArray as $r) {
                // Required
                if ($r === 'required' && empty($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' không được để trống';
                    break;
                }
                
                // Email
                if ($r === 'email' && !empty($data[$field]) && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = ucfirst($field) . ' không đúng định dạng email';
                    break;
                }
                
                // Numeric
                if ($r === 'numeric' && !empty($data[$field]) && !is_numeric($data[$field])) {
                    $errors[$field] = ucfirst($field) . ' phải là số';
                    break;
                }
                
                // Min length
                if (strpos($r, 'min:') === 0) {
                    $min = (int)str_replace('min:', '', $r);
                    if (!empty($data[$field]) && strlen($data[$field]) < $min) {
                        $errors[$field] = ucfirst($field) . " phải có ít nhất $min ký tự";
                        break;
                    }
                }
                
                // Max length
                if (strpos($r, 'max:') === 0) {
                    $max = (int)str_replace('max:', '', $r);
                    if (!empty($data[$field]) && strlen($data[$field]) > $max) {
                        $errors[$field] = ucfirst($field) . " không được vượt quá $max ký tự";
                        break;
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Upload file
     */
    protected function uploadFile($file, $destination, $allowedTypes = []) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Không có file được upload'];
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Lỗi khi upload file'];
        }
        
        // Check file size
        if ($file['size'] > MAX_FILE_SIZE) {
            return ['success' => false, 'message' => 'File quá lớn (tối đa 10MB)'];
        }
        
        // Check file type
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!empty($allowedTypes) && !in_array($ext, $allowedTypes)) {
            return ['success' => false, 'message' => 'Định dạng file không được hỗ trợ'];
        }
        
        // Generate unique filename
        $filename = generateUniqueFilename($file['name']);
        $filepath = $destination . '/' . $filename;
        
        // Create directory if not exists
        if (!file_exists($destination)) {
            mkdir($destination, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'filepath' => $filepath
            ];
        }
        
        return ['success' => false, 'message' => 'Không thể lưu file'];
    }
    
    /**
     * Delete file
     */
    protected function deleteFile($filepath) {
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    /**
     * JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}