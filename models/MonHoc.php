<?php
require_once __DIR__ . '/Model.php';

/**
 * MonHoc Model
 * TÊN CỘT DATABASE: mon_hoc_id, nghe_id, nien_khoa_id, ma_mon_hoc, ten_mon_hoc, 
 * so_tiet_ly_thuyet, so_tiet_thuc_hanh, tong_so_tiet, mo_ta, thu_tu, is_active
 */
class MonHoc extends Model {
    protected $table = 'mon_hoc';
    protected $primaryKey = 'mon_hoc_id';
    
    /**
     * Lấy tất cả môn học với thông tin nghề và niên khóa
     */
    public function getAllWithDetails() {
        $sql = "SELECT 
                    mh.mon_hoc_id,
                    mh.nghe_id,
                    mh.nien_khoa_id,
                    mh.ma_mon_hoc,
                    mh.ten_mon_hoc,
                    mh.so_tiet_ly_thuyet,
                    mh.so_tiet_thuc_hanh,
                    mh.tong_so_tiet,
                    mh.mo_ta,
                    mh.thu_tu,
                    mh.is_active,
                    mh.created_at,
                    mh.updated_at,
                    n.ma_nghe,
                    n.ten_nghe,
                    nk.ma_nien_khoa,
                    nk.ten_nien_khoa,
                    k.ma_khoa,
                    k.ten_khoa
                FROM mon_hoc mh
                LEFT JOIN nghe n ON mh.nghe_id = n.nghe_id
                LEFT JOIN nien_khoa nk ON mh.nien_khoa_id = nk.nien_khoa_id
                LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                ORDER BY k.ten_khoa ASC, n.ten_nghe ASC, mh.thu_tu ASC, mh.ten_mon_hoc ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy môn học theo ID với thông tin chi tiết
     */
    public function getByIdWithDetails($id) {
        $sql = "SELECT 
                    mh.*,
                    n.ten_nghe,
                    nk.ten_nien_khoa
                FROM mon_hoc mh
                LEFT JOIN nghe n ON mh.nghe_id = n.nghe_id
                LEFT JOIN nien_khoa nk ON mh.nien_khoa_id = nk.nien_khoa_id
                WHERE mh.mon_hoc_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy môn học theo nghề
     */
    public function getByNghe($nghe_id, $is_active = 1) {
        $conditions = ['nghe_id' => $nghe_id];
        
        if ($is_active !== null) {
            $conditions['is_active'] = $is_active;
        }
        
        return $this->getAll($conditions, 'thu_tu ASC, ten_mon_hoc ASC');
    }
    
    /**
     * Kiểm tra mã môn học đã tồn tại trong nghề chưa
     */
    public function checkMaMonHocExists($nghe_id, $ma_mon_hoc, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM mon_hoc WHERE nghe_id = :nghe_id AND ma_mon_hoc = :ma_mon_hoc";
        
        if ($exclude_id) {
            $sql .= " AND mon_hoc_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $nghe_id, PDO::PARAM_INT);
        $stmt->bindValue(':ma_mon_hoc', $ma_mon_hoc);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Đếm số hợp đồng thuộc môn học
     */
    public function countHopDong($mon_hoc_id) {
        $sql = "SELECT COUNT(*) FROM hop_dong WHERE mon_hoc_id = :mon_hoc_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':mon_hoc_id', $mon_hoc_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Thêm môn học mới - TÊN CỘT CHÍNH XÁC
     */
    public function createMonHoc($data) {
        $sql = "INSERT INTO mon_hoc (
                    nghe_id,
                    nien_khoa_id,
                    ma_mon_hoc,
                    ten_mon_hoc,
                    so_tiet_ly_thuyet,
                    so_tiet_thuc_hanh,
                    tong_so_tiet,
                    mo_ta,
                    thu_tu,
                    is_active,
                    created_by
                ) VALUES (
                    :nghe_id,
                    :nien_khoa_id,
                    :ma_mon_hoc,
                    :ten_mon_hoc,
                    :so_tiet_ly_thuyet,
                    :so_tiet_thuc_hanh,
                    :tong_so_tiet,
                    :mo_ta,
                    :thu_tu,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ma_mon_hoc', $data['ma_mon_hoc']);
        $stmt->bindValue(':ten_mon_hoc', $data['ten_mon_hoc']);
        $stmt->bindValue(':so_tiet_ly_thuyet', $data['so_tiet_ly_thuyet'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':so_tiet_thuc_hanh', $data['so_tiet_thuc_hanh'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':tong_so_tiet', $data['tong_so_tiet'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật môn học - TÊN CỘT CHÍNH XÁC
     */
    public function updateMonHoc($id, $data) {
        $sql = "UPDATE mon_hoc SET
                    nghe_id = :nghe_id,
                    nien_khoa_id = :nien_khoa_id,
                    ma_mon_hoc = :ma_mon_hoc,
                    ten_mon_hoc = :ten_mon_hoc,
                    so_tiet_ly_thuyet = :so_tiet_ly_thuyet,
                    so_tiet_thuc_hanh = :so_tiet_thuc_hanh,
                    tong_so_tiet = :tong_so_tiet,
                    mo_ta = :mo_ta,
                    thu_tu = :thu_tu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE mon_hoc_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ma_mon_hoc', $data['ma_mon_hoc']);
        $stmt->bindValue(':ten_mon_hoc', $data['ten_mon_hoc']);
        $stmt->bindValue(':so_tiet_ly_thuyet', $data['so_tiet_ly_thuyet'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':so_tiet_thuc_hanh', $data['so_tiet_thuc_hanh'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':tong_so_tiet', $data['tong_so_tiet'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa môn học (soft delete)
     */
    public function deleteMonHoc($id) {
        // Kiểm tra có hợp đồng nào không
        if ($this->countHopDong($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa môn học vì có hợp đồng liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE mon_hoc SET is_active = 0, updated_by = :updated_by WHERE mon_hoc_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa môn học thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa môn học'];
    }
}