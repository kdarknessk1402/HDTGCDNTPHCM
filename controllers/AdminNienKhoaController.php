<?php
/**
 * Controller: Admin Niên khóa
 * Quản lý niên khóa đào tạo (Admin only)
 */

require_once __DIR__ . '/../models/NienKhoa.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/Khoa.php';

class AdminNienKhoaController {
    private $db;
    private $nienKhoaModel;
    private $ngheModel;
    private $khoaModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->nienKhoaModel = new NienKhoa($db);
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
     * Hiển thị danh sách niên khóa
     */
    public function index() {
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : null;
        $cap_do_id = isset($_GET['cap_do_id']) ? (int)$_GET['cap_do_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        // Lấy danh sách niên khóa
        $nien_khoa_list = $this->nienKhoaModel->getAll($nghe_id, $cap_do_id, $is_active);
        
        // Lấy danh sách khoa và cấp độ cho filter
        $khoa_list = $this->khoaModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        // Hiển thị view
        $pageTitle = 'Quản lý Niên khóa';
        require_once __DIR__ . '/../views/admin/nien_khoa/index.php';
    }
    
    /**
     * Thêm niên khóa mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                // Kiểm tra trùng
                if ($this->nienKhoaModel->checkDuplicate(
                    $_POST['nghe_id'], 
                    $_POST['cap_do_id'], 
                    $_POST['ten_nien_khoa']
                )) {
                    $errors[] = 'Niên khóa này đã tồn tại cho nghề và cấp độ đã chọn!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'nghe_id' => $_POST['nghe_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'ma_nien_khoa' => trim($_POST['ma_nien_khoa']),
                    'ten_nien_khoa' => trim($_POST['ten_nien_khoa']),
                    'nam_bat_dau' => (int)$_POST['nam_bat_dau'],
                    'nam_ket_thuc' => (int)$_POST['nam_ket_thuc'],
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                $nien_khoa_id = $this->nienKhoaModel->create($data);
                
                if ($nien_khoa_id) {
                    setFlashMessage('success', 'Thêm niên khóa thành công!');
                    redirect('/admin/nien-khoa');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi thêm niên khóa!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $khoa_list = $this->khoaModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        // Hiển thị form
        $pageTitle = 'Thêm Niên khóa Mới';
        require_once __DIR__ . '/../views/admin/nien_khoa/create.php';
    }
    
    /**
     * Sửa niên khóa
     */
    public function edit($id) {
        $nien_khoa = $this->nienKhoaModel->getById($id);
        
        if (!$nien_khoa) {
            setFlashMessage('error', 'Không tìm thấy niên khóa!');
            redirect('/admin/nien-khoa');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                // Kiểm tra trùng
                if ($this->nienKhoaModel->checkDuplicate(
                    $_POST['nghe_id'], 
                    $_POST['cap_do_id'], 
                    $_POST['ten_nien_khoa'],
                    $id
                )) {
                    $errors[] = 'Niên khóa này đã tồn tại cho nghề và cấp độ đã chọn!';
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'nghe_id' => $_POST['nghe_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'ma_nien_khoa' => trim($_POST['ma_nien_khoa']),
                    'ten_nien_khoa' => trim($_POST['ten_nien_khoa']),
                    'nam_bat_dau' => (int)$_POST['nam_bat_dau'],
                    'nam_ket_thuc' => (int)$_POST['nam_ket_thuc'],
                    'mo_ta' => trim($_POST['mo_ta'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->nienKhoaModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật niên khóa thành công!');
                    redirect('/admin/nien-khoa');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi cập nhật niên khóa!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $khoa_list = $this->khoaModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        // Hiển thị form
        $pageTitle = 'Sửa Niên khóa';
        require_once __DIR__ . '/../views/admin/nien_khoa/edit.php';
    }
    
    /**
     * Xóa niên khóa
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/nien-khoa');
            return;
        }
        
        $nien_khoa = $this->nienKhoaModel->getById($id);
        
        if (!$nien_khoa) {
            setFlashMessage('error', 'Không tìm thấy niên khóa!');
            redirect('/admin/nien-khoa');
            return;
        }
        
        // Kiểm tra ràng buộc
        if ($this->nienKhoaModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa niên khóa này vì đang có dữ liệu liên quan (Lớp, Môn học, Hợp đồng)!');
            redirect('/admin/nien-khoa');
            return;
        }
        
        if ($this->nienKhoaModel->delete($id)) {
            setFlashMessage('success', 'Xóa niên khóa thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra khi xóa niên khóa!');
        }
        
        redirect('/admin/nien-khoa');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $nien_khoa = $this->nienKhoaModel->getById($id);
        
        if (!$nien_khoa) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy niên khóa!']);
            return;
        }
        
        $is_active = $nien_khoa['is_active'] == 1 ? 0 : 1;
        
        if ($this->nienKhoaModel->updateStatus($id, $is_active, getUserId())) {
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
        
        // Kiểm tra nghề
        if (empty($data['nghe_id'])) {
            $errors[] = 'Vui lòng chọn nghề!';
        }
        
        // Kiểm tra cấp độ
        if (empty($data['cap_do_id'])) {
            $errors[] = 'Vui lòng chọn cấp độ!';
        }
        
        // Kiểm tra mã niên khóa
        if (empty($data['ma_nien_khoa'])) {
            $errors[] = 'Vui lòng nhập mã niên khóa!';
        } elseif (strlen($data['ma_nien_khoa']) > 20) {
            $errors[] = 'Mã niên khóa không được quá 20 ký tự!';
        }
        
        // Kiểm tra tên niên khóa
        if (empty($data['ten_nien_khoa'])) {
            $errors[] = 'Vui lòng nhập tên niên khóa!';
        } elseif (strlen($data['ten_nien_khoa']) > 50) {
            $errors[] = 'Tên niên khóa không được quá 50 ký tự!';
        }
        
        // Kiểm tra năm bắt đầu
        if (empty($data['nam_bat_dau'])) {
            $errors[] = 'Vui lòng nhập năm bắt đầu!';
        } elseif ($data['nam_bat_dau'] < 2000 || $data['nam_bat_dau'] > 2100) {
            $errors[] = 'Năm bắt đầu phải từ 2000 đến 2100!';
        }
        
        // Kiểm tra năm kết thúc
        if (empty($data['nam_ket_thuc'])) {
            $errors[] = 'Vui lòng nhập năm kết thúc!';
        } elseif ($data['nam_ket_thuc'] < 2000 || $data['nam_ket_thuc'] > 2100) {
            $errors[] = 'Năm kết thúc phải từ 2000 đến 2100!';
        }
        
        // Kiểm tra logic năm
        if (!empty($data['nam_bat_dau']) && !empty($data['nam_ket_thuc'])) {
            if ($data['nam_ket_thuc'] <= $data['nam_bat_dau']) {
                $errors[] = 'Năm kết thúc phải lớn hơn năm bắt đầu!';
            }
            
            $khoang_cach = $data['nam_ket_thuc'] - $data['nam_bat_dau'];
            if ($khoang_cach > 10) {
                $errors[] = 'Khoảng cách giữa năm bắt đầu và kết thúc không được quá 10 năm!';
            }
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
    
    /**
     * AJAX: Lấy niên khóa theo nghề
     */
    public function getByNghe() {
        if (!isset($_GET['nghe_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu nghe_id']);
            return;
        }
        
        $nghe_id = (int)$_GET['nghe_id'];
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : 1;
        
        $nien_khoa_list = $this->nienKhoaModel->getByNghe($nghe_id, $is_active);
        
        echo json_encode([
            'success' => true,
            'data' => $nien_khoa_list
        ]);
    }
    
    /**
     * Lấy danh sách cấp độ giảng dạy
     */
    private function getCapDoList() {
        $query = "SELECT * FROM cap_do_giang_day WHERE is_active = 1 ORDER BY thu_tu ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}