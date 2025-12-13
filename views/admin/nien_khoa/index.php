<?php
/**
 * View: Danh sách Niên khóa (Admin)
 * File: views/admin/nien_khoa/index.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Quản lý Niên khóa';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active">Quản lý Niên khóa</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Lọc và tìm kiếm
        </div>
        <div class="card-body">
            <form method="GET" action="/admin/nien-khoa" class="row g-3" id="filterForm">
                <div class="col-md-3">
                    <label for="khoa_id_filter" class="form-label">Khoa</label>
                    <select name="khoa_id_filter" id="khoa_id_filter" class="form-select">
                        <option value="">-- Tất cả khoa --</option>
                        <?php foreach ($khoa_list as $khoa): ?>
                            <option value="<?= $khoa['khoa_id'] ?>">
                                <?= htmlspecialchars($khoa['ten_khoa']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="nghe_id" class="form-label">Nghề</label>
                    <select name="nghe_id" id="nghe_id" class="form-select">
                        <option value="">-- Chọn nghề --</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="cap_do_id" class="form-label">Cấp độ</label>
                    <select name="cap_do_id" id="cap_do_id" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($cap_do_list as $cd): ?>
                            <option value="<?= $cd['cap_do_id'] ?>" 
                                <?= (isset($_GET['cap_do_id']) && $_GET['cap_do_id'] == $cd['cap_do_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cd['ten_cap_do']) ?>
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

                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Lọc
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-2">
                <a href="/admin/nien-khoa" class="btn btn-secondary btn-sm">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="/admin/nien-khoa/create" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Thêm niên khóa mới
                </a>
            </div>
        </div>
    </div>

    <!-- Danh sách niên khóa -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Danh sách Niên khóa (<?= count($nien_khoa_list) ?> niên khóa)
        </div>
        <div class="card-body">
            <?php if (empty($nien_khoa_list)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Không có niên khóa nào.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="nienKhoaTable">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">STT</th>
                                <th width="10%">Mã NK</th>
                                <th width="20%">Tên niên khóa</th>
                                <th width="15%">Nghề</th>
                                <th width="10%">Khoa</th>
                                <th width="8%">Cấp độ</th>
                                <th width="10%">Năm ĐT</th>
                                <th width="8%">Trạng thái</th>
                                <th width="10%">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nien_khoa_list as $index => $nk): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($nk['ma_nien_khoa']) ?></strong></td>
                                    <td><?= htmlspecialchars($nk['ten_nien_khoa']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars($nk['ma_nghe']) ?>
                                        </span>
                                        <?= htmlspecialchars($nk['ten_nghe']) ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($nk['ten_khoa']) ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">
                                            <?= htmlspecialchars($nk['ten_cap_do']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?= $nk['nam_bat_dau'] ?> - <?= $nk['nam_ket_thuc'] ?>
                                        <br>
                                        <small class="text-muted">
                                            (<?= ($nk['nam_ket_thuc'] - $nk['nam_bat_dau']) ?> năm)
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-toggle-status <?= $nk['is_active'] ? 'btn-success' : 'btn-danger' ?>" 
                                                data-id="<?= $nk['nien_khoa_id'] ?>"
                                                data-status="<?= $nk['is_active'] ?>">
                                            <i class="fas fa-<?= $nk['is_active'] ? 'check' : 'times' ?>"></i>
                                            <?= $nk['is_active'] ? 'Hoạt động' : 'Ngừng' ?>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="/admin/nien-khoa/edit/<?= $nk['nien_khoa_id'] ?>" 
                                               class="btn btn-warning" 
                                               title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-danger btn-delete" 
                                                    data-id="<?= $nk['nien_khoa_id'] ?>"
                                                    data-name="<?= htmlspecialchars($nk['ten_nien_khoa']) ?>"
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
                <p>Bạn có chắc chắn muốn xóa niên khóa: <strong id="deleteName"></strong>?</p>
                <p class="text-danger">
                    <i class="fas fa-info-circle"></i> 
                    Lưu ý: Không thể xóa nếu niên khóa đã có dữ liệu liên quan (Lớp, Môn học, Hợp đồng).
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
// Cascade dropdown: Khoa → Nghề
document.getElementById('khoa_id_filter').addEventListener('change', async function() {
    const khoaId = this.value;
    const ngheSelect = document.getElementById('nghe_id');
    
    // Reset nghề select
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    
    if (!khoaId) return;
    
    try {
        const response = await fetch(`/admin/nien-khoa/get-by-khoa?khoa_id=${khoaId}&is_active=1`);
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(nghe => {
                const option = new Option(
                    `[${nghe.ma_nghe}] ${nghe.ten_nghe}`, 
                    nghe.nghe_id
                );
                ngheSelect.add(option);
            });
        }
    } catch (error) {
        console.error('Error loading nghề:', error);
    }
});

// Toggle trạng thái
document.querySelectorAll('.btn-toggle-status').forEach(button => {
    button.addEventListener('click', async function() {
        const nienKhoaId = this.dataset.id;
        
        if (!confirm('Bạn có chắc muốn thay đổi trạng thái?')) return;
        
        try {
            const response = await fetch(`/admin/nien-khoa/update-status/${nienKhoaId}`, {
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

// Xóa niên khóa
document.querySelectorAll('.btn-delete').forEach(button => {
    button.addEventListener('click', function() {
        const nienKhoaId = this.dataset.id;
        const nienKhoaName = this.dataset.name;
        
        document.getElementById('deleteName').textContent = nienKhoaName;
        document.getElementById('deleteForm').action = `/admin/nien-khoa/delete/${nienKhoaId}`;
        
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