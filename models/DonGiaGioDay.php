<?php
require_once __DIR__ . '/Model.php';

/**
 * DonGiaGioDay Model
 * TÊN CỘT DATABASE: don_gia_id, co_so_id, trinh_do_id, don_gia, nam_ap_dung, 
 * tu_ngay, den_ngay, mo_ta, is_active
 */
class DonGiaGioDay extends Model {
    protected $table = 'don_gia_gio_day';
    protected $primaryKey = 'don_gia_id';
    
    /**
     * Lấy tất cả đơn giá với thông tin cơ sở và trình độ
     */
    public function getAllWithDetails() {
        $sql = "SELECT 
                    dg.don_gia_id,
                    dg.co_so_id,
                    dg.trinh_do_id,
                    dg.don_gia,
                    dg.nam_ap_dung,
                    dg.tu_ngay,
                    dg.den_ngay,
                    dg.mo_ta,
                    dg.is_active,
                    dg.created_at,
                    dg.updated_at,
                    cs.ma_co_so,
                    cs.ten_co_so,
                    td.ma_trinh_do,
                    td.ten_trinh_do
                FROM don_gia_gio_day dg
                LEFT JOIN co_so cs ON dg.co_so_id = cs.co_so_id
                LEFT JOIN trinh_do_chuyen_mon td ON dg.trinh_do_id = td.trinh_do_id
                ORDER BY dg.nam_ap_dung DESC, cs.ten_co_so ASC, td.ten_trinh_do ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy đơn giá theo ID với thông tin chi tiết
     */
    public function getByIdWithDetails($id) {
        $sql = "SELECT 
                    dg.*,
                    cs.ten_co_so,
                    td.ten_trinh_do
                FROM don_gia_gio_day dg
                LEFT JOIN co_so cs ON dg.co_so_id = cs.co_so_id
                LEFT JOIN trinh_do_chuyen_mon td ON dg.trinh_do_id = td.trinh_do_id
                WHERE dg.don_gia_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy đơn giá theo cơ sở và trình độ
     */
    public function getByCoSoAndTrinhDo($co_so_id, $trinh_do_id, $nam_ap_dung = null) {
        $sql = "SELECT * FROM don_gia_gio_day 
                WHERE co_so_id = :co_so_id 
                AND trinh_do_id = :trinh_do_id
                AND is_active = 1";
        
        if ($nam_ap_dung) {
            $sql .= " AND nam_ap_dung = :nam_ap_dung";
        }
        
        $sql .= " ORDER BY nam_ap_dung DESC LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $co_so_id, PDO::PARAM_INT);
        $stmt->bindValue(':trinh_do_id', $trinh_do_id, PDO::PARAM_INT);
        
        if ($nam_ap_dung) {
            $stmt->bindValue(':nam_ap_dung', $nam_ap_dung, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Kiểm tra đơn giá đã tồn tại chưa (cùng cơ sở, trình độ, năm)
     */
    public function checkExists($co_so_id, $trinh_do_id, $nam_ap_dung, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM don_gia_gio_day 
                WHERE co_so_id = :co_so_id 
                AND trinh_do_id = :trinh_do_id 
                AND nam_ap_dung = :nam_ap_dung";
        
        if ($exclude_id) {
            $sql .= " AND don_gia_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $co_so_id, PDO::PARAM_INT);
        $stmt->bindValue(':trinh_do_id', $trinh_do_id, PDO::PARAM_INT);
        $stmt->bindValue(':nam_ap_dung', $nam_ap_dung, PDO::PARAM_INT);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Thêm đơn giá mới - TÊN CỘT CHÍNH XÁC
     */
    public function createDonGia($data) {
        $sql = "INSERT INTO don_gia_gio_day (
                    co_so_id,
                    trinh_do_id,
                    don_gia,
                    nam_ap_dung,
                    tu_ngay,
                    den_ngay,
                    mo_ta,
                    is_active,
                    created_by
                ) VALUES (
                    :co_so_id,
                    :trinh_do_id,
                    :don_gia,
                    :nam_ap_dung,
                    :tu_ngay,
                    :den_ngay,
                    :mo_ta,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $data['co_so_id'], PDO::PARAM_INT);
        $stmt->bindValue(':trinh_do_id', $data['trinh_do_id'], PDO::PARAM_INT);
        $stmt->bindValue(':don_gia', $data['don_gia'], PDO::PARAM_INT);
        $stmt->bindValue(':nam_ap_dung', $data['nam_ap_dung'], PDO::PARAM_INT);
        $stmt->bindValue(':tu_ngay', $data['tu_ngay'] ?? null);
        $stmt->bindValue(':den_ngay', $data['den_ngay'] ?? null);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật đơn giá - TÊN CỘT CHÍNH XÁC
     */
    public function updateDonGia($id, $data) {
        $sql = "UPDATE don_gia_gio_day SET
                    co_so_id = :co_so_id,
                    trinh_do_id = :trinh_do_id,
                    don_gia = :don_gia,
                    nam_ap_dung = :nam_ap_dung,
                    tu_ngay = :tu_ngay,
                    den_ngay = :den_ngay,
                    mo_ta = :mo_ta,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE don_gia_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':co_so_id', $data['co_so_id'], PDO::PARAM_INT);
        $stmt->bindValue(':trinh_do_id', $data['trinh_do_id'], PDO::PARAM_INT);
        $stmt->bindValue(':don_gia', $data['don_gia'], PDO::PARAM_INT);
        $stmt->bindValue(':nam_ap_dung', $data['nam_ap_dung'], PDO::PARAM_INT);
        $stmt->bindValue(':tu_ngay', $data['tu_ngay'] ?? null);
        $stmt->bindValue(':den_ngay', $data['den_ngay'] ?? null);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa đơn giá (soft delete)
     */
    public function deleteDonGia($id) {
        $sql = "UPDATE don_gia_gio_day SET is_active = 0, updated_by = :updated_by WHERE don_gia_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa đơn giá thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa đơn giá'];
    }
}