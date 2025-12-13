<?php
/**
 * View: Danh sách Lớp học (Admin)
 * File: views/admin/lop/index.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Quản lý Lớp học';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Lớp</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/lop" class="row g-3">
                <div class="col-md-3">
                    <label for="khoa_id" class="form-label">Khoa</label>
                    <select name="khoa_id" id="khoa_id" class="form-select">
                        <option value="">-- Tất cả khoa --</option>
                        <?php foreach ($khoa_list as $k): ?>
                            <option value="<?= $k['khoa_id'] ?>" 
                                <?= (isset($_GET['khoa_id']) && $_GET['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['ten_khoa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="nghe_id" class="form-label">Nghề</label>
                    <select name="nghe_id" id="nghe_id" class="form-select">
                        <option value="">-- Tất cả nghề --</option>
                        <?php foreach ($nghe_list as $n): ?>
                            <option value="<?= $n['nghe_id'] ?>" 
                                <?= (isset($_GET['nghe_id']) && $_GET['nghe_id'] == $n['nghe_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($n['ten_nghe']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="nien_khoa_id" class="form-label">Niên khóa</label>
                    <select name="nien_khoa_id" id="nien_khoa_id" class="form-select">
                        <option value="">-- Tất cả niên khóa --</option>
                        <?php foreach ($nien_khoa_list as $nk): ?>
                            <option value="<?= $nk['nien_khoa_id'] ?>" 
                                <?= (isset($_GET['nien_khoa_id']) && $_GET['nien_khoa_id'] == $nk['nien_khoa_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($nk['ten_nien_khoa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="is_active" class="form-label">Trạng thái</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '1') ? 'selected' : '' ?>>
                            Hoạt động
                        </option>
                        <option value="0" <?= (isset($_GET['is_active']) && $_GET['is_active'] == '0') ? 'selected' : '' ?>>
                            Ngừng
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="/admin/lop" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/lop/create" class="btn btn-success ms-auto">
                            <i class="fas fa-plus"></i> Thêm lớp
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách Lớp (<?= count($lop_list) ?> lớp)
        </div>
        <div class="card-body">
            <?php if (empty($lop_list)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có lớp nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">STT</th>
                                <th width="10%">Mã lớp</th>
                                <th width="18%">Tên lớp</th>
                                <th width="12%">Khoa</th>
                                <th width="15%">Nghề</th>
                                <th width="15%">Niên khóa</th>
                                <th width="8%">Cấp độ</th>
                                <th width="6%">Sĩ số</th>
                                <th width="8%">Trạng thái</th>
                                <th width="4%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lop_list as $index => $lop): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($lop['ma_lop']) ?></strong></td>
                                    <td><?= htmlspecialchars($lop['ten_lop']) ?></td>
                                    <td><small><?= htmlspecialchars($lop['ten_khoa']) ?></small></td>
                                    <td><small><?= htmlspecialchars($lop['ten_nghe']) ?></small></td>
                                    <td><small><?= htmlspecialchars($lop['ten_nien_khoa']) ?></small></td>
                                    <td><small><?= htmlspecialchars($lop['ten_cap_do']) ?></small></td>
                                    <td class="text-center"><?= $lop['si_so'] ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $lop['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $lop['lop_id'] ?>"
                                                data-status="<?= $lop['is_active'] ?>">
                                            <i class="fas fa-<?= $lop['is_active'] ? 'check' : 'times' ?>"></i>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/lop/edit/<?= $lop['lop_id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-delete" 
                                                    data-id="<?= $lop['lop_id'] ?>"
                                                    data-name="<?= htmlspecialchars($lop['ten_lop']) ?>"
                                                    title="Xóa">
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

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa lớp: <strong id="deleteName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-info-circle"></i> 
                    Không thể xóa nếu đã có Môn học hoặc Hợp đồng liên quan.
                </p>
            </div>
            <div class="modal-footer">
                <form method="POST" id="deleteForm">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Xóa
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle trạng thái
document.querySelectorAll('.btn-toggle-status').forEach(button => {
    button.addEventListener('click', async function() {
        if (!confirm('Bạn có chắc muốn thay đổi trạng thái?')) return;
        
        try {
            const response = await fetch(`/admin/lop/update-status/${this.dataset.id}`, {
                method: 'POST'
            });
            const result = await response.json();
            
            if (result.success) {
                this.className = `btn btn-sm btn-toggle-status ${result.new_status == 1 ? 'btn-success' : 'btn-danger'}`;
                this.innerHTML = `<i class="fas fa-${result.new_status == 1 ? 'check' : 'times'}"></i>`;
            } else {
                alert(result.message);
            }
        } catch (error) {
            alert('Có lỗi xảy ra!');
        }
    });
});

// Xóa
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        document.getElementById('deleteName').textContent = this.dataset.name;
        document.getElementById('deleteForm').action = `/admin/lop/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>