<?php
/**
 * Controller: Admin Nghề
 * Quản lý các nghề đào tạo (Admin only)
 */

require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/Khoa.php';

class AdminNgheController {
    private $db;
    private $ngheModel;
    private $khoaModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->ngheModel = new Nghe($db);
        $this->khoaModel = new Khoa($db);
        
        // Kiểm tra quyền Admin
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập trang này!');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách nghề
     */
    public function index() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        // Lấy danh sách nghề
        $nghe_list = $this->ngheModel->getAll($khoa_id, $is_active);
        
        // Lấy danh sách khoa cho filter
        $khoa_list = $this->khoaModel->getAll(1);
        
        // Hiển thị view
        $pageTitle = 'Quản lý Nghề';
        require_once __DIR__ . '/../views/admin/nghe/index.php';
    }
    
    /**
     * Thêm nghề mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate dữ liệu
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                // Kiểm tra trùng mã
                if ($this->ngheModel->checkDuplicateMa($_POST['ma_nghe'], $_POST['khoa_id'])) {
                    $errors[] = 'Mã nghề đã tồn tại trong khoa này!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'khoa_id' => $_POST['khoa_id'],
                    'ma_nghe' => trim($_POST['ma_nghe']),
                    'ten_nghe' => trim($_POST['ten_nghe']),
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'so_nam_dao_tao' => (int)$_POST['so_nam_dao_tao'],
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                $nghe_id = $this->ngheModel->create($data);
                
                if ($nghe_id) {
                    setFlashMessage('success', 'Thêm nghề thành công!');
                    redirect('/admin/nghe');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi thêm nghề!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách khoa
        $khoa_list = $this->khoaModel->getAll(1);
        
        // Hiển thị form
        $pageTitle = 'Thêm Nghề Mới';
        require_once __DIR__ . '/../views/admin/nghe/create.php';
    }
    
    /**
     * Sửa nghề
     */
    public function edit($id) {
        $nghe = $this->ngheModel->getById($id);
        
        if (!$nghe) {
            setFlashMessage('error', 'Không tìm thấy nghề!');
            redirect('/admin/nghe');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate dữ liệu
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                // Kiểm tra trùng mã
                if ($this->ngheModel->checkDuplicateMa($_POST['ma_nghe'], $_POST['khoa_id'], $id)) {
                    $errors[] = 'Mã nghề đã tồn tại trong khoa này!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'khoa_id' => $_POST['khoa_id'],
                    'ma_nghe' => trim($_POST['ma_nghe']),
                    'ten_nghe' => trim($_POST['ten_nghe']),
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'so_nam_dao_tao' => (int)$_POST['so_nam_dao_tao'],
                    'thu_tu' => (int)$_POST['thu_tu'],
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->ngheModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật nghề thành công!');
                    redirect('/admin/nghe');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi cập nhật nghề!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách khoa
        $khoa_list = $this->khoaModel->getAll(1);
        
        // Hiển thị form
        $pageTitle = 'Sửa Nghề';
        require_once __DIR__ . '/../views/admin/nghe/edit.php';
    }
    
    /**
     * Xóa nghề
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/nghe');
            return;
        }
        
        $nghe = $this->ngheModel->getById($id);
        
        if (!$nghe) {
            setFlashMessage('error', 'Không tìm thấy nghề!');
            redirect('/admin/nghe');
            return;
        }
        
        // Kiểm tra ràng buộc
        if ($this->ngheModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa nghề này vì đang có dữ liệu liên quan (Niên khóa, Lớp, Môn học, Hợp đồng)!');
            redirect('/admin/nghe');
            return;
        }
        
        if ($this->ngheModel->delete($id)) {
            setFlashMessage('success', 'Xóa nghề thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra khi xóa nghề!');
        }
        
        redirect('/admin/nghe');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $nghe = $this->ngheModel->getById($id);
        
        if (!$nghe) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy nghề!']);
            return;
        }
        
        $is_active = $nghe['is_active'] == 1 ? 0 : 1;
        
        if ($this->ngheModel->updateStatus($id, $is_active, getUserId())) {
            $status_text = $is_active == 1 ? 'Hoạt động' : 'Ngừng';
            echo json_encode([
                'success' => true, 
                'message' => 'Cập nhật trạng thái thành công!',
                'new_status' => $is_active,
                'status_text' => $status_text
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra!']);
        }
    }
    
    /**
     * Validate dữ liệu
     */
    private function validateData($data, $exclude_id = null) {
        $errors = [];
        
        // Kiểm tra khoa
        if (empty($data['khoa_id'])) {
            $errors[] = 'Vui lòng chọn khoa!';
        }
        
        // Kiểm tra mã nghề
        if (empty($data['ma_nghe'])) {
            $errors[] = 'Vui lòng nhập mã nghề!';
        } elseif (strlen($data['ma_nghe']) > 20) {
            $errors[] = 'Mã nghề không được quá 20 ký tự!';
        }
        
        // Kiểm tra tên nghề
        if (empty($data['ten_nghe'])) {
            $errors[] = 'Vui lòng nhập tên nghề!';
        } elseif (strlen($data['ten_nghe']) > 100) {
            $errors[] = 'Tên nghề không được quá 100 ký tự!';
        }
        
        // Kiểm tra số năm đào tạo
        if (empty($data['so_nam_dao_tao']) || $data['so_nam_dao_tao'] < 1 || $data['so_nam_dao_tao'] > 10) {
            $errors[] = 'Số năm đào tạo phải từ 1 đến 10!';
        }
        
        // Kiểm tra thứ tự
        if (!isset($data['thu_tu']) || $data['thu_tu'] < 0) {
            $errors[] = 'Thứ tự phải >= 0!';
        }
        
        return $errors;
    }
    
    /**
     * AJAX: Lấy nghề theo khoa
     */
    public function getByKhoa() {
        if (!isset($_GET['khoa_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu khoa_id']);
            return;
        }
        
        $khoa_id = (int)$_GET['khoa_id'];
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : 1;
        
        $nghe_list = $this->ngheModel->getByKhoa($khoa_id, $is_active);
        
        echo json_encode([
            'success' => true,
            'data' => $nghe_list
        ]);
    }
}