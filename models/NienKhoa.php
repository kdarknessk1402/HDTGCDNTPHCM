<?php
/**
 * Model: NienKhoa
 * Quản lý niên khóa đào tạo
 */

class NienKhoa {
    private $db;
    private $table = 'nien_khoa';
    
    public $nien_khoa_id;
    public $nghe_id;
    public $cap_do_id;
    public $ma_nien_khoa;
    public $ten_nien_khoa;
    public $nam_bat_dau;
    public $nam_ket_thuc;
    public $mo_ta;
    public $is_active;
    public $created_by;
    public $updated_by;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả niên khóa
     */
    public function getAll($nghe_id = null, $cap_do_id = null, $is_active = null) {
        $query = "SELECT nk.*, 
                         n.ma_nghe, n.ten_nghe, 
                         k.ma_khoa, k.ten_khoa,
                         cd.ten_cap_do, cd.ma_cap_do,
                         u1.full_name as created_by_name
                  FROM {$this->table} nk
                  LEFT JOIN nghe n ON nk.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN cap_do_giang_day cd ON nk.cap_do_id = cd.cap_do_id
                  LEFT JOIN users u1 ON nk.created_by = u1.user_id
                  WHERE 1=1";
        
        if ($nghe_id !== null) {
            $query .= " AND nk.nghe_id = :nghe_id";
        }
        
        if ($cap_do_id !== null) {
            $query .= " AND nk.cap_do_id = :cap_do_id";
        }
        
        if ($is_active !== null) {
            $query .= " AND nk.is_active = :is_active";
        }
        
        $query .= " ORDER BY nk.nam_bat_dau DESC, nk.ten_nien_khoa ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($nghe_id !== null) {
            $stmt->bindParam(':nghe_id', $nghe_id);
        }
        
        if ($cap_do_id !== null) {
            $stmt->bindParam(':cap_do_id', $cap_do_id);
        }
        
        if ($is_active !== null) {
            $stmt->bindParam(':is_active', $is_active);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy niên khóa theo ID
     */
    public function getById($id) {
        $query = "SELECT nk.*, 
                         n.ma_nghe, n.ten_nghe, n.khoa_id,
                         k.ma_khoa, k.ten_khoa,
                         cd.ten_cap_do, cd.ma_cap_do
                  FROM {$this->table} nk
                  LEFT JOIN nghe n ON nk.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN cap_do_giang_day cd ON nk.cap_do_id = cd.cap_do_id
                  WHERE nk.nien_khoa_id = :id
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo niên khóa mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nghe_id, cap_do_id, ma_nien_khoa, ten_nien_khoa, nam_bat_dau, nam_ket_thuc, mo_ta, is_active, created_by)
                  VALUES 
                  (:nghe_id, :cap_do_id, :ma_nien_khoa, :ten_nien_khoa, :nam_bat_dau, :nam_ket_thuc, :mo_ta, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':cap_do_id', $data['cap_do_id']);
        $stmt->bindParam(':ma_nien_khoa', $data['ma_nien_khoa']);
        $stmt->bindParam(':ten_nien_khoa', $data['ten_nien_khoa']);
        $stmt->bindParam(':nam_bat_dau', $data['nam_bat_dau']);
        $stmt->bindParam(':nam_ket_thuc', $data['nam_ket_thuc']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật niên khóa
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET nghe_id = :nghe_id,
                      cap_do_id = :cap_do_id,
                      ma_nien_khoa = :ma_nien_khoa,
                      ten_nien_khoa = :ten_nien_khoa,
                      nam_bat_dau = :nam_bat_dau,
                      nam_ket_thuc = :nam_ket_thuc,
                      mo_ta = :mo_ta,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE nien_khoa_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':cap_do_id', $data['cap_do_id']);
        $stmt->bindParam(':ma_nien_khoa', $data['ma_nien_khoa']);
        $stmt->bindParam(':ten_nien_khoa', $data['ten_nien_khoa']);
        $stmt->bindParam(':nam_bat_dau', $data['nam_bat_dau']);
        $stmt->bindParam(':nam_ket_thuc', $data['nam_ket_thuc']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa niên khóa
     */
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) {
            return false;
        }
        
        $query = "DELETE FROM {$this->table} WHERE nien_khoa_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra ràng buộc
     */
    public function hasRelatedRecords($id) {
        // Kiểm tra lớp
        $query = "SELECT COUNT(*) as count FROM lop WHERE nien_khoa_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) return true;
        
        // Kiểm tra môn học
        $query = "SELECT COUNT(*) as count FROM mon_hoc WHERE nien_khoa_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) return true;
        
        // Kiểm tra hợp đồng
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE nien_khoa_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        if ($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) return true;
        
        return false;
    }
    
    /**
     * Kiểm tra trùng
     */
    public function checkDuplicate($nghe_id, $cap_do_id, $ten_nien_khoa, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE nghe_id = :nghe_id 
                  AND cap_do_id = :cap_do_id
                  AND ten_nien_khoa = :ten_nien_khoa";
        
        if ($exclude_id !== null) {
            $query .= " AND nien_khoa_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nghe_id', $nghe_id);
        $stmt->bindParam(':cap_do_id', $cap_do_id);
        $stmt->bindParam(':ten_nien_khoa', $ten_nien_khoa);
        
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Lấy niên khóa theo nghề (cho dropdown)
     */
    public function getByNghe($nghe_id, $is_active = 1) {
        $query = "SELECT nk.nien_khoa_id, nk.ma_nien_khoa, nk.ten_nien_khoa, 
                         nk.nam_bat_dau, nk.nam_ket_thuc,
                         cd.ten_cap_do
                  FROM {$this->table} nk
                  LEFT JOIN cap_do_giang_day cd ON nk.cap_do_id = cd.cap_do_id
                  WHERE nk.nghe_id = :nghe_id";
        
        if ($is_active !== null) {
            $query .= " AND nk.is_active = :is_active";
        }
        
        $query .= " ORDER BY nk.nam_bat_dau DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nghe_id', $nghe_id);
        
        if ($is_active !== null) {
            $stmt->bindParam(':is_active', $is_active);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $is_active, $updated_by) {
        $query = "UPDATE {$this->table}
                  SET is_active = :is_active,
                      updated_by = :updated_by
                  WHERE nien_khoa_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Đếm số lượng theo nghề
     */
    public function countByNghe($nghe_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE nghe_id = :nghe_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nghe_id', $nghe_id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}