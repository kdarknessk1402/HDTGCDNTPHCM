<?php
/**
 * Controller: Admin Lớp
 * File: controllers/AdminLopController.php
 * Quản lý lớp học (Admin only)
 */

require_once __DIR__ . '/../models/Lop.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/NienKhoa.php';

class AdminLopController {
    private $db;
    private $lopModel;
    private $khoaModel;
    private $ngheModel;
    private $nienKhoaModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->lopModel = new Lop($db);
        $this->khoaModel = new Khoa($db);
        $this->ngheModel = new Nghe($db);
        $this->nienKhoaModel = new NienKhoa($db);
        
        // Kiểm tra quyền Admin
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập trang này!');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách lớp
     */
    public function index() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : null;
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : null;
        $nien_khoa_id = isset($_GET['nien_khoa_id']) ? (int)$_GET['nien_khoa_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        $lop_list = $this->lopModel->getAll($khoa_id, $nghe_id, $nien_khoa_id, $is_active);
        
        // Lấy danh sách cho filter
        $khoa_list = $this->khoaModel->getAll(1);
        $nghe_list = $this->ngheModel->getAll(null, 1);
        $nien_khoa_list = $this->nienKhoaModel->getAll(null, null, null, 1);
        $cap_do_list = $this->getCapDoList();
        
        $pageTitle = 'Quản lý Lớp học';
        require_once __DIR__ . '/../views/admin/lop/index.php';
    }
    
    /**
     * Thêm lớp mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                if ($this->lopModel->checkDuplicateMa($_POST['ma_lop'])) {
                    $errors[] = 'Mã lớp đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_lop' => trim($_POST['ma_lop']),
                    'ten_lop' => trim($_POST['ten_lop']),
                    'nghe_id' => $_POST['nghe_id'],
                    'nien_khoa_id' => $_POST['nien_khoa_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'si_so' => (int)$_POST['si_so'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                if ($this->lopModel->create($data)) {
                    setFlashMessage('success', 'Thêm lớp thành công!');
                    redirect('/admin/lop');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $khoa_list = $this->khoaModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        $pageTitle = 'Thêm Lớp Mới';
        require_once __DIR__ . '/../views/admin/lop/create.php';
    }
    
    /**
     * Sửa lớp
     */
    public function edit($id) {
        $lop = $this->lopModel->getById($id);
        
        if (!$lop) {
            setFlashMessage('error', 'Không tìm thấy lớp!');
            redirect('/admin/lop');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                if ($this->lopModel->checkDuplicateMa($_POST['ma_lop'], $id)) {
                    $errors[] = 'Mã lớp đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_lop' => trim($_POST['ma_lop']),
                    'ten_lop' => trim($_POST['ten_lop']),
                    'nghe_id' => $_POST['nghe_id'],
                    'nien_khoa_id' => $_POST['nien_khoa_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'si_so' => (int)$_POST['si_so'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->lopModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật lớp thành công!');
                    redirect('/admin/lop');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $khoa_list = $this->khoaModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        $pageTitle = 'Sửa Lớp';
        require_once __DIR__ . '/../views/admin/lop/edit.php';
    }
    
    /**
     * Xóa lớp
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/lop');
            return;
        }
        
        if (!$this->lopModel->getById($id)) {
            setFlashMessage('error', 'Không tìm thấy lớp!');
            redirect('/admin/lop');
            return;
        }
        
        if ($this->lopModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa vì đang có Môn học hoặc Hợp đồng!');
            redirect('/admin/lop');
            return;
        }
        
        if ($this->lopModel->delete($id)) {
            setFlashMessage('success', 'Xóa lớp thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/lop');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $lop = $this->lopModel->getById($id);
        
        if (!$lop) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy!']);
            return;
        }
        
        $is_active = $lop['is_active'] == 1 ? 0 : 1;
        
        if ($this->lopModel->updateStatus($id, $is_active, getUserId())) {
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
     * AJAX: Lấy nghề theo khoa
     */
    public function getNgheByKhoa() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : 0;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        if (!$khoa_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu khoa_id']);
            return;
        }
        
        $nghe_list = $this->ngheModel->getAll($khoa_id, $is_active);
        
        echo json_encode([
            'success' => true,
            'data' => $nghe_list
        ]);
    }
    
    /**
     * AJAX: Lấy niên khóa theo nghề
     */
    public function getNienKhoaByNghe() {
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : 0;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        if (!$nghe_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu nghe_id']);
            return;
        }
        
        $nien_khoa_list = $this->nienKhoaModel->getAll($nghe_id, null, null, $is_active);
        
        echo json_encode([
            'success' => true,
            'data' => $nien_khoa_list
        ]);
    }
    
    /**
     * Validate
     */
    private function validateData($data, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['ma_lop'])) {
            $errors[] = 'Vui lòng nhập mã lớp!';
        } elseif (strlen($data['ma_lop']) > 20) {
            $errors[] = 'Mã lớp tối đa 20 ký tự!';
        }
        
        if (empty($data['ten_lop'])) {
            $errors[] = 'Vui lòng nhập tên lớp!';
        } elseif (strlen($data['ten_lop']) > 100) {
            $errors[] = 'Tên lớp tối đa 100 ký tự!';
        }
        
        if (empty($data['nghe_id'])) {
            $errors[] = 'Vui lòng chọn nghề!';
        }
        
        if (empty($data['nien_khoa_id'])) {
            $errors[] = 'Vui lòng chọn niên khóa!';
        }
        
        if (empty($data['cap_do_id'])) {
            $errors[] = 'Vui lòng chọn cấp độ!';
        }
        
        if (!isset($data['si_so']) || $data['si_so'] < 0) {
            $errors[] = 'Sĩ số phải >= 0!';
        } elseif ($data['si_so'] > 200) {
            $errors[] = 'Sĩ số không được quá 200!';
        }
        
        return $errors;
    }
    
    /**
     * Lấy danh sách cấp độ
     */
    private function getCapDoList() {
        $query = "SELECT * FROM cap_do_giang_day WHERE is_active = 1 ORDER BY ten_cap_do ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}