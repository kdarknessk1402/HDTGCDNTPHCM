<?php
require_once __DIR__ . '/Model.php';

/**
 * NienKhoa Model
 * TÊN CỘT DATABASE: nien_khoa_id, nghe_id, cap_do_id, ma_nien_khoa, ten_nien_khoa, nam_bat_dau, nam_ket_thuc
 */
class NienKhoa extends Model {
    protected $table = 'nien_khoa';
    protected $primaryKey = 'nien_khoa_id';
    
    /**
     * Lấy niên khóa theo nghề và cấp độ
     */
    public function getByNgheAndCapDo($nghe_id, $cap_do_id = null) {
        if ($cap_do_id) {
            return $this->getAll([
                'nghe_id' => $nghe_id,
                'cap_do_id' => $cap_do_id
            ], 'nam_bat_dau DESC');
        }
        
        return $this->getAll(['nghe_id' => $nghe_id], 'nam_bat_dau DESC');
    }
    
    /**
     * Lấy tất cả niên khóa active
     */
    public function getActiveList() {
        $sql = "SELECT 
                    nk.*,
                    n.ten_nghe,
                    cd.ten_cap_do
                FROM nien_khoa nk
                LEFT JOIN nghe n ON nk.nghe_id = n.nghe_id
                LEFT JOIN cap_do_giang_day cd ON nk.cap_do_id = cd.cap_do_id
                ORDER BY nk.nam_bat_dau DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}