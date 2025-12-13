<?php
/**
 * Model: DonGia
 * Quản lý đơn giá giờ dạy theo cơ sở và trình độ
 * QUAN TRỌNG: Dùng để tính tiền hợp đồng tự động
 */

class DonGia {
    private $db;
    private $table = 'don_gia_gio_day';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả đơn giá
     */
    public function getAll($co_so_id = null, $trinh_do_id = null, $is_active = null) {
        $query = "SELECT dg.*, 
                         cs.ma_co_so, cs.ten_co_so,
                         td.ma_trinh_do, td.ten_trinh_do,
                         u1.full_name as created_by_name
                  FROM {$this->table} dg
                  LEFT JOIN co_so cs ON dg.co_so_id = cs.co_so_id
                  LEFT JOIN trinh_do_chuyen_mon td ON dg.trinh_do_id = td.trinh_do_id
                  LEFT JOIN users u1 ON dg.created_by = u1.user_id
                  WHERE 1=1";
        
        if ($co_so_id !== null) $query .= " AND dg.co_so_id = :co_so_id";
        if ($trinh_do_id !== null) $query .= " AND dg.trinh_do_id = :trinh_do_id";
        if ($is_active !== null) $query .= " AND dg.is_active = :is_active";
        
        $query .= " ORDER BY dg.ngay_ap_dung DESC, cs.ten_co_so ASC, td.thu_tu ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($co_so_id !== null) $stmt->bindParam(':co_so_id', $co_so_id);
        if ($trinh_do_id !== null) $stmt->bindParam(':trinh_do_id', $trinh_do_id);
        if ($is_active !== null) $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy đơn giá theo ID
     */
    public function getById($id) {
        $query = "SELECT dg.*, cs.ten_co_so, td.ten_trinh_do
                  FROM {$this->table} dg
                  LEFT JOIN co_so cs ON dg.co_so_id = cs.co_so_id
                  LEFT JOIN trinh_do_chuyen_mon td ON dg.trinh_do_id = td.trinh_do_id
                  WHERE dg.don_gia_id = :id LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo đơn giá mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (co_so_id, trinh_do_id, don_gia, ngay_ap_dung, ngay_ket_thuc, ghi_chu, is_active, created_by)
                  VALUES 
                  (:co_so_id, :trinh_do_id, :don_gia, :ngay_ap_dung, :ngay_ket_thuc, :ghi_chu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':co_so_id', $data['co_so_id']);
        $stmt->bindParam(':trinh_do_id', $data['trinh_do_id']);
        $stmt->bindParam(':don_gia', $data['don_gia']);
        $stmt->bindParam(':ngay_ap_dung', $data['ngay_ap_dung']);
        $stmt->bindParam(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cập nhật đơn giá
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET co_so_id = :co_so_id,
                      trinh_do_id = :trinh_do_id,
                      don_gia = :don_gia,
                      ngay_ap_dung = :ngay_ap_dung,
                      ngay_ket_thuc = :ngay_ket_thuc,
                      ghi_chu = :ghi_chu,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE don_gia_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':co_so_id', $data['co_so_id']);
        $stmt->bindParam(':trinh_do_id', $data['trinh_do_id']);
        $stmt->bindParam(':don_gia', $data['don_gia']);
        $stmt->bindParam(':ngay_ap_dung', $data['ngay_ap_dung']);
        $stmt->bindParam(':ngay_ket_thuc', $data['ngay_ket_thuc']);
        $stmt->bindParam(':ghi_chu', $data['ghi_chu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa đơn giá
     */
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) return false;
        
        $query = "DELETE FROM {$this->table} WHERE don_gia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra ràng buộc
     */
    public function hasRelatedRecords($id) {
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE don_gia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Lấy đơn giá hiện hành (theo ngày) - QUAN TRỌNG cho hợp đồng
     */
    public function getCurrentDonGia($co_so_id, $trinh_do_id, $ngay = null) {
        if ($ngay === null) $ngay = date('Y-m-d');
        
        $query = "SELECT * 
                  FROM {$this->table}
                  WHERE co_so_id = :co_so_id
                  AND trinh_do_id = :trinh_do_id
                  AND ngay_ap_dung <= :ngay
                  AND (ngay_ket_thuc IS NULL OR ngay_ket_thuc >= :ngay)
                  AND is_active = 1
                  ORDER BY ngay_ap_dung DESC
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':co_so_id', $co_so_id);
        $stmt->bindParam(':trinh_do_id', $trinh_do_id);
        $stmt->bindParam(':ngay', $ngay);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $is_active, $updated_by) {
        $query = "UPDATE {$this->table} SET is_active = :is_active, updated_by = :updated_by WHERE don_gia_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}