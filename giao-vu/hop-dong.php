<?php
/**
 * Quản lý Hợp đồng (Giáo vụ)
 * File: /giao-vu/hop-dong.php
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

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$hop_dong_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $hop_dong_id && hasRole('Admin')) {
    $result = $hopDongModel->deleteHopDong($hop_dong_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/giao-vu/hop-dong.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
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

// Lấy danh sách
if ($action === 'list') {
    $listHopDong = $hopDongModel->getAllWithDetails();
}

// Lấy thông tin hợp đồng để edit
if ($action === 'edit' && $hop_dong_id) {
    $hopDong = $hopDongModel->getByIdWithFullDetails($hop_dong_id);
    if (!$hopDong) {
        $_SESSION['error'] = 'Không tìm thấy hợp đồng';
        header('Location: ' . BASE_URL . '/giao-vu/hop-dong.php');
        exit;
    }
}

// Xem chi tiết
if ($action === 'view' && $hop_dong_id) {
    $hopDong = $hopDongModel->getByIdWithFullDetails($hop_dong_id);
    if (!$hopDong) {
        $_SESSION['error'] = 'Không tìm thấy hợp đồng';
        header('Location: ' . BASE_URL . '/giao-vu/hop-dong.php');
        exit;
    }
}

// Lấy danh sách cho dropdown
$listGiangVien = $giangVienModel->getAll(['is_active' => 1], 'ten_giang_vien ASC');
$listMonHoc = $monHocModel->getAll(['is_active' => 1], 'ten_mon_hoc ASC');
$listNghe = $ngheModel->getAll(['is_active' => 1], 'ten_nghe ASC');
$listLop = $lopModel->getAll(['is_active' => 1], 'ten_lop ASC');
$listCoSo = $coSoModel->getActiveList();
$listCapDo = $danhMucModel->getCapDoGiangDay();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Hợp đồng';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-file-earmark-text"></i> Quản lý Hợp đồng thỉnh giảng</h2>
            <?php if ($action === 'list'): ?>
            <div>
                <button type="button" class="btn btn-success" onclick="alert('Chức năng Export Excel đang phát triển')">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </button>
                <a href="?action=add" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm Hợp đồng
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH HỢP ĐỒNG -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-sm" id="tableHopDong">
                        <thead class="table-primary">
                            <tr>
                                <th width="3%">STT</th>
                                <th width="8%">Số HĐ</th>
                                <th width="8%">Ngày HĐ</th>
                                <th width="15%">Giảng viên</th>
                                <th width="12%">Môn học</th>
                                <th width="10%">Lớp</th>
                                <th width="8%">Cơ sở</th>
                                <th width="6%">Tổng giờ</th>
                                <th width="10%">Tổng tiền</th>
                                <th width="10%">Trạng thái</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listHopDong)): ?>
                            <tr>
                                <td colspan="11" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listHopDong as $index => $hd): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <?php if ($hd['so_hop_dong']): ?>
                                            <strong><?php echo htmlspecialchars($hd['so_hop_dong']); ?></strong>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <small><?php echo formatDate($hd['ngay_hop_dong']); ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($hd['ma_giang_vien']); ?></small><br>
                                        <?php echo htmlspecialchars($hd['ten_giang_vien']); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($hd['ma_mon_hoc']); ?></small><br>
                                        <?php echo htmlspecialchars($hd['ten_mon_hoc']); ?>
                                    </td>
                                    <td>
                                        <?php if ($hd['ten_lop']): ?>
                                            <small><?php echo htmlspecialchars($hd['ten_lop']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($hd['ten_co_so']): ?>
                                            <small><?php echo htmlspecialchars($hd['ten_co_so']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <strong><?php echo $hd['tong_gio_mon_hoc']; ?></strong>
                                    </td>
                                    <td class="text-end">
                                        <strong><?php echo formatMoney($hd['tong_tien']); ?></strong>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $badgeClass = 'secondary';
                                        switch ($hd['trang_thai']) {
                                            case 'Mới tạo': $badgeClass = 'info'; break;
                                            case 'Đã duyệt': $badgeClass = 'success'; break;
                                            case 'Đang thực hiện': $badgeClass = 'warning'; break;
                                            case 'Hoàn thành': $badgeClass = 'primary'; break;
                                            case 'Hủy': $badgeClass = 'danger'; break;
                                        }
                                        ?>
                                        <span class="badge bg-<?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($hd['trang_thai']); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?action=view&id=<?php echo $hd['hop_dong_id']; ?>" 
                                           class="btn btn-sm btn-info" title="Xem">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?action=edit&id=<?php echo $hd['hop_dong_id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-success" 
                                                onclick="alert('Chức năng In hợp đồng đang phát triển')"
                                                title="In">
                                            <i class="bi bi-printer"></i>
                                        </button>
                                        <?php if (hasRole('Admin')): ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteHopDong(<?php echo $hd['hop_dong_id']; ?>)"
                                                title="Xóa">
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

<script>
function deleteHopDong(id) {
    if (confirm('Bạn có chắc chắn muốn xóa hợp đồng này?')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php elseif ($action === 'view'): ?>
<!-- CHI TIẾT HỢP ĐỒNG -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-file-earmark-text"></i> Chi tiết Hợp đồng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-info-circle"></i> Thông tin hợp đồng</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td width="40%"><strong>Số hợp đồng:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['so_hop_dong']) ?: 'Chưa có'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày hợp đồng:</strong></td>
                                <td><?php echo formatDate($hopDong['ngay_hop_dong']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày bắt đầu:</strong></td>
                                <td><?php echo formatDate($hopDong['ngay_bat_dau']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Ngày kết thúc:</strong></td>
                                <td><?php echo formatDate($hopDong['ngay_ket_thuc']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
                                <td>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($hopDong['trang_thai']); ?></span>
                                </td>
                            </tr>
                        </table>
                        
                        <h6 class="text-primary mt-4"><i class="bi bi-person"></i> Thông tin giảng viên</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td width="40%"><strong>Mã GV:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ma_giang_vien']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Họ tên:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_giang_vien']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>SĐT:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['sdt_giang_vien']) ?: '-'; ?></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-book"></i> Thông tin môn học</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td width="40%"><strong>Khoa:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_khoa']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Nghề:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_nghe']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Môn học:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_mon_hoc']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Lớp:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_lop']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Cơ sở:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_co_so']) ?: '-'; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Cấp độ:</strong></td>
                                <td><?php echo htmlspecialchars($hopDong['ten_cap_do']) ?: '-'; ?></td>
                            </tr>
                        </table>
                        
                        <h6 class="text-primary mt-4"><i class="bi bi-cash-coin"></i> Thông tin thanh toán</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td width="40%"><strong>Tổng giờ:</strong></td>
                                <td><strong><?php echo $hopDong['tong_gio_mon_hoc']; ?></strong> giờ</td>
                            </tr>
                            <tr>
                                <td><strong>Đơn giá/giờ:</strong></td>
                                <td><?php echo formatMoney($hopDong['don_gia_gio']); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Tổng tiền:</strong></td>
                                <td><strong class="text-danger"><?php echo formatMoney($hopDong['tong_tien']); ?></strong></td>
                            </tr>
                            <tr>
                                <td><strong>Bằng chữ:</strong></td>
                                <td><em><?php echo htmlspecialchars($hopDong['tong_tien_chu']); ?></em></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($hopDong['ghi_chu']): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-primary"><i class="bi bi-chat-left-text"></i> Ghi chú</h6>
                        <p><?php echo nl2br(htmlspecialchars($hopDong['ghi_chu'])); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="?action=list" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại
                    </a>
                    <a href="?action=edit&id=<?php echo $hopDong['hop_dong_id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Sửa
                    </a>
                    <button type="button" class="btn btn-success" onclick="alert('Chức năng In hợp đồng đang phát triển')">
                        <i class="bi bi-printer"></i> In hợp đồng
                    </button>
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