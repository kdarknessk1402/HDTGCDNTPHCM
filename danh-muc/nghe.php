<?php
/**
 * Quản lý Nghề
 * File: /danh-muc/nghe.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Nghe.php';
require_once __DIR__ . '/../models/Khoa.php';

// Kiểm tra đăng nhập và quyền Admin
if (!isLoggedIn() || !hasRole('Admin')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$ngheModel = new Nghe($db);
$khoaModel = new Khoa($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$nghe_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $nghe_id) {
    $result = $ngheModel->deleteNghe($nghe_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/nghe.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $data = [
        'khoa_id' => (int)($_POST['khoa_id'] ?? 0),
        'ma_nghe' => trim($_POST['ma_nghe'] ?? ''),
        'ten_nghe' => trim($_POST['ten_nghe'] ?? ''),
        'mo_ta' => trim($_POST['mo_ta'] ?? ''),
        'so_nam_dao_tao' => !empty($_POST['so_nam_dao_tao']) ? (int)$_POST['so_nam_dao_tao'] : null,
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['khoa_id'])) {
        $errors[] = 'Vui lòng chọn khoa';
    }
    if (empty($data['ma_nghe'])) {
        $errors[] = 'Mã nghề không được để trống';
    }
    if (empty($data['ten_nghe'])) {
        $errors[] = 'Tên nghề không được để trống';
    }
    
    // Kiểm tra mã nghề trùng trong khoa
    if ($ngheModel->checkMaNgheExists($data['khoa_id'], $data['ma_nghe'], $action === 'edit' ? $nghe_id : null)) {
        $errors[] = 'Mã nghề đã tồn tại trong khoa này';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $ngheModel->createNghe($data);
            $message = $success ? 'Thêm nghề thành công' : 'Lỗi khi thêm nghề';
        } else {
            $success = $ngheModel->updateNghe($nghe_id, $data);
            $message = $success ? 'Cập nhật nghề thành công' : 'Lỗi khi cập nhật nghề';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/nghe.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listNghe = $ngheModel->getAllWithKhoa();
}

// Lấy thông tin nghề để edit
if ($action === 'edit' && $nghe_id) {
    $nghe = $ngheModel->getByIdWithKhoa($nghe_id);
    if (!$nghe) {
        $_SESSION['error'] = 'Không tìm thấy nghề';
        header('Location: ' . BASE_URL . '/danh-muc/nghe.php');
        exit;
    }
}

// Lấy danh sách khoa
$listKhoa = $khoaModel->getActiveList();

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Nghề';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-briefcase"></i> Quản lý Nghề</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Nghề
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH NGHỀ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="15%">Khoa</th>
                                <th width="10%">Mã nghề</th>
                                <th width="25%">Tên nghề</th>
                                <th width="15%">Mô tả</th>
                                <th width="8%">Số năm ĐT</th>
                                <th width="8%">Thứ tự</th>
                                <th width="8%">Trạng thái</th>
                                <th width="11%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listNghe)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listNghe as $index => $nghe): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($nghe['ma_khoa']); ?>
                                        </span>
                                        <?php echo htmlspecialchars($nghe['ten_khoa']); ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($nghe['ma_nghe']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($nghe['ten_nghe']); ?></td>
                                    <td>
                                        <?php if ($nghe['mo_ta']): ?>
                                            <?php echo truncate(htmlspecialchars($nghe['mo_ta']), 50); ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($nghe['so_nam_dao_tao']): ?>
                                            <?php echo $nghe['so_nam_dao_tao']; ?> năm
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?php echo $nghe['thu_tu']; ?></td>
                                    <td class="text-center">
                                        <?php if ($nghe['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editNghe(<?php echo htmlspecialchars(json_encode($nghe)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteNghe(<?php echo $nghe['nghe_id']; ?>, '<?php echo htmlspecialchars($nghe['ten_nghe']); ?>')">
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

<!-- MODAL THÊM NGHỀ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Nghề Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
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
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã nghề <span class="text-danger">*</span></label>
                            <input type="text" name="ma_nghe" class="form-control" required 
                                   placeholder="Ví dụ: CNTT01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên nghề <span class="text-danger">*</span></label>
                            <input type="text" name="ten_nghe" class="form-control" required
                                   placeholder="Ví dụ: Lập trình viên">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số năm đào tạo</label>
                            <input type="number" name="so_nam_dao_tao" class="form-control" 
                                   min="1" max="10" placeholder="Ví dụ: 2">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3"
                                  placeholder="Mô tả về nghề..."></textarea>
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

<!-- MODAL SỬA NGHỀ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Nghề</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Khoa <span class="text-danger">*</span></label>
                            <select name="khoa_id" id="edit_khoa_id" class="form-select" required>
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($listKhoa as $khoa): ?>
                                <option value="<?php echo $khoa['khoa_id']; ?>">
                                    <?php echo htmlspecialchars($khoa['ten_khoa']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã nghề <span class="text-danger">*</span></label>
                            <input type="text" name="ma_nghe" id="edit_ma_nghe" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên nghề <span class="text-danger">*</span></label>
                            <input type="text" name="ten_nghe" id="edit_ten_nghe" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số năm đào tạo</label>
                            <input type="number" name="so_nam_dao_tao" id="edit_so_nam_dao_tao" class="form-control" min="1" max="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="edit_thu_tu" class="form-control" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" id="edit_mo_ta" class="form-control" rows="3"></textarea>
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
function editNghe(nghe) {
    document.getElementById('editForm').action = '?action=edit&id=' + nghe.nghe_id;
    document.getElementById('edit_khoa_id').value = nghe.khoa_id;
    document.getElementById('edit_ma_nghe').value = nghe.ma_nghe;
    document.getElementById('edit_ten_nghe').value = nghe.ten_nghe;
    document.getElementById('edit_mo_ta').value = nghe.mo_ta || '';
    document.getElementById('edit_so_nam_dao_tao').value = nghe.so_nam_dao_tao || '';
    document.getElementById('edit_thu_tu').value = nghe.thu_tu;
    document.getElementById('edit_is_active').checked = nghe.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteNghe(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa nghề "' + name + '"?\n\nLưu ý: Chỉ có thể xóa nghề không có lớp và môn học liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>