<?php
/**
 * View: Danh sách Nghề (Admin)
 * File: views/admin/nghe/index.php
 */

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Quản lý Nghề';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Nghề</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/nghe" class="row g-3">
                <div class="col-md-4">
                    <label for="khoa_id" class="form-label">Khoa</label>
                    <select name="khoa_id" id="khoa_id" class="form-select">
                        <option value="">-- Tất cả khoa --</option>
                        <?php foreach ($khoa_list as $khoa): ?>
                            <option value="<?= $khoa['khoa_id'] ?>" 
                                <?= (isset($_GET['khoa_id']) && $_GET['khoa_id'] == $khoa['khoa_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($khoa['ten_khoa']) ?>
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

                <div class="col-md-5">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="/admin/nghe" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/nghe/create" class="btn btn-success ms-auto">
                            <i class="fas fa-plus"></i> Thêm nghề mới
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách nghề -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách Nghề (<?= count($nghe_list) ?> nghề)
        </div>
        <div class="card-body">
            <?php if (empty($nghe_list)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Không có nghề nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="ngheTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã nghề</th>
                                <th width="20%">Tên nghề</th>
                                <th width="15%">Khoa</th>
                                <th width="10%">Số năm ĐT</th>
                                <th width="10%">Thứ tự</th>
                                <th width="10%">Trạng thái</th>
                                <th width="10%">Người tạo</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nghe_list as $index => $nghe): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($nghe['ma_nghe']) ?></strong></td>
                                    <td><?= htmlspecialchars($nghe['ten_nghe']) ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($nghe['ma_khoa']) ?>
                                        </span>
                                        <?= htmlspecialchars($nghe['ten_khoa']) ?>
                                    </td>
                                    <td class="text-center"><?= $nghe['so_nam_dao_tao'] ?> năm</td>
                                    <td class="text-center"><?= $nghe['thu_tu'] ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $nghe['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $nghe['nghe_id'] ?>"
                                                data-status="<?= $nghe['is_active'] ?>">
                                            <i class="fas fa-<?= $nghe['is_active'] ? 'check' : 'times' ?>"></i>
                                            <?= $nghe['is_active'] ? 'Hoạt động' : 'Ngừng' ?>
                                        </button>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($nghe['created_by_name'] ?? 'N/A') ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/nghe/edit/<?= $nghe['nghe_id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-delete" 
                                                    data-id="<?= $nghe['nghe_id'] ?>"
                                                    data-name="<?= htmlspecialchars($nghe['ten_nghe']) ?>"
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
                <p>Bạn có chắc chắn muốn xóa nghề: <strong id="deleteName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-info-circle"></i> 
                    Lưu ý: Không thể xóa nếu nghề đã có dữ liệu liên quan (Niên khóa, Lớp, Môn học, Hợp đồng).
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
        const ngheId = this.dataset.id;
        const currentStatus = this.dataset.status;
        
        if (!confirm('Bạn có chắc muốn thay đổi trạng thái?')) return;
        
        try {
            const response = await fetch(`/admin/nghe/update-status/${ngheId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Update button
                this.dataset.status = result.new_status;
                this.className = `btn btn-sm btn-toggle-status ${result.new_status == 1 ? 'btn-success' : 'btn-danger'}`;
                this.innerHTML = `<i class="fas fa-${result.new_status == 1 ? 'check' : 'times'}"></i> ${result.status_text}`;
                
                showToast('success', result.message);
            } else {
                showToast('error', result.message);
            }
        } catch (error) {
            showToast('error', 'Có lỗi xảy ra!');
        }
    });
});

// Xóa nghề
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        const ngheId = this.dataset.id;
        const ngheName = this.dataset.name;
        
        document.getElementById('deleteName').textContent = ngheName;
        document.getElementById('deleteForm').action = `/admin/nghe/delete/${ngheId}`;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

// Toast notification helper
function showToast(type, message) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
    toastContainer.innerHTML = toastHtml;
    document.body.appendChild(toastContainer);
    
    const toast = new bootstrap.Toast(toastContainer.querySelector('.toast'));
    toast.show();
    
    setTimeout(() => toastContainer.remove(), 3000);
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>