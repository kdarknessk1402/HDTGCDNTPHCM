<?php
/**
 * View: Danh sách Môn học (Admin)
 * File: views/admin/mon_hoc/index.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Quản lý Môn học';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Môn học</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-filter me-1"></i> Lọc</div>
        <div class="card-body">
            <form method="GET" action="/admin/mon-hoc" class="row g-3">
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label class="form-label">Nghề</label>
                    <select name="nghe_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($nghe_list as $n): ?>
                            <option value="<?= $n['nghe_id'] ?>" <?= (isset($_GET['nghe_id']) && $_GET['nghe_id'] == $n['nghe_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($n['ten_nghe']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lớp</label>
                    <select name="lop_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($lop_list as $l): ?>
                            <option value="<?= $l['lop_id'] ?>" <?= (isset($_GET['lop_id']) && $_GET['lop_id'] == $l['lop_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['ten_lop']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '1') ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '0') ? 'selected' : '' ?>>Ngừng</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
                    <a href="/admin/mon-hoc" class="btn btn-secondary"><i class="fas fa-redo"></i> Reset</a>
                    <a href="/admin/mon-hoc/create" class="btn btn-success ms-auto"><i class="fas fa-plus"></i> Thêm môn học</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><i class="fas fa-table me-1"></i> Danh sách (<?= count($mon_hoc_list) ?> môn học)</div>
        <div class="card-body">
            <?php if (empty($mon_hoc_list)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> Chưa có môn học nào.</div>
            <?php else: ?>
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th width="4%">STT</th>
                            <th width="10%">Mã MH</th>
                            <th width="20%">Tên môn học</th>
                            <th width="15%">Lớp</th>
                            <th width="10%">Nghề</th>
                            <th width="8%">Tín chỉ</th>
                            <th width="8%">Số tiết</th>
                            <th width="8%">Trạng thái</th>
                            <th width="7%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mon_hoc_list as $index => $mh): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><strong><?= htmlspecialchars($mh['ma_mon_hoc']) ?></strong></td>
                                <td><?= htmlspecialchars($mh['ten_mon_hoc']) ?></td>
                                <td><small><?= htmlspecialchars($mh['ten_lop']) ?></small></td>
                                <td><small><?= htmlspecialchars($mh['ten_nghe']) ?></small></td>
                                <td class="text-center"><?= $mh['so_tin_chi'] ?></td>
                                <td class="text-center"><?= $mh['tong_so_tiet'] ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-toggle-status <?= $mh['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                            data-id="<?= $mh['mon_hoc_id'] ?>" data-status="<?= $mh['is_active'] ?>">
                                        <i class="fas fa-<?= $mh['is_active'] ? 'check' : 'times' ?>"></i>
                                    </button>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="/admin/mon-hoc/edit/<?= $mh['mon_hoc_id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-danger btn-delete" data-id="<?= $mh['mon_hoc_id'] ?>" data-name="<?= htmlspecialchars($mh['ten_mon_hoc']) ?>"><i class="fas fa-trash"></i></button>
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
                <p>Xóa môn học: <strong id="deleteName"></strong>?</p>
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
            const res = await fetch(`/admin/mon-hoc/update-status/${this.dataset.id}`, {method: 'POST'});
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
        document.getElementById('deleteForm').action = `/admin/mon-hoc/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>