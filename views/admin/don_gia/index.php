<?php
/**
 * View: Danh sách Đơn giá (Admin)
 * File: views/admin/don_gia/index.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Quản lý Đơn giá Giờ dạy';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Đơn giá</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/don-gia" class="row g-3">
                <div class="col-md-4">
                    <label for="co_so_id" class="form-label">Cơ sở</label>
                    <select name="co_so_id" id="co_so_id" class="form-select">
                        <option value="">-- Tất cả cơ sở --</option>
                        <?php foreach ($co_so_list as $cs): ?>
                            <option value="<?= $cs['co_so_id'] ?>" 
                                <?= (isset($_GET['co_so_id']) && $_GET['co_so_id'] == $cs['co_so_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cs['ten_co_so']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="trinh_do_id" class="form-label">Trình độ</label>
                    <select name="trinh_do_id" id="trinh_do_id" class="form-select">
                        <option value="">-- Tất cả trình độ --</option>
                        <?php foreach ($trinh_do_list as $td): ?>
                            <option value="<?= $td['trinh_do_id'] ?>" 
                                <?= (isset($_GET['trinh_do_id']) && $_GET['trinh_do_id'] == $td['trinh_do_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($td['ten_trinh_do']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
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

                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                        <a href="/admin/don-gia" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                        <a href="/admin/don-gia/create" class="btn btn-success ms-auto">
                            <i class="fas fa-plus"></i> Thêm đơn giá
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách đơn giá -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách Đơn giá (<?= count($don_gia_list) ?> bản ghi)
        </div>
        <div class="card-body">
            <?php if (empty($don_gia_list)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Chưa có đơn giá nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="donGiaTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="15%">Cơ sở</th>
                                <th width="15%">Trình độ</th>
                                <th width="12%">Đơn giá (VNĐ)</th>
                                <th width="12%">Ngày áp dụng</th>
                                <th width="12%">Ngày kết thúc</th>
                                <th width="10%">Trạng thái</th>
                                <th width="10%">Người tạo</th>
                                <th width="9%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($don_gia_list as $index => $dg): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($dg['ma_co_so']) ?>
                                        </span>
                                        <br>
                                        <small><?= htmlspecialchars($dg['ten_co_so']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($dg['ma_trinh_do']) ?>
                                        </span>
                                        <?= htmlspecialchars($dg['ten_trinh_do']) ?>
                                    </td>
                                    <td class="text-end">
                                        <strong class="text-primary">
                                            <?= number_format($dg['don_gia'], 0, ',', '.') ?>
                                        </strong>
                                    </td>
                                    <td class="text-center">
                                        <?= date('d/m/Y', strtotime($dg['ngay_ap_dung'])) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($dg['ngay_ket_thuc']): ?>
                                            <?= date('d/m/Y', strtotime($dg['ngay_ket_thuc'])) ?>
                                        <?php else: ?>
                                            <span class="badge bg-success">Không giới hạn</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $dg['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $dg['don_gia_id'] ?>"
                                                data-status="<?= $dg['is_active'] ?>">
                                            <i class="fas fa-<?= $dg['is_active'] ? 'check' : 'times' ?>"></i>
                                            <?= $dg['is_active'] ? 'Hoạt động' : 'Ngừng' ?>
                                        </button>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($dg['created_by_name'] ?? 'N/A') ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/don-gia/edit/<?= $dg['don_gia_id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-delete" 
                                                    data-id="<?= $dg['don_gia_id'] ?>"
                                                    data-name="<?= htmlspecialchars($dg['ten_co_so']) ?> - <?= htmlspecialchars($dg['ten_trinh_do']) ?>"
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
                <p>Bạn có chắc chắn muốn xóa đơn giá: <strong id="deleteName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-info-circle"></i> 
                    Lưu ý: Không thể xóa nếu đơn giá đã được sử dụng trong hợp đồng.
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
        const donGiaId = this.dataset.id;
        
        if (!confirm('Bạn có chắc muốn thay đổi trạng thái?')) return;
        
        try {
            const response = await fetch(`/admin/don-gia/update-status/${donGiaId}`, {
                method: 'POST'
            });
            
            const result = await response.json();
            
            if (result.success) {
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

// Xóa đơn giá
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        const donGiaId = this.dataset.id;
        const donGiaName = this.dataset.name;
        
        document.getElementById('deleteName').textContent = donGiaName;
        document.getElementById('deleteForm').action = `/admin/don-gia/delete/${donGiaId}`;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

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