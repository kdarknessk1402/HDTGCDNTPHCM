<?php
/**
 * Quản lý Cơ sở
 * File: /danh-muc/co-so.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/CoSo.php';

// Kiểm tra đăng nhập và quyền Admin
if (!isLoggedIn() || !hasRole('Admin')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$coSoModel = new CoSo($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$co_so_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $co_so_id) {
    $result = $coSoModel->deleteCoSo($co_so_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/co-so.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $data = [
        'ma_co_so' => trim($_POST['ma_co_so'] ?? ''),
        'ten_co_so' => trim($_POST['ten_co_so'] ?? ''),
        'dia_chi' => trim($_POST['dia_chi'] ?? ''),
        'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'nguoi_phu_trach' => trim($_POST['nguoi_phu_trach'] ?? ''),
        'mo_ta' => trim($_POST['mo_ta'] ?? ''),
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['ma_co_so'])) {
        $errors[] = 'Mã cơ sở không được để trống';
    }
    if (empty($data['ten_co_so'])) {
        $errors[] = 'Tên cơ sở không được để trống';
    }
    
    // Kiểm tra email
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors[] = 'Email không đúng định dạng';
    }
    
    // Kiểm tra mã cơ sở trùng
    if ($coSoModel->checkMaCoSoExists($data['ma_co_so'], $action === 'edit' ? $co_so_id : null)) {
        $errors[] = 'Mã cơ sở đã tồn tại';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $coSoModel->createCoSo($data);
            $message = $success ? 'Thêm cơ sở thành công' : 'Lỗi khi thêm cơ sở';
        } else {
            $success = $coSoModel->updateCoSo($co_so_id, $data);
            $message = $success ? 'Cập nhật cơ sở thành công' : 'Lỗi khi cập nhật cơ sở';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/co-so.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listCoSo = $coSoModel->getAllCoSo();
}

// Lấy thông tin cơ sở để edit
if ($action === 'edit' && $co_so_id) {
    $coSo = $coSoModel->getCoSoById($co_so_id);
    if (!$coSo) {
        $_SESSION['error'] = 'Không tìm thấy cơ sở';
        header('Location: ' . BASE_URL . '/danh-muc/co-so.php');
        exit;
    }
}

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Cơ sở';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-building"></i> Quản lý Cơ sở</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Cơ sở
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH CƠ SỞ -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã cơ sở</th>
                                <th width="20%">Tên cơ sở</th>
                                <th width="20%">Địa chỉ</th>
                                <th width="10%">Điện thoại</th>
                                <th width="12%">Email</th>
                                <th width="12%">Người phụ trách</th>
                                <th width="8%">Trạng thái</th>
                                <th width="8%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listCoSo)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listCoSo as $index => $cs): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($cs['ma_co_so']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($cs['ten_co_so']); ?></td>
                                    <td>
                                        <?php if ($cs['dia_chi']): ?>
                                            <small><?php echo htmlspecialchars($cs['dia_chi']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($cs['so_dien_thoai']) ?: '-'; ?></td>
                                    <td>
                                        <?php if ($cs['email']): ?>
                                            <small><?php echo htmlspecialchars($cs['email']); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($cs['nguoi_phu_trach']) ?: '-'; ?></td>
                                    <td class="text-center">
                                        <?php if ($cs['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editCoSo(<?php echo htmlspecialchars(json_encode($cs)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteCoSo(<?php echo $cs['co_so_id']; ?>, '<?php echo htmlspecialchars($cs['ten_co_so']); ?>')">
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

<!-- MODAL THÊM CƠ SỞ -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Cơ sở Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã cơ sở <span class="text-danger">*</span></label>
                            <input type="text" name="ma_co_so" class="form-control" required 
                                   placeholder="Ví dụ: CS01">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
                            <input type="text" name="ten_co_so" class="form-control" required
                                   placeholder="Ví dụ: Cơ sở Quận 1">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="dia_chi" class="form-control"
                                   placeholder="Số nhà, đường, phường, quận">
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
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Người phụ trách</label>
                            <input type="text" name="nguoi_phu_trach" class="form-control"
                                   placeholder="Họ tên người phụ trách">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="2"
                                  placeholder="Mô tả về cơ sở..."></textarea>
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

<!-- MODAL SỬA CƠ SỞ -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Cơ sở</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã cơ sở <span class="text-danger">*</span></label>
                            <input type="text" name="ma_co_so" id="edit_ma_co_so" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên cơ sở <span class="text-danger">*</span></label>
                            <input type="text" name="ten_co_so" id="edit_ten_co_so" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="dia_chi" id="edit_dia_chi" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="so_dien_thoai" id="edit_so_dien_thoai" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Người phụ trách</label>
                            <input type="text" name="nguoi_phu_trach" id="edit_nguoi_phu_trach" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" id="edit_thu_tu" class="form-control" min="0">
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
function editCoSo(cs) {
    document.getElementById('editForm').action = '?action=edit&id=' + cs.co_so_id;
    document.getElementById('edit_ma_co_so').value = cs.ma_co_so;
    document.getElementById('edit_ten_co_so').value = cs.ten_co_so;
    document.getElementById('edit_dia_chi').value = cs.dia_chi || '';
    document.getElementById('edit_so_dien_thoai').value = cs.so_dien_thoai || '';
    document.getElementById('edit_email').value = cs.email || '';
    document.getElementById('edit_nguoi_phu_trach').value = cs.nguoi_phu_trach || '';
    document.getElementById('edit_mo_ta').value = cs.mo_ta || '';
    document.getElementById('edit_thu_tu').value = cs.thu_tu;
    document.getElementById('edit_is_active').checked = cs.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteCoSo(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa cơ sở "' + name + '"?\n\nLưu ý: Chỉ có thể xóa cơ sở không có hợp đồng và đơn giá liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>