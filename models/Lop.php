<?php
/**
 * Model: Lop
 * Quản lý lớp học
 */

class Lop {
    private $db;
    private $table = 'lop';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả lớp
     */
    public function getAll($nghe_id = null, $nien_khoa_id = null, $is_active = null) {
        $query = "SELECT l.*, 
                         n.ma_nghe, n.ten_nghe,
                         k.ten_khoa,
                         nk.ten_nien_khoa
                  FROM {$this->table} l
                  LEFT JOIN nghe n ON l.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN nien_khoa nk ON l.nien_khoa_id = nk.nien_khoa_id
                  WHERE 1=1";
        
        if ($nghe_id !== null) $query .= " AND l.nghe_id = :nghe_id";
        if ($nien_khoa_id !== null) $query .= " AND l.nien_khoa_id = :nien_khoa_id";
        if ($is_active !== null) $query .= " AND l.is_active = :is_active";
        
        $query .= " ORDER BY l.ten_lop ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($nghe_id !== null) $stmt->bindParam(':nghe_id', $nghe_id);
        if ($nien_khoa_id !== null) $stmt->bindParam(':nien_khoa_id', $nien_khoa_id);
        if ($is_active !== null) $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy lớp theo ID
     */
    public function getById($id) {
        $query = "SELECT l.*, n.ten_nghe, k.ten_khoa, nk.ten_nien_khoa
                  FROM {$this->table} l
                  LEFT JOIN nghe n ON l.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN nien_khoa nk ON l.nien_khoa_id = nk.nien_khoa_id
                  WHERE l.lop_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo lớp mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nghe_id, nien_khoa_id, ma_lop, ten_lop, si_so, giao_vien_chu_nhiem, thu_tu, is_active, created_by)
                  VALUES 
                  (:nghe_id, :nien_khoa_id, :ma_lop, :ten_lop, :si_so, :giao_vien_chu_nhiem, :thu_tu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':nien_khoa_id', $data['nien_khoa_id']);
        $stmt->bindParam(':ma_lop', $data['ma_lop']);
        $stmt->bindParam(':ten_lop', $data['ten_lop']);
        $stmt->bindParam(':si_so', $data['si_so']);
        $stmt->bindParam(':giao_vien_chu_nhiem', $data['giao_vien_chu_nhiem']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cập nhật lớp
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET nghe_id = :nghe_id,
                      nien_khoa_id = :nien_khoa_id,
                      ma_lop = :ma_lop,
                      ten_lop = :ten_lop,
                      si_so = :si_so,
                      giao_vien_chu_nhiem = :giao_vien_chu_nhiem,
                      thu_tu = :thu_tu,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE lop_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':nien_khoa_id', $data['nien_khoa_id']);
        $stmt->bindParam(':ma_lop', $data['ma_lop']);
        $stmt->bindParam(':ten_lop', $data['ten_lop']);
        $stmt->bindParam(':si_so', $data['si_so']);
        $stmt->bindParam(':giao_vien_chu_nhiem', $data['giao_vien_chu_nhiem']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa lớp
     */
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) return false;
        
        $query = "DELETE FROM {$this->table} WHERE lop_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra ràng buộc
     */
    public function hasRelatedRecords($id) {
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE lop_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Kiểm tra trùng mã
     */
    public function checkDuplicateMa($ma_lop, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE ma_lop = :ma_lop";
        if ($exclude_id !== null) $query .= " AND lop_id != :exclude_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_lop', $ma_lop);
        if ($exclude_id !== null) $stmt->bindParam(':exclude_id', $exclude_id);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Lấy lớp theo nghề và niên khóa (cho dropdown)
     */
    public function getByNgheNienKhoa($nghe_id, $nien_khoa_id, $is_active = 1) {
        $query = "SELECT lop_id, ma_lop, ten_lop, si_so
                  FROM {$this->table}
                  WHERE nghe_id = :nghe_id AND nien_khoa_id = :nien_khoa_id";
        
        if ($is_active !== null) $query .= " AND is_active = :is_active";
        $query .= " ORDER BY ten_lop ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nghe_id', $nghe_id);
        $stmt->bindParam(':nien_khoa_id', $nien_khoa_id);
        if ($is_active !== null) $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cập nhật trạng thái
     */
    public function updateStatus($id, $is_active, $updated_by) {
        $query = "UPDATE {$this->table} SET is_active = :is_active, updated_by = :updated_by WHERE lop_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}