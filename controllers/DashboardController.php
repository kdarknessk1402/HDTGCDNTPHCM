<?php
/**
 * Controller: Dashboard
 * File: controllers/DashboardController.php
 * Dashboard cho 4 vai trò: Admin, Phòng ĐT, Trưởng Khoa, Giáo vụ
 */

class DashboardController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }
    }
    
    public function index() {
        $role = getUserRole();
        $user_id = getUserId();
        
        // Lấy thông tin user
        $user = $this->getUserInfo($user_id);
        
        // Route theo vai trò
        switch ($role) {
            case 'Admin':
                $this->adminDashboard($user);
                break;
            case 'Phong_Dao_Tao':
                $this->phongDaoTaoDashboard($user);
                break;
            case 'Truong_Khoa':
                $this->truongKhoaDashboard($user);
                break;
            case 'Giao_Vu':
                $this->giaoVuDashboard($user);
                break;
            default:
                setFlashMessage('error', 'Vai trò không hợp lệ!');
                redirect('/logout');
        }
    }
    
    /**
     * DASHBOARD ADMIN - Toàn bộ hệ thống
     */
    private function adminDashboard($user) {
        // Thống kê tổng quan
        $stats = [
            'tong_giang_vien' => $this->count('giang_vien'),
            'tong_hop_dong' => $this->count('hop_dong'),
            'tong_khoa' => $this->count('khoa', 'is_active = 1'),
            'tong_nghe' => $this->count('nghe', 'is_active = 1'),
            'tong_lop' => $this->count('lop', 'is_active = 1'),
            'tong_mon_hoc' => $this->count('mon_hoc', 'is_active = 1')
        ];
        
        // Hợp đồng theo trạng thái
        $stats['hd_moi_tao'] = $this->count('hop_dong', "trang_thai = 'Mới tạo'");
        $stats['hd_da_duyet'] = $this->count('hop_dong', "trang_thai = 'Đã duyệt'");
        $stats['hd_dang_thuc_hien'] = $this->count('hop_dong', "trang_thai = 'Đang thực hiện'");
        $stats['hd_hoan_thanh'] = $this->count('hop_dong', "trang_thai = 'Hoàn thành'");
        
        // Top 5 giảng viên nhiều HĐ nhất
        $top_giang_vien = $this->getTopGiangVien();
        
        // Thống kê theo tháng (6 tháng gần nhất)
        $chart_data = $this->getHopDongByMonth(6);
        
        // Tổng tiền theo tháng
        $tien_theo_thang = $this->getTongTienByMonth(6);
        
        $pageTitle = 'Dashboard Admin';
        require_once __DIR__ . '/../views/dashboard/admin.php';
    }
    
    /**
     * DASHBOARD PHÒNG ĐÀO TẠO - Tổng hợp toàn trường
     */
    private function phongDaoTaoDashboard($user) {
        // Tương tự Admin nhưng focus vào báo cáo
        $stats = [
            'tong_giang_vien' => $this->count('giang_vien', 'is_active = 1'),
            'tong_hop_dong_thang' => $this->count('hop_dong', "thang_hop_dong = MONTH(CURDATE()) AND nam_hop_dong = YEAR(CURDATE())"),
            'tong_gio_thang' => $this->sum('hop_dong', 'tong_gio_mon_hoc', "thang_hop_dong = MONTH(CURDATE()) AND nam_hop_dong = YEAR(CURDATE())"),
            'tong_tien_thang' => $this->sum('hop_dong', 'tong_tien', "thang_hop_dong = MONTH(CURDATE()) AND nam_hop_dong = YEAR(CURDATE())")
        ];
        
        // HĐ chờ duyệt
        $hop_dong_cho_duyet = $this->getHopDongChoDuyet();
        
        // Thống kê theo khoa
        $thong_ke_khoa = $this->getThongKeTheoKhoa();
        
        // Chart theo tháng
        $chart_data = $this->getHopDongByMonth(12);
        
        $pageTitle = 'Dashboard Phòng Đào tạo';
        require_once __DIR__ . '/../views/dashboard/phong_dao_tao.php';
    }
    
    /**
     * DASHBOARD TRƯỞNG KHOA - Chỉ khoa của mình
     */
    private function truongKhoaDashboard($user) {
        $khoa_id = $user['khoa_id'];
        
        if (!$khoa_id) {
            setFlashMessage('error', 'Tài khoản chưa được gán Khoa!');
            redirect('/logout');
            return;
        }
        
        // Thống kê khoa
        $stats = [
            'tong_giang_vien' => $this->count('giang_vien', "khoa_id = $khoa_id AND is_active = 1"),
            'tong_nghe' => $this->count('nghe', "khoa_id = $khoa_id AND is_active = 1"),
            'tong_lop' => $this->countWithJoin('lop', 'nghe', 'nghe_id', "nghe.khoa_id = $khoa_id AND lop.is_active = 1"),
            'tong_hop_dong_thang' => $this->countHopDongKhoa($khoa_id, "thang_hop_dong = MONTH(CURDATE())")
        ];
        
        // Danh sách GV khoa
        $giang_vien_khoa = $this->getGiangVienKhoa($khoa_id);
        
        // HĐ gần đây của khoa
        $hop_dong_gan_day = $this->getHopDongKhoa($khoa_id, 10);
        
        // Chart
        $chart_data = $this->getHopDongKhoaByMonth($khoa_id, 6);
        
        $pageTitle = 'Dashboard Trưởng Khoa';
        require_once __DIR__ . '/../views/dashboard/truong_khoa.php';
    }
    
    /**
     * DASHBOARD GIÁO VỤ - Quản lý hợp đồng
     */
    private function giaoVuDashboard($user) {
        // Thống kê công việc
        $stats = [
            'hd_tao_boi_toi' => $this->count('hop_dong', "created_by = " . getUserId()),
            'hd_thang_nay' => $this->count('hop_dong', "thang_hop_dong = MONTH(CURDATE()) AND created_by = " . getUserId()),
            'tong_giang_vien' => $this->count('giang_vien', 'is_active = 1'),
            'hd_moi_tao' => $this->count('hop_dong', "trang_thai = 'Mới tạo' AND created_by = " . getUserId())
        ];
        
        // HĐ gần đây của mình
        $hop_dong_cua_toi = $this->getHopDongByUser(getUserId(), 10);
        
        // Quick actions
        $giang_vien_list = $this->getGiangVienActive();
        
        $pageTitle = 'Dashboard Giáo vụ';
        require_once __DIR__ . '/../views/dashboard/giao_vu.php';
    }
    
    // ==================== HELPER FUNCTIONS ====================
    
    private function getUserInfo($user_id) {
        $query = "SELECT u.*, r.role_name, r.role_display_name, k.ten_khoa
                  FROM users u
                  LEFT JOIN roles r ON u.role_id = r.role_id
                  LEFT JOIN khoa k ON u.khoa_id = k.khoa_id
                  WHERE u.user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function count($table, $where = '1=1') {
        $query = "SELECT COUNT(*) as total FROM $table WHERE $where";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    private function sum($table, $column, $where = '1=1') {
        $query = "SELECT COALESCE(SUM($column), 0) as total FROM $table WHERE $where";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    private function countWithJoin($table, $join_table, $join_on, $where) {
        $query = "SELECT COUNT(*) as total FROM $table 
                  LEFT JOIN $join_table ON $table.$join_on = $join_table.$join_on
                  WHERE $where";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    private function countHopDongKhoa($khoa_id, $where = '1=1') {
        $query = "SELECT COUNT(*) as total FROM hop_dong hd
                  LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                  WHERE n.khoa_id = $khoa_id AND $where";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    
    private function getTopGiangVien($limit = 5) {
        $query = "SELECT gv.ten_giang_vien, gv.ma_giang_vien, k.ten_khoa,
                         COUNT(hd.hop_dong_id) as so_hop_dong,
                         SUM(hd.tong_gio_mon_hoc) as tong_gio,
                         SUM(hd.tong_tien) as tong_tien
                  FROM giang_vien gv
                  LEFT JOIN hop_dong hd ON gv.giang_vien_id = hd.giang_vien_id
                  LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                  WHERE hd.hop_dong_id IS NOT NULL
                  GROUP BY gv.giang_vien_id
                  ORDER BY so_hop_dong DESC
                  LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getHopDongByMonth($months = 6) {
        $query = "SELECT DATE_FORMAT(ngay_hop_dong, '%Y-%m') as thang,
                         COUNT(*) as so_luong,
                         SUM(tong_tien) as tong_tien
                  FROM hop_dong
                  WHERE ngay_hop_dong >= DATE_SUB(CURDATE(), INTERVAL $months MONTH)
                  GROUP BY thang
                  ORDER BY thang ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getTongTienByMonth($months = 6) {
        return $this->getHopDongByMonth($months);
    }
    
    private function getHopDongChoDuyet($limit = 10) {
        $query = "SELECT hd.*, gv.ten_giang_vien, mh.ten_mon_hoc
                  FROM hop_dong hd
                  LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                  LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                  WHERE hd.trang_thai = 'Mới tạo'
                  ORDER BY hd.created_at DESC
                  LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getThongKeTheoKhoa() {
        $query = "SELECT k.ten_khoa,
                         COUNT(DISTINCT gv.giang_vien_id) as so_giang_vien,
                         COUNT(DISTINCT hd.hop_dong_id) as so_hop_dong,
                         COALESCE(SUM(hd.tong_tien), 0) as tong_tien
                  FROM khoa k
                  LEFT JOIN giang_vien gv ON k.khoa_id = gv.khoa_id AND gv.is_active = 1
                  LEFT JOIN nghe n ON k.khoa_id = n.khoa_id
                  LEFT JOIN hop_dong hd ON n.nghe_id = hd.nghe_id
                  WHERE k.is_active = 1
                  GROUP BY k.khoa_id
                  ORDER BY so_hop_dong DESC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getGiangVienKhoa($khoa_id, $limit = 10) {
        $query = "SELECT * FROM giang_vien 
                  WHERE khoa_id = $khoa_id AND is_active = 1
                  ORDER BY created_at DESC
                  LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getHopDongKhoa($khoa_id, $limit = 10) {
        $query = "SELECT hd.*, gv.ten_giang_vien, mh.ten_mon_hoc
                  FROM hop_dong hd
                  LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                  LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                  LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                  WHERE n.khoa_id = $khoa_id
                  ORDER BY hd.created_at DESC
                  LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getHopDongKhoaByMonth($khoa_id, $months = 6) {
        $query = "SELECT DATE_FORMAT(hd.ngay_hop_dong, '%Y-%m') as thang,
                         COUNT(*) as so_luong
                  FROM hop_dong hd
                  LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                  WHERE n.khoa_id = $khoa_id 
                    AND hd.ngay_hop_dong >= DATE_SUB(CURDATE(), INTERVAL $months MONTH)
                  GROUP BY thang
                  ORDER BY thang ASC";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getHopDongByUser($user_id, $limit = 10) {
        $query = "SELECT hd.*, gv.ten_giang_vien, mh.ten_mon_hoc
                  FROM hop_dong hd
                  LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                  LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                  WHERE hd.created_by = $user_id
                  ORDER BY hd.created_at DESC
                  LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getGiangVienActive($limit = 20) {
        $query = "SELECT * FROM giang_vien WHERE is_active = 1 ORDER BY ten_giang_vien LIMIT $limit";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}