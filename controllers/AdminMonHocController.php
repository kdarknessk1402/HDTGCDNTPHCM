<?php
/**
 * Controller: Admin Môn học
 * File: controllers/AdminMonHocController.php
 * Quản lý môn học (Admin only)
 */

require_once __DIR__ . '/../models/MonHoc.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/Lop.php';

class AdminMonHocController {
    private $db;
    private $monHocModel;
    private $khoaModel;
    private $ngheModel;
    private $lopModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->monHocModel = new MonHoc($db);
        $this->khoaModel = new Khoa($db);
        $this->ngheModel = new Nghe($db);
        $this->lopModel = new Lop($db);
        
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập!');
            redirect('/');
            exit;
        }
    }
    
    public function index() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : null;
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : null;
        $lop_id = isset($_GET['lop_id']) ? (int)$_GET['lop_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        $mon_hoc_list = $this->monHocModel->getAll($khoa_id, $nghe_id, $lop_id, $is_active);
        
        $khoa_list = $this->khoaModel->getAll(1);
        $nghe_list = $this->ngheModel->getAll(null, 1);
        $lop_list = $this->lopModel->getAll(null, null, null, 1);
        
        $pageTitle = 'Quản lý Môn học';
        require_once __DIR__ . '/../views/admin/mon_hoc/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                if ($this->monHocModel->checkDuplicateMa($_POST['ma_mon_hoc'])) {
                    $errors[] = 'Mã môn học đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_mon_hoc' => trim($_POST['ma_mon_hoc']),
                    'ten_mon_hoc' => trim($_POST['ten_mon_hoc']),
                    'lop_id' => $_POST['lop_id'],
                    'so_tin_chi' => (int)$_POST['so_tin_chi'],
                    'tong_so_tiet' => (int)$_POST['tong_so_tiet'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                if ($this->monHocModel->create($data)) {
                    setFlashMessage('success', 'Thêm môn học thành công!');
                    redirect('/admin/mon-hoc');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        
        $pageTitle = 'Thêm Môn học Mới';
        require_once __DIR__ . '/../views/admin/mon_hoc/create.php';
    }
    
    public function edit($id) {
        $mon_hoc = $this->monHocModel->getById($id);
        
        if (!$mon_hoc) {
            setFlashMessage('error', 'Không tìm thấy môn học!');
            redirect('/admin/mon-hoc');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                if ($this->monHocModel->checkDuplicateMa($_POST['ma_mon_hoc'], $id)) {
                    $errors[] = 'Mã môn học đã tồn tại!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'ma_mon_hoc' => trim($_POST['ma_mon_hoc']),
                    'ten_mon_hoc' => trim($_POST['ten_mon_hoc']),
                    'lop_id' => $_POST['lop_id'],
                    'so_tin_chi' => (int)$_POST['so_tin_chi'],
                    'tong_so_tiet' => (int)$_POST['tong_so_tiet'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->monHocModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật môn học thành công!');
                    redirect('/admin/mon-hoc');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        
        $pageTitle = 'Sửa Môn học';
        require_once __DIR__ . '/../views/admin/mon_hoc/edit.php';
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/mon-hoc');
            return;
        }
        
        if (!$this->monHocModel->getById($id)) {
            setFlashMessage('error', 'Không tìm thấy môn học!');
            redirect('/admin/mon-hoc');
            return;
        }
        
        if ($this->monHocModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa vì đang có Hợp đồng!');
            redirect('/admin/mon-hoc');
            return;
        }
        
        if ($this->monHocModel->delete($id)) {
            setFlashMessage('success', 'Xóa môn học thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/mon-hoc');
    }
    
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $mon_hoc = $this->monHocModel->getById($id);
        
        if (!$mon_hoc) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy!']);
            return;
        }
        
        $is_active = $mon_hoc['is_active'] == 1 ? 0 : 1;
        
        if ($this->monHocModel->updateStatus($id, $is_active, getUserId())) {
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
    
    public function getLopByNghe() {
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : 0;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        if (!$nghe_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu nghe_id']);
            return;
        }
        
        $lop_list = $this->lopModel->getAll(null, $nghe_id, null, $is_active);
        
        echo json_encode([
            'success' => true,
            'data' => $lop_list
        ]);
    }
    
    private function validateData($data, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['ma_mon_hoc'])) {
            $errors[] = 'Vui lòng nhập mã môn học!';
        } elseif (strlen($data['ma_mon_hoc']) > 20) {
            $errors[] = 'Mã môn học tối đa 20 ký tự!';
        }
        
        if (empty($data['ten_mon_hoc'])) {
            $errors[] = 'Vui lòng nhập tên môn học!';
        } elseif (strlen($data['ten_mon_hoc']) > 100) {
            $errors[] = 'Tên môn học tối đa 100 ký tự!';
        }
        
        if (empty($data['lop_id'])) {
            $errors[] = 'Vui lòng chọn lớp!';
        }
        
        if (!isset($data['so_tin_chi']) || $data['so_tin_chi'] < 0) {
            $errors[] = 'Số tín chỉ phải >= 0!';
        } elseif ($data['so_tin_chi'] > 10) {
            $errors[] = 'Số tín chỉ không được quá 10!';
        }
        
        if (!isset($data['tong_so_tiet']) || $data['tong_so_tiet'] <= 0) {
            $errors[] = 'Tổng số tiết phải > 0!';
        } elseif ($data['tong_so_tiet'] > 500) {
            $errors[] = 'Tổng số tiết không được quá 500!';
        }
        
        return $errors;
    }
}