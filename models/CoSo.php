<?php
/**
 * Model: CoSo
 * Quản lý cơ sở đào tạo
 */

class CoSo {
    private $db;
    private $table = 'co_so';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả cơ sở
     */
    public function getAll($is_active = null) {
        $query = "SELECT * FROM {$this->table} WHERE 1=1";
        
        if ($is_active !== null) {
            $query .= " AND is_active = :is_active";
        }
        
        $query .= " ORDER BY thu_tu ASC, ten_co_so ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($is_active !== null) {
            $stmt->bindParam(':is_active', $is_active);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy cơ sở theo ID
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE co_so_id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo cơ sở mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (ma_co_so, ten_co_so, dia_chi, so_dien_thoai, email, nguoi_phu_trach, thu_tu, is_active, created_by)
                  VALUES 
                  (:ma_co_so, :ten_co_so, :dia_chi, :so_dien_thoai, :email, :nguoi_phu_trach, :thu_tu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':ma_co_so', $data['ma_co_so']);
        $stmt->bindParam(':ten_co_so', $data['ten_co_so']);
        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':nguoi_phu_trach', $data['nguoi_phu_trach']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cập nhật cơ sở
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET ma_co_so = :ma_co_so,
                      ten_co_so = :ten_co_so,
                      dia_chi = :dia_chi,
                      so_dien_thoai = :so_dien_thoai,
                      email = :email,
                      nguoi_phu_trach = :nguoi_phu_trach,
                      thu_tu = :thu_tu,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE co_so_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':ma_co_so', $data['ma_co_so']);
        $stmt->bindParam(':ten_co_so', $data['ten_co_so']);
        $stmt->bindParam(':dia_chi', $data['dia_chi']);
        $stmt->bindParam(':so_dien_thoai', $data['so_dien_thoai']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':nguoi_phu_trach', $data['nguoi_phu_trach']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa cơ sở
     */
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) return false;
        
        $query = "DELETE FROM {$this->table} WHERE co_so_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra ràng buộc
     */
    public function hasRelatedRecords($id) {
        // Kiểm tra đơn giá
        $query = "SELECT COUNT(*) as count FROM don_gia_gio_day WHERE co_so_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) return true;
        
        // Kiểm tra hợp đồng
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE co_so_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) return true;
        
        return false;
    }
    
    /**
     * Kiểm tra trùng mã
     */
    public function checkDuplicateMa($ma_co_so, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE ma_co_so = :ma_co_so";
        if ($exclude_id !== null) $query .= " AND co_so_id != :exclude_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_co_so', $ma_co_so);
        if ($exclude_id !== null) $stmt->bindParam(':exclude_id', $exclude_id);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $is_active, $updated_by) {
        $query = "UPDATE {$this->table} SET is_active = :is_active, updated_by = :updated_by WHERE co_so_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}