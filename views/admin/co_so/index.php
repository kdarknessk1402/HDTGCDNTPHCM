<?php
/**
 * View: Danh sách Cơ sở đào tạo (Admin)
 * File: views/admin/co_so/index.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Quản lý Cơ sở Đào tạo';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Cơ sở</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <!-- Filter -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/co-so" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-8">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="/admin/co-so" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/co-so/create" class="btn btn-success ms-auto">
                            <i class="fas fa-plus"></i> Thêm cơ sở
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
            Danh sách Cơ sở (<?= count($co_so_list) ?> cơ sở)
        </div>
        <div class="card-body">
            <?php if (empty($co_so_list)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có cơ sở nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã cơ sở</th>
                                <th width="20%">Tên cơ sở</th>
                                <th width="20%">Địa chỉ</th>
                                <th width="10%">Điện thoại</th>
                                <th width="12%">Người phụ trách</th>
                                <th width="6%">Thứ tự</th>
                                <th width="10%">Trạng thái</th>
                                <th width="7%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($co_so_list as $index => $cs): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($cs['ma_co_so']) ?></strong></td>
                                    <td><?= htmlspecialchars($cs['ten_co_so']) ?></td>
                                    <td><small class="text-muted"><?= htmlspecialchars($cs['dia_chi']) ?></small></td>
                                    <td><?= htmlspecialchars($cs['so_dien_thoai']) ?></td>
                                    <td><?= htmlspecialchars($cs['nguoi_phu_trach']) ?></td>
                                    <td class="text-center"><?= $cs['thu_tu'] ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $cs['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $cs['co_so_id'] ?>"
                                                data-status="<?= $cs['is_active'] ?>">
                                            <i class="fas fa-<?= $cs['is_active'] ? 'check' : 'times' ?>"></i>
                                            <?= $cs['is_active'] ? 'Hoạt động' : 'Ngừng' ?>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/co-so/edit/<?= $cs['co_so_id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-delete" 
                                                    data-id="<?= $cs['co_so_id'] ?>"
                                                    data-name="<?= htmlspecialchars($cs['ten_co_so']) ?>"
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
                <p>Bạn có chắc chắn muốn xóa cơ sở: <strong id="deleteName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-info-circle"></i> 
                    Không thể xóa nếu đã có Đơn giá hoặc Hợp đồng sử dụng cơ sở này.
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
            const response = await fetch(`/admin/co-so/update-status/${this.dataset.id}`, {
                method: 'POST'
            });
            const result = await response.json();
            
            if (result.success) {
                this.className = `btn btn-sm btn-toggle-status ${result.new_status == 1 ? 'btn-success' : 'btn-danger'}`;
                this.innerHTML = `<i class="fas fa-${result.new_status == 1 ? 'check' : 'times'}"></i> ${result.status_text}`;
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
        document.getElementById('deleteForm').action = `/admin/co-so/delete/${this.dataset.id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>