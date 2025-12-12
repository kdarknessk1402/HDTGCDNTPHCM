<?php
require_once __DIR__ . '/Model.php';

/**
 * GiangVien Model
 * TÊN CỘT DATABASE: giang_vien_id, khoa_id, ma_giang_vien, ten_giang_vien, nam_sinh, gioi_tinh, 
 * ngay_sinh, noi_sinh, so_cccd, ngay_cap_cccd, noi_cap_cccd, trinh_do_id, chuyen_nganh_dao_tao, 
 * truong_dao_tao, nam_tot_nghiep, chung_chi_su_pham, dia_chi, dia_chi_tam_tru, so_dien_thoai, 
 * email, so_tai_khoan, ten_ngan_hang, chi_nhanh_ngan_hang, chu_tai_khoan, ma_so_thue, 
 * file_cccd, file_bang_cap, file_chung_chi, ghi_chu, is_active
 */
class GiangVien extends Model {
    protected $table = 'giang_vien';
    protected $primaryKey = 'giang_vien_id';
    
    /**
     * Lấy tất cả giảng viên với thông tin khoa và trình độ
     */
    public function getAllWithDetails() {
        $sql = "SELECT 
                    gv.*,
                    k.ma_khoa,
                    k.ten_khoa,
                    td.ma_trinh_do,
                    td.ten_trinh_do
                FROM giang_vien gv
                LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                LEFT JOIN trinh_do_chuyen_mon td ON gv.trinh_do_id = td.trinh_do_id
                ORDER BY k.ten_khoa ASC, gv.ten_giang_vien ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy giảng viên theo khoa
     */
    public function getByKhoa($khoa_id, $is_active = 1) {
        $conditions = ['khoa_id' => $khoa_id];
        
        if ($is_active !== null) {
            $conditions['is_active'] = $is_active;
        }
        
        return $this->getAll($conditions, 'ten_giang_vien ASC');
    }
    
    /**
     * Lấy giảng viên theo ID với thông tin chi tiết
     */
    public function getByIdWithDetails($id) {
        $sql = "SELECT 
                    gv.*,
                    k.ten_khoa,
                    td.ten_trinh_do
                FROM giang_vien gv
                LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                LEFT JOIN trinh_do_chuyen_mon td ON gv.trinh_do_id = td.trinh_do_id
                WHERE gv.giang_vien_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Kiểm tra mã giảng viên đã tồn tại chưa
     */
    public function checkMaGiangVienExists($ma_giang_vien, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM giang_vien WHERE ma_giang_vien = :ma_giang_vien";
        
        if ($exclude_id) {
            $sql .= " AND giang_vien_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_giang_vien', $ma_giang_vien);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Kiểm tra CCCD đã tồn tại chưa
     */
    public function checkCccdExists($so_cccd, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM giang_vien WHERE so_cccd = :so_cccd";
        
        if ($exclude_id) {
            $sql .= " AND giang_vien_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':so_cccd', $so_cccd);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Đếm số hợp đồng của giảng viên
     */
    public function countHopDong($giang_vien_id) {
        $sql = "SELECT COUNT(*) FROM hop_dong WHERE giang_vien_id = :giang_vien_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':giang_vien_id', $giang_vien_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Tạo giảng viên mới - TÊN CỘT CHÍNH XÁC
     */
    public function createGiangVien($data) {
        $sql = "INSERT INTO giang_vien (
                    khoa_id, ma_giang_vien, ten_giang_vien, nam_sinh, gioi_tinh,
                    ngay_sinh, noi_sinh, so_cccd, ngay_cap_cccd, noi_cap_cccd,
                    trinh_do_id, chuyen_nganh_dao_tao, truong_dao_tao, nam_tot_nghiep,
                    chung_chi_su_pham, dia_chi, dia_chi_tam_tru, so_dien_thoai, email,
                    so_tai_khoan, ten_ngan_hang, chi_nhanh_ngan_hang, chu_tai_khoan,
                    ma_so_thue, file_cccd, file_bang_cap, file_chung_chi, ghi_chu,
                    is_active, created_by
                ) VALUES (
                    :khoa_id, :ma_giang_vien, :ten_giang_vien, :nam_sinh, :gioi_tinh,
                    :ngay_sinh, :noi_sinh, :so_cccd, :ngay_cap_cccd, :noi_cap_cccd,
                    :trinh_do_id, :chuyen_nganh_dao_tao, :truong_dao_tao, :nam_tot_nghiep,
                    :chung_chi_su_pham, :dia_chi, :dia_chi_tam_tru, :so_dien_thoai, :email,
                    :so_tai_khoan, :ten_ngan_hang, :chi_nhanh_ngan_hang, :chu_tai_khoan,
                    :ma_so_thue, :file_cccd, :file_bang_cap, :file_chung_chi, :ghi_chu,
                    :is_active, :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        
        // Bind all parameters
        $stmt->bindValue(':khoa_id', $data['khoa_id'], PDO::PARAM_INT);
        $stmt->bindValue(':ma_giang_vien', $data['ma_giang_vien']);
        $stmt->bindValue(':ten_giang_vien', $data['ten_giang_vien']);
        $stmt->bindValue(':nam_sinh', $data['nam_sinh'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':gioi_tinh', $data['gioi_tinh'] ?? null);
        $stmt->bindValue(':ngay_sinh', $data['ngay_sinh'] ?? null);
        $stmt->bindValue(':noi_sinh', $data['noi_sinh'] ?? null);
        $stmt->bindValue(':so_cccd', $data['so_cccd'] ?? null);
        $stmt->bindValue(':ngay_cap_cccd', $data['ngay_cap_cccd'] ?? null);
        $stmt->bindValue(':noi_cap_cccd', $data['noi_cap_cccd'] ?? null);
        $stmt->bindValue(':trinh_do_id', $data['trinh_do_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':chuyen_nganh_dao_tao', $data['chuyen_nganh_dao_tao'] ?? null);
        $stmt->bindValue(':truong_dao_tao', $data['truong_dao_tao'] ?? null);
        $stmt->bindValue(':nam_tot_nghiep', $data['nam_tot_nghiep'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':chung_chi_su_pham', $data['chung_chi_su_pham'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':dia_chi_tam_tru', $data['dia_chi_tam_tru'] ?? null);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':so_tai_khoan', $data['so_tai_khoan'] ?? null);
        $stmt->bindValue(':ten_ngan_hang', $data['ten_ngan_hang'] ?? null);
        $stmt->bindValue(':chi_nhanh_ngan_hang', $data['chi_nhanh_ngan_hang'] ?? null);
        $stmt->bindValue(':chu_tai_khoan', $data['chu_tai_khoan'] ?? null);
        $stmt->bindValue(':ma_so_thue', $data['ma_so_thue'] ?? null);
        $stmt->bindValue(':file_cccd', $data['file_cccd'] ?? null);
        $stmt->bindValue(':file_bang_cap', $data['file_bang_cap'] ?? null);
        $stmt->bindValue(':file_chung_chi', $data['file_chung_chi'] ?? null);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật giảng viên - TÊN CỘT CHÍNH XÁC
     */
    public function updateGiangVien($id, $data) {
        $sql = "UPDATE giang_vien SET
                    khoa_id = :khoa_id,
                    ma_giang_vien = :ma_giang_vien,
                    ten_giang_vien = :ten_giang_vien,
                    nam_sinh = :nam_sinh,
                    gioi_tinh = :gioi_tinh,
                    ngay_sinh = :ngay_sinh,
                    noi_sinh = :noi_sinh,
                    so_cccd = :so_cccd,
                    ngay_cap_cccd = :ngay_cap_cccd,
                    noi_cap_cccd = :noi_cap_cccd,
                    trinh_do_id = :trinh_do_id,
                    chuyen_nganh_dao_tao = :chuyen_nganh_dao_tao,
                    truong_dao_tao = :truong_dao_tao,
                    nam_tot_nghiep = :nam_tot_nghiep,
                    chung_chi_su_pham = :chung_chi_su_pham,
                    dia_chi = :dia_chi,
                    dia_chi_tam_tru = :dia_chi_tam_tru,
                    so_dien_thoai = :so_dien_thoai,
                    email = :email,
                    so_tai_khoan = :so_tai_khoan,
                    ten_ngan_hang = :ten_ngan_hang,
                    chi_nhanh_ngan_hang = :chi_nhanh_ngan_hang,
                    chu_tai_khoan = :chu_tai_khoan,
                    ma_so_thue = :ma_so_thue,
                    ghi_chu = :ghi_chu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE giang_vien_id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        // Bind all parameters
        $stmt->bindValue(':khoa_id', $data['khoa_id'], PDO::PARAM_INT);
        $stmt->bindValue(':ma_giang_vien', $data['ma_giang_vien']);
        $stmt->bindValue(':ten_giang_vien', $data['ten_giang_vien']);
        $stmt->bindValue(':nam_sinh', $data['nam_sinh'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':gioi_tinh', $data['gioi_tinh'] ?? null);
        $stmt->bindValue(':ngay_sinh', $data['ngay_sinh'] ?? null);
        $stmt->bindValue(':noi_sinh', $data['noi_sinh'] ?? null);
        $stmt->bindValue(':so_cccd', $data['so_cccd'] ?? null);
        $stmt->bindValue(':ngay_cap_cccd', $data['ngay_cap_cccd'] ?? null);
        $stmt->bindValue(':noi_cap_cccd', $data['noi_cap_cccd'] ?? null);
        $stmt->bindValue(':trinh_do_id', $data['trinh_do_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':chuyen_nganh_dao_tao', $data['chuyen_nganh_dao_tao'] ?? null);
        $stmt->bindValue(':truong_dao_tao', $data['truong_dao_tao'] ?? null);
        $stmt->bindValue(':nam_tot_nghiep', $data['nam_tot_nghiep'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':chung_chi_su_pham', $data['chung_chi_su_pham'] ?? null);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':dia_chi_tam_tru', $data['dia_chi_tam_tru'] ?? null);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':so_tai_khoan', $data['so_tai_khoan'] ?? null);
        $stmt->bindValue(':ten_ngan_hang', $data['ten_ngan_hang'] ?? null);
        $stmt->bindValue(':chi_nhanh_ngan_hang', $data['chi_nhanh_ngan_hang'] ?? null);
        $stmt->bindValue(':chu_tai_khoan', $data['chu_tai_khoan'] ?? null);
        $stmt->bindValue(':ma_so_thue', $data['ma_so_thue'] ?? null);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa giảng viên (soft delete)
     */
    public function deleteGiangVien($id) {
        // Kiểm tra có hợp đồng nào không
        if ($this->countHopDong($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa giảng viên vì có hợp đồng liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE giang_vien SET is_active = 0, updated_by = :updated_by WHERE giang_vien_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa giảng viên thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa giảng viên'];
    }
}