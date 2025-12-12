<?php
/**
 * Quản lý Lớp
 * File: /danh-muc/lop.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Lop.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/NienKhoa.php';
require_once __DIR__ . '/../models/Khoa.php';

// Kiểm tra đăng nhập và quyền Admin
if (!isLoggedIn() || !hasRole('Admin')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$lopModel = new Lop($db);
$ngheModel = new Nghe($db);
$nienKhoaModel = new NienKhoa($db);
$khoaModel = new Khoa($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$lop_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $lop_id) {
    $result = $lopModel->deleteLop($lop_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/lop.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $data = [
        'nghe_id' => (int)($_POST['nghe_id'] ?? 0),
        'nien_khoa_id' => !empty($_POST['nien_khoa_id']) ? (int)$_POST['nien_khoa_id'] : null,
        'ma_lop' => trim($_POST['ma_lop'] ?? ''),
        'ten_lop' => trim($_POST['ten_lop'] ?? ''),
        'si_so' => !empty($_POST['si_so']) ? (int)$_POST['si_so'] : null,
        'giao_vien_chu_nhiem' => trim($_POST['giao_vien_chu_nhiem'] ?? ''),
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['nghe_id'])) {
        $errors[] = 'Vui lòng chọn nghề';
    }
    if (empty($data['ma_lop'])) {
        $errors[] = 'Mã lớp không được để trống';
    }
    if (empty($data['ten_lop'])) {
        $errors[] = 'Tên lớp không được để trống';
    }
    
    // Kiểm tra mã lớp trùng trong nghề
    if ($lopModel->checkMaLopExists($data['nghe_id'], $data['ma_lop'], $action === 'edit' ? $lop_id : null)) {
        $errors[] = 'Mã lớp đã tồn tại trong nghề này';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $lopModel->createLop($data);
            $message = $success ? 'Thêm lớp thành công' : 'Lỗi khi thêm lớp';
        } else {
            $success = $lopModel->updateLop($lop_id, $data);
            $message = $success ? 'Cập nhật lớp thành công' : 'Lỗi khi cập nhật lớp';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/lop.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listLop = $lopModel->getAllWithDetails();
}

// Lấy thông tin lớp để edit
if ($action === 'edit' && $lop_id) {
    $lop = $lopModel->getByIdWithDetails($lop_id);
    if (!$lop) {
        $_SESSION['error'] = 'Không tìm thấy lớp';
        header('Location: ' . BASE_URL . '/danh-muc/lop.php');
        exit;
    }
}

// Lấy danh sách khoa, nghề, niên khóa
$listKhoa = $khoaModel->getActiveList();
$listNghe = $ngheModel->getAll(['is_active' => 1], 'ten_nghe ASC');
$listNienKhoa = $nienKhoaModel->getActiveList();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Lớp';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-people"></i> Quản lý Lớp</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Lớp
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH LỚP -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableLop">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="12%">Khoa</th>
                                <th width="15%">Nghề</th>
                                <th width="10%">Mã lớp</th>
                                <th width="15%">Tên lớp</th>
                                <th width="10%">Niên khóa</th>
                                <th width="8%">Sĩ số</th>
                                <th width="12%">GVCN</th>
                                <th width="8%">Trạng thái</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listLop)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listLop as $index => $lop): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($lop['ma_khoa']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($lop['ma_nghe']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($lop['ten_nghe']); ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($lop['ma_lop']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($lop['ten_lop']); ?></td>
                                    <td>
                                        <?php if ($lop['ma_nien_khoa']): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($lop['ma_nien_khoa']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo $lop['si_so'] ?? '-'; ?>
                                    </td>
                                    <td>
                                        <?php if ($lop['giao_vien_chu_nhiem']): ?>
                                            <small><?php echo htmlspecialchars($lop['giao_vien_chu_nhiem']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($lop['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editLop(<?php echo htmlspecialchars(json_encode($lop)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteLop(<?php echo $lop['lop_id']; ?>, '<?php echo htmlspecialchars($lop['ten_lop']); ?>')">
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

<!-- MODAL THÊM LỚP -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Lớp Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                            <select class="form-select" id="add_khoa_id" onchange="loadNgheByKhoa('add')">
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($listKhoa as $khoa): ?>
                                <option value="<?php echo $khoa['khoa_id']; ?>">
                                    <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nghề <span class="text-danger">*</span></label>
                            <select name="nghe_id" id="add_nghe_id" class="form-select" required>
                                <option value="">-- Chọn nghề --</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã lớp <span class="text-danger">*</span></label>
                            <input type="text" name="ma_lop" class="form-control" required 
                                   placeholder="Ví dụ: CNTT01-K1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên lớp <span class="text-danger">*</span></label>
                            <input type="text" name="ten_lop" class="form-control" required
                                   placeholder="Ví dụ: Lập trình K1">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Niên khóa</label>
                            <select name="nien_khoa_id" class="form-select">
                                <option value="">-- Chọn niên khóa --</option>
                                <?php foreach ($listNienKhoa as $nk): ?>
                                <option value="<?php echo $nk['nien_khoa_id']; ?>">
                                    <?php echo htmlspecialchars($nk['ten_nien_khoa']); ?>
                                    (<?php echo htmlspecialchars($nk['ten_nghe']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sĩ số</label>
                            <input type="number" name="si_so" class="form-control" min="1" max="200"
                                   placeholder="Ví dụ: 35">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giáo viên chủ nhiệm</label>
                            <input type="text" name="giao_vien_chu_nhiem" class="form-control"
                                   placeholder="Ví dụ: Nguyễn Văn A">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                        </div>
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

<!-- MODAL SỬA LỚP -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Lớp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_khoa_id" onchange="loadNgheByKhoa('edit')">
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($listKhoa as $khoa): ?>
                                <option value="<?php echo $khoa['khoa_id']; ?>">
                                    <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nghề <span class="text-danger">*</span></label>
                            <select name="nghe_id" id="edit_nghe_id" class="form-select" required>
                                <option value="">-- Chọn nghề --</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã lớp <span class="text-danger">*</span></label>
                            <input type="text" name="ma_lop" id="edit_ma_lop" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên lớp <span class="text-danger">*</span></label>
                            <input type="text" name="ten_lop" id="edit_ten_lop" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Niên khóa</label>
                            <select name="nien_khoa_id" id="edit_nien_khoa_id" class="form-select">
                                <option value="">-- Chọn niên khóa --</option>
                                <?php foreach ($listNienKhoa as $nk): ?>
                                <option value="<?php echo $nk['nien_khoa_id']; ?>">
                                    <?php echo htmlspecialchars($nk['ten_nien_khoa']); ?>
                                    (<?php echo htmlspecialchars($nk['ten_nghe']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sĩ số</label>
                            <input type="number" name="si_so" id="edit_si_so" class="form-control" min="1" max="200">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Giáo viên chủ nhiệm</label>
                            <input type="text" name="giao_vien_chu_nhiem" id="edit_giao_vien_chu_nhiem" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="edit_thu_tu" class="form-control" min="0">
                        </div>
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
// Danh sách nghề theo khoa (từ PHP)
const ngheByKhoa = <?php echo json_encode(array_reduce($listNghe, function($carry, $item) {
    $carry[$item['khoa_id']][] = $item;
    return $carry;
}, [])); ?>;

function loadNgheByKhoa(mode) {
    const khoaId = document.getElementById(mode + '_khoa_id').value;
    const ngheSelect = document.getElementById(mode + '_nghe_id');
    
    // Clear options
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    
    if (khoaId && ngheByKhoa[khoaId]) {
        ngheByKhoa[khoaId].forEach(nghe => {
            const option = document.createElement('option');
            option.value = nghe.nghe_id;
            option.textContent = nghe.ten_nghe;
            ngheSelect.appendChild(option);
        });
    }
}

function editLop(lop) {
    document.getElementById('editForm').action = '?action=edit&id=' + lop.lop_id;
    
    // Load nghề theo khoa trước
    const ngheItem = <?php echo json_encode($listNghe); ?>.find(n => n.nghe_id == lop.nghe_id);
    if (ngheItem) {
        document.getElementById('edit_khoa_id').value = ngheItem.khoa_id;
        loadNgheByKhoa('edit');
        setTimeout(() => {
            document.getElementById('edit_nghe_id').value = lop.nghe_id;
        }, 100);
    }
    
    document.getElementById('edit_ma_lop').value = lop.ma_lop;
    document.getElementById('edit_ten_lop').value = lop.ten_lop;
    document.getElementById('edit_nien_khoa_id').value = lop.nien_khoa_id || '';
    document.getElementById('edit_si_so').value = lop.si_so || '';
    document.getElementById('edit_giao_vien_chu_nhiem').value = lop.giao_vien_chu_nhiem || '';
    document.getElementById('edit_thu_tu').value = lop.thu_tu;
    document.getElementById('edit_is_active').checked = lop.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteLop(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa lớp "' + name + '"?\n\nLưu ý: Chỉ có thể xóa lớp không có hợp đồng liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>