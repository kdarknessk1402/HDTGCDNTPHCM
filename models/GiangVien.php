<?php
/**
 * Model: GiangVien
 * File: models/GiangVien.php
 * Quản lý giảng viên thỉnh giảng
 */

class GiangVien {
    private $db;
    private $table = 'giang_vien';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll($khoa_id = null, $trinh_do_id = null, $is_active = null) {
        $query = "SELECT gv.*, 
                         k.ten_khoa, k.ma_khoa,
                         td.ten_trinh_do, td.ma_trinh_do
                  FROM {$this->table} gv
                  LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                  LEFT JOIN trinh_do_chuyen_mon td ON gv.trinh_do_id = td.trinh_do_id
                  WHERE 1=1";
        
        if ($khoa_id !== null) $query .= " AND gv.khoa_id = :khoa_id";
        if ($trinh_do_id !== null) $query .= " AND gv.trinh_do_id = :trinh_do_id";
        if ($is_active !== null) $query .= " AND gv.is_active = :is_active";
        
        $query .= " ORDER BY gv.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        
        if ($khoa_id !== null) $stmt->bindParam(':khoa_id', $khoa_id);
        if ($trinh_do_id !== null) $stmt->bindParam(':trinh_do_id', $trinh_do_id);
        if ($is_active !== null) $stmt->bindParam(':is_active', $is_active);
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT gv.*, k.ten_khoa, td.ten_trinh_do
                  FROM {$this->table} gv
                  LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                  LEFT JOIN trinh_do_chuyen_mon td ON gv.trinh_do_id = td.trinh_do_id
                  WHERE gv.giang_vien_id = :id LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (khoa_id, ma_giang_vien, ten_giang_vien, nam_sinh, gioi_tinh, ngay_sinh, noi_sinh,
                   so_cccd, ngay_cap_cccd, noi_cap_cccd, trinh_do_id, chuyen_nganh_dao_tao,
                   truong_dao_tao, nam_tot_nghiep, chung_chi_su_pham, dia_chi, dia_chi_tam_tru,
                   so_dien_thoai, email, so_tai_khoan, ten_ngan_hang, chi_nhanh_ngan_hang,
                   chu_tai_khoan, ma_so_thue, file_cccd, file_bang_cap, file_chung_chi,
                   ghi_chu, is_active, created_by)
                  VALUES 
                  (:khoa_id, :ma_giang_vien, :ten_giang_vien, :nam_sinh, :gioi_tinh, :ngay_sinh, :noi_sinh,
                   :so_cccd, :ngay_cap_cccd, :noi_cap_cccd, :trinh_do_id, :chuyen_nganh_dao_tao,
                   :truong_dao_tao, :nam_tot_nghiep, :chung_chi_su_pham, :dia_chi, :dia_chi_tam_tru,
                   :so_dien_thoai, :email, :so_tai_khoan, :ten_ngan_hang, :chi_nhanh_ngan_hang,
                   :chu_tai_khoan, :ma_so_thue, :file_cccd, :file_bang_cap, :file_chung_chi,
                   :ghi_chu, :is_active, :created_by)";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    public function update($id, $data) {
        $query = "UPDATE {$this->table}
                  SET khoa_id = :khoa_id, ma_giang_vien = :ma_giang_vien,
                      ten_giang_vien = :ten_giang_vien, nam_sinh = :nam_sinh,
                      gioi_tinh = :gioi_tinh, ngay_sinh = :ngay_sinh, noi_sinh = :noi_sinh,
                      so_cccd = :so_cccd, ngay_cap_cccd = :ngay_cap_cccd, noi_cap_cccd = :noi_cap_cccd,
                      trinh_do_id = :trinh_do_id, chuyen_nganh_dao_tao = :chuyen_nganh_dao_tao,
                      truong_dao_tao = :truong_dao_tao, nam_tot_nghiep = :nam_tot_nghiep,
                      chung_chi_su_pham = :chung_chi_su_pham, dia_chi = :dia_chi,
                      dia_chi_tam_tru = :dia_chi_tam_tru, so_dien_thoai = :so_dien_thoai,
                      email = :email, so_tai_khoan = :so_tai_khoan, ten_ngan_hang = :ten_ngan_hang,
                      chi_nhanh_ngan_hang = :chi_nhanh_ngan_hang, chu_tai_khoan = :chu_tai_khoan,
                      ma_so_thue = :ma_so_thue, file_cccd = :file_cccd, file_bang_cap = :file_bang_cap,
                      file_chung_chi = :file_chung_chi, ghi_chu = :ghi_chu,
                      is_active = :is_active, updated_by = :updated_by
                  WHERE giang_vien_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        if ($this->hasRelatedRecords($id)) return false;
        
        $query = "DELETE FROM {$this->table} WHERE giang_vien_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function hasRelatedRecords($id) {
        $query = "SELECT COUNT(*) as count FROM hop_dong WHERE giang_vien_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
    
    public function updateStatus($id, $is_active, $updated_by) {
        $query = "UPDATE {$this->table} SET is_active = :is_active, updated_by = :updated_by WHERE giang_vien_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':is_active', $is_active);
        $stmt->bindParam(':updated_by', $updated_by);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}