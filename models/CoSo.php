<?php
require_once __DIR__ . '/Model.php';

/**
 * CoSo Model
 * TÊN CỘT DATABASE: co_so_id, ma_co_so, ten_co_so, dia_chi, so_dien_thoai, 
 * email, nguoi_phu_trach, mo_ta, thu_tu, is_active
 */
class CoSo extends Model {
    protected $table = 'co_so';
    protected $primaryKey = 'co_so_id';
    
    /**
     * Lấy tất cả cơ sở
     */
    public function getAllCoSo() {
        return $this->getAll([], 'thu_tu ASC, ten_co_so ASC');
    }
    
    /**
     * Lấy danh sách cơ sở active
     */
    public function getActiveList() {
        return $this->getAll(['is_active' => 1], 'thu_tu ASC, ten_co_so ASC');
    }
    
    /**
     * Lấy cơ sở theo ID
     */
    public function getCoSoById($id) {
        return $this->getById($id);
    }
    
    /**
     * Kiểm tra mã cơ sở đã tồn tại chưa
     */
    public function checkMaCoSoExists($ma_co_so, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM co_so WHERE ma_co_so = :ma_co_so";
        
        if ($exclude_id) {
            $sql .= " AND co_so_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_co_so', $ma_co_so);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Đếm số hợp đồng thuộc cơ sở
     */
    public function countHopDong($co_so_id) {
        $sql = "SELECT COUNT(*) FROM hop_dong WHERE co_so_id = :co_so_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $co_so_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Đếm số đơn giá thuộc cơ sở
     */
    public function countDonGia($co_so_id) {
        $sql = "SELECT COUNT(*) FROM don_gia_gio_day WHERE co_so_id = :co_so_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $co_so_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Thêm cơ sở mới - TÊN CỘT CHÍNH XÁC
     */
    public function createCoSo($data) {
        $sql = "INSERT INTO co_so (
                    ma_co_so,
                    ten_co_so,
                    dia_chi,
                    so_dien_thoai,
                    email,
                    nguoi_phu_trach,
                    mo_ta,
                    thu_tu,
                    is_active,
                    created_by
                ) VALUES (
                    :ma_co_so,
                    :ten_co_so,
                    :dia_chi,
                    :so_dien_thoai,
                    :email,
                    :nguoi_phu_trach,
                    :mo_ta,
                    :thu_tu,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_co_so', $data['ma_co_so']);
        $stmt->bindValue(':ten_co_so', $data['ten_co_so']);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':nguoi_phu_trach', $data['nguoi_phu_trach'] ?? null);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật cơ sở - TÊN CỘT CHÍNH XÁC
     */
    public function updateCoSo($id, $data) {
        $sql = "UPDATE co_so SET
                    ma_co_so = :ma_co_so,
                    ten_co_so = :ten_co_so,
                    dia_chi = :dia_chi,
                    so_dien_thoai = :so_dien_thoai,
                    email = :email,
                    nguoi_phu_trach = :nguoi_phu_trach,
                    mo_ta = :mo_ta,
                    thu_tu = :thu_tu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE co_so_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_co_so', $data['ma_co_so']);
        $stmt->bindValue(':ten_co_so', $data['ten_co_so']);
        $stmt->bindValue(':dia_chi', $data['dia_chi'] ?? null);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':nguoi_phu_trach', $data['nguoi_phu_trach'] ?? null);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa cơ sở (soft delete)
     */
    public function deleteCoSo($id) {
        // Kiểm tra có hợp đồng nào không
        if ($this->countHopDong($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa cơ sở vì có hợp đồng liên quan'];
        }
        
        // Kiểm tra có đơn giá nào không
        if ($this->countDonGia($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa cơ sở vì có đơn giá giờ dạy liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE co_so SET is_active = 0, updated_by = :updated_by WHERE co_so_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa cơ sở thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa cơ sở'];
    }
}