<?php
/**
 * Controller: Reports
 * File: controllers/ReportController.php
 * Báo cáo và xuất dữ liệu
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }
    }
    
    /**
     * Trang báo cáo chính
     */
    public function index() {
        $role = getUserRole();
        $khoa_id = getUserKhoaId();
        
        // Lấy danh sách khoa (cho Admin/Phòng ĐT)
        $khoa_list = [];
        if (in_array($role, ['Admin', 'Phong_Dao_Tao'])) {
            $query = "SELECT * FROM khoa WHERE is_active = 1 ORDER BY ten_khoa";
            $stmt = $this->db->query($query);
            $khoa_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Lấy danh sách giảng viên (theo quyền)
        $giang_vien_list = $this->getGiangVienByRole($role, $khoa_id);
        
        // Năm học
        $nam_hoc_list = range(date('Y'), 2020);
        
        $pageTitle = 'Báo cáo & Xuất dữ liệu';
        require_once __DIR__ . '/../views/reports/index.php';
    }
    
    /**
     * Báo cáo tổng hợp hợp đồng
     */
    public function baoCaoHopDong() {
        $filters = [
            'khoa_id' => $_POST['khoa_id'] ?? null,
            'giang_vien_id' => $_POST['giang_vien_id'] ?? null,
            'tu_ngay' => $_POST['tu_ngay'] ?? null,
            'den_ngay' => $_POST['den_ngay'] ?? null,
            'trang_thai' => $_POST['trang_thai'] ?? null,
            'format' => $_POST['format'] ?? 'excel'
        ];
        
        // Lấy dữ liệu
        $data = $this->getHopDongReport($filters);
        
        if ($filters['format'] === 'excel') {
            $this->exportHopDongExcel($data, $filters);
        } else {
            $this->exportHopDongPDF($data, $filters);
        }
    }
    
    /**
     * Báo cáo giảng viên
     */
    public function baoCaoGiangVien() {
        $filters = [
            'khoa_id' => $_POST['khoa_id'] ?? null,
            'trinh_do_id' => $_POST['trinh_do_id'] ?? null,
            'is_active' => $_POST['is_active'] ?? null,
            'format' => $_POST['format'] ?? 'excel'
        ];
        
        $data = $this->getGiangVienReport($filters);
        
        if ($filters['format'] === 'excel') {
            $this->exportGiangVienExcel($data, $filters);
        }
    }
    
    /**
     * Báo cáo tổng hợp theo khoa
     */
    public function baoCaoTheoKhoa() {
        $filters = [
            'thang' => $_POST['thang'] ?? date('m'),
            'nam' => $_POST['nam'] ?? date('Y'),
            'format' => $_POST['format'] ?? 'excel'
        ];
        
        $data = $this->getReportByKhoa($filters);
        
        if ($filters['format'] === 'excel') {
            $this->exportKhoaExcel($data, $filters);
        }
    }
    
    // ==================== DATA FETCHING ====================
    
    private function getHopDongReport($filters) {
        $query = "SELECT hd.*,
                         gv.ma_giang_vien, gv.ten_giang_vien, gv.so_dien_thoai,
                         mh.ma_mon_hoc, mh.ten_mon_hoc,
                         l.ma_lop, l.ten_lop,
                         n.ma_nghe, n.ten_nghe,
                         nk.ten_nien_khoa,
                         cs.ten_co_so,
                         cd.ten_cap_do,
                         k.ten_khoa
                  FROM hop_dong hd
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
        
        if (!empty($filters['tu_ngay'])) {
            $query .= " AND hd.ngay_hop_dong >= :tu_ngay";
            $params[':tu_ngay'] = $filters['tu_ngay'];
        }
        
        if (!empty($filters['den_ngay'])) {
            $query .= " AND hd.ngay_hop_dong <= :den_ngay";
            $params[':den_ngay'] = $filters['den_ngay'];
        }
        
        if (!empty($filters['trang_thai'])) {
            $query .= " AND hd.trang_thai = :trang_thai";
            $params[':trang_thai'] = $filters['trang_thai'];
        }
        
        $query .= " ORDER BY hd.ngay_hop_dong DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getGiangVienReport($filters) {
        $query = "SELECT gv.*,
                         k.ten_khoa,
                         td.ten_trinh_do,
                         COUNT(hd.hop_dong_id) as so_hop_dong,
                         COALESCE(SUM(hd.tong_gio_mon_hoc), 0) as tong_gio,
                         COALESCE(SUM(hd.tong_tien), 0) as tong_tien
                  FROM giang_vien gv
                  LEFT JOIN khoa k ON gv.khoa_id = k.khoa_id
                  LEFT JOIN trinh_do_chuyen_mon td ON gv.trinh_do_id = td.trinh_do_id
                  LEFT JOIN hop_dong hd ON gv.giang_vien_id = hd.giang_vien_id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['khoa_id'])) {
            $query .= " AND gv.khoa_id = :khoa_id";
            $params[':khoa_id'] = $filters['khoa_id'];
        }
        
        if (!empty($filters['trinh_do_id'])) {
            $query .= " AND gv.trinh_do_id = :trinh_do_id";
            $params[':trinh_do_id'] = $filters['trinh_do_id'];
        }
        
        if (isset($filters['is_active'])) {
            $query .= " AND gv.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }
        
        $query .= " GROUP BY gv.giang_vien_id ORDER BY gv.ten_giang_vien";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getReportByKhoa($filters) {
        $query = "SELECT k.ten_khoa,
                         COUNT(DISTINCT gv.giang_vien_id) as so_giang_vien,
                         COUNT(DISTINCT hd.hop_dong_id) as so_hop_dong,
                         COALESCE(SUM(hd.tong_gio_mon_hoc), 0) as tong_gio,
                         COALESCE(SUM(hd.tong_tien), 0) as tong_tien
                  FROM khoa k
                  LEFT JOIN giang_vien gv ON k.khoa_id = gv.khoa_id AND gv.is_active = 1
                  LEFT JOIN nghe n ON k.khoa_id = n.khoa_id
                  LEFT JOIN hop_dong hd ON n.nghe_id = hd.nghe_id 
                      AND MONTH(hd.ngay_hop_dong) = :thang
                      AND YEAR(hd.ngay_hop_dong) = :nam
                  WHERE k.is_active = 1
                  GROUP BY k.khoa_id
                  ORDER BY so_hop_dong DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':thang' => $filters['thang'],
            ':nam' => $filters['nam']
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ==================== EXCEL EXPORT ====================
    
    private function exportHopDongExcel($data, $filters) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Title
        $sheet->setCellValue('A1', 'BÁO CÁO HỢP ĐỒNG THỈNH GIẢNG');
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Period
        $period = 'Từ ' . ($filters['tu_ngay'] ?? 'đầu') . ' đến ' . ($filters['den_ngay'] ?? 'nay');
        $sheet->setCellValue('A2', $period);
        $sheet->mergeCells('A2:N2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Headers
        $headers = ['STT', 'Số HĐ', 'Ngày HĐ', 'Giảng viên', 'Khoa', 'Nghề', 'Lớp', 'Môn học', 
                    'Cơ sở', 'Giờ', 'Đơn giá', 'Tổng tiền', 'Trạng thái', 'Ghi chú'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '4', $header);
            $col++;
        }
        
        // Style header
        $sheet->getStyle('A4:N4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCE5FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        // Data
        $row = 5;
        $stt = 1;
        $tong_gio = 0;
        $tong_tien = 0;
        
        foreach ($data as $hd) {
            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $hd['so_hop_dong']);
            $sheet->setCellValue('C' . $row, date('d/m/Y', strtotime($hd['ngay_hop_dong'])));
            $sheet->setCellValue('D' . $row, $hd['ten_giang_vien']);
            $sheet->setCellValue('E' . $row, $hd['ten_khoa']);
            $sheet->setCellValue('F' . $row, $hd['ten_nghe']);
            $sheet->setCellValue('G' . $row, $hd['ten_lop']);
            $sheet->setCellValue('H' . $row, $hd['ten_mon_hoc']);
            $sheet->setCellValue('I' . $row, $hd['ten_co_so']);
            $sheet->setCellValue('J' . $row, $hd['tong_gio_mon_hoc']);
            $sheet->setCellValue('K' . $row, $hd['don_gia_gio']);
            $sheet->setCellValue('L' . $row, $hd['tong_tien']);
            $sheet->setCellValue('M' . $row, $hd['trang_thai']);
            $sheet->setCellValue('N' . $row, $hd['ghi_chu']);
            
            $tong_gio += $hd['tong_gio_mon_hoc'];
            $tong_tien += $hd['tong_tien'];
            $row++;
        }
        
        // Tổng cộng
        $sheet->setCellValue('A' . $row, 'TỔNG CỘNG');
        $sheet->mergeCells('A' . $row . ':I' . $row);
        $sheet->setCellValue('J' . $row, $tong_gio);
        $sheet->setCellValue('L' . $row, $tong_tien);
        $sheet->getStyle('A' . $row . ':N' . $row)->getFont()->setBold(true);
        
        // Auto width
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Download
        $filename = 'BaoCaoHopDong_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    private function exportGiangVienExcel($data, $filters) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'BÁO CÁO GIẢNG VIÊN THỈNH GIẢNG');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['STT', 'Mã GV', 'Họ tên', 'Năm sinh', 'Giới tính', 'Khoa', 'Trình độ', 
                    'Điện thoại', 'Số HĐ', 'Tổng giờ', 'Tổng tiền'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '3', $header);
            $col++;
        }
        
        $sheet->getStyle('A3:K3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCE5FF']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
        ]);
        
        $row = 4;
        $stt = 1;
        
        foreach ($data as $gv) {
            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $gv['ma_giang_vien']);
            $sheet->setCellValue('C' . $row, $gv['ten_giang_vien']);
            $sheet->setCellValue('D' . $row, $gv['nam_sinh']);
            $sheet->setCellValue('E' . $row, $gv['gioi_tinh']);
            $sheet->setCellValue('F' . $row, $gv['ten_khoa']);
            $sheet->setCellValue('G' . $row, $gv['ten_trinh_do']);
            $sheet->setCellValue('H' . $row, $gv['so_dien_thoai']);
            $sheet->setCellValue('I' . $row, $gv['so_hop_dong']);
            $sheet->setCellValue('J' . $row, $gv['tong_gio']);
            $sheet->setCellValue('K' . $row, $gv['tong_tien']);
            $row++;
        }
        
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'BaoCaoGiangVien_' . date('Y-m-d_His') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    private function exportKhoaExcel($data, $filters) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet->setCellValue('A1', 'BÁO CÁO TỔNG HỢP THEO KHOA');
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $sheet->setCellValue('A2', 'Tháng ' . $filters['thang'] . '/' . $filters['nam']);
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        $headers = ['STT', 'Khoa', 'Số GV', 'Số HĐ', 'Tổng giờ', 'Tổng tiền'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $header);
        }
        
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CCE5FF']]
        ]);
        
        $row = 5;
        $stt = 1;
        foreach ($data as $k) {
            $sheet->setCellValue('A' . $row, $stt++);
            $sheet->setCellValue('B' . $row, $k['ten_khoa']);
            $sheet->setCellValue('C' . $row, $k['so_giang_vien']);
            $sheet->setCellValue('D' . $row, $k['so_hop_dong']);
            $sheet->setCellValue('E' . $row, $k['tong_gio']);
            $sheet->setCellValue('F' . $row, $k['tong_tien']);
            $row++;
        }
        
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = 'BaoCaoTheoKhoa_' . $filters['thang'] . '-' . $filters['nam'] . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    // ==================== HELPERS ====================
    
    private function getGiangVienByRole($role, $khoa_id) {
        $query = "SELECT * FROM giang_vien WHERE is_active = 1";
        
        if ($role == 'Truong_Khoa' && $khoa_id) {
            $query .= " AND khoa_id = $khoa_id";
        }
        
        $query .= " ORDER BY ten_giang_vien";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}