<?php
/**
 * Quản lý Giảng viên (Giáo vụ)
 * File: /giao-vu/giang-vien.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/GiangVien.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/DanhMuc.php';

// Kiểm tra đăng nhập và quyền Giáo vụ hoặc Admin
if (!isLoggedIn() || !hasRole(['Admin', 'Giao_Vu'])) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$giangVienModel = new GiangVien($db);
$khoaModel = new Khoa($db);
$danhMucModel = new DanhMuc($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$giang_vien_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $giang_vien_id && hasRole('Admin')) {
    $result = $giangVienModel->deleteGiangVien($giang_vien_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
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
    if ($giangVienModel->checkMaGiangVienExists($data['ma_giang_vien'], $action === 'edit' ? $giang_vien_id : null)) {
        $errors[] = 'Mã giảng viên đã tồn tại';
    }
    
    // Kiểm tra CCCD trùng
    if (!empty($data['so_cccd']) && $giangVienModel->checkCccdExists($data['so_cccd'], $action === 'edit' ? $giang_vien_id : null)) {
        $errors[] = 'Số CCCD đã tồn tại';
    }
    
    // Kiểm tra email
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = 'Email không đúng định dạng';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $giangVienModel->createGiangVien($data);
            $message = $success ? 'Thêm giảng viên thành công' : 'Lỗi khi thêm giảng viên';
        } else {
            $success = $giangVienModel->updateGiangVien($giang_vien_id, $data);
            $message = $success ? 'Cập nhật giảng viên thành công' : 'Lỗi khi cập nhật giảng viên';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listGiangVien = $giangVienModel->getAllWithDetails();
}

// Lấy thông tin giảng viên để edit
if ($action === 'edit' && $giang_vien_id) {
    $giangVien = $giangVienModel->getByIdWithDetails($giang_vien_id);
    if (!$giangVien) {
        $_SESSION['error'] = 'Không tìm thấy giảng viên';
        header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
        exit;
    }
}

// Xem chi tiết
if ($action === 'view' && $giang_vien_id) {
    $giangVien = $giangVienModel->getByIdWithDetails($giang_vien_id);
    if (!$giangVien) {
        $_SESSION['error'] = 'Không tìm thấy giảng viên';
        header('Location: ' . BASE_URL . '/giao-vu/giang-vien.php');
        exit;
    }
}

// Lấy danh sách khoa và trình độ
$listKhoa = $khoaModel->getActiveList();
$listTrinhDo = $danhMucModel->getTrinhDoChuyenMon();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Giảng viên';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-person-badge"></i> Quản lý Giảng viên</h2>
            <?php if ($action === 'list'): ?>
            <div>
                <button type="button" class="btn btn-success" onclick="alert('Chức năng Import Excel đang phát triển')">
                    <i class="bi bi-file-earmark-excel"></i> Import Excel
                </button>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle"></i> Thêm Giảng viên
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH GIẢNG VIÊN -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableGiangVien">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã GV</th>
                                <th width="15%">Họ tên</th>
                                <th width="8%">Năm sinh</th>
                                <th width="12%">Khoa</th>
                                <th width="12%">Trình độ</th>
                                <th width="10%">SĐT</th>
                                <th width="15%">Email</th>
                                <th width="8%">Trạng thái</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listGiangVien)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listGiangVien as $index => $gv): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($gv['ma_giang_vien']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($gv['ten_giang_vien']); ?></td>
                                    <td class="text-center"><?php echo $gv['nam_sinh'] ?? '-'; ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($gv['ma_khoa']); ?>
                                        </span>
                                        <small><?php echo htmlspecialchars($gv['ten_khoa']); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($gv['ten_trinh_do']): ?>
                                            <small><?php echo htmlspecialchars($gv['ten_trinh_do']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($gv['so_dien_thoai']) ?: '-'; ?></td>
                                    <td>
                                        <?php if ($gv['email']): ?>
                                            <small><?php echo htmlspecialchars($gv['email']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($gv['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?action=view&id=<?php echo $gv['giang_vien_id']; ?>" 
                                           class="btn btn-sm btn-info" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?action=edit&id=<?php echo $gv['giang_vien_id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (hasRole('Admin')): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteGiangVien(<?php echo $gv['giang_vien_id']; ?>, '<?php echo htmlspecialchars($gv['ten_giang_vien']); ?>')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL THÊM GIẢNG VIÊN -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Giảng viên Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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
                                        <option value="<?php echo $khoa['khoa_id']; ?>">
                                            <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã giảng viên <span class="text-danger">*</span></label>
                                    <input type="text" name="ma_giang_vien" class="form-control" required
                                           placeholder="Ví dụ: GV001">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_giang_vien" class="form-control" required
                                           placeholder="Nguyễn Văn A">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Giới tính</label>
                                    <select name="gioi_tinh" class="form-select">
                                        <option value="">-- Chọn --</option>
                                        <option value="Nam">Nam</option>
                                        <option value="Nữ">Nữ</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Năm sinh</label>
                                    <input type="number" name="nam_sinh" class="form-control" 
                                           min="1950" max="2010" placeholder="1990">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày sinh</label>
                                    <input type="date" name="ngay_sinh" class="form-control">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nơi sinh</label>
                                    <input type="text" name="noi_sinh" class="form-control"
                                           placeholder="Tỉnh/Thành phố">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Số CCCD</label>
                                    <input type="text" name="so_cccd" class="form-control"
                                           placeholder="001234567890">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Ngày cấp</label>
                                    <input type="date" name="ngay_cap_cccd" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Nơi cấp</label>
                                    <input type="text" name="noi_cap_cccd" class="form-control"
                                           placeholder="Cục cảnh sát...">
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
                                        <option value="<?php echo $td['trinh_do_id']; ?>">
                                            <?php echo htmlspecialchars($td['ten_trinh_do']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chuyên ngành đào tạo</label>
                                    <input type="text" name="chuyen_nganh_dao_tao" class="form-control"
                                           placeholder="Ví dụ: Công nghệ thông tin">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Trường đào tạo</label>
                                    <input type="text" name="truong_dao_tao" class="form-control"
                                           placeholder="Ví dụ: ĐH Bách Khoa">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Năm tốt nghiệp</label>
                                    <input type="number" name="nam_tot_nghiep" class="form-control"
                                           min="1980" max="2030" placeholder="2015">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Chứng chỉ sư phạm</label>
                                <input type="text" name="chung_chi_su_pham" class="form-control"
                                       placeholder="Số chứng chỉ, cấp...">
                            </div>
                        </div>
                        
                        <!-- Tab 3: Liên hệ -->
                        <div class="tab-pane fade" id="tab_contact">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Địa chỉ thường trú</label>
                                    <input type="text" name="dia_chi" class="form-control"
                                           placeholder="Số nhà, đường, phường, quận, TP">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Địa chỉ tạm trú</label>
                                    <input type="text" name="dia_chi_tam_tru" class="form-control"
                                           placeholder="Số nhà, đường, phường, quận, TP">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="text" name="so_dien_thoai" class="form-control"
                                           placeholder="0901234567">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control"
                                           placeholder="example@email.com">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tab 4: Ngân hàng -->
                        <div class="tab-pane fade" id="tab_bank">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số tài khoản</label>
                                    <input type="text" name="so_tai_khoan" class="form-control"
                                           placeholder="1234567890">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chủ tài khoản</label>
                                    <input type="text" name="chu_tai_khoan" class="form-control"
                                           placeholder="NGUYEN VAN A">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên ngân hàng</label>
                                    <input type="text" name="ten_ngan_hang" class="form-control"
                                           placeholder="Vietcombank">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Chi nhánh</label>
                                    <input type="text" name="chi_nhanh_ngan_hang" class="form-control"
                                           placeholder="CN Quận 1">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Mã số thuế</label>
                                    <input type="text" name="ma_so_thue" class="form-control"
                                           placeholder="0123456789">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Ghi chú</label>
                                <textarea name="ghi_chu" class="form-control" rows="3"
                                          placeholder="Ghi chú thêm..."></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-check mt-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active_add" checked>
                        <label class="form-check-label" for="is_active_add">
                            Hoạt động
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteGiangVien(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa giảng viên "' + name + '"?\n\nLưu ý: Chỉ có thể xóa giảng viên không có hợp đồng liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php elseif ($action === 'view'): ?>
<!-- CHI TIẾT GIẢNG VIÊN -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-person-vcard"></i> Thông tin Giảng viên: <?php echo htmlspecialchars($giangVien['ten_giang_vien']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Thông tin cơ bản</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Mã giảng viên:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ma_giang_vien']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Họ tên:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ten_giang_vien']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Khoa:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ten_khoa']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Giới tính:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['gioi_tinh']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Năm sinh:</strong></td>
                                <td><?php echo $giangVien['nam_sinh'] ?? '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày sinh:</strong></td>
                                <td><?php echo $giangVien['ngay_sinh'] ? formatDate($giangVien['ngay_sinh']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nơi sinh:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['noi_sinh']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Số CCCD:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['so_cccd']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày cấp:</strong></td>
                                <td><?php echo $giangVien['ngay_cap_cccd'] ? formatDate($giangVien['ngay_cap_cccd']) : '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nơi cấp:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['noi_cap_cccd']) ?: '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-mortarboard"></i> Trình độ</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Trình độ:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ten_trinh_do']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Chuyên ngành:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['chuyen_nganh_dao_tao']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Trường đào tạo:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['truong_dao_tao']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Năm tốt nghiệp:</strong></td>
                                <td><?php echo $giangVien['nam_tot_nghiep'] ?? '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Chứng chỉ sư phạm:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['chung_chi_su_pham']) ?: '-'; ?></td>
                            </tr>
                        </table>
                        
                        <h6 class="text-primary mt-4"><i class="bi bi-telephone"></i> Liên hệ</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Số điện thoại:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['so_dien_thoai']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['email']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['dia_chi']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ tạm trú:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['dia_chi_tam_tru']) ?: '-'; ?></td>
                            </tr>
                        </table>
                        
                        <h6 class="text-primary mt-4"><i class="bi bi-bank"></i> Ngân hàng</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="40%"><strong>Số TK:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['so_tai_khoan']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Chủ TK:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['chu_tai_khoan']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngân hàng:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ten_ngan_hang']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Chi nhánh:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['chi_nhanh_ngan_hang']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Mã số thuế:</strong></td>
                                <td><?php echo htmlspecialchars($giangVien['ma_so_thue']) ?: '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($giangVien['ghi_chu']): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="bi bi-chat-left-text"></i> Ghi chú</h6>
                        <p><?php echo nl2br(htmlspecialchars($giangVien['ghi_chu'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <a href="?action=edit&id=<?php echo $giangVien['giang_vien_id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>