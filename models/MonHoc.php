<?php
/**
 * Model: MonHoc
 * Quản lý môn học
 */

class MonHoc {
    private $db;
    private $table = 'mon_hoc';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả môn học
     */
    public function getAll($nghe_id = null, $nien_khoa_id = null, $is_active = null) {
        $query = "SELECT mh.*, 
                         n.ma_nghe, n.ten_nghe,
                         k.ten_khoa,
                         nk.ten_nien_khoa
                  FROM {$this->table} mh
                  LEFT JOIN nghe n ON mh.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN nien_khoa nk ON mh.nien_khoa_id = nk.nien_khoa_id
                  WHERE 1=1";
        
        if ($nghe_id !== null) $query .= " AND mh.nghe_id = :nghe_id";
        if ($nien_khoa_id !== null) $query .= " AND mh.nien_khoa_id = :nien_khoa_id";
        if ($is_active !== null) $query .= " AND mh.is_active = :is_active";
        
        $query .= " ORDER BY mh.hoc_ky ASC, mh.thu_tu ASC, mh.ten_mon_hoc ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($nghe_id !== null) $stmt->bindParam(':nghe_id', $nghe_id);
        if ($nien_khoa_id !== null) $stmt->bindParam(':nien_khoa_id', $nien_khoa_id);
        if ($is_active !== null) $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy môn học theo ID
     */
    public function getById($id) {
        $query = "SELECT mh.*, n.ten_nghe, k.ten_khoa, nk.ten_nien_khoa
                  FROM {$this->table} mh
                  LEFT JOIN nghe n ON mh.nghe_id = n.nghe_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN nien_khoa nk ON mh.nien_khoa_id = nk.nien_khoa_id
                  WHERE mh.mon_hoc_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo môn học mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (nghe_id, nien_khoa_id, ma_mon_hoc, ten_mon_hoc, so_tin_chi, so_gio_ly_thuyet, so_gio_thuc_hanh, so_gio_chuan, hoc_ky, mo_ta, thu_tu, is_active, created_by)
                  VALUES 
                  (:nghe_id, :nien_khoa_id, :ma_mon_hoc, :ten_mon_hoc, :so_tin_chi, :so_gio_ly_thuyet, :so_gio_thuc_hanh, :so_gio_chuan, :hoc_ky, :mo_ta, :thu_tu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':nien_khoa_id', $data['nien_khoa_id']);
        $stmt->bindParam(':ma_mon_hoc', $data['ma_mon_hoc']);
        $stmt->bindParam(':ten_mon_hoc', $data['ten_mon_hoc']);
        $stmt->bindParam(':so_tin_chi', $data['so_tin_chi']);
        $stmt->bindParam(':so_gio_ly_thuyet', $data['so_gio_ly_thuyet']);
        $stmt->bindParam(':so_gio_thuc_hanh', $data['so_gio_thuc_hanh']);
        $stmt->bindParam(':so_gio_chuan', $data['so_gio_chuan']);
        $stmt->bindParam(':hoc_ky', $data['hoc_ky']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cập nhật môn học
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET nghe_id = :nghe_id,
                      nien_khoa_id = :nien_khoa_id,
                      ma_mon_hoc = :ma_mon_hoc,
                      ten_mon_hoc = :ten_mon_hoc,
                      so_tin_chi = :so_tin_chi,
                      so_gio_ly_thuyet = :so_gio_ly_thuyet,
                      so_gio_thuc_hanh = :so_gio_thuc_hanh,
                      so_gio_chuan = :so_gio_chuan,
                      hoc_ky = :hoc_ky,
                      mo_ta = :mo_ta,
                      thu_tu = :thu_tu,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE mon_hoc_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':nghe_id', $data['nghe_id']);
        $stmt->bindParam(':nien_khoa_id', $data['nien_khoa_id']);
        $stmt->bindParam(':ma_mon_hoc', $data['ma_mon_hoc']);
        $stmt->bindParam(':ten_mon_hoc', $data['ten_mon_hoc']);
        $stmt->bindParam(':so_tin_chi', $data['so_tin_chi']);
        $stmt->bindParam(':so_gio_ly_thuyet', $data['so_gio_ly_thuyet']);
        $stmt->bindParam(':so_gio_thuc_hanh', $data['so_gio_thuc_hanh']);
        $stmt->bindParam(':so_gio_chuan', $data['so_gio_chuan']);
        $stmt->bindParam(':hoc_ky', $data['hoc_ky']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa môn học
     */
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) return false;
        
        $query = "DELETE FROM {$this->table} WHERE mon_hoc_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra ràng buộc
     */
    public function hasRelatedRecords($id) {
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE mon_hoc_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Kiểm tra trùng mã
     */
    public function checkDuplicateMa($ma_mon_hoc, $nghe_id, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE ma_mon_hoc = :ma_mon_hoc AND nghe_id = :nghe_id";
        if ($exclude_id !== null) $query .= " AND mon_hoc_id != :exclude_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_mon_hoc', $ma_mon_hoc);
        $stmt->bindParam(':nghe_id', $nghe_id);
        if ($exclude_id !== null) $stmt->bindParam(':exclude_id', $exclude_id);
        
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    /**
     * Lấy môn học theo nghề và niên khóa (cho dropdown)
     */
    public function getByNgheNienKhoa($nghe_id, $nien_khoa_id, $is_active = 1) {
        $query = "SELECT mon_hoc_id, ma_mon_hoc, ten_mon_hoc, so_gio_chuan, hoc_ky
                  FROM {$this->table}
                  WHERE nghe_id = :nghe_id AND nien_khoa_id = :nien_khoa_id";
        
        if ($is_active !== null) $query .= " AND is_active = :is_active";
        $query .= " ORDER BY hoc_ky ASC, thu_tu ASC, ten_mon_hoc ASC";
        
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
        $query = "UPDATE {$this->table} SET is_active = :is_active, updated_by = :updated_by WHERE mon_hoc_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}