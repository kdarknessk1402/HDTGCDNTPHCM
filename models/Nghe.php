<?php
/**
 * Model: Nghe
 * Quản lý các nghề đào tạo
 */

class Nghe {
    private $db;
    
    // Tên bảng
    private $table = 'nghe';
    
    // Các cột
    public $nghe_id;
    public $khoa_id;
    public $ma_nghe;
    public $ten_nghe;
    public $mo_ta;
    public $so_nam_dao_tao;
    public $thu_tu;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $updated_at;
    
    /**
     * Constructor
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả nghề (có filter)
     */
    public function getAll($khoa_id = null, $is_active = null) {
        $query = "SELECT n.*, k.ten_khoa, k.ma_khoa,
                         u1.full_name as created_by_name,
                         u2.full_name as updated_by_name
                  FROM {$this->table} n
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  LEFT JOIN users u1 ON n.created_by = u1.user_id
                  LEFT JOIN users u2 ON n.updated_by = u2.user_id
                  WHERE 1=1";
        
        if ($khoa_id !== null) {
            $query .= " AND n.khoa_id = :khoa_id";
        }
        
        if ($is_active !== null) {
            $query .= " AND n.is_active = :is_active";
        }
        
        $query .= " ORDER BY n.thu_tu ASC, n.ten_nghe ASC";
        
        $stmt = $this->db->prepare($query);
        
        if ($khoa_id !== null) {
            $stmt->bindParam(':khoa_id', $khoa_id);
        }
        
        if ($is_active !== null) {
            $stmt->bindParam(':is_active', $is_active);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy nghề theo ID
     */
    public function getById($id) {
        $query = "SELECT n.*, k.ten_khoa, k.ma_khoa
                  FROM {$this->table} n
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  WHERE n.nghe_id = :id
                  LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tạo nghề mới
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (khoa_id, ma_nghe, ten_nghe, mo_ta, so_nam_dao_tao, thu_tu, is_active, created_by)
                  VALUES 
                  (:khoa_id, :ma_nghe, :ten_nghe, :mo_ta, :so_nam_dao_tao, :thu_tu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        // Bind values
        $stmt->bindParam(':khoa_id', $data['khoa_id']);
        $stmt->bindParam(':ma_nghe', $data['ma_nghe']);
        $stmt->bindParam(':ten_nghe', $data['ten_nghe']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':so_nam_dao_tao', $data['so_nam_dao_tao']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':created_by', $data['created_by']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Cập nhật nghề
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET khoa_id = :khoa_id,
                      ma_nghe = :ma_nghe,
                      ten_nghe = :ten_nghe,
                      mo_ta = :mo_ta,
                      so_nam_dao_tao = :so_nam_dao_tao,
                      thu_tu = :thu_tu,
                      is_active = :is_active,
                      updated_by = :updated_by
                  WHERE nghe_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        // Bind values
        $stmt->bindParam(':khoa_id', $data['khoa_id']);
        $stmt->bindParam(':ma_nghe', $data['ma_nghe']);
        $stmt->bindParam(':ten_nghe', $data['ten_nghe']);
        $stmt->bindParam(':mo_ta', $data['mo_ta']);
        $stmt->bindParam(':so_nam_dao_tao', $data['so_nam_dao_tao']);
        $stmt->bindParam(':thu_tu', $data['thu_tu']);
        $stmt->bindParam(':is_active', $data['is_active']);
        $stmt->bindParam(':updated_by', $data['updated_by']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa nghề
     */
    public function delete($id) {
        // Kiểm tra ràng buộc trước khi xóa
        if ($this->hasRelatedRecords($id)) {
            return false;
        }
        
        $query = "DELETE FROM {$this->table} WHERE nghe_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    /**
     * Kiểm tra xem nghề có ràng buộc với bảng khác không
     */
    public function hasRelatedRecords($id) {
        // Kiểm tra niên khóa
        $query = "SELECT COUNT(*) as count FROM nien_khoa WHERE nghe_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return true;
        }
        
        // Kiểm tra lớp
        $query = "SELECT COUNT(*) as count FROM lop WHERE nghe_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return true;
        }
        
        // Kiểm tra môn học
        $query = "SELECT COUNT(*) as count FROM mon_hoc WHERE nghe_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return true;
        }
        
        // Kiểm tra hợp đồng
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE nghe_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result['count'] > 0) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Kiểm tra trùng mã nghề
     */
    public function checkDuplicateMa($ma_nghe, $khoa_id, $exclude_id = null) {
        $query = "SELECT COUNT(*) as count 
                  FROM {$this->table} 
                  WHERE ma_nghe = :ma_nghe 
                  AND khoa_id = :khoa_id";
        
        if ($exclude_id !== null) {
            $query .= " AND nghe_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':ma_nghe', $ma_nghe);
        $stmt->bindParam(':khoa_id', $khoa_id);
        
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] > 0;
    }
    
    /**
     * Đếm số lượng nghề theo khoa
     */
    public function countByKhoa($khoa_id) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE khoa_id = :khoa_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':khoa_id', $khoa_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
    
    /**
     * Lấy nghề theo khoa (dùng cho dropdown)
     */
    public function getByKhoa($khoa_id, $is_active = 1) {
        $query = "SELECT nghe_id, ma_nghe, ten_nghe, so_nam_dao_tao
                  FROM {$this->table}
                  WHERE khoa_id = :khoa_id";
        
        if ($is_active !== null) {
            $query .= " AND is_active = :is_active";
        }
        
        $query .= " ORDER BY thu_tu ASC, ten_nghe ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':khoa_id', $khoa_id);
        
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
                  WHERE nghe_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}