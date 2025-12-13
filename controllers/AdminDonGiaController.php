<?php
/**
 * Controller: Admin Đơn giá
 * Quản lý đơn giá giờ dạy theo cơ sở và trình độ (Admin only)
 */

require_once __DIR__ . '/../models/DonGia.php';
require_once __DIR__ . '/../models/CoSo.php';

class AdminDonGiaController {
    private $db;
    private $donGiaModel;
    private $coSoModel;
    
    public function __construct($db) {
        $this->db = $db;
        $this->donGiaModel = new DonGia($db);
        $this->coSoModel = new CoSo($db);
        
        // Kiểm tra quyền Admin
        if (!isLoggedIn() || getUserRole() !== 'Admin') {
            setFlashMessage('error', 'Bạn không có quyền truy cập trang này!');
            redirect('/');
            exit;
        }
    }
    
    /**
     * Hiển thị danh sách đơn giá
     */
    public function index() {
        $co_so_id = isset($_GET['co_so_id']) ? (int)$_GET['co_so_id'] : null;
        $trinh_do_id = isset($_GET['trinh_do_id']) ? (int)$_GET['trinh_do_id'] : null;
        $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
        
        // Lấy danh sách đơn giá
        $don_gia_list = $this->donGiaModel->getAll($co_so_id, $trinh_do_id, $is_active);
        
        // Lấy danh sách cho filter
        $co_so_list = $this->coSoModel->getAll(1);
        $trinh_do_list = $this->getTrinhDoList();
        
        // Hiển thị view
        $pageTitle = 'Quản lý Đơn giá';
        require_once __DIR__ . '/../views/admin/don_gia/index.php';
    }
    
    /**
     * Thêm đơn giá mới
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate
            $errors = $this->validateData($_POST);
            
            if (empty($errors)) {
                $data = [
                    'co_so_id' => $_POST['co_so_id'],
                    'trinh_do_id' => $_POST['trinh_do_id'],
                    'don_gia' => (float)$_POST['don_gia'],
                    'ngay_ap_dung' => $_POST['ngay_ap_dung'],
                    'ngay_ket_thuc' => !empty($_POST['ngay_ket_thuc']) ? $_POST['ngay_ket_thuc'] : null,
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'created_by' => getUserId()
                ];
                
                $don_gia_id = $this->donGiaModel->create($data);
                
                if ($don_gia_id) {
                    setFlashMessage('success', 'Thêm đơn giá thành công!');
                    redirect('/admin/don-gia');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi thêm đơn giá!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $co_so_list = $this->coSoModel->getAll(1);
        $trinh_do_list = $this->getTrinhDoList();
        
        // Hiển thị form
        $pageTitle = 'Thêm Đơn giá Mới';
        require_once __DIR__ . '/../views/admin/don_gia/create.php';
    }
    
    /**
     * Sửa đơn giá
     */
    public function edit($id) {
        $don_gia = $this->donGiaModel->getById($id);
        
        if (!$don_gia) {
            setFlashMessage('error', 'Không tìm thấy đơn giá!');
            redirect('/admin/don-gia');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate
            $errors = $this->validateData($_POST, $id);
            
            if (empty($errors)) {
                $data = [
                    'co_so_id' => $_POST['co_so_id'],
                    'trinh_do_id' => $_POST['trinh_do_id'],
                    'don_gia' => (float)$_POST['don_gia'],
                    'ngay_ap_dung' => $_POST['ngay_ap_dung'],
                    'ngay_ket_thuc' => !empty($_POST['ngay_ket_thuc']) ? $_POST['ngay_ket_thuc'] : null,
                    'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
                    'is_active' => isset($_POST['is_active']) ? 1 : 0,
                    'updated_by' => getUserId()
                ];
                
                if ($this->donGiaModel->update($id, $data)) {
                    setFlashMessage('success', 'Cập nhật đơn giá thành công!');
                    redirect('/admin/don-gia');
                } else {
                    setFlashMessage('error', 'Có lỗi xảy ra khi cập nhật đơn giá!');
                }
            } else {
                setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        // Lấy danh sách cho dropdown
        $co_so_list = $this->coSoModel->getAll(1);
        $trinh_do_list = $this->getTrinhDoList();
        
        // Hiển thị form
        $pageTitle = 'Sửa Đơn giá';
        require_once __DIR__ . '/../views/admin/don_gia/edit.php';
    }
    
    /**
     * Xóa đơn giá
     */
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/don-gia');
            return;
        }
        
        $don_gia = $this->donGiaModel->getById($id);
        
        if (!$don_gia) {
            setFlashMessage('error', 'Không tìm thấy đơn giá!');
            redirect('/admin/don-gia');
            return;
        }
        
        // Kiểm tra ràng buộc
        if ($this->donGiaModel->hasRelatedRecords($id)) {
            setFlashMessage('error', 'Không thể xóa đơn giá này vì đang có hợp đồng sử dụng!');
            redirect('/admin/don-gia');
            return;
        }
        
        if ($this->donGiaModel->delete($id)) {
            setFlashMessage('success', 'Xóa đơn giá thành công!');
        } else {
            setFlashMessage('error', 'Có lỗi xảy ra khi xóa đơn giá!');
        }
        
        redirect('/admin/don-gia');
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            return;
        }
        
