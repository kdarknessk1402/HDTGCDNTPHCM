<?php
/**
 * Controller: Admin Giảng viên
 * File: controllers/AdminGiangVienController.php
 * Quản lý giảng viên thỉnh giảng (Admin only)
 */

require_once __DIR__ . '/../models/GiangVien.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/TrinhDo.php';

class AdminGiangVienController {
    private $db;
    private $giangVienModel;
    private $khoaModel;
    private $trinhDoModel;
    private $uploadPath = __DIR__ . '/../../uploads/giang_vien/';
    
    public function __construct($db) {
        $this->db = $db;
        $this->giangVienModel = new GiangVien($db);
        $this->khoaModel = new Khoa($db);
        $this->trinhDoModel = new TrinhDo($db);
        
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập!');
            redirect('/');
            exit;
        }
        
        // Tạo thư mục upload nếu chưa có
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    public function index() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : null;
        $trinh_do_id = isset($_GET['trinh_do_id']) ? (int)$_GET['trinh_do_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        $giang_vien_list = $this->giangVienModel->getAll($khoa_id, $trinh_do_id, $is_active);
        
        $khoa_list = $this->khoaModel->getAll(1);
        $trinh_do_list = $this->trinhDoModel->getAll(1);
        
        $pageTitle = 'Quản lý Giảng viên';
        require_once __DIR__ . '/../views/admin/giang_vien/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $_FILES);
            
            if (empty($errors)) {
                // Upload files
                $files = $this->handleFileUploads($_FILES);
                
                $data = [
                    'khoa_id' => $_POST['khoa_id'],
                    'ma_giang_vien' => trim($_POST['ma_giang_vien']),
                    'ten_giang_vien' => trim($_POST['ten_giang_vien']),
                    'nam_sinh' => !empty($_POST['nam_sinh']) ? $_POST['nam_sinh'] : null,
                    'gioi_tinh' => $_POST['gioi_tinh'],
                    'ngay_sinh' => !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null,
                    'noi_sinh' => trim($_POST['noi_sinh'] ?? ''),
                    'so_cccd' => trim($_POST['so_cccd'] ?? ''),
                    'ngay_cap_cccd' => !empty($_POST['ngay_cap_cccd']) ? $_POST['ngay_cap_cccd'] : null,
                    'noi_cap_cccd' => trim($_POST['noi_cap_cccd'] ?? 'Cục Cảnh sát Quản lý Hành chính về Trật tự xã hội'),
                    'trinh_do_id' => !empty($_POST['trinh_do_id']) ? $_POST['trinh_do_id'] : null,
                    'chuyen_nganh_dao_tao' => trim($_POST['chuyen_nganh_dao_tao'] ?? ''),
                    'truong_dao_tao' => trim($_POST['truong_dao_tao'] ?? ''),
                    'nam_tot_nghiep' => !empty($_POST['nam_tot_nghiep']) ? $_POST['nam_tot_nghiep'] : null,
                    'chung_chi_su_pham' => trim($_POST['chung_chi_su_pham'] ?? ''),
                    'dia_chi' => trim($_POST['dia_chi'] ?? ''),
                    'dia_chi_tam_tru' => trim($_POST['dia_chi_tam_tru'] ?? ''),
                    'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'so_tai_khoan' => trim($_POST['so_tai_khoan'] ?? ''),
                    'ten_ngan_hang' => trim($_POST['ten_ngan_hang'] ?? ''),
                    'chi_nhanh_ngan_hang' => trim($_POST['chi_nhanh_ngan_hang'] ?? ''),
                    'chu_tai_khoan' => trim($_POST['chu_tai_khoan'] ?? ''),
                    'ma_so_thue' => trim($_POST['ma_so_thue'] ?? ''),
                    'file_cccd' => $files['file_cccd'] ?? null,
                    'file_bang_cap' => $files['file_bang_cap'] ?? null,
                    'file_chung_chi' => $files['file_chung_chi'] ?? null,
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                if ($this->giangVienModel->create($data)) {
                    setFlashMessage('success', 'Thêm giảng viên thành công!');
                    redirect('/admin/giang-vien');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        $trinh_do_list = $this->trinhDoModel->getAll(1);
        
        $pageTitle = 'Thêm Giảng viên Mới';
        require_once __DIR__ . '/../views/admin/giang_vien/create.php';
    }
    
    public function edit($id) {
        $giang_vien = $this->giangVienModel->getById($id);
        
        if (!$giang_vien) {
            setFlashMessage('error', 'Không tìm thấy giảng viên!');
            redirect('/admin/giang-vien');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $_FILES, $id);
            
            if (empty($errors)) {
                // Upload files mới (nếu có)
                $files = $this->handleFileUploads($_FILES);
                
                $data = [
                    'khoa_id' => $_POST['khoa_id'],
                    'ma_giang_vien' => trim($_POST['ma_giang_vien']),
                    'ten_giang_vien' => trim($_POST['ten_giang_vien']),
                    'nam_sinh' => !empty($_POST['nam_sinh']) ? $_POST['nam_sinh'] : null,
                    'gioi_tinh' => $_POST['gioi_tinh'],
                    'ngay_sinh' => !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null,
                    'noi_sinh' => trim($_POST['noi_sinh'] ?? ''),
                    'so_cccd' => trim($_POST['so_cccd'] ?? ''),
                    'ngay_cap_cccd' => !empty($_POST['ngay_cap_cccd']) ? $_POST['ngay_cap_cccd'] : null,
                    'noi_cap_cccd' => trim($_POST['noi_cap_cccd'] ?? ''),
                    'trinh_do_id' => !empty($_POST['trinh_do_id']) ? $_POST['trinh_do_id'] : null,
                    'chuyen_nganh_dao_tao' => trim($_POST['chuyen_nganh_dao_tao'] ?? ''),
                    'truong_dao_tao' => trim($_POST['truong_dao_tao'] ?? ''),
                    'nam_tot_nghiep' => !empty($_POST['nam_tot_nghiep']) ? $_POST['nam_tot_nghiep'] : null,
                    'chung_chi_su_pham' => trim($_POST['chung_chi_su_pham'] ?? ''),
                    'dia_chi' => trim($_POST['dia_chi'] ?? ''),
                    'dia_chi_tam_tru' => trim($_POST['dia_chi_tam_tru'] ?? ''),
                    'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'so_tai_khoan' => trim($_POST['so_tai_khoan'] ?? ''),
                    'ten_ngan_hang' => trim($_POST['ten_ngan_hang'] ?? ''),
                    'chi_nhanh_ngan_hang' => trim($_POST['chi_nhanh_ngan_hang'] ?? ''),
                    'chu_tai_khoan' => trim($_POST['chu_tai_khoan'] ?? ''),
                    'ma_so_thue' => trim($_POST['ma_so_thue'] ?? ''),
                    'file_cccd' => $files['file_cccd'] ?? $giang_vien['file_cccd'],
                    'file_bang_cap' => $files['file_bang_cap'] ?? $giang_vien['file_bang_cap'],
                    'file_chung_chi' => $files['file_chung_chi'] ?? $giang_vien['file_chung_chi'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->giangVienModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật giảng viên thành công!');
                    redirect('/admin/giang-vien');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        $trinh_do_list = $this->trinhDoModel->getAll(1);
        
        $pageTitle = 'Sửa Giảng viên';
        require_once __DIR__ . '/../views/admin/giang_vien/edit.php';
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/giang-vien');
            return;
        }
        
        if (!$this->giangVienModel->getById($id)) {
            setFlashMessage('error', 'Không tìm thấy giảng viên!');
            redirect('/admin/giang-vien');
            return;
        }
        
        if ($this->giangVienModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa vì đang có Hợp đồng!');
            redirect('/admin/giang-vien');
            return;
        }
        
        if ($this->giangVienModel->delete($id)) {
            setFlashMessage('success', 'Xóa giảng viên thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/giang-vien');
    }
    
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false]);
            return;
        }
        
        $gv = $this->giangVienModel->getById($id);
        if (!$gv) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $is_active = $gv['is_active'] == 1 ? 0 : 1;
        
        if ($this->giangVienModel->updateStatus($id, $is_active, getUserId())) {
            echo json_encode([
                'success' => true,
                'new_status' => $is_active,
                'status_text' => $is_active == 1 ? 'Hoạt động' : 'Ngừng'
            ]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
    
    private function validateData($data, $files, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['ten_giang_vien'])) {
            $errors[] = 'Vui lòng nhập tên giảng viên!';
        }
        
        if (empty($data['khoa_id'])) {
            $errors[] = 'Vui lòng chọn khoa!';
        }
        
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ!';
        }
        
        if (!empty($data['so_dien_thoai']) && !preg_match('/^[0-9]{10,11}$/', $data['so_dien_thoai'])) {
            $errors[] = 'Số điện thoại phải 10-11 chữ số!';
        }
        
        return $errors;
    }
    
    private function handleFileUploads($files) {
        $uploaded = [];
        $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        foreach (['file_cccd', 'file_bang_cap', 'file_chung_chi'] as $field) {
            if (isset($files[$field]) && $files[$field]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files[$field]['name'], PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed_ext)) continue;
                if ($files[$field]['size'] > $max_size) continue;
                
                $filename = uniqid() . '_' . time() . '.' . $ext;
                $filepath = $this->uploadPath . $filename;
                
                if (move_uploaded_file($files[$field]['tmp_name'], $filepath)) {
                    $uploaded[$field] = $filename;
                }
            }
        }
        
        return $uploaded;
    }
}