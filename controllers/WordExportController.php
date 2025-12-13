<?php
/**
 * Controller: Word Export
 * File: controllers/WordExportController.php
 * Xuất hợp đồng ra file Word
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Style\Font;

class WordExportController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
        
        if (!isLoggedIn()) {
            redirect('/login');
            exit;
        }
    }
    
    /**
     * Xuất hợp đồng thỉnh giảng
     */
    public function exportHopDong($hop_dong_id) {
        // Lấy thông tin hợp đồng
        $hd = $this->getHopDongDetail($hop_dong_id);
        
        if (!$hd) {
            setFlashMessage('error', 'Không tìm thấy hợp đồng!');
            redirect('/admin/hop-dong');
            return;
        }
        
        // Tạo file Word
        $phpWord = new PhpWord();
        
        // Cấu hình mặc định
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);
        
        // Tạo section
        $section = $phpWord->addSection([
            'marginTop' => Converter::cmToTwip(2),
            'marginBottom' => Converter::cmToTwip(2),
            'marginLeft' => Converter::cmToTwip(3),
            'marginRight' => Converter::cmToTwip(2)
        ]);
        
        // Header
        $this->addHeader($section);
        
        // Tiêu đề
        $section->addText(
            'HỢP ĐỒNG THỈNH GIẢNG',
            ['bold' => true, 'size' => 16],
            ['alignment' => 'center', 'spaceAfter' => 200]
        );
        
        $section->addText(
            'Số: ' . $hd['so_hop_dong'],
            ['bold' => true, 'size' => 13],
            ['alignment' => 'center', 'spaceAfter' => 400]
        );
        
        // Căn cứ
        $section->addText('Căn cứ:', ['bold' => true, 'underline' => 'single'], ['spaceAfter' => 100]);
        $section->addText('- Luật Giáo dục nghề nghiệp số 74/2014/QH13;', [], ['spaceAfter' => 50]);
        $section->addText('- Quyết định thành lập Trường Cao đẳng Nghề TP.HCM;', [], ['spaceAfter' => 50]);
        $section->addText('- Nhu cầu thực tế của Nhà trường.', [], ['spaceAfter' => 300]);
        
        // Hôm nay
        $ngay = date('d', strtotime($hd['ngay_hop_dong']));
        $thang = date('m', strtotime($hd['ngay_hop_dong']));
        $nam = date('Y', strtotime($hd['ngay_hop_dong']));
        
        $section->addText(
            "Hôm nay, ngày $ngay tháng $thang năm $nam, tại Trường Cao đẳng Nghề TP.HCM, chúng tôi gồm có:",
            [],
            ['spaceAfter' => 200]
        );
        
        // Bên A
        $section->addText('BÊN A: TRƯỜNG CAO ĐẲNG NGHỀ TP.HCM', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText('Đại diện: Ông/Bà ..............................', [], ['spaceAfter' => 50]);
        $section->addText('Chức vụ: Hiệu trưởng', [], ['spaceAfter' => 50]);
        $section->addText('Địa chỉ: ' . ($hd['ten_co_so'] ?? 'Cơ sở chính'), [], ['spaceAfter' => 50]);
        $section->addText('Điện thoại: .......................', [], ['spaceAfter' => 300]);
        
        // Bên B
        $section->addText('BÊN B: GIẢNG VIÊN THỈNH GIẢNG', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText('Họ và tên: ' . strtoupper($hd['ten_giang_vien']), ['bold' => true], ['spaceAfter' => 50]);
        $section->addText('Năm sinh: ' . $hd['nam_sinh'], [], ['spaceAfter' => 50]);
        $section->addText('CCCD/CMND: ' . $hd['so_cccd'], [], ['spaceAfter' => 50]);
        $section->addText('Địa chỉ: ' . $hd['dia_chi'], [], ['spaceAfter' => 50]);
        $section->addText('Điện thoại: ' . $hd['so_dien_thoai'], [], ['spaceAfter' => 50]);
        $section->addText('Email: ' . $hd['email'], [], ['spaceAfter' => 300]);
        
        // Hai bên thỏa thuận
        $section->addText(
            'Hai bên thỏa thuận ký kết hợp đồng thỉnh giảng với các điều khoản sau:',
            ['bold' => true],
            ['spaceAfter' => 200]
        );
        
        // Điều 1
        $section->addText('ĐIỀU 1: NỘI DUNG CÔNG VIỆC', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText(
            'Bên B nhận nhiệm vụ giảng dạy môn học: ' . $hd['ten_mon_hoc'],
            [],
            ['spaceAfter' => 50]
        );
        $section->addText('Lớp: ' . $hd['ten_lop'], [], ['spaceAfter' => 50]);
        $section->addText('Nghề: ' . $hd['ten_nghe'], [], ['spaceAfter' => 50]);
        $section->addText('Niên khóa: ' . $hd['ten_nien_khoa'], [], ['spaceAfter' => 50]);
        $section->addText('Cấp độ giảng dạy: ' . $hd['ten_cap_do'], [], ['spaceAfter' => 300]);
        
        // Điều 2
        $section->addText('ĐIỀU 2: THỜI GIAN THỰC HIỆN', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText(
            'Từ ngày: ' . date('d/m/Y', strtotime($hd['ngay_bat_dau'])) . 
            ' đến ngày: ' . date('d/m/Y', strtotime($hd['ngay_ket_thuc'])),
            [],
            ['spaceAfter' => 50]
        );
        $section->addText('Tổng số giờ giảng dạy: ' . $hd['tong_gio_mon_hoc'] . ' giờ', [], ['spaceAfter' => 300]);
        
        // Điều 3
        $section->addText('ĐIỀU 3: CHẾ ĐỘ THANH TOÁN', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText(
            'Đơn giá: ' . number_format($hd['don_gia_gio'], 0, ',', '.') . ' đồng/giờ',
            [],
            ['spaceAfter' => 50]
        );
        $section->addText(
            'Tổng thành tiền: ' . number_format($hd['tong_tien'], 0, ',', '.') . ' đồng',
            ['bold' => true],
            ['spaceAfter' => 50]
        );
        $section->addText(
            'Bằng chữ: ' . ($hd['tong_tien_chu'] ?? $this->docSo($hd['tong_tien'])),
            ['italic' => true],
            ['spaceAfter' => 50]
        );
        $section->addText('Hình thức thanh toán: ' . $hd['hinh_thuc_thanh_toan'], [], ['spaceAfter' => 300]);
        
        // Điều 4
        $section->addText('ĐIỀU 4: QUYỀN VÀ NGHĨA VỤ CÁC BÊN', ['bold' => true], ['spaceAfter' => 100]);
        
        $section->addText('1. Quyền và nghĩa vụ của Bên A:', ['bold' => true], ['spaceAfter' => 50]);
        $section->addText('- Cung cấp đầy đủ tài liệu, thiết bị giảng dạy;', [], ['spaceAfter' => 50]);
        $section->addText('- Thanh toán đầy đủ, đúng hạn theo thỏa thuận;', [], ['spaceAfter' => 50]);
        $section->addText('- Giám sát, đánh giá chất lượng giảng dạy.', [], ['spaceAfter' => 100]);
        
        $section->addText('2. Quyền và nghĩa vụ của Bên B:', ['bold' => true], ['spaceAfter' => 50]);
        $section->addText('- Thực hiện đầy đủ nội dung giảng dạy theo kế hoạch;', [], ['spaceAfter' => 50]);
        $section->addText('- Chịu trách nhiệm về chất lượng giảng dạy;', [], ['spaceAfter' => 50]);
        $section->addText('- Tuân thủ nội quy, quy chế của Nhà trường.', [], ['spaceAfter' => 300]);
        
        // Điều 5
        $section->addText('ĐIỀU 5: ĐIỀU KHOẢN CHUNG', ['bold' => true], ['spaceAfter' => 100]);
        $section->addText('- Hợp đồng có hiệu lực kể từ ngày ký;', [], ['spaceAfter' => 50]);
        $section->addText('- Mọi tranh chấp được giải quyết bằng thương lượng;', [], ['spaceAfter' => 50]);
        $section->addText('- Hợp đồng được lập thành 02 bản có giá trị pháp lý như nhau.', [], ['spaceAfter' => 400]);
        
        // Chữ ký
        $table = $section->addTable(['alignment' => 'center']);
        $table->addRow();
        $cell1 = $table->addCell(4500);
        $cell1->addText('ĐẠI DIỆN BÊN A', ['bold' => true], ['alignment' => 'center', 'spaceAfter' => 50]);
        $cell1->addText('(Ký, ghi rõ họ tên, đóng dấu)', ['italic' => true, 'size' => 11], ['alignment' => 'center', 'spaceAfter' => 1000]);
        
        $cell2 = $table->addCell(4500);
        $cell2->addText('ĐẠI DIỆN BÊN B', ['bold' => true], ['alignment' => 'center', 'spaceAfter' => 50]);
        $cell2->addText('(Ký, ghi rõ họ tên)', ['italic' => true, 'size' => 11], ['alignment' => 'center', 'spaceAfter' => 1000]);
        
        // Lưu file
        $filename = 'HopDong_' . $hd['so_hop_dong'] . '_' . date('YmdHis') . '.docx';
        $filename = str_replace(['/', '\\'], '_', $filename);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('php://output');
        exit;
    }
    
    /**
     * Header văn bản
     */
    private function addHeader($section) {
        $table = $section->addTable();
        $table->addRow();
        
        $cell1 = $table->addCell(4500);
        $cell1->addText('TRƯỜNG CAO ĐẲNG NGHỀ', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
        $cell1->addText('TP. HỒ CHÍ MINH', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
        $cell1->addText(str_repeat('_', 25), [], ['alignment' => 'center', 'spaceAfter' => 200]);
        
        $cell2 = $table->addCell(4500);
        $cell2->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
        $cell2->addText('Độc lập - Tự do - Hạnh phúc', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
        $cell2->addText(str_repeat('_', 35), [], ['alignment' => 'center', 'spaceAfter' => 200]);
        
        $section->addTextBreak(1);
    }
    
    /**
     * Lấy chi tiết hợp đồng
     */
    private function getHopDongDetail($id) {
        $query = "SELECT hd.*,
                         gv.ten_giang_vien, gv.nam_sinh, gv.so_cccd, gv.dia_chi, 
                         gv.so_dien_thoai, gv.email, gv.so_tai_khoan, gv.ten_ngan_hang,
                         mh.ten_mon_hoc,
                         l.ten_lop,
                         n.ten_nghe,
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
                  WHERE hd.hop_dong_id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Đọc số thành chữ (đơn giản)
     */
    private function docSo($so) {
        if ($so == 0) return 'Không đồng';
        
        $mangSo = ['', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $donVi = ['', 'nghìn', 'triệu', 'tỷ'];
        
        $ketQua = '';
        $viTri = 0;
        
        while ($so > 0) {
            $nhom = $so % 1000;
            if ($nhom > 0) {
                $tram = floor($nhom / 100);
                $chuc = floor(($nhom % 100) / 10);
                $donvi = $nhom % 10;
                
                $chuoi = '';
                if ($tram > 0) $chuoi .= $mangSo[$tram] . ' trăm ';
                if ($chuc > 1) $chuoi .= $mangSo[$chuc] . ' mươi ';
                else if ($chuc == 1) $chuoi .= 'mười ';
                if ($donvi > 0) $chuoi .= $mangSo[$donvi] . ' ';
                
                $ketQua = $chuoi . $donVi[$viTri] . ' ' . $ketQua;
            }
            
            $so = floor($so / 1000);
            $viTri++;
        }
        
        return ucfirst(trim($ketQua)) . ' đồng';
    }
}