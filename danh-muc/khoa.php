<?php
/**
 * Quản lý Khoa
 * File: /danh-muc/khoa.php
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Khoa.php';
require_once __DIR__ . '/../models/User.php';

// Kiểm tra đăng nhập và quyền Admin
if (!isLoggedIn() || !hasRole('Admin')) {
    $_SESSION['error'] = 'Bạn không có quyền truy cập chức năng này';
    header('Location: ' . BASE_URL . '/');
    exit;
}

$db = Database::getInstance()->getConnection();
$khoaModel = new Khoa($db);
$userModel = new User($db);

// Xử lý các action
$action = $_GET['action'] ?? 'list';
$khoa_id = $_GET['id'] ?? null;

// XỬ LÝ XÓA
if ($action === 'delete' && $khoa_id) {
    $result = $khoaModel->deleteKhoa($khoa_id);
    $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
    header('Location: ' . BASE_URL . '/danh-muc/khoa.php');
    exit;
}

// XỬ LÝ THÊM/SỬA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['add', 'edit'])) {
    $data = [
        'ma_khoa' => trim($_POST['ma_khoa'] ?? ''),
        'ten_khoa' => trim($_POST['ten_khoa'] ?? ''),
        'mo_ta' => trim($_POST['mo_ta'] ?? ''),
        'truong_khoa_id' => !empty($_POST['truong_khoa_id']) ? (int)$_POST['truong_khoa_id'] : null,
        'so_dien_thoai' => trim($_POST['so_dien_thoai'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'thu_tu' => (int)($_POST['thu_tu'] ?? 0),
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];
    
    // Validate
    $errors = [];
    if (empty($data['ma_khoa'])) {
        $errors[] = 'Mã khoa không được để trống';
    }
    if (empty($data['ten_khoa'])) {
        $errors[] = 'Tên khoa không được để trống';
    }
    
    // Kiểm tra mã khoa trùng
    if ($khoaModel->checkMaKhoaExists($data['ma_khoa'], $action === 'edit' ? $khoa_id : null)) {
        $errors[] = 'Mã khoa đã tồn tại';
    }
    
    if (empty($errors)) {
        if ($action === 'add') {
            $success = $khoaModel->createKhoa($data);
            $message = $success ? 'Thêm khoa thành công' : 'Lỗi khi thêm khoa';
        } else {
            $success = $khoaModel->updateKhoa($khoa_id, $data);
            $message = $success ? 'Cập nhật khoa thành công' : 'Lỗi khi cập nhật khoa';
        }
        
        $_SESSION[$success ? 'success' : 'error'] = $message;
        header('Location: ' . BASE_URL . '/danh-muc/khoa.php');
        exit;
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Lấy danh sách
if ($action === 'list') {
    $listKhoa = $khoaModel->getAllWithTruongKhoa();
}

// Lấy thông tin khoa để edit
if ($action === 'edit' && $khoa_id) {
    $khoa = $khoaModel->getByIdWithTruongKhoa($khoa_id);
    if (!$khoa) {
        $_SESSION['error'] = 'Không tìm thấy khoa';
        header('Location: ' . BASE_URL . '/danh-muc/khoa.php');
        exit;
    }
}

// Lấy danh sách trưởng khoa (role Truong_Khoa)
$listTruongKhoa = $userModel->getUsersByRole(3); // role_id = 3 là Trưởng Khoa

$currentUser = getCurrentUser();
$pageTitle = 'Quản lý Khoa';

// Include header
include __DIR__ . '/../views/layouts/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-building"></i> Quản lý Khoa</h2>
            <?php if ($action === 'list'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="bi bi-plus-circle"></i> Thêm Khoa
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if ($action === 'list'): ?>
<!-- DANH SÁCH KHOA -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-primary">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã khoa</th>
                                <th width="20%">Tên khoa</th>
                                <th width="15%">Trưởng khoa</th>
                                <th width="12%">SĐT</th>
                                <th width="15%">Email</th>
                                <th width="8%">Thứ tự</th>
                                <th width="8%">Trạng thái</th>
                                <th width="12%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($listKhoa)): ?>
                            <tr>
                                <td colspan="9" class="text-center">Chưa có dữ liệu</td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($listKhoa as $index => $khoa): ?>
                                <tr>
                                    <td class="text-center"><?php echo $index + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($khoa['ma_khoa']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($khoa['ten_khoa']); ?></td>
                                    <td>
                                        <?php if ($khoa['ten_truong_khoa']): ?>
                                            <i class="bi bi-person-badge"></i>
                                            <?php echo htmlspecialchars($khoa['ten_truong_khoa']); ?>
                                        <?php else: ?>
                                            <span class="text-muted">Chưa có</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($khoa['so_dien_thoai'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($khoa['email'] ?? ''); ?></td>
                                    <td class="text-center"><?php echo $khoa['thu_tu']; ?></td>
                                    <td class="text-center">
                                        <?php if ($khoa['is_active']): ?>
                                            <span class="badge bg-success">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Ngừng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="editKhoa(<?php echo htmlspecialchars(json_encode($khoa)); ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="deleteKhoa(<?php echo $khoa['khoa_id']; ?>, '<?php echo htmlspecialchars($khoa['ten_khoa']); ?>')">
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

<!-- MODAL THÊM KHOA -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="?action=add">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Thêm Khoa Mới</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã khoa <span class="text-danger">*</span></label>
                            <input type="text" name="ma_khoa" class="form-control" required 
                                   placeholder="Ví dụ: CNTT">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khoa <span class="text-danger">*</span></label>
                            <input type="text" name="ten_khoa" class="form-control" required
                                   placeholder="Ví dụ: Khoa Công nghệ Thông tin">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trưởng khoa</label>
                            <select name="truong_khoa_id" class="form-select">
                                <option value="">-- Chọn trưởng khoa --</option>
                                <?php foreach ($listTruongKhoa as $tk): ?>
                                <option value="<?php echo $tk['user_id']; ?>">
                                    <?php echo htmlspecialchars($tk['full_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="so_dien_thoai" class="form-control"
                                   placeholder="Ví dụ: 0901234567">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   placeholder="Ví dụ: cntt@cdnhcm.edu.vn">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Thứ tự hiển thị</label>
                            <input type="number" name="thu_tu" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3"
                                  placeholder="Mô tả về khoa..."></textarea>
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

<!-- MODAL SỬA KHOA -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" id="editForm">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-pencil"></i> Sửa Khoa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mã khoa <span class="text-danger">*</span></label>
                            <input type="text" name="ma_khoa" id="edit_ma_khoa" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khoa <span class="text-danger">*</span></label>
                            <input type="text" name="ten_khoa" id="edit_ten_khoa" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Trưởng khoa</label>
                            <select name="truong_khoa_id" id="edit_truong_khoa_id" class="form-select">
                                <option value="">-- Chọn trưởng khoa --</option>
                                <?php foreach ($listTruongKhoa as $tk): ?>
                                <option value="<?php echo $tk['user_id']; ?>">
                                    <?php echo htmlspecialchars($tk['full_name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="so_dien_thoai" id="edit_so_dien_thoai" class="form-control">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
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
function editKhoa(khoa) {
    document.getElementById('editForm').action = '?action=edit&id=' + khoa.khoa_id;
    document.getElementById('edit_ma_khoa').value = khoa.ma_khoa;
    document.getElementById('edit_ten_khoa').value = khoa.ten_khoa;
    document.getElementById('edit_mo_ta').value = khoa.mo_ta || '';
    document.getElementById('edit_truong_khoa_id').value = khoa.truong_khoa_id || '';
    document.getElementById('edit_so_dien_thoai').value = khoa.so_dien_thoai || '';
    document.getElementById('edit_email').value = khoa.email || '';
    document.getElementById('edit_thu_tu').value = khoa.thu_tu;
    document.getElementById('edit_is_active').checked = khoa.is_active == 1;
    
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function deleteKhoa(id, name) {
    if (confirm('Bạn có chắc chắn muốn xóa khoa "' + name + '"?\n\nLưu ý: Chỉ có thể xóa khoa không có nghề và giảng viên liên quan.')) {
        window.location.href = '?action=delete&id=' + id;
    }
}
</script>

<?php endif; ?>

<?php
// Include footer
include __DIR__ . '/../views/layouts/footer.php';
?>