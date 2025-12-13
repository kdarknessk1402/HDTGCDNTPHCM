<?php
/**
 * Controller: Admin Trình độ
 * Quản lý trình độ chuyên môn (Admin only)
 */

require_once __DIR__ . '/../models/TrinhDo.php';

class AdminTrinhDoController {
    private $db;
    private $trinhDoModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->trinhDoModel = new TrinhDo($db);
        
        // Kiểm tra quyền Admin
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập trang này!');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách trình độ
     */
    public function index() {
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        $trinh_do_list = $this->trinhDoModel->getAll($is_active);
        
        $pageTitle = 'Quản lý Trình độ Chuyên môn';
        require_once __DIR__ . '/../views/admin/trinh_do/index.php';
    }
    
    /**
     * Thêm trình độ mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                if ($this->trinhDoModel->checkDuplicateMa($_POST['ma_trinh_do'])) {
                    $errors[] = 'Mã trình độ đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_trinh_do' => trim($_POST['ma_trinh_do']),
                    'ten_trinh_do' => trim($_POST['ten_trinh_do']),
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                if ($this->trinhDoModel->create($data)) {
                    setFlashMessage('success', 'Thêm trình độ thành công!');
                    redirect('/admin/trinh-do');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $pageTitle = 'Thêm Trình độ Mới';
        require_once __DIR__ . '/../views/admin/trinh_do/create.php';
    }
    
    /**
     * Sửa trình độ
     */
    public function edit($id) {
        $trinh_do = $this->trinhDoModel->getById($id);
        
        if (!$trinh_do) {
            setFlashMessage('error', 'Không tìm thấy trình độ!');
            redirect('/admin/trinh-do');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                if ($this->trinhDoModel->checkDuplicateMa($_POST['ma_trinh_do'], $id)) {
                    $errors[] = 'Mã trình độ đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_trinh_do' => trim($_POST['ma_trinh_do']),
                    'ten_trinh_do' => trim($_POST['ten_trinh_do']),
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->trinhDoModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật trình độ thành công!');
                    redirect('/admin/trinh-do');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $pageTitle = 'Sửa Trình độ';
        require_once __DIR__ . '/../views/admin/trinh_do/edit.php';
    }
    
    /**
     * Xóa trình độ
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/trinh-do');
            return;
        }
        
        if (!$this->trinhDoModel->getById($id)) {
            setFlashMessage('error', 'Không tìm thấy trình độ!');
            redirect('/admin/trinh-do');
            return;
        }
        
        if ($this->trinhDoModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa vì đang có Giảng viên hoặc Đơn giá sử dụng!');
            redirect('/admin/trinh-do');
            return;
        }
        
        if ($this->trinhDoModel->delete($id)) {
            setFlashMessage('success', 'Xóa trình độ thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/trinh-do');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $trinh_do = $this->trinhDoModel->getById($id);
        
        if (!$trinh_do) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy!']);
            return;
        }
        
        $is_active = $trinh_do['is_active'] == 1 ? 0 : 1;
        
        if ($this->trinhDoModel->updateStatus($id, $is_active, getUserId())) {
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
        
        if (empty($data['ma_trinh_do'])) {
            $errors[] = 'Vui lòng nhập mã trình độ!';
        } elseif (strlen($data['ma_trinh_do']) > 20) {
            $errors[] = 'Mã trình độ tối đa 20 ký tự!';
        }
        
        if (empty($data['ten_trinh_do'])) {
            $errors[] = 'Vui lòng nhập tên trình độ!';
        } elseif (strlen($data['ten_trinh_do']) > 50) {
            $errors[] = 'Tên trình độ tối đa 50 ký tự!';
        }
        
        if (!isset($data['thu_tu']) || $data['thu_tu'] < 0) {
            $errors[] = 'Thứ tự phải >= 0!';
        }
        
        return $errors;
    }
}