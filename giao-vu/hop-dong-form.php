<?php
/**
 * Form Thêm/Sửa Hợp đồng
 * File: /giao-vu/hop-dong-form.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/HopDong.php';
require_once __DIR__ . '/../models/GiangVien.php';
require_once __DIR__ . '/../models/MonHoc.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/Lop.php';
require_once __DIR__ . '/../models/CoSo.php';
require_once __DIR__ . '/../models/DanhMuc.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/NienKhoa.php';
require_once __DIR__ . '/../models/DonGiaGioDay.php';

// Kiểm tra đăng nhập và quyền
if (!isLoggedIn() || !hasRole(['Admin', 'Giao_Vu'])) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$hopDongModel = new HopDong($db);
$giangVienModel = new GiangVien($db);
$monHocModel = new MonHoc($db);
$ngheModel = new Nghe($db);
$lopModel = new Lop($db);
$coSoModel = new CoSo($db);
$danhMucModel = new DanhMuc($db);
$khoaModel = new Khoa($db);
$nienKhoaModel = new NienKhoa($db);
$donGiaModel = new DonGiaGioDay($db);

$action = $_GET['action'] ?? 'add';
$hop_dong_id = $_GET['id'] ?? null;

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tính tổng tiền
    $tong_gio = (int)($_POST['tong_gio_mon_hoc'] ?? 0);
    $don_gia = (int)($_POST['don_gia_gio'] ?? 0);
    $tong_tien = $tong_gio * $don_gia;
    
    $data = [
        'giang_vien_id' => (int)($_POST['giang_vien_id'] ?? 0),
        'mon_hoc_id' => (int)($_POST['mon_hoc_id'] ?? 0),
        'nghe_id' => (int)($_POST['nghe_id'] ?? 0),
        'lop_id' => !empty($_POST['lop_id']) ? (int)$_POST['lop_id'] : null,
        'nien_khoa_id' => !empty($_POST['nien_khoa_id']) ? (int)$_POST['nien_khoa_id'] : null,
        'cap_do_id' => !empty($_POST['cap_do_id']) ? (int)$_POST['cap_do_id'] : null,
        'co_so_id' => !empty($_POST['co_so_id']) ? (int)$_POST['co_so_id'] : null,
        'ngay_hop_dong' => $_POST['ngay_hop_dong'] ?? date('Y-m-d'),
        'ngay_bat_dau' => $_POST['ngay_bat_dau'],
        'ngay_ket_thuc' => $_POST['ngay_ket_thuc'],
        'tong_gio_mon_hoc' => $tong_gio,
        'don_gia_gio' => $don_gia,
        'tong_tien' => $tong_tien,
        'tong_tien_chu' => numberToVietnameseWords($tong_tien),
        'trang_thai' => $_POST['trang_thai'] ?? 'Mới tạo',
        'ghi_chu' => trim($_POST['ghi_chu'] ?? '')
    ];
    
    // Validate
    $errors = [];
    if (empty($data['giang_vien_id'])) {
        $errors[] = 'Vui lòng chọn giảng viên';
    }
    if (empty($data['mon_hoc_id'])) {
        $errors[] = 'Vui lòng chọn môn học';
    }
    if (empty($data['nghe_id'])) {
        $errors[] = 'Vui lòng chọn nghề';
    }
    if (empty($data['ngay_bat_dau'])) {
        $errors[] = 'Ngày bắt đầu không được để trống';
    }
    if (empty($data['ngay_ket_thuc'])) {
        $errors[] = 'Ngày kết thúc không được để trống';
    }
    if ($data['tong_gio_mon_hoc'] <= 0) {
        $errors[] = 'Tổng giờ môn học phải lớn hơn 0';
    }
    if ($data['don_gia_gio'] <= 0) {
        $errors[] = 'Đơn giá giờ phải lớn hơn 0';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $hopDongModel->createHopDong($data);
            $message = $success ? 'Thêm hợp đồng thành công' : 'Lỗi khi thêm hợp đồng';
        } else {
            $success = $hopDongModel->updateHopDong($hop_dong_id, $data);
            $message = $success ? 'Cập nhật hợp đồng thành công' : 'Lỗi khi cập nhật hợp đồng';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/giao-vu/hop-dong.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy thông tin hợp đồng để edit
$hopDong = null;
if ($action === 'edit' && $hop_dong_id) {
    $hopDong = $hopDongModel->getByIdWithFullDetails($hop_dong_id);
    if (!$hopDong) {
        $_SESSION['error'] = 'Không tìm thấy hợp đồng';
        header('Location: ' . BASE_URL . '/giao-vu/hop-dong.php');
        exit;
    }
}

// Lấy danh sách cho dropdown
$listGiangVien = $giangVienModel->getAllWithDetails();
$listMonHoc = $monHocModel->getAllWithDetails();
$listNghe = $ngheModel->getAllWithKhoa();
$listLop = $lopModel->getAllWithDetails();
$listCoSo = $coSoModel->getActiveList();
$listCapDo = $danhMucModel->getCapDoGiangDay();
$listKhoa = $khoaModel->getActiveList();
$listNienKhoa = $nienKhoaModel->getActiveList();
$listTrinhDo = $danhMucModel->getTrinhDoChuyenMon();

$currentUser = getCurrentUser();
$pageTitle = ($action === 'add') ? 'Thêm Hợp đồng' : 'Sửa Hợp đồng';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>
                <i class="bi bi-<?php echo ($action === 'add') ? 'plus-circle' : 'pencil-square'; ?>"></i> 
                <?php echo $pageTitle; ?>
            </h2>
            <a href="<?php echo BASE_URL; ?>/giao-vu/hop-dong.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" id="formHopDong">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_basic">Thông tin cơ bản</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_detail">Chi tiết môn học</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_payment">Thanh toán</a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Tab 1: Thông tin cơ bản -->
                        <div class="tab-pane fade show active" id="tab_basic">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày hợp đồng <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_hop_dong" class="form-control" required
                                           value="<?php echo $hopDong['ngay_hop_dong'] ?? date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_bat_dau" class="form-control" required
                                           value="<?php echo $hopDong['ngay_bat_dau'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
                                    <input type="date" name="ngay_ket_thuc" class="form-control" required
                                           value="<?php echo $hopDong['ngay_ket_thuc'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Khoa</label>
                                    <select class="form-select" id="khoa_id" onchange="filterGiangVienByKhoa()">
                                        <option value="">-- Tất cả khoa --</option>
                                        <?php foreach ($listKhoa as $khoa): ?>
                                        <option value="<?php echo $khoa['khoa_id']; ?>">
                                            <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Giảng viên <span class="text-danger">*</span></label>
                                    <select name="giang_vien_id" id="giang_vien_id" class="form-select" required>
                                        <option value="">-- Chọn giảng viên --</option>
                                        <?php foreach ($listGiangVien as $gv): ?>
                                        <option value="<?php echo $gv['giang_vien_id']; ?>" 
                                                data-khoa="<?php echo $gv['khoa_id']; ?>"
                                                data-trinh-do="<?php echo $gv['trinh_do_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['giang_vien_id'] == $gv['giang_vien_id']) ? 'selected' : ''; ?>>
                                            [<?php echo htmlspecialchars($gv['ma_giang_vien']); ?>] 
                                            <?php echo htmlspecialchars($gv['ten_giang_vien']); ?> 
                                            - <?php echo htmlspecialchars($gv['ten_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trạng thái</label>
                                    <select name="trang_thai" class="form-select">
                                        <option value="Mới tạo" <?php echo (isset($hopDong) && $hopDong['trang_thai'] == 'Mới tạo') ? 'selected' : ''; ?>>Mới tạo</option>
                                        <option value="Đã duyệt" <?php echo (isset($hopDong) && $hopDong['trang_thai'] == 'Đã duyệt') ? 'selected' : ''; ?>>Đã duyệt</option>
                                        <option value="Đang thực hiện" <?php echo (isset($hopDong) && $hopDong['trang_thai'] == 'Đang thực hiện') ? 'selected' : ''; ?>>Đang thực hiện</option>
                                        <option value="Hoàn thành" <?php echo (isset($hopDong) && $hopDong['trang_thai'] == 'Hoàn thành') ? 'selected' : ''; ?>>Hoàn thành</option>
                                        <option value="Hủy" <?php echo (isset($hopDong) && $hopDong['trang_thai'] == 'Hủy') ? 'selected' : ''; ?>>Hủy</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 2: Chi tiết môn học -->
                        <div class="tab-pane fade" id="tab_detail">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Khoa (Môn học)</label>
                                    <select class="form-select" id="khoa_mon_hoc" onchange="filterMonHocByKhoa()">
                                        <option value="">-- Tất cả khoa --</option>
                                        <?php foreach ($listKhoa as $khoa): ?>
                                        <option value="<?php echo $khoa['khoa_id']; ?>">
                                            <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nghề <span class="text-danger">*</span></label>
                                    <select name="nghe_id" id="nghe_id" class="form-select" required onchange="filterMonHocByNghe()">
                                        <option value="">-- Chọn nghề --</option>
                                        <?php foreach ($listNghe as $nghe): ?>
                                        <option value="<?php echo $nghe['nghe_id']; ?>" 
                                                data-khoa="<?php echo $nghe['khoa_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['nghe_id'] == $nghe['nghe_id']) ? 'selected' : ''; ?>>
                                            [<?php echo htmlspecialchars($nghe['ma_nghe']); ?>] 
                                            <?php echo htmlspecialchars($nghe['ten_nghe']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Môn học <span class="text-danger">*</span></label>
                                    <select name="mon_hoc_id" id="mon_hoc_id" class="form-select" required>
                                        <option value="">-- Chọn môn học --</option>
                                        <?php foreach ($listMonHoc as $mh): ?>
                                        <option value="<?php echo $mh['mon_hoc_id']; ?>" 
                                                data-nghe="<?php echo $mh['nghe_id']; ?>"
                                                data-tong-tiet="<?php echo $mh['tong_so_tiet']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['mon_hoc_id'] == $mh['mon_hoc_id']) ? 'selected' : ''; ?>>
                                            [<?php echo htmlspecialchars($mh['ma_mon_hoc']); ?>] 
                                            <?php echo htmlspecialchars($mh['ten_mon_hoc']); ?> 
                                            (<?php echo $mh['tong_so_tiet']; ?> tiết)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lớp</label>
                                    <select name="lop_id" id="lop_id" class="form-select">
                                        <option value="">-- Chọn lớp --</option>
                                        <?php foreach ($listLop as $lop): ?>
                                        <option value="<?php echo $lop['lop_id']; ?>" 
                                                data-nghe="<?php echo $lop['nghe_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['lop_id'] == $lop['lop_id']) ? 'selected' : ''; ?>>
                                            [<?php echo htmlspecialchars($lop['ma_lop']); ?>] 
                                            <?php echo htmlspecialchars($lop['ten_lop']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Niên khóa</label>
                                    <select name="nien_khoa_id" class="form-select">
                                        <option value="">-- Chọn niên khóa --</option>
                                        <?php foreach ($listNienKhoa as $nk): ?>
                                        <option value="<?php echo $nk['nien_khoa_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['nien_khoa_id'] == $nk['nien_khoa_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($nk['ten_nien_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cấp độ</label>
                                    <select name="cap_do_id" id="cap_do_id" class="form-select">
                                        <option value="">-- Chọn cấp độ --</option>
                                        <?php foreach ($listCapDo as $cd): ?>
                                        <option value="<?php echo $cd['cap_do_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['cap_do_id'] == $cd['cap_do_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cd['ten_cap_do']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Cơ sở</label>
                                    <select name="co_so_id" id="co_so_id" class="form-select" onchange="updateDonGia()">
                                        <option value="">-- Chọn cơ sở --</option>
                                        <?php foreach ($listCoSo as $cs): ?>
                                        <option value="<?php echo $cs['co_so_id']; ?>"
                                                <?php echo (isset($hopDong) && $hopDong['co_so_id'] == $cs['co_so_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cs['ten_co_so']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 3: Thanh toán -->
                        <div class="tab-pane fade" id="tab_payment">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tổng giờ môn học <span class="text-danger">*</span></label>
                                    <input type="number" name="tong_gio_mon_hoc" id="tong_gio_mon_hoc" class="form-control" 
                                           required min="1" step="1" 
                                           value="<?php echo $hopDong['tong_gio_mon_hoc'] ?? ''; ?>"
                                           onchange="calculateTotal()">
                                    <small class="text-muted">Đơn vị: giờ</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Đơn giá/giờ (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="don_gia_gio" id="don_gia_gio" class="form-control" 
                                           required min="1000" step="1000" 
                                           value="<?php echo $hopDong['don_gia_gio'] ?? ''; ?>"
                                           onchange="calculateTotal()">
                                    <small class="text-muted" id="don_gia_hint">Tự động lấy theo cơ sở + trình độ</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Tổng tiền (VNĐ)</label>
                                    <input type="text" id="tong_tien_display" class="form-control bg-light" readonly 
                                           value="<?php echo isset($hopDong) ? formatMoney($hopDong['tong_tien']) : '0đ'; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Tổng tiền bằng chữ</label>
                                    <input type="text" id="tong_tien_chu" class="form-control bg-light" readonly
                                           value="<?php echo $hopDong['tong_tien_chu'] ?? 'Không đồng'; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Ghi chú</label>
                                    <textarea name="ghi_chu" class="form-control" rows="3"
                                              placeholder="Ghi chú thêm về hợp đồng..."><?php echo $hopDong['ghi_chu'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle"></i> 
                            <?php echo ($action === 'add') ? 'Tạo hợp đồng' : 'Cập nhật'; ?>
                        </button>
                        <a href="<?php echo BASE_URL; ?>/giao-vu/hop-dong.php" class="btn btn-secondary btn-lg">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Danh sách đơn giá từ PHP
const donGiaList = <?php echo json_encode($donGiaModel->getAllWithDetails()); ?>;

// Filter giảng viên theo khoa
function filterGiangVienByKhoa() {
    const khoaId = document.getElementById('khoa_id').value;
    const gvSelect = document.getElementById('giang_vien_id');
    const options = gvSelect.getElementsByTagName('option');
    
    for (let i = 1; i < options.length; i++) {
        if (!khoaId || options[i].dataset.khoa == khoaId) {
            options[i].style.display = '';
        } else {
            options[i].style.display = 'none';
        }
    }
}

// Filter môn học theo khoa
function filterMonHocByKhoa() {
    const khoaId = document.getElementById('khoa_mon_hoc').value;
    const ngheSelect = document.getElementById('nghe_id');
    const options = ngheSelect.getElementsByTagName('option');
    
    for (let i = 1; i < options.length; i++) {
        if (!khoaId || options[i].dataset.khoa == khoaId) {
            options[i].style.display = '';
        } else {
            options[i].style.display = 'none';
        }
    }
    
    // Reset môn học
    document.getElementById('mon_hoc_id').value = '';
}

// Filter môn học theo nghề
function filterMonHocByNghe() {
    const ngheId = document.getElementById('nghe_id').value;
    const mhSelect = document.getElementById('mon_hoc_id');
    const lopSelect = document.getElementById('lop_id');
    const options = mhSelect.getElementsByTagName('option');
    const lopOptions = lopSelect.getElementsByTagName('option');
    
    // Filter môn học
    for (let i = 1; i < options.length; i++) {
        if (!ngheId || options[i].dataset.nghe == ngheId) {
            options[i].style.display = '';
        } else {
            options[i].style.display = 'none';
        }
    }
    
    // Filter lớp
    for (let i = 1; i < lopOptions.length; i++) {
        if (!ngheId || lopOptions[i].dataset.nghe == ngheId) {
            lopOptions[i].style.display = '';
        } else {
            lopOptions[i].style.display = 'none';
        }
    }
}

// Update đơn giá tự động
function updateDonGia() {
    const coSoId = document.getElementById('co_so_id').value;
    const gvSelect = document.getElementById('giang_vien_id');
    const selectedGv = gvSelect.options[gvSelect.selectedIndex];
    const trinhDoId = selectedGv ? selectedGv.dataset.trinhDo : null;
    
    if (coSoId && trinhDoId) {
        // Tìm đơn giá phù hợp
        const donGia = donGiaList.find(dg => 
            dg.co_so_id == coSoId && 
            dg.trinh_do_id == trinhDoId && 
            dg.is_active == 1
        );
        
        if (donGia) {
            document.getElementById('don_gia_gio').value = donGia.don_gia;
            document.getElementById('don_gia_hint').innerHTML = 
                '<span class="text-success">✓ Đơn giá năm ' + donGia.nam_ap_dung + '</span>';
            calculateTotal();
        } else {
            document.getElementById('don_gia_hint').innerHTML = 
                '<span class="text-warning">⚠ Chưa có đơn giá cho cơ sở + trình độ này</span>';
        }
    }
}

// Tính tổng tiền
function calculateTotal() {
    const tongGio = parseInt(document.getElementById('tong_gio_mon_hoc').value) || 0;
    const donGia = parseInt(document.getElementById('don_gia_gio').value) || 0;
    const tongTien = tongGio * donGia;
    
    // Format hiển thị
    document.getElementById('tong_tien_display').value = 
        tongTien.toLocaleString('vi-VN') + 'đ';
    
    // Chuyển sang chữ
    fetch('<?php echo BASE_URL; ?>/api/number-to-words.php?amount=' + tongTien)
        .then(response => response.json())
        .then(data => {
            document.getElementById('tong_tien_chu').value = data.words;
        })
        .catch(() => {
            document.getElementById('tong_tien_chu').value = 'Không xác định';
        });
}

// Auto update đơn giá khi chọn giảng viên
document.getElementById('giang_vien_id').addEventListener('change', updateDonGia);
document.getElementById('cap_do_id').addEventListener('change', updateDonGia);

// Calculate total on page load
<?php if (isset($hopDong)): ?>
calculateTotal();
<?php endif; ?>
</script>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>