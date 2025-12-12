<?php
/**
 * Quản lý Đơn giá giờ dạy
 * File: /danh-muc/don-gia.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/DonGiaGioDay.php';
require_once __DIR__ . '/../models/CoSo.php';
require_once __DIR__ . '/../models/DanhMuc.php';

// Kiểm tra đăng nhập và quyền Admin
if (!isLoggedIn() || !hasRole('Admin')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$donGiaModel = new DonGiaGioDay($db);
$coSoModel = new CoSo($db);
$danhMucModel = new DanhMuc($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$don_gia_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $don_gia_id) {
    $result = $donGiaModel->deleteDonGia($don_gia_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/don-gia.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $data = [
        'co_so_id' => (int)($_POST['co_so_id'] ?? 0),
        'trinh_do_id' => (int)($_POST['trinh_do_id'] ?? 0),
        'don_gia' => (int)($_POST['don_gia'] ?? 0),
        'nam_ap_dung' => (int)($_POST['nam_ap_dung'] ?? date('Y')),
        'tu_ngay' => !empty($_POST['tu_ngay']) ? $_POST['tu_ngay'] : null,
        'den_ngay' => !empty($_POST['den_ngay']) ? $_POST['den_ngay'] : null,
        'mo_ta' => trim($_POST['mo_ta'] ?? ''),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['co_so_id'])) {
        $errors[] = 'Vui lòng chọn cơ sở';
    }
    if (empty($data['trinh_do_id'])) {
        $errors[] = 'Vui lòng chọn trình độ';
    }
    if (empty($data['don_gia']) || $data['don_gia'] <= 0) {
        $errors[] = 'Đơn giá phải lớn hơn 0';
    }
    if (empty($data['nam_ap_dung'])) {
        $errors[] = 'Năm áp dụng không được để trống';
    }
    
    // Kiểm tra trùng lặp
    if ($donGiaModel->checkExists($data['co_so_id'], $data['trinh_do_id'], $data['nam_ap_dung'], $action === 'edit' ? $don_gia_id : null)) {
        $errors[] = 'Đơn giá cho cơ sở, trình độ và năm này đã tồn tại';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $donGiaModel->createDonGia($data);
            $message = $success ? 'Thêm đơn giá thành công' : 'Lỗi khi thêm đơn giá';
        } else {
            $success = $donGiaModel->updateDonGia($don_gia_id, $data);
            $message = $success ? 'Cập nhật đơn giá thành công' : 'Lỗi khi cập nhật đơn giá';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/don-gia.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listDonGia = $donGiaModel->getAllWithDetails();
}

// Lấy thông tin đơn giá để edit
if ($action === 'edit' && $don_gia_id) {
    $donGia = $donGiaModel->getByIdWithDetails($don_gia_id);
    if (!$donGia) {
        $_SESSION['error'] = 'Không tìm thấy đơn giá';
        header('Location: ' . BASE_URL . '/danh-muc/don-gia.php');
        exit;
    }
}

// Lấy danh sách cơ sở và trình độ
$listCoSo = $coSoModel->getActiveList();
$listTrinhDo = $danhMucModel->getTrinhDoChuyenMon();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Đơn giá giờ dạy';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-cash-coin"></i> Quản lý Đơn giá giờ dạy</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Đơn giá
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH ĐƠN GIÁ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="15%">Cơ sở</th>
                                <th width="15%">Trình độ</th>
                                <th width="12%">Đơn giá (VNĐ)</th>
                                <th width="8%">Năm áp dụng</th>
                                <th width="10%">Từ ngày</th>
                                <th width="10%">Đến ngày</th>
                                <th width="15%">Mô tả</th>
                                <th width="8%">Trạng thái</th>
                                <th width="7%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listDonGia)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listDonGia as $index => $dg): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($dg['ma_co_so']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($dg['ten_co_so']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($dg['ma_trinh_do']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($dg['ten_trinh_do']); ?>
                                    </td>
                                    <td class="text-end">
                                        <strong><?php echo formatMoney($dg['don_gia']); ?></strong>
                                    </td>
                                    <td class="text-center"><?php echo $dg['nam_ap_dung']; ?></td>
                                    <td class="text-center">
                                        <?php echo $dg['tu_ngay'] ? formatDate($dg['tu_ngay']) : '-'; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $dg['den_ngay'] ? formatDate($dg['den_ngay']) : '-'; ?>
                                    </td>
                                    <td>
                                        <?php if ($dg['mo_ta']): ?>
                                            <small><?php echo truncate(htmlspecialchars($dg['mo_ta']), 50); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($dg['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editDonGia(<?php echo htmlspecialchars(json_encode($dg)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteDonGia(<?php echo $dg['don_gia_id']; ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
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

<!-- MODAL THÊM ĐƠN GIÁ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Đơn giá Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cơ sở <span class="text-danger">*</span></label>
                            <select name="co_so_id" class="form-select" required>
                                <option value="">-- Chọn cơ sở --</option>
                                <?php foreach ($listCoSo as $cs): ?>
                                <option value="<?php echo $cs['co_so_id']; ?>">
                                    <?php echo htmlspecialchars($cs['ten_co_so']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trình độ <span class="text-danger">*</span></label>
                            <select name="trinh_do_id" class="form-select" required>
                                <option value="">-- Chọn trình độ --</option>
                                <?php foreach ($listTrinhDo as $td): ?>
                                <option value="<?php echo $td['trinh_do_id']; ?>">
                                    <?php echo htmlspecialchars($td['ten_trinh_do']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="don_gia" class="form-control" required 
                                   min="1000" step="1000" placeholder="Ví dụ: 100000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Năm áp dụng <span class="text-danger">*</span></label>
                            <input type="number" name="nam_ap_dung" class="form-control" required
                                   min="2020" max="2050" value="<?php echo date('Y'); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="2"
                                  placeholder="Ghi chú về đơn giá..."></textarea>
                    </div>
                    
                    <div class="form-check">
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

<!-- MODAL SỬA ĐƠN GIÁ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Đơn giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cơ sở <span class="text-danger">*</span></label>
                            <select name="co_so_id" id="edit_co_so_id" class="form-select" required>
                                <option value="">-- Chọn cơ sở --</option>
                                <?php foreach ($listCoSo as $cs): ?>
                                <option value="<?php echo $cs['co_so_id']; ?>">
                                    <?php echo htmlspecialchars($cs['ten_co_so']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trình độ <span class="text-danger">*</span></label>
                            <select name="trinh_do_id" id="edit_trinh_do_id" class="form-select" required>
                                <option value="">-- Chọn trình độ --</option>
                                <?php foreach ($listTrinhDo as $td): ?>
                                <option value="<?php echo $td['trinh_do_id']; ?>">
                                    <?php echo htmlspecialchars($td['ten_trinh_do']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đơn giá (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="don_gia" id="edit_don_gia" class="form-control" required 
                                   min="1000" step="1000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Năm áp dụng <span class="text-danger">*</span></label>
                            <input type="number" name="nam_ap_dung" id="edit_nam_ap_dung" class="form-control" required
                                   min="2020" max="2050">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Từ ngày</label>
                            <input type="date" name="tu_ngay" id="edit_tu_ngay" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Đến ngày</label>
                            <input type="date" name="den_ngay" id="edit_den_ngay" class="form-control">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" id="edit_mo_ta" class="form-control" rows="2"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Hoạt động
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-check-circle"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDonGia(dg) {
    document.getElementById('editForm').action = '?action=edit&id=' + dg.don_gia_id;
    document.getElementById('edit_co_so_id').value = dg.co_so_id;
    document.getElementById('edit_trinh_do_id').value = dg.trinh_do_id;
    document.getElementById('edit_don_gia').value = dg.don_gia;
    document.getElementById('edit_nam_ap_dung').value = dg.nam_ap_dung;
    document.getElementById('edit_tu_ngay').value = dg.tu_ngay || '';
    document.getElementById('edit_den_ngay').value = dg.den_ngay || '';
    document.getElementById('edit_mo_ta').value = dg.mo_ta || '';
    document.getElementById('edit_is_active').checked = dg.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteDonGia(id) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn giá này?')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>