<?php
require_once __DIR__ . '/Model.php';

/**
 * Khoa Model
 * TÊN CỘT DATABASE: khoa_id, ma_khoa, ten_khoa, mo_ta, truong_khoa_id, so_dien_thoai, email, thu_tu, is_active
 */
class Khoa extends Model {
    protected $table = 'khoa';
    protected $primaryKey = 'khoa_id';
    
    /**
     * Lấy tất cả khoa với thông tin trưởng khoa
     */
    public function getAllWithTruongKhoa() {
        $sql = "SELECT 
                    k.khoa_id,
                    k.ma_khoa,
                    k.ten_khoa,
                    k.mo_ta,
                    k.truong_khoa_id,
                    k.so_dien_thoai,
                    k.email,
                    k.thu_tu,
                    k.is_active,
                    k.created_at,
                    k.updated_at,
                    u.full_name as ten_truong_khoa,
                    u.phone as sdt_truong_khoa,
                    u.email as email_truong_khoa
                FROM khoa k
                LEFT JOIN users u ON k.truong_khoa_id = u.user_id
                ORDER BY k.thu_tu ASC, k.ten_khoa ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Lấy khoa theo ID với thông tin trưởng khoa
     */
    public function getByIdWithTruongKhoa($id) {
        $sql = "SELECT 
                    k.*,
                    u.full_name as ten_truong_khoa
                FROM khoa k
                LEFT JOIN users u ON k.truong_khoa_id = u.user_id
                WHERE k.khoa_id = :id
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Kiểm tra mã khoa đã tồn tại chưa
     */
    public function checkMaKhoaExists($ma_khoa, $exclude_id = null) {
        $sql = "SELECT COUNT(*) FROM khoa WHERE ma_khoa = :ma_khoa";
        
        if ($exclude_id) {
            $sql .= " AND khoa_id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_khoa', $ma_khoa);
        
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Lấy danh sách khoa active (cho dropdown)
     */
    public function getActiveList() {
        return $this->getAll(['is_active' => 1], 'thu_tu ASC, ten_khoa ASC');
    }
    
    /**
     * Đếm số nghề thuộc khoa
     */
    public function countNghe($khoa_id) {
        $sql = "SELECT COUNT(*) FROM nghe WHERE khoa_id = :khoa_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':khoa_id', $khoa_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Đếm số giảng viên thuộc khoa
     */
    public function countGiangVien($khoa_id) {
        $sql = "SELECT COUNT(*) FROM giang_vien WHERE khoa_id = :khoa_id AND is_active = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':khoa_id', $khoa_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Thêm khoa mới - TÊN CỘT CHÍNH XÁC
     */
    public function createKhoa($data) {
        $sql = "INSERT INTO khoa (
                    ma_khoa, 
                    ten_khoa, 
                    mo_ta, 
                    truong_khoa_id, 
                    so_dien_thoai, 
                    email, 
                    thu_tu, 
                    is_active,
                    created_by
                ) VALUES (
                    :ma_khoa,
                    :ten_khoa,
                    :mo_ta,
                    :truong_khoa_id,
                    :so_dien_thoai,
                    :email,
                    :thu_tu,
                    :is_active,
                    :created_by
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_khoa', $data['ma_khoa']);
        $stmt->bindValue(':ten_khoa', $data['ten_khoa']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':truong_khoa_id', $data['truong_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':created_by', $_SESSION['user_id'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Cập nhật khoa - TÊN CỘT CHÍNH XÁC
     */
    public function updateKhoa($id, $data) {
        $sql = "UPDATE khoa SET
                    ma_khoa = :ma_khoa,
                    ten_khoa = :ten_khoa,
                    mo_ta = :mo_ta,
                    truong_khoa_id = :truong_khoa_id,
                    so_dien_thoai = :so_dien_thoai,
                    email = :email,
                    thu_tu = :thu_tu,
                    is_active = :is_active,
                    updated_by = :updated_by
                WHERE khoa_id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ma_khoa', $data['ma_khoa']);
        $stmt->bindValue(':ten_khoa', $data['ten_khoa']);
        $stmt->bindValue(':mo_ta', $data['mo_ta'] ?? null);
        $stmt->bindValue(':truong_khoa_id', $data['truong_khoa_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':so_dien_thoai', $data['so_dien_thoai'] ?? null);
        $stmt->bindValue(':email', $data['email'] ?? null);
        $stmt->bindValue(':thu_tu', $data['thu_tu'] ?? 0, PDO::PARAM_INT);
        $stmt->bindValue(':is_active', $data['is_active'] ?? 1, PDO::PARAM_INT);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Xóa khoa (soft delete)
     */
    public function deleteKhoa($id) {
        // Kiểm tra có nghề nào không
        if ($this->countNghe($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa khoa vì có nghề liên quan'];
        }
        
        // Kiểm tra có giảng viên nào không
        if ($this->countGiangVien($id) > 0) {
            return ['success' => false, 'message' => 'Không thể xóa khoa vì có giảng viên liên quan'];
        }
        
        // Xóa
        $sql = "UPDATE khoa SET is_active = 0, updated_by = :updated_by WHERE khoa_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':updated_by', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Xóa khoa thành công'];
        }
        
        return ['success' => false, 'message' => 'Lỗi khi xóa khoa'];
    }
}