<?php
require_once __DIR__ . '/Model.php';

/**
 * DanhMuc Model - Model chung cho các danh mục đơn giản
 * File: /models/DanhMuc.php
 */
class DanhMuc extends Model {
    
    /**
     * Lấy danh sách cấp độ giảng dạy
     */
    public function getCapDoGiangDay() {
        $this->table = 'cap_do_giang_day';
        return $this->getAll([], 'thu_tu ASC');
    }
    
    /**
     * Lấy danh sách trình độ chuyên môn
     */
    public function getTrinhDoChuyenMon() {
        $this->table = 'trinh_do_chuyen_mon';
        return $this->getAll([], 'thu_tu ASC');
    }
    
    /**
     * Lấy danh sách cơ sở
     */
    public function getCoSo() {
        $this->table = 'co_so';
        return $this->getAll(['is_active' => 1], 'thu_tu ASC');
    }
    
    /**
     * Lấy tất cả cơ sở (kể cả inactive)
     */
    public function getAllCoSo() {
        $this->table = 'co_so';
        return $this->getAll([], 'thu_tu ASC');
    }
    
    /**
     * Lấy cơ sở theo ID
     */
    public function getCoSoById($id) {
        $this->table = 'co_so';
        $this->primaryKey = 'co_so_id';
        return $this->getById($id);
    }
    
    /**
     * Tạo cơ sở mới
     */
    public function createCoSo($data) {
        $this->table = 'co_so';
        return $this->insert($data);
    }
    
    /**
     * Cập nhật cơ sở
     */
    public function updateCoSo($id, $data) {
        $this->table = 'co_so';
        $this->primaryKey = 'co_so_id';
        return $this->update($id, $data);
    }
    
    /**
     * Xóa cơ sở
     */
    public function deleteCoSo($id) {
        $this->table = 'co_so';
        $this->primaryKey = 'co_so_id';
        
        // Soft delete
        return $this->update($id, [
            'is_active' => 0,
            'updated_by' => $_SESSION['user_id'] ?? null
        ]);
    }
    
    /**
     * Kiểm tra mã cơ sở đã tồn tại chưa
     */
    public function checkMaCoSoExists($ma_co_so, $exclude_id = null) {
        $this->table = 'co_so';
        
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
}