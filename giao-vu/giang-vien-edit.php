<?php
/**
 * Sửa Giảng viên
 * File: /giao-vu/giang-vien-edit.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/GiangVien.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/DanhMuc.php';

// Kiểm tra đăng nhập và quyền
if (!isLoggedIn() || !hasRole(['Admin', 'Giao_Vu'])) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$giangVienModel = new GiangVien($db);
$khoaModel = new Khoa($db);
$danhMucModel = new DanhMuc($db);

$giang_vien_id = $_GET['id'] ?? null;

if (!$giang_vien_id) {
    $_SESSION['error'] = 'ID giảng viên không hợp lệ';
    header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
    exit;
}

// XỬ LÝ CÁP NHẬT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'khoa_id' => (int)($_POST['khoa_id'] ?? 0),
        'ma_giang_vien' => trim($_POST['ma_giang_vien'] ?? ''),
        'ten_giang_vien' => trim($_POST['ten_giang_vien'] ?? ''),
        'nam_sinh' => !empty($_POST['nam_sinh']) ? (int)$_POST['nam_sinh'] : null,
        'gioi_tinh' => trim($_POST['gioi_tinh'] ?? ''),
        'ngay_sinh' => !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null,
        'noi_sinh' => trim($_POST['noi_sinh'] ?? ''),
        'so_cccd' => trim($_POST['so_cccd'] ?? ''),
        'ngay_cap_cccd' => !empty($_POST['ngay_cap_cccd']) ? $_POST['ngay_cap_cccd'] : null,
        'noi_cap_cccd' => trim($_POST['noi_cap_cccd'] ?? ''),
        'trinh_do_id' => !empty($_POST['trinh_do_id']) ? (int)$_POST['trinh_do_id'] : null,
        'chuyen_nganh_dao_tao' => trim($_POST['chuyen_nganh_dao_tao'] ?? ''),
        'truong_dao_tao' => trim($_POST['truong_dao_tao'] ?? ''),
        'nam_tot_nghiep' => !empty($_POST['nam_tot_nghiep']) ? (int)$_POST['nam_tot_nghiep'] : null,
        'chung_chi_su_pham' => trim($_POST['chung_chi_su_pham'] ?? ''),
        'dia_chi' => trim($_POST['dia_chi'] ?? ''),
        'dia_chi_tam_tru' => trim($_POST['dia_chi_tam_tru'] ?? ''),
        'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'so_tai_khoan' => trim($_POST['so_tai_khoan'] ?? ''),
        'ten_ngan_hang' => trim($_POST['ten_ngan_hang'] ?? ''),
        'chi_nhanh_ngan_hang' => trim($_POST['chi_nhanh_ngan_hang'] ?? ''),
        'chu_tai_khoan' => trim($_POST['chu_tai_khoan'] ?? ''),
        'ma_so_thue' => trim($_POST['ma_so_thue'] ?? ''),
        'ghi_chu' => trim($_POST['ghi_chu'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['khoa_id'])) {
        $errors[] = 'Vui lòng chọn khoa';
    }
    if (empty($data['ma_giang_vien'])) {
        $errors[] = 'Mã giảng viên không được để trống';
    }
    if (empty($data['ten_giang_vien'])) {
        $errors[] = 'Tên giảng viên không được để trống';
    }
    
    // Kiểm tra mã giảng viên trùng
    if ($giangVienModel->checkMaGiangVienExists($data['ma_giang_vien'], $giang_vien_id)) {
        $errors[] = 'Mã giảng viên đã tồn tại';
    }
    
    // Kiểm tra CCCD trùng
    if (!empty($data['so_cccd']) && $giangVienModel->checkCccdExists($data['so_cccd'], $giang_vien_id)) {
        $errors[] = 'Số CCCD đã tồn tại';
    }
    
    // Kiểm tra email
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = 'Email không đúng định dạng';
    }
    
    if (empty($errors)) {
        $success = $giangVienModel->updateGiangVien($giang_vien_id, $data);
        
        if ($success) {
            $_SESSION['success'] = 'Cập nhật giảng viên thành công';
            header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
            exit;
        } else {
            $_SESSION['error'] = 'Lỗi khi cập nhật giảng viên';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy thông tin giảng viên
$giangVien = $giangVienModel->getByIdWithDetails($giang_vien_id);
if (!$giangVien) {
    $_SESSION['error'] = 'Không tìm thấy giảng viên';
    header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
    exit;
}

// Lấy danh sách khoa và trình độ
$listKhoa = $khoaModel->getActiveList();
$listTrinhDo = $danhMucModel->getTrinhDoChuyenMon();

$currentUser = getCurrentUser();
$pageTitle = 'Sửa Giảng viên';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-pencil-square"></i> Sửa Giảng viên: <?php echo htmlspecialchars($giangVien['ten_giang_vien']); ?></h2>
            <a href="<?php echo BASE_URL; ?>/giao-vu/giang-vien.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_basic">Thông tin cơ bản</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_education">Trình độ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_contact">Liên hệ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_bank">Ngân hàng</a>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Tab 1: Thông tin cơ bản -->
                        <div class="tab-pane fade show active" id="tab_basic">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Khoa <span class="text-danger">*</span></label>
                                    <select name="khoa_id" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <?php foreach ($listKhoa as $khoa): ?>
                                        <option value="<?php echo $khoa['khoa_id']; ?>"
                                                <?php echo ($khoa['khoa_id'] == $giangVien['khoa_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã giảng viên <span class="text-danger">*</span></label>
                                    <input type="text" name="ma_giang_vien" class="form-control" required
                                           value="<?php echo htmlspecialchars($giangVien['ma_giang_vien']); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_giang_vien" class="form-control" required
                                           value="<?php echo htmlspecialchars($giangVien['ten_giang_vien']); ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select name="gioi_tinh" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam" <?php echo ($giangVien['gioi_tinh'] == 'Nam') ? 'selected' : ''; ?>>Nam</option>
                                        <option value="Nữ" <?php echo ($giangVien['gioi_tinh'] == 'Nữ') ? 'selected' : ''; ?>>Nữ</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Năm sinh</label>
                                    <input type="number" name="nam_sinh" class="form-control" 
                                           min="1950" max="2010" value="<?php echo $giangVien['nam_sinh'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" class="form-control"
                                           value="<?php echo $giangVien['ngay_sinh'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nơi sinh</label>
                                    <input type="text" name="noi_sinh" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['noi_sinh']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số CCCD</label>
                                    <input type="text" name="so_cccd" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['so_cccd']) ?? ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày cấp</label>
                                    <input type="date" name="ngay_cap_cccd" class="form-control"
                                           value="<?php echo $giangVien['ngay_cap_cccd'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nơi cấp</label>
                                    <input type="text" name="noi_cap_cccd" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['noi_cap_cccd']) ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 2: Trình độ -->
                        <div class="tab-pane fade" id="tab_education">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trình độ</label>
                                    <select name="trinh_do_id" class="form-select">
                                        <option value="">-- Chọn trình độ --</option>
                                        <?php foreach ($listTrinhDo as $td): ?>
                                        <option value="<?php echo $td['trinh_do_id']; ?>"
                                                <?php echo ($td['trinh_do_id'] == $giangVien['trinh_do_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($td['ten_trinh_do']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chuyên ngành đào tạo</label>
                                    <input type="text" name="chuyen_nganh_dao_tao" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['chuyen_nganh_dao_tao']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trường đào tạo</label>
                                    <input type="text" name="truong_dao_tao" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['truong_dao_tao']) ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Năm tốt nghiệp</label>
                                    <input type="number" name="nam_tot_nghiep" class="form-control"
                                           min="1980" max="2030" value="<?php echo $giangVien['nam_tot_nghiep'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Chứng chỉ sư phạm</label>
                                <input type="text" name="chung_chi_su_pham" class="form-control"
                                       value="<?php echo htmlspecialchars($giangVien['chung_chi_su_pham']) ?? ''; ?>">
                            </div>
                        </div>
                        
                        <!-- Tab 3: Liên hệ -->
                        <div class="tab-pane fade" id="tab_contact">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Địa chỉ thường trú</label>
                                    <input type="text" name="dia_chi" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['dia_chi']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Địa chỉ tạm trú</label>
                                    <input type="text" name="dia_chi_tam_tru" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['dia_chi_tam_tru']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="so_dien_thoai" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['so_dien_thoai']) ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['email']) ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 4: Ngân hàng -->
                        <div class="tab-pane fade" id="tab_bank">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số tài khoản</label>
                                    <input type="text" name="so_tai_khoan" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['so_tai_khoan']) ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chủ tài khoản</label>
                                    <input type="text" name="chu_tai_khoan" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['chu_tai_khoan']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên ngân hàng</label>
                                    <input type="text" name="ten_ngan_hang" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['ten_ngan_hang']) ?? ''; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chi nhánh</label>
                                    <input type="text" name="chi_nhanh_ngan_hang" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['chi_nhanh_ngan_hang']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã số thuế</label>
                                    <input type="text" name="ma_so_thue" class="form-control"
                                           value="<?php echo htmlspecialchars($giangVien['ma_so_thue']) ?? ''; ?>">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" class="form-control" rows="3"><?php echo htmlspecialchars($giangVien['ghi_chu']) ?? ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                               <?php echo ($giangVien['is_active'] == 1) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">
                            Hoạt động
                        </label>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Cập nhật
                        </button>
                        <a href="<?php echo BASE_URL; ?>/giao-vu/giang-vien.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>