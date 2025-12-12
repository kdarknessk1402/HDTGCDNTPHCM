<?php
require_once __DIR__ . '/Model.php';

/**
 * Nghe Model
 * TÊN CỘT DATABASE: nghe_id, khoa_id, ma_nghe, ten_nghe, mo_ta, so_nam_dao_tao, thu_tu, is_active
 */
class Nghe extends Model {
    protected $table = 'nghe';
    protected $primaryKey = 'nghe_id';
    
    /**
     * Lấy tất cả nghề với thông tin khoa
     */
    public function getAllWithKhoa() {
        $sql = "SELECT 
                    n.nghe_id,
                    n.khoa_id,
                    n.ma_nghe,
                    n.ten_nghe,
                    n.mo_ta,
                    n.so_nam_dao_tao,
                    n.thu_tu,
                    n.is_active,
                    n.created_at,
                    n.updated_at,
                    k.ma_khoa,
                    k.ten_khoa
                FROM nghe n
                LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                ORDER BY k.ten_khoa ASC, n.thu_tu ASC, n.ten_nghe ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy nghề theo ID với thông tin khoa
     */
    public function getByIdWithKhoa($id) {
        $sql = "SELECT 
                    n.*,
                    k.ten_khoa
                FROM nghe n
                LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                WHERE n.nghe_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy nghề theo khoa
     */
    public function getByKhoa($khoa_id) {
        return $this->getAll(['khoa_id' => $khoa_id, 'is_active' => 1], 'thu_tu ASC, ten_nghe ASC');
    }
    
    /**
     * Kiểm tra mã nghề đã tồn tại trong khoa chưa
     */
    public function checkMaNgheExists($khoa_id, $ma_nghe, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM nghe WHERE khoa_id = :khoa_id AND ma_nghe = :ma_nghe";
        
        if ($exclude_id) {
            $sql .= " AND nghe_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':khoa_id', $khoa_id, PDO::PARAM_INT);
        $stmt->bindValue(':ma_nghe', $ma_nghe);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Đếm số lớp thuộc nghề
     */
    public function countLop($nghe_id) {
        $sql = "SELECT COUNT(*) FROM lop WHERE nghe_id = :nghe_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $nghe_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Đếm số môn học thuộc nghề
     */
    public function countMonHoc($nghe_id) {
        $sql = "SELECT COUNT(*) FROM mon_hoc WHERE nghe_id = :nghe_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nghe_id', $nghe_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Thêm nghề mới - TÊN CỘT CHÍNH XÁC
     */
    public function createNghe($data) {
        $sql = "INSERT INTO nghe (
                    khoa_id,
                    ma_nghe,
                    ten_nghe,
                    mo_ta,
                    so_nam_dao_tao,
                    thu_tu,
                    is_active,
                    created_by
                ) VALUES (
                    :khoa_id,
                    :ma_nghe,
                    :ten_nghe,
                    :mo_ta,
                    :so_nam_dao_tao,
                    :thu_tu,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':khoa_id', $data['khoa_id'], PDO::PARAM_INT);
        $stmt->bindValue(':ma_nghe', $data['ma_nghe']);
        $stmt->bindValue(':ten_nghe', $data['ten_nghe']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':so_nam_dao_tao', $data['so_nam_dao_tao'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật nghề - TÊN CỘT CHÍNH XÁC
     */
    public function updateNghe($id, $data) {
        $sql = "UPDATE nghe SET
                    khoa_id = :khoa_id,
                    ma_nghe = :ma_nghe,
                    ten_nghe = :ten_nghe,
                    mo_ta = :mo_ta,
                    so_nam_dao_tao = :so_nam_dao_tao,
                    thu_tu = :thu_tu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE nghe_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':khoa_id', $data['khoa_id'], PDO::PARAM_INT);
        $stmt->bindValue(':ma_nghe', $data['ma_nghe']);
        $stmt->bindValue(':ten_nghe', $data['ten_nghe']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':so_nam_dao_tao', $data['so_nam_dao_tao'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa nghề (soft delete)
     */
    public function deleteNghe($id) {
        // Kiểm tra có lớp nào không
        if ($this->countLop($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa nghề vì có lớp liên quan'];
        }
        
        // Kiểm tra có môn học nào không
        if ($this->countMonHoc($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa nghề vì có môn học liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE nghe SET is_active = 0, updated_by = :updated_by WHERE nghe_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa nghề thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa nghề'];
    }
}