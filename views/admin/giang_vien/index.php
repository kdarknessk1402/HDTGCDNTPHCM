<?php
/**
 * View: Danh sách Giảng viên (Admin)
 * File: views/admin/giang_vien/index.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Quản lý Giảng viên';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Giảng viên</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-filter me-1"></i> Lọc</div>
        <div class="card-body">
            <form method="GET" action="/admin/giang-vien" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Khoa</label>
                    <select name="khoa_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($khoa_list as $k): ?>
                            <option value="<?= $k['khoa_id'] ?>" <?= (isset($_GET['khoa_id']) && $_GET['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['ten_khoa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trình độ</label>
                    <select name="trinh_do_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($trinh_do_list as $td): ?>
                            <option value="<?= $td['trinh_do_id'] ?>" <?= (isset($_GET['trinh_do_id']) && $_GET['trinh_do_id'] == $td['trinh_do_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($td['ten_trinh_do']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '1') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '0') ? 'selected' : '' ?>>Ngừng</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                    <a href="/admin/giang-vien" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    <a href="/admin/giang-vien/create" class="btn btn-success ms-auto"><i class="fas fa-plus"></i> Thêm giảng viên</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-table me-1"></i> Danh sách (<?= count($giang_vien_list) ?> giảng viên)</div>
        <div class="card-body">
            <?php if (empty($giang_vien_list)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Chưa có giảng viên nào.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="3%">STT</th>
                                <th width="8%">Mã GV</th>
                                <th width="15%">Họ tên</th>
                                <th width="10%">Khoa</th>
                                <th width="10%">Trình độ</th>
                                <th width="8%">Năm sinh</th>
                                <th width="10%">Điện thoại</th>
                                <th width="12%">Email</th>
                                <th width="8%">Trạng thái</th>
                                <th width="6%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($giang_vien_list as $index => $gv): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($gv['ma_giang_vien']) ?></strong></td>
                                    <td>
                                        <?= htmlspecialchars($gv['ten_giang_vien']) ?>
                                        <br><small class="text-muted"><?= $gv['gioi_tinh'] ?></small>
                                    </td>
                                    <td><small><?= htmlspecialchars($gv['ten_khoa']) ?></small></td>
                                    <td><small><?= htmlspecialchars($gv['ten_trinh_do'] ?? 'Chưa có') ?></small></td>
                                    <td class="text-center"><?= $gv['nam_sinh'] ?></td>
                                    <td><?= htmlspecialchars($gv['so_dien_thoai']) ?></td>
                                    <td><small><?= htmlspecialchars($gv['email']) ?></small></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $gv['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $gv['giang_vien_id'] ?>" data-status="<?= $gv['is_active'] ?>">
                                            <i class="fas fa-<?= $gv['is_active'] ? 'check' : 'times' ?>"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/admin/giang-vien/edit/<?= $gv['giang_vien_id'] ?>" class="btn btn-warning" title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger btn-delete" data-id="<?= $gv['giang_vien_id'] ?>" 
                                                    data-name="<?= htmlspecialchars($gv['ten_giang_vien']) ?>" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
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
                <p>Xóa giảng viên: <strong id="deleteName"></strong>?</p>
                <p class="text-danger"><i class="fas fa-info-circle"></i> Không thể xóa nếu có Hợp đồng.</p>
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
            const res = await fetch(`/admin/giang-vien/update-status/${this.dataset.id}`, {method: 'POST'});
            const data = await res.json();
            if (data.success) {
                this.className = `btn btn-sm btn-toggle-status ${data.new_status == 1 ? 'btn-success' : 'btn-danger'}`;
                this.innerHTML = `<i class="fas fa-${data.new_status == 1 ? 'check' : 'times'}"></i>`;
            }
        } catch (e) {}
    });
});

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('deleteName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = `/admin/giang-vien/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>