        $don_gia = $this->donGiaModel->getById($id);
        
        if (!$don_gia) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn giá!']);
            return;
        }
        
        $is_active = $don_gia['is_active'] == 1 ? 0 : 1;
        
        if ($this->donGiaModel->updateStatus($id, $is_active, getUserId())) {
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
        
        // Kiểm tra cơ sở
        if (empty($data['co_so_id'])) {
            $errors[] = 'Vui lòng chọn cơ sở!';
        }
        
        // Kiểm tra trình độ
        if (empty($data['trinh_do_id'])) {
            $errors[] = 'Vui lòng chọn trình độ!';
        }
        
        // Kiểm tra đơn giá
        if (empty($data['don_gia']) || $data['don_gia'] <= 0) {
            $errors[] = 'Đơn giá phải lớn hơn 0!';
        } elseif ($data['don_gia'] > 999999999) {
            $errors[] = 'Đơn giá quá lớn!';
        }
        
        // Kiểm tra ngày áp dụng
        if (empty($data['ngay_ap_dung'])) {
            $errors[] = 'Vui lòng nhập ngày áp dụng!';
        }
        
        // Kiểm tra ngày kết thúc (nếu có)
        if (!empty($data['ngay_ket_thuc'])) {
            if ($data['ngay_ket_thuc'] <= $data['ngay_ap_dung']) {
                $errors[] = 'Ngày kết thúc phải sau ngày áp dụng!';
            }
        }
        
        return $errors;
    }
    
    /**
     * AJAX: Lấy đơn giá hiện hành
     */
    public function getCurrentDonGia() {
        if (!isset($_GET['co_so_id']) || !isset($_GET['trinh_do_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu tham số!']);
            return;
        }
        
        $co_so_id = (int)$_GET['co_so_id'];
        $trinh_do_id = (int)$_GET['trinh_do_id'];
        $ngay = $_GET['ngay'] ?? date('Y-m-d');
        
        $don_gia = $this->donGiaModel->getCurrentDonGia($co_so_id, $trinh_do_id, $ngay);
        
        if ($don_gia) {
            echo json_encode([
                'success' => true,
                'data' => $don_gia
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Chưa có đơn giá cho cơ sở và trình độ này!'
            ]);
        }
    }
    
    /**
     * Lấy danh sách trình độ
     */
    private function getTrinhDoList() {
        $query = "SELECT * FROM trinh_do_chuyen_mon WHERE is_active = 1 ORDER BY thu_tu ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}