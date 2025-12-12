<?php
/**
 * Quản lý Môn học
 * File: /danh-muc/mon-hoc.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/MonHoc.php';
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
$monHocModel = new MonHoc($db);
$ngheModel = new Nghe($db);
$nienKhoaModel = new NienKhoa($db);
$khoaModel = new Khoa($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$mon_hoc_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $mon_hoc_id) {
    $result = $monHocModel->deleteMonHoc($mon_hoc_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/mon-hoc.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    // Tính tổng số tiết
    $so_tiet_lt = (int)($_POST['so_tiet_ly_thuyet'] ?? 0);
    $so_tiet_th = (int)($_POST['so_tiet_thuc_hanh'] ?? 0);
    $tong_so_tiet = $so_tiet_lt + $so_tiet_th;
    
    $data = [
        'nghe_id' => (int)($_POST['nghe_id'] ?? 0),
        'nien_khoa_id' => !empty($_POST['nien_khoa_id']) ? (int)$_POST['nien_khoa_id'] : null,
        'ma_mon_hoc' => trim($_POST['ma_mon_hoc'] ?? ''),
        'ten_mon_hoc' => trim($_POST['ten_mon_hoc'] ?? ''),
        'so_tiet_ly_thuyet' => $so_tiet_lt,
        'so_tiet_thuc_hanh' => $so_tiet_th,
        'tong_so_tiet' => $tong_so_tiet,
        'mo_ta' => trim($_POST['mo_ta'] ?? ''),
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['nghe_id'])) {
        $errors[] = 'Vui lòng chọn nghề';
    }
    if (empty($data['ma_mon_hoc'])) {
        $errors[] = 'Mã môn học không được để trống';
    }
    if (empty($data['ten_mon_hoc'])) {
        $errors[] = 'Tên môn học không được để trống';
    }
    
    // Kiểm tra mã môn học trùng trong nghề
    if ($monHocModel->checkMaMonHocExists($data['nghe_id'], $data['ma_mon_hoc'], $action === 'edit' ? $mon_hoc_id : null)) {
        $errors[] = 'Mã môn học đã tồn tại trong nghề này';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $monHocModel->createMonHoc($data);
            $message = $success ? 'Thêm môn học thành công' : 'Lỗi khi thêm môn học';
        } else {
            $success = $monHocModel->updateMonHoc($mon_hoc_id, $data);
            $message = $success ? 'Cập nhật môn học thành công' : 'Lỗi khi cập nhật môn học';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/mon-hoc.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listMonHoc = $monHocModel->getAllWithDetails();
}

// Lấy thông tin môn học để edit
if ($action === 'edit' && $mon_hoc_id) {
    $monHoc = $monHocModel->getByIdWithDetails($mon_hoc_id);
    if (!$monHoc) {
        $_SESSION['error'] = 'Không tìm thấy môn học';
        header('Location: ' . BASE_URL . '/danh-muc/mon-hoc.php');
        exit;
    }
}

// Lấy danh sách khoa, nghề, niên khóa
$listKhoa = $khoaModel->getActiveList();
$listNghe = $ngheModel->getAll(['is_active' => 1], 'ten_nghe ASC');
$listNienKhoa = $nienKhoaModel->getActiveList();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Môn học';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-book"></i> Quản lý Môn học</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Môn học
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH MÔN HỌC -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="tableMonHoc">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Khoa</th>
                                <th width="15%">Nghề</th>
                                <th width="10%">Mã MH</th>
                                <th width="20%">Tên môn học</th>
                                <th width="8%">Niên khóa</th>
                                <th width="7%">LT</th>
                                <th width="7%">TH</th>
                                <th width="7%">Tổng</th>
                                <th width="8%">Trạng thái</th>
                                <th width="8%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listMonHoc)): ?>
                            <tr>
                                <td colspan="11" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listMonHoc as $index => $mh): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo htmlspecialchars($mh['ma_khoa']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($mh['ma_nghe']); ?>
                                        </span>
                                        <small><?php echo htmlspecialchars($mh['ten_nghe']); ?></small>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($mh['ma_mon_hoc']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($mh['ten_mon_hoc']); ?></td>
                                    <td>
                                        <?php if ($mh['ma_nien_khoa']): ?>
                                            <small class="text-muted"><?php echo htmlspecialchars($mh['ma_nien_khoa']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $mh['so_tiet_ly_thuyet']; ?></td>
                                    <td class="text-center"><?php echo $mh['so_tiet_thuc_hanh']; ?></td>
                                    <td class="text-center"><strong><?php echo $mh['tong_so_tiet']; ?></strong></td>
                                    <td class="text-center">
                                        <?php if ($mh['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editMonHoc(<?php echo htmlspecialchars(json_encode($mh)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteMonHoc(<?php echo $mh['mon_hoc_id']; ?>, '<?php echo htmlspecialchars($mh['ten_mon_hoc']); ?>')">
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

<!-- MODAL THÊM MÔN HỌC -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Môn học Mới</h5>
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
                            <label class="form-label">Mã môn học <span class="text-danger">*</span></label>
                            <input type="text" name="ma_mon_hoc" class="form-control" required 
                                   placeholder="Ví dụ: MH001">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                            <input type="text" name="ten_mon_hoc" class="form-control" required
                                   placeholder="Ví dụ: Lập trình C++">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Số tiết lý thuyết</label>
                            <input type="number" name="so_tiet_ly_thuyet" class="form-control" 
                                   min="0" value="0" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Số tiết thực hành</label>
                            <input type="number" name="so_tiet_thuc_hanh" class="form-control" 
                                   min="0" value="0" placeholder="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
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
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="2"
                                  placeholder="Mô tả về môn học..."></textarea>
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

<!-- MODAL SỬA MÔN HỌC -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Môn học</h5>
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
                            <label class="form-label">Mã môn học <span class="text-danger">*</span></label>
                            <input type="text" name="ma_mon_hoc" id="edit_ma_mon_hoc" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên môn học <span class="text-danger">*</span></label>
                            <input type="text" name="ten_mon_hoc" id="edit_ten_mon_hoc" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Số tiết lý thuyết</label>
                            <input type="number" name="so_tiet_ly_thuyet" id="edit_so_tiet_ly_thuyet" class="form-control" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Số tiết thực hành</label>
                            <input type="number" name="so_tiet_thuc_hanh" id="edit_so_tiet_thuc_hanh" class="form-control" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="edit_thu_tu" class="form-control" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
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

function editMonHoc(mh) {
    document.getElementById('editForm').action = '?action=edit&id=' + mh.mon_hoc_id;
    
    // Load nghề theo khoa trước
    const ngheItem = <?php echo json_encode($listNghe); ?>.find(n => n.nghe_id == mh.nghe_id);
    if (ngheItem) {
        document.getElementById('edit_khoa_id').value = ngheItem.khoa_id;
        loadNgheByKhoa('edit');
        setTimeout(() => {
            document.getElementById('edit_nghe_id').value = mh.nghe_id;
        }, 100);
    }
    
    document.getElementById('edit_ma_mon_hoc').value = mh.ma_mon_hoc;
    document.getElementById('edit_ten_mon_hoc').value = mh.ten_mon_hoc;
    document.getElementById('edit_so_tiet_ly_thuyet').value = mh.so_tiet_ly_thuyet;
    document.getElementById('edit_so_tiet_thuc_hanh').value = mh.so_tiet_thuc_hanh;
    document.getElementById('edit_nien_khoa_id').value = mh.nien_khoa_id || '';
    document.getElementById('edit_mo_ta').value = mh.mo_ta || '';
    document.getElementById('edit_thu_tu').value = mh.thu_tu;
    document.getElementById('edit_is_active').checked = mh.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteMonHoc(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa môn học "' + name + '"?\n\nLưu ý: Chỉ có thể xóa môn học không có hợp đồng liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>