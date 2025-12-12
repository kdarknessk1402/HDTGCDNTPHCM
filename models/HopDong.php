<?php
require_once __DIR__ . '/Model.php';

/**
 * HopDong Model
 * TÊN CỘT DATABASE: hop_dong_id, so_hop_dong, nam_hop_dong, ngay_hop_dong, thang_hop_dong,
 * giang_vien_id, mon_hoc_id, nghe_id, lop_id, nien_khoa_id, cap_do_id, co_so_id,
 * ngay_bat_dau, ngay_ket_thuc, tong_gio_mon_hoc, don_gia_gio, tong_tien, tong_tien_chu,
 * da_thanh_toan, ngay_thanh_toan, hinh_thuc_thanh_toan, trang_thai, 
 * file_hop_dong, file_bien_ban_giao_nhan, ghi_chu, ly_do_huy
 */
class HopDong extends Model {
    protected $table = 'hop_dong';
    protected $primaryKey = 'hop_dong_id';
    
    /**
     * Lấy tất cả hợp đồng với thông tin chi tiết
     */
    public function getAllWithDetails($conditions = []) {
        $sql = "SELECT 
                    hd.*,
                    gv.ma_giang_vien,
                    gv.ten_giang_vien,
                    gv.so_dien_thoai as sdt_giang_vien,
                    mh.ma_mon_hoc,
                    mh.ten_mon_hoc,
                    n.ma_nghe,
                    n.ten_nghe,
                    l.ma_lop,
                    l.ten_lop,
                    k.ma_khoa,
                    k.ten_khoa,
                    cs.ma_co_so,
                    cs.ten_co_so,
                    cd.ten_cap_do,
                    u.full_name as nguoi_tao
                FROM hop_dong hd
                LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                LEFT JOIN lop l ON hd.lop_id = l.lop_id
                LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                LEFT JOIN co_so cs ON hd.co_so_id = cs.co_so_id
                LEFT JOIN cap_do_giang_day cd ON hd.cap_do_id = cd.cap_do_id
                LEFT JOIN users u ON hd.created_by = u.user_id";
        
        if (!empty($conditions)) {
            $where = [];
            foreach ($conditions as $key => $value) {
                $where[] = "$key = :$key";
            }
            $sql .= " WHERE " . implode(' AND ', $where);
        }
        
        $sql .= " ORDER BY hd.ngay_hop_dong DESC, hd.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        
        if (!empty($conditions)) {
            foreach ($conditions as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy hợp đồng theo ID với thông tin đầy đủ
     */
    public function getByIdWithFullDetails($id) {
        $sql = "SELECT * FROM v_hop_dong_chi_tiet WHERE hop_dong_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy hợp đồng theo khoa
     */
    public function getByKhoa($khoa_id, $conditions = []) {
        $conditions['k.khoa_id'] = $khoa_id;
        return $this->getAllWithDetails($conditions);
    }
    
    /**
     * Tạo hợp đồng mới - TÊN CỘT CHÍNH XÁC
     */
    public function createHopDong($data) {
        $sql = "INSERT INTO hop_dong (
                    so_hop_dong, nam_hop_dong, ngay_hop_dong, thang_hop_dong,
                    giang_vien_id, mon_hoc_id, nghe_id, lop_id, nien_khoa_id,
                    cap_do_id, co_so_id, ngay_bat_dau, ngay_ket_thuc,
                    tong_gio_mon_hoc, don_gia_gio, tong_tien, tong_tien_chu,
                    da_thanh_toan, trang_thai, ghi_chu, created_by
                ) VALUES (
                    :so_hop_dong, :nam_hop_dong, :ngay_hop_dong, :thang_hop_dong,
                    :giang_vien_id, :mon_hoc_id, :nghe_id, :lop_id, :nien_khoa_id,
                    :cap_do_id, :co_so_id, :ngay_bat_dau, :ngay_ket_thuc,
                    :tong_gio_mon_hoc, :don_gia_gio, :tong_tien, :tong_tien_chu,
                    :da_thanh_toan, :trang_thai, :ghi_chu, :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':so_hop_dong', $data['so_hop_dong'] ?? null);
        $stmt->bindValue(':nam_hop_dong', $data['nam_hop_dong'] ?? date('Y'), PDO::PARAM_INT);
        $stmt->bindValue(':ngay_hop_dong', $data['ngay_hop_dong']);
        $stmt->bindValue(':thang_hop_dong', $data['thang_hop_dong'] ?? date('n'), PDO::PARAM_INT);
        $stmt->bindValue(':giang_vien_id', $data['giang_vien_id'], PDO::PARAM_INT);
        $stmt->bindValue(':mon_hoc_id', $data['mon_hoc_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':lop_id', $data['lop_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':cap_do_id', $data['cap_do_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':co_so_id', $data['co_so_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ngay_bat_dau', $data['ngay_bat_dau']);
        $stmt->bindValue(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindValue(':tong_gio_mon_hoc', $data['tong_gio_mon_hoc'], PDO::PARAM_INT);
        $stmt->bindValue(':don_gia_gio', $data['don_gia_gio'], PDO::PARAM_INT);
        $stmt->bindValue(':tong_tien', $data['tong_tien'], PDO::PARAM_INT);
        $stmt->bindValue(':tong_tien_chu', $data['tong_tien_chu'] ?? null);
        $stmt->bindValue(':da_thanh_toan', $data['da_thanh_toan'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':trang_thai', $data['trang_thai'] ?? 'Mới tạo');
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật hợp đồng - TÊN CỘT CHÍNH XÁC
     */
    public function updateHopDong($id, $data) {
        $sql = "UPDATE hop_dong SET
                    giang_vien_id = :giang_vien_id,
                    mon_hoc_id = :mon_hoc_id,
                    nghe_id = :nghe_id,
                    lop_id = :lop_id,
                    nien_khoa_id = :nien_khoa_id,
                    cap_do_id = :cap_do_id,
                    co_so_id = :co_so_id,
                    ngay_hop_dong = :ngay_hop_dong,
                    ngay_bat_dau = :ngay_bat_dau,
                    ngay_ket_thuc = :ngay_ket_thuc,
                    tong_gio_mon_hoc = :tong_gio_mon_hoc,
                    don_gia_gio = :don_gia_gio,
                    tong_tien = :tong_tien,
                    tong_tien_chu = :tong_tien_chu,
                    trang_thai = :trang_thai,
                    ghi_chu = :ghi_chu,
                    updated_by = :updated_by
                WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':giang_vien_id', $data['giang_vien_id'], PDO::PARAM_INT);
        $stmt->bindValue(':mon_hoc_id', $data['mon_hoc_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':lop_id', $data['lop_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':cap_do_id', $data['cap_do_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':co_so_id', $data['co_so_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ngay_hop_dong', $data['ngay_hop_dong']);
        $stmt->bindValue(':ngay_bat_dau', $data['ngay_bat_dau']);
        $stmt->bindValue(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindValue(':tong_gio_mon_hoc', $data['tong_gio_mon_hoc'], PDO::PARAM_INT);
        $stmt->bindValue(':don_gia_gio', $data['don_gia_gio'], PDO::PARAM_INT);
        $stmt->bindValue(':tong_tien', $data['tong_tien'], PDO::PARAM_INT);
        $stmt->bindValue(':tong_tien_chu', $data['tong_tien_chu'] ?? null);
        $stmt->bindValue(':trang_thai', $data['trang_thai']);
        $stmt->bindValue(':ghi_chu', $data['ghi_chu'] ?? null);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa hợp đồng
     */
    public function deleteHopDong($id) {
        // Xóa thật (có thể thay bằng soft delete nếu cần)
        $sql = "DELETE FROM hop_dong WHERE hop_dong_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa hợp đồng thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa hợp đồng'];
    }
}