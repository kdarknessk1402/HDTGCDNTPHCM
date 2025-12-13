<?php
/**
 * Controller: Admin Hợp đồng
 * File: controllers/AdminHopDongController.php
 * Quản lý hợp đồng thỉnh giảng
 */

require_once __DIR__ . '/../models/HopDong.php';
require_once __DIR__ . '/../models/GiangVien.php';
require_once __DIR__ . '/../models/MonHoc.php';
require_once __DIR__ . '/../models/Lop.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/NienKhoa.php';
require_once __DIR__ . '/../models/CoSo.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/DonGia.php';

class AdminHopDongController {
    private $db;
    private $hopDongModel;
    private $giangVienModel;
    private $monHocModel;
    private $lopModel;
    private $ngheModel;
    private $nienKhoaModel;
    private $coSoModel;
    private $khoaModel;
    private $donGiaModel;
    private $uploadPath = __DIR__ . '/../../uploads/hop_dong/';
    
    public function __construct($db) {
        $this->db = $db;
        $this->hopDongModel = new HopDong($db);
        $this->giangVienModel = new GiangVien($db);
        $this->monHocModel = new MonHoc($db);
        $this->lopModel = new Lop($db);
        $this->ngheModel = new Nghe($db);
        $this->nienKhoaModel = new NienKhoa($db);
        $this->coSoModel = new CoSo($db);
        $this->khoaModel = new Khoa($db);
        $this->donGiaModel = new DonGia($db);
        
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }
        
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    public function index() {
        $filters = [
            'khoa_id' => $_GET['khoa_id'] ?? null,
            'giang_vien_id' => $_GET['giang_vien_id'] ?? null,
            'trang_thai' => $_GET['trang_thai'] ?? null,
            'nam_hop_dong' => $_GET['nam_hop_dong'] ?? null,
            'thang_hop_dong' => $_GET['thang_hop_dong'] ?? null
        ];
        
        $hop_dong_list = $this->hopDongModel->getAll($filters);
        
        $khoa_list = $this->khoaModel->getAll(1);
        $giang_vien_list = $this->giangVienModel->getAll(null, null, 1);
        
        $pageTitle = 'Quản lý Hợp đồng';
        require_once __DIR__ . '/../views/admin/hop_dong/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                $files = $this->handleFileUploads($_FILES);
                
                $data = [
                    'so_hop_dong' => trim($_POST['so_hop_dong'] ?? ''),
                    'nam_hop_dong' => (int)date('Y', strtotime($_POST['ngay_hop_dong'])),
                    'ngay_hop_dong' => $_POST['ngay_hop_dong'],
                    'thang_hop_dong' => (int)date('m', strtotime($_POST['ngay_hop_dong'])),
                    'giang_vien_id' => $_POST['giang_vien_id'],
                    'mon_hoc_id' => $_POST['mon_hoc_id'],
                    'nghe_id' => $_POST['nghe_id'],
                    'lop_id' => $_POST['lop_id'],
                    'nien_khoa_id' => $_POST['nien_khoa_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'co_so_id' => $_POST['co_so_id'],
                    'ngay_bat_dau' => $_POST['ngay_bat_dau'],
                    'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
                    'tong_gio_mon_hoc' => (int)$_POST['tong_gio_mon_hoc'],
                    'don_gia_gio' => (float)$_POST['don_gia_gio'],
                    'tong_tien' => (float)$_POST['don_gia_gio'] * (int)$_POST['tong_gio_mon_hoc'],
                    'tong_tien_chu' => trim($_POST['tong_tien_chu'] ?? ''),
                    'da_thanh_toan' => (float)($_POST['da_thanh_toan'] ?? 0),
                    'ngay_thanh_toan' => !empty($_POST['ngay_thanh_toan']) ? $_POST['ngay_thanh_toan'] : null,
                    'hinh_thuc_thanh_toan' => $_POST['hinh_thuc_thanh_toan'] ?? 'Chuyển khoản',
                    'trang_thai' => 'Mới tạo',
                    'file_hop_dong' => $files['file_hop_dong'] ?? null,
                    'file_bien_ban_giao_nhan' => $files['file_bien_ban_giao_nhan'] ?? null,
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'created_by' => getUserId()
                ];
                
                if ($this->hopDongModel->create($data)) {
                    setFlashMessage('success', 'Thêm hợp đồng thành công!');
                    redirect('/admin/hop-dong');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        $giang_vien_list = $this->giangVienModel->getAll(null, null, 1);
        $co_so_list = $this->coSoModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        $pageTitle = 'Thêm Hợp đồng Mới';
        require_once __DIR__ . '/../views/admin/hop_dong/create.php';
    }
    
    public function edit($id) {
        $hop_dong = $this->hopDongModel->getById($id);
        
        if (!$hop_dong) {
            setFlashMessage('error', 'Không tìm thấy hợp đồng!');
            redirect('/admin/hop-dong');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                $files = $this->handleFileUploads($_FILES);
                
                $data = [
                    'so_hop_dong' => trim($_POST['so_hop_dong']),
                    'nam_hop_dong' => (int)date('Y', strtotime($_POST['ngay_hop_dong'])),
                    'ngay_hop_dong' => $_POST['ngay_hop_dong'],
                    'thang_hop_dong' => (int)date('m', strtotime($_POST['ngay_hop_dong'])),
                    'giang_vien_id' => $_POST['giang_vien_id'],
                    'mon_hoc_id' => $_POST['mon_hoc_id'],
                    'nghe_id' => $_POST['nghe_id'],
                    'lop_id' => $_POST['lop_id'],
                    'nien_khoa_id' => $_POST['nien_khoa_id'],
                    'cap_do_id' => $_POST['cap_do_id'],
                    'co_so_id' => $_POST['co_so_id'],
                    'ngay_bat_dau' => $_POST['ngay_bat_dau'],
                    'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
                    'tong_gio_mon_hoc' => (int)$_POST['tong_gio_mon_hoc'],
                    'don_gia_gio' => (float)$_POST['don_gia_gio'],
                    'tong_tien' => (float)$_POST['don_gia_gio'] * (int)$_POST['tong_gio_mon_hoc'],
                    'tong_tien_chu' => trim($_POST['tong_tien_chu'] ?? ''),
                    'da_thanh_toan' => (float)($_POST['da_thanh_toan'] ?? 0),
                    'ngay_thanh_toan' => !empty($_POST['ngay_thanh_toan']) ? $_POST['ngay_thanh_toan'] : null,
                    'hinh_thuc_thanh_toan' => $_POST['hinh_thuc_thanh_toan'] ?? 'Chuyển khoản',
                    'trang_thai' => $_POST['trang_thai'],
                    'file_hop_dong' => $files['file_hop_dong'] ?? $hop_dong['file_hop_dong'],
                    'file_bien_ban_giao_nhan' => $files['file_bien_ban_giao_nhan'] ?? $hop_dong['file_bien_ban_giao_nhan'],
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'updated_by' => getUserId()
                ];
                
                if ($this->hopDongModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật hợp đồng thành công!');
                    redirect('/admin/hop-dong');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $khoa_list = $this->khoaModel->getAll(1);
        $giang_vien_list = $this->giangVienModel->getAll(null, null, 1);
        $co_so_list = $this->coSoModel->getAll(1);
        $cap_do_list = $this->getCapDoList();
        
        $pageTitle = 'Sửa Hợp đồng';
        require_once __DIR__ . '/../views/admin/hop_dong/edit.php';
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/hop-dong');
            return;
        }
        
        if ($this->hopDongModel->delete($id)) {
            setFlashMessage('success', 'Xóa hợp đồng thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra!');
        }
        
        redirect('/admin/hop-dong');
    }
    
    // AJAX: Lấy nghề theo khoa
    public function getNgheByKhoa() {
        $khoa_id = isset($_GET['khoa_id']) ? (int)$_GET['khoa_id'] : 0;
        if (!$khoa_id) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $nghe_list = $this->ngheModel->getAll($khoa_id, 1);
        echo json_encode(['success' => true, 'data' => $nghe_list]);
    }
    
    // AJAX: Lấy lớp theo nghề
    public function getLopByNghe() {
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : 0;
        if (!$nghe_id) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $lop_list = $this->lopModel->getAll(null, $nghe_id, null, 1);
        echo json_encode(['success' => true, 'data' => $lop_list]);
    }
    
    // AJAX: Lấy niên khóa theo nghề
    public function getNienKhoaByNghe() {
        $nghe_id = isset($_GET['nghe_id']) ? (int)$_GET['nghe_id'] : 0;
        if (!$nghe_id) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $nien_khoa_list = $this->nienKhoaModel->getAll($nghe_id, null, null, 1);
        echo json_encode(['success' => true, 'data' => $nien_khoa_list]);
    }
    
    // AJAX: Lấy môn học theo lớp
    public function getMonHocByLop() {
        $lop_id = isset($_GET['lop_id']) ? (int)$_GET['lop_id'] : 0;
        if (!$lop_id) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $mon_hoc_list = $this->monHocModel->getAll(null, null, $lop_id, 1);
        echo json_encode(['success' => true, 'data' => $mon_hoc_list]);
    }
    
    // AJAX: Lấy đơn giá hiện hành
    public function getDonGiaHienHanh() {
        $co_so_id = isset($_GET['co_so_id']) ? (int)$_GET['co_so_id'] : 0;
        $trinh_do_id = isset($_GET['trinh_do_id']) ? (int)$_GET['trinh_do_id'] : 0;
        $ngay = $_GET['ngay'] ?? date('Y-m-d');
        
        if (!$co_so_id || !$trinh_do_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
            return;
        }
        
        $don_gia = $this->donGiaModel->getCurrentDonGia($co_so_id, $trinh_do_id, $ngay);
        
        if ($don_gia) {
            echo json_encode([
                'success' => true,
                'don_gia' => $don_gia['don_gia'],
                'ngay_ap_dung' => $don_gia['ngay_ap_dung']
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn giá']);
        }
    }
    
    private function validateData($data, $exclude_id = null) {
        $errors = [];
        
        if (empty($data['ngay_hop_dong'])) {
            $errors[] = 'Vui lòng chọn ngày hợp đồng!';
        }
        
        if (empty($data['giang_vien_id'])) {
            $errors[] = 'Vui lòng chọn giảng viên!';
        }
        
        if (empty($data['mon_hoc_id'])) {
            $errors[] = 'Vui lòng chọn môn học!';
        }
        
        if (empty($data['co_so_id'])) {
            $errors[] = 'Vui lòng chọn cơ sở!';
        }
        
        if (empty($data['tong_gio_mon_hoc']) || $data['tong_gio_mon_hoc'] <= 0) {
            $errors[] = 'Tổng giờ phải > 0!';
        }
        
        if (empty($data['don_gia_gio']) || $data['don_gia_gio'] <= 0) {
            $errors[] = 'Đơn giá phải > 0!';
        }
        
        return $errors;
    }
    
    private function handleFileUploads($files) {
        $uploaded = [];
        $allowed = ['pdf', 'doc', 'docx'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        foreach (['file_hop_dong', 'file_bien_ban_giao_nhan'] as $field) {
            if (isset($files[$field]) && $files[$field]['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($files[$field]['name'], PATHINFO_EXTENSION));
                
                if (!in_array($ext, $allowed)) continue;
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
    
    private function getCapDoList() {
        $query = "SELECT * FROM cap_do_giang_day WHERE is_active = 1 ORDER BY ten_cap_do";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}