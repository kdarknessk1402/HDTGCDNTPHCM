<?php
require_once __DIR__ . '/Model.php';

/**
 * Lop Model
 * TÊN CỘT DATABASE: lop_id, nghe_id, nien_khoa_id, ma_lop, ten_lop, si_so, giao_vien_chu_nhiem, thu_tu, is_active
 */
class Lop extends Model {
    protected $table = 'lop';
    protected $primaryKey = 'lop_id';
    
    /**
     * Lấy tất cả lớp với thông tin nghề và niên khóa
     */
    public function getAllWithDetails() {
        $sql = "SELECT 
                    l.lop_id,
                    l.nghe_id,
                    l.nien_khoa_id,
                    l.ma_lop,
                    l.ten_lop,
                    l.si_so,
                    l.giao_vien_chu_nhiem,
                    l.thu_tu,
                    l.is_active,
                    l.created_at,
                    l.updated_at,
                    n.ma_nghe,
                    n.ten_nghe,
                    nk.ma_nien_khoa,
                    nk.ten_nien_khoa,
                    k.ma_khoa,
                    k.ten_khoa
                FROM lop l
                LEFT JOIN nghe n ON l.nghe_id = n.nghe_id
                LEFT JOIN nien_khoa nk ON l.nien_khoa_id = nk.nien_khoa_id
                LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                ORDER BY k.ten_khoa ASC, n.ten_nghe ASC, l.thu_tu ASC, l.ten_lop ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy lớp theo ID với thông tin chi tiết
     */
    public function getByIdWithDetails($id) {
        $sql = "SELECT 
                    l.*,
                    n.ten_nghe,
                    nk.ten_nien_khoa
                FROM lop l
                LEFT JOIN nghe n ON l.nghe_id = n.nghe_id
                LEFT JOIN nien_khoa nk ON l.nien_khoa_id = nk.nien_khoa_id
                WHERE l.lop_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy lớp theo nghề
     */
    public function getByNghe($nghe_id) {
        return $this->getAll(['nghe_id' => $nghe_id, 'is_active' => 1], 'thu_tu ASC, ten_lop ASC');
    }
    
    /**
     * Kiểm tra mã lớp đã tồn tại trong nghề chưa
     */
    public function checkMaLopExists($nghe_id, $ma_lop, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM lop WHERE nghe_id = :nghe_id AND ma_lop = :ma_lop";
        
        if ($exclude_id) {
            $sql .= " AND lop_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $nghe_id, PDO::PARAM_INT);
        $stmt->bindValue(':ma_lop', $ma_lop);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Đếm số hợp đồng thuộc lớp
     */
    public function countHopDong($lop_id) {
        $sql = "SELECT COUNT(*) FROM hop_dong WHERE lop_id = :lop_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lop_id', $lop_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Thêm lớp mới - TÊN CỘT CHÍNH XÁC
     */
    public function createLop($data) {
        $sql = "INSERT INTO lop (
                    nghe_id,
                    nien_khoa_id,
                    ma_lop,
                    ten_lop,
                    si_so,
                    giao_vien_chu_nhiem,
                    thu_tu,
                    is_active,
                    created_by
                ) VALUES (
                    :nghe_id,
                    :nien_khoa_id,
                    :ma_lop,
                    :ten_lop,
                    :si_so,
                    :giao_vien_chu_nhiem,
                    :thu_tu,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ma_lop', $data['ma_lop']);
        $stmt->bindValue(':ten_lop', $data['ten_lop']);
        $stmt->bindValue(':si_so', $data['si_so'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':giao_vien_chu_nhiem', $data['giao_vien_chu_nhiem'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật lớp - TÊN CỘT CHÍNH XÁC
     */
    public function updateLop($id, $data) {
        $sql = "UPDATE lop SET
                    nghe_id = :nghe_id,
                    nien_khoa_id = :nien_khoa_id,
                    ma_lop = :ma_lop,
                    ten_lop = :ten_lop,
                    si_so = :si_so,
                    giao_vien_chu_nhiem = :giao_vien_chu_nhiem,
                    thu_tu = :thu_tu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE lop_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $data['nghe_id'], PDO::PARAM_INT);
        $stmt->bindValue(':nien_khoa_id', $data['nien_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':ma_lop', $data['ma_lop']);
        $stmt->bindValue(':ten_lop', $data['ten_lop']);
        $stmt->bindValue(':si_so', $data['si_so'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':giao_vien_chu_nhiem', $data['giao_vien_chu_nhiem'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa lớp (soft delete)
     */
    public function deleteLop($id) {
        // Kiểm tra có hợp đồng nào không
        if ($this->countHopDong($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa lớp vì có hợp đồng liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE lop SET is_active = 0, updated_by = :updated_by WHERE lop_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa lớp thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa lớp'];
    }
}