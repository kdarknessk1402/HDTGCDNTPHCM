<?php
/**
 * Controller: Admin Cơ sở
 * File: controllers/AdminCoSoController.php
 * Quản lý cơ sở đào tạo (Admin only)
 */

require_once __DIR__ . '/../models/CoSo.php';

class AdminCoSoController {
    private $db;
    private $coSoModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->coSoModel = new CoSo($db);
        
        // Kiểm tra quyền Admin
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập trang này!');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách cơ sở
     */
    public function index() {
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        $co_so_list = $this->coSoModel->getAll($is_active);
        
        $pageTitle = 'Quản lý Cơ sở Đào tạo';
        require_once __DIR__ . '/../views/admin/co_so/index.php';
    }
    
    /**
     * Thêm cơ sở mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                if ($this->coSoModel->checkDuplicateMa($_POST['ma_co_so'])) {
                    $errors[] = 'Mã cơ sở đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_co_so' => trim($_POST['ma_co_so']),
                    'ten_co_so' => trim($_POST['ten_co_so']),
                    'dia_chi' => trim($_POST['dia_chi'] ?? ''),
                    'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'nguoi_phu_trach' => trim($_POST['nguoi_phu_trach'] ?? ''),
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                if ($this->coSoModel->create($data)) {
                    setFlashMessage('success', 'Thêm cơ sở thành công!');
                    redirect('/admin/co-so');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $pageTitle = 'Thêm Cơ sở Mới';
        require_once __DIR__ . '/../views/admin/co_so/create.php';
    }
    
    /**
     * Sửa cơ sở
     */
    public function edit($id) {
        $co_so = $this->coSoModel->getById($id);
        
        if (!$co_so) {
            setFlashMessage('error', 'Không tìm thấy cơ sở!');
            redirect('/admin/co-so');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                if ($this->coSoModel->checkDuplicateMa($_POST['ma_co_so'], $id)) {
                    $errors[] = 'Mã cơ sở đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_co_so' => trim($_POST['ma_co_so']),
                    'ten_co_so' => trim($_POST['ten_co_so']),
                    'dia_chi' => trim($_POST['dia_chi'] ?? ''),
                    'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'nguoi_phu_trach' => trim($_POST['nguoi_phu_trach'] ?? ''),
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->coSoModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật cơ sở thành công!');
                    redirect('/admin/co-so');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $pageTitle = 'Sửa Cơ sở';
        require_once __DIR__ . '/../views/admin/co_so/edit.php';
    }
    
    /**
     * Xóa cơ sở
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/co-so');
            return;
        }
        
        if (!$this->coSoModel->getById($id)) {
            setFlashMessage('error', 'Không tìm thấy cơ sở!');
            redirect('/admin/co-so');
            return;
        }
        
        if ($this->coSoModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa vì đang có Đơn giá hoặc Hợp đồng sử dụng!');
            redirect('/admin/co-so');
            return;
        }
        
        if ($this->coSoModel->delete($id)) {
            setFlashMessage('success', 'Xóa cơ sở thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/co-so');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $co_so = $this->coSoModel->getById($id);
        
        if (!$co_so) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy!']);
            return;
        }
        
        $is_active = $co_so['is_active'] == 1 ? 0 : 1;
        
        if ($this->coSoModel->updateStatus($id, $is_active, getUserId())) {
            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật thành công!',
                'new_status' => $is_active,
                'status_text' => $is_active == 1 ? 'Hoạt động' : 'Ngừng'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi!']);
        }
    }
    
    /**
     * Validate
     */
    private function validateData($data, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['ma_co_so'])) {
            $errors[] = 'Vui lòng nhập mã cơ sở!';
        } elseif (strlen($data['ma_co_so']) > 20) {
            $errors[] = 'Mã cơ sở tối đa 20 ký tự!';
        }
        
        if (empty($data['ten_co_so'])) {
            $errors[] = 'Vui lòng nhập tên cơ sở!';
        } elseif (strlen($data['ten_co_so']) > 100) {
            $errors[] = 'Tên cơ sở tối đa 100 ký tự!';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        }
        
        if (!empty($data['so_dien_thoai']) && !preg_match('/^[0-9]{10,11}$/', $data['so_dien_thoai'])) {
            $errors[] = 'Số điện thoại phải 10-11 chữ số!';
        }
        
        if (!isset($data['thu_tu']) || $data['thu_tu'] < 0) {
            $errors[] = 'Thứ tự phải >= 0!';
        }
        
        return $errors;
    }
}