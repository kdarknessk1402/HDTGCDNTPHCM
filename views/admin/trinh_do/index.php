<?php
/**
 * FILE 1: views/admin/trinh_do/index.php
 * Danh sách Trình độ
 */
?>
<!-- Copy vào file: views/admin/trinh_do/index.php -->

<?php
if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Quản lý Trình độ Chuyên môn';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Trình độ</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i> Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/trinh-do" class="row g-3">
                <div class="col-md-4">
                    <label for="is_active" class="form-label">Trạng thái</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '1') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '0') ? 'selected' : '' ?>>Ngừng</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                        <a href="/admin/trinh-do" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                        <a href="/admin/trinh-do/create" class="btn btn-success ms-auto"><i class="fas fa-plus"></i> Thêm trình độ</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i> Danh sách Trình độ (<?= count($trinh_do_list) ?> trình độ)
        </div>
        <div class="card-body">
            <?php if (empty($trinh_do_list)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Chưa có trình độ nào.</div>
            <?php else: ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="15%">Mã trình độ</th>
                            <th width="25%">Tên trình độ</th>
                            <th width="30%">Mô tả</th>
                            <th width="8%">Thứ tự</th>
                            <th width="10%">Trạng thái</th>
                            <th width="7%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($trinh_do_list as $index => $td): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($td['ma_trinh_do']) ?></strong></td>
                                <td><?= htmlspecialchars($td['ten_trinh_do']) ?></td>
                                <td><small class="text-muted"><?= htmlspecialchars($td['mo_ta']) ?></small></td>
                                <td class="text-center"><?= $td['thu_tu'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-toggle-status <?= $td['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                            data-id="<?= $td['trinh_do_id'] ?>" data-status="<?= $td['is_active'] ?>">
                                        <i class="fas fa-<?= $td['is_active'] ? 'check' : 'times' ?>"></i>
                                        <?= $td['is_active'] ? 'Hoạt động' : 'Ngừng' ?>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/trinh-do/edit/<?= $td['trinh_do_id'] ?>" class="btn btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-delete" 
                                                data-id="<?= $td['trinh_do_id'] ?>" data-name="<?= htmlspecialchars($td['ten_trinh_do']) ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa trình độ: <strong id="deleteName"></strong>?</p>
                <p class="text-danger"><i class="fas fa-info-circle"></i> Không thể xóa nếu đã có Giảng viên hoặc Đơn giá.</p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Xóa</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.btn-toggle-status').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Thay đổi trạng thái?')) return;
        try {
            const res = await fetch(`/admin/trinh-do/update-status/${this.dataset.id}`, {method: 'POST'});
            const data = await res.json();
            if (data.success) {
                this.className = `btn btn-sm btn-toggle-status ${data.new_status == 1 ? 'btn-success' : 'btn-danger'}`;
                this.innerHTML = `<i class="fas fa-${data.new_status == 1 ? 'check' : 'times'}"></i> ${data.status_text}`;
            }
        } catch (e) {}
    });
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = `/admin/trinh-do/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

<?php die("\n\n=== END FILE 1 ===\n\n"); ?>


<?php
/**
 * FILE 2: views/admin/trinh_do/create.php
 * Form thêm Trình độ
 */
?>
<!-- Copy vào file: views/admin/trinh_do/create.php -->

<?php
if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Thêm Trình độ Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/trinh-do">Quản lý Trình độ</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-plus-circle me-1"></i> Thông tin trình độ mới</div>
                <div class="card-body">
                    <form method="POST" action="/admin/trinh-do/create">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_trinh_do" class="form-label">Mã trình độ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ma_trinh_do" name="ma_trinh_do" 
                                           value="<?= htmlspecialchars($_POST['ma_trinh_do'] ?? '') ?>" maxlength="20" required 
                                           placeholder="VD: TS">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_trinh_do" class="form-label">Tên trình độ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ten_trinh_do" name="ten_trinh_do" 
                                           value="<?= htmlspecialchars($_POST['ten_trinh_do'] ?? '') ?>" maxlength="50" required 
                                           placeholder="VD: Tiến sĩ">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3"><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="thu_tu" class="form-label">Thứ tự hiển thị <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="thu_tu" name="thu_tu" 
                                   value="<?= htmlspecialchars($_POST['thu_tu'] ?? '0') ?>" min="0" required>
                            <div class="form-text">Thứ tự càng nhỏ hiển thị càng trước</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active"><strong>Kích hoạt</strong></label>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/trinh-do" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ma_trinh_do').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>

<?php die("\n\n=== END FILE 2 ===\n\n"); ?>


<?php
/**
 * FILE 3: views/admin/trinh_do/edit.php
 * Form sửa Trình độ
 */
?>
<!-- Copy vào file: views/admin/trinh_do/edit.php -->

<?php
if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Sửa Trình độ';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/trinh-do">Quản lý Trình độ</a></li>
        <li class="breadcrumb-item active">Sửa: <?= htmlspecialchars($trinh_do['ten_trinh_do']) ?></li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header bg-warning"><i class="fas fa-edit me-1"></i> Cập nhật thông tin</div>
                <div class="card-body">
                    <form method="POST" action="/admin/trinh-do/edit/<?= $trinh_do['trinh_do_id'] ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_trinh_do" class="form-label">Mã trình độ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ma_trinh_do" name="ma_trinh_do" 
                                           value="<?= htmlspecialchars($_POST['ma_trinh_do'] ?? $trinh_do['ma_trinh_do']) ?>" 
                                           maxlength="20" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_trinh_do" class="form-label">Tên trình độ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ten_trinh_do" name="ten_trinh_do" 
                                           value="<?= htmlspecialchars($_POST['ten_trinh_do'] ?? $trinh_do['ten_trinh_do']) ?>" 
                                           maxlength="50" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" id="mo_ta" name="mo_ta" rows="3"><?= htmlspecialchars($_POST['mo_ta'] ?? $trinh_do['mo_ta']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="thu_tu" class="form-label">Thứ tự hiển thị <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="thu_tu" name="thu_tu" 
                                   value="<?= htmlspecialchars($_POST['thu_tu'] ?? $trinh_do['thu_tu']) ?>" min="0" required>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <?php $is_active = $_POST['is_active'] ?? $trinh_do['is_active']; ?>
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= $is_active ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active"><strong>Kích hoạt</strong></label>
                            </div>
                        </div>
                        
                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/trinh-do" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('ma_trinh_do').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>