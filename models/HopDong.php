<?php
/**
 * Model: HopDong
 * File: models/HopDong.php
 * Quản lý hợp đồng thỉnh giảng
 */

class HopDong {
    private $db;
    private $table = 'hop_dong';
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getAll($filters = []) {
        $query = "SELECT hd.*,
                         gv.ma_giang_vien, gv.ten_giang_vien, gv.so_dien_thoai,
                         mh.ma_mon_hoc, mh.ten_mon_hoc,
                         l.ma_lop, l.ten_lop,
                         n.ma_nghe, n.ten_nghe,
                         nk.ma_nien_khoa, nk.ten_nien_khoa,
                         cs.ma_co_so, cs.ten_co_so,
                         cd.ten_cap_do,
                         k.ten_khoa
                  FROM {$this->table} hd
                  LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                  LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                  LEFT JOIN lop l ON hd.lop_id = l.lop_id
                  LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                  LEFT JOIN nien_khoa nk ON hd.nien_khoa_id = nk.nien_khoa_id
                  LEFT JOIN co_so cs ON hd.co_so_id = cs.co_so_id
                  LEFT JOIN cap_do_giang_day cd ON hd.cap_do_id = cd.cap_do_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['khoa_id'])) {
            $query .= " AND n.khoa_id = :khoa_id";
            $params[':khoa_id'] = $filters['khoa_id'];
        }
        
        if (!empty($filters['giang_vien_id'])) {
            $query .= " AND hd.giang_vien_id = :giang_vien_id";
            $params[':giang_vien_id'] = $filters['giang_vien_id'];
        }
        
        if (!empty($filters['trang_thai'])) {
            $query .= " AND hd.trang_thai = :trang_thai";
            $params[':trang_thai'] = $filters['trang_thai'];
        }
        
        if (!empty($filters['nam_hop_dong'])) {
            $query .= " AND hd.nam_hop_dong = :nam_hop_dong";
            $params[':nam_hop_dong'] = $filters['nam_hop_dong'];
        }
        
        if (!empty($filters['thang_hop_dong'])) {
            $query .= " AND hd.thang_hop_dong = :thang_hop_dong";
            $params[':thang_hop_dong'] = $filters['thang_hop_dong'];
        }
        
        $query .= " ORDER BY hd.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT hd.*,
                         gv.ten_giang_vien, gv.so_dien_thoai, gv.email, gv.dia_chi,
                         gv.so_tai_khoan, gv.ten_ngan_hang, gv.chu_tai_khoan,
                         mh.ten_mon_hoc, mh.tong_so_tiet,
                         l.ten_lop, n.ten_nghe, nk.ten_nien_khoa,
                         cs.ten_co_so, cd.ten_cap_do, k.ten_khoa
                  FROM {$this->table} hd
                  LEFT JOIN giang_vien gv ON hd.giang_vien_id = gv.giang_vien_id
                  LEFT JOIN mon_hoc mh ON hd.mon_hoc_id = mh.mon_hoc_id
                  LEFT JOIN lop l ON hd.lop_id = l.lop_id
                  LEFT JOIN nghe n ON hd.nghe_id = n.nghe_id
                  LEFT JOIN nien_khoa nk ON hd.nien_khoa_id = nk.nien_khoa_id
                  LEFT JOIN co_so cs ON hd.co_so_id = cs.co_so_id
                  LEFT JOIN cap_do_giang_day cd ON hd.cap_do_id = cd.cap_do_id
                  LEFT JOIN khoa k ON n.khoa_id = k.khoa_id
                  WHERE hd.hop_dong_id = :id LIMIT 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $query = "INSERT INTO {$this->table}
                  (so_hop_dong, nam_hop_dong, ngay_hop_dong, thang_hop_dong,
                   giang_vien_id, mon_hoc_id, nghe_id, lop_id, nien_khoa_id,
                   cap_do_id, co_so_id, ngay_bat_dau, ngay_ket_thuc,
                   tong_gio_mon_hoc, don_gia_gio, tong_tien, tong_tien_chu,
                   da_thanh_toan, ngay_thanh_toan, hinh_thuc_thanh_toan,
                   trang_thai, file_hop_dong, file_bien_ban_giao_nhan,
                   ghi_chu, created_by)
                  VALUES
                  (:so_hop_dong, :nam_hop_dong, :ngay_hop_dong, :thang_hop_dong,
                   :giang_vien_id, :mon_hoc_id, :nghe_id, :lop_id, :nien_khoa_id,
                   :cap_do_id, :co_so_id, :ngay_bat_dau, :ngay_ket_thuc,
                   :tong_gio_mon_hoc, :don_gia_gio, :tong_tien, :tong_tien_chu,
                   :da_thanh_toan, :ngay_thanh_toan, :hinh_thuc_thanh_toan,
                   :trang_thai, :file_hop_dong, :file_bien_ban_giao_nhan,
                   :ghi_chu, :created_by)";
        
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
                  SET so_hop_dong = :so_hop_dong,
                      nam_hop_dong = :nam_hop_dong,
                      ngay_hop_dong = :ngay_hop_dong,
                      thang_hop_dong = :thang_hop_dong,
                      giang_vien_id = :giang_vien_id,
                      mon_hoc_id = :mon_hoc_id,
                      nghe_id = :nghe_id,
                      lop_id = :lop_id,
                      nien_khoa_id = :nien_khoa_id,
                      cap_do_id = :cap_do_id,
                      co_so_id = :co_so_id,
                      ngay_bat_dau = :ngay_bat_dau,
                      ngay_ket_thuc = :ngay_ket_thuc,
                      tong_gio_mon_hoc = :tong_gio_mon_hoc,
                      don_gia_gio = :don_gia_gio,
                      tong_tien = :tong_tien,
                      tong_tien_chu = :tong_tien_chu,
                      da_thanh_toan = :da_thanh_toan,
                      ngay_thanh_toan = :ngay_thanh_toan,
                      hinh_thuc_thanh_toan = :hinh_thuc_thanh_toan,
                      trang_thai = :trang_thai,
                      file_hop_dong = :file_hop_dong,
                      file_bien_ban_giao_nhan = :file_bien_ban_giao_nhan,
                      ghi_chu = :ghi_chu,
                      updated_by = :updated_by
                  WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE hop_dong_id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function approve($id, $approved_by) {
        $query = "UPDATE {$this->table}
                  SET trang_thai = 'Đã duyệt',
                      approved_by = :approved_by,
                      approved_at = NOW()
                  WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':approved_by', $approved_by);
        return $stmt->execute();
    }
    
    public function cancel($id, $ly_do_huy, $updated_by) {
        $query = "UPDATE {$this->table}
                  SET trang_thai = 'Hủy',
                      ly_do_huy = :ly_do_huy,
                      updated_by = :updated_by
                  WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':ly_do_huy', $ly_do_huy);
        $stmt->bindParam(':updated_by', $updated_by);
        return $stmt->execute();
    }
    
    public function updateTrangThai($id, $trang_thai, $updated_by) {
        $query = "UPDATE {$this->table}
                  SET trang_thai = :trang_thai,
                      updated_by = :updated_by
                  WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':trang_thai', $trang_thai);
        $stmt->bindParam(':updated_by', $updated_by);
        return $stmt->execute();
    }
    
    public function updateThanhToan($id, $data) {
        $query = "UPDATE {$this->table}
                  SET da_thanh_toan = :da_thanh_toan,
                      ngay_thanh_toan = :ngay_thanh_toan,
                      hinh_thuc_thanh_toan = :hinh_thuc_thanh_toan,
                      updated_by = :updated_by
                  WHERE hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':da_thanh_toan', $data['da_thanh_toan']);
        $stmt->bindValue(':ngay_thanh_toan', $data['ngay_thanh_toan']);
        $stmt->bindValue(':hinh_thuc_thanh_toan', $data['hinh_thuc_thanh_toan']);
        $stmt->bindValue(':updated_by', $data['updated_by']);
        $stmt->bindValue(':id', $id);
        
        return $stmt->execute();
    }
}