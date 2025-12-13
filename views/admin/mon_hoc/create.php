<?php
/**
 * View: Form thêm Môn học (Admin)
 * File: views/admin/mon_hoc/create.php
 */

if (!isLoggedIn()) { redirect('/login'); exit; }
$pageTitle = 'Thêm Môn học Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/mon-hoc">Quản lý Môn học</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-plus-circle me-1"></i> Thông tin môn học mới</div>
                <div class="card-body">
                    <form method="POST" action="/admin/mon-hoc/create">
                        
                        <!-- Cascade: Khoa → Nghề → Lớp -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="khoa_id" class="form-label">Khoa <span class="text-danger">*</span></label>
                                    <select name="khoa_id" id="khoa_id" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <?php foreach ($khoa_list as $k): ?>
                                            <option value="<?= $k['khoa_id'] ?>" <?= (isset($_POST['khoa_id']) && $_POST['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($k['ten_khoa']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nghe_id" class="form-label">Nghề <span class="text-danger">*</span></label>
                                    <select name="nghe_id" id="nghe_id" class="form-select" required>
                                        <option value="">-- Chọn khoa trước --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="lop_id" class="form-label">Lớp <span class="text-danger">*</span></label>
                                    <select name="lop_id" id="lop_id" class="form-select" required>
                                        <option value="">-- Chọn nghề trước --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_mon_hoc" class="form-label">Mã môn học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ma_mon_hoc" name="ma_mon_hoc" 
                                           value="<?= htmlspecialchars($_POST['ma_mon_hoc'] ?? '') ?>" maxlength="20" required placeholder="VD: LTMT01">
                                    <div class="form-text">Tự động viết hoa</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_mon_hoc" class="form-label">Tên môn học <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="ten_mon_hoc" name="ten_mon_hoc" 
                                           value="<?= htmlspecialchars($_POST['ten_mon_hoc'] ?? '') ?>" maxlength="100" required placeholder="VD: Lập trình máy tính">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="so_tin_chi" class="form-label">Số tín chỉ <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="so_tin_chi" name="so_tin_chi" 
                                           value="<?= htmlspecialchars($_POST['so_tin_chi'] ?? '3') ?>" min="0" max="10" required>
                                    <div class="form-text">Từ 0-10 tín chỉ</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tong_so_tiet" class="form-label">Tổng số tiết <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="tong_so_tiet" name="tong_so_tiet" 
                                           value="<?= htmlspecialchars($_POST['tong_so_tiet'] ?? '45') ?>" min="1" max="500" required>
                                    <div class="form-text">Từ 1-500 tiết</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3"><?= htmlspecialchars($_POST['ghi_chu'] ?? '') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active"><strong>Kích hoạt môn học</strong></label>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <a href="/admin/mon-hoc" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('khoa_id').addEventListener('change', async function() {
    const khoaId = this.value;
    const ngheSelect = document.getElementById('nghe_id');
    const lopSelect = document.getElementById('lop_id');
    
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    lopSelect.innerHTML = '<option value="">-- Chọn nghề trước --</option>';
    
    if (!khoaId) return;
    
    try {
        const res = await fetch(`/admin/lop/get-nghe-by-khoa?khoa_id=${khoaId}&is_active=1`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(n => ngheSelect.add(new Option(`[${n.ma_nghe}] ${n.ten_nghe}`, n.nghe_id)));
        }
    } catch (e) {}
});

document.getElementById('nghe_id').addEventListener('change', async function() {
    const ngheId = this.value;
    const lopSelect = document.getElementById('lop_id');
    
    lopSelect.innerHTML = '<option value="">-- Chọn lớp --</option>';
    
    if (!ngheId) return;
    
    try {
        const res = await fetch(`/admin/mon-hoc/get-lop-by-nghe?nghe_id=${ngheId}&is_active=1`);
        const data = await res.json();
        if (data.success && data.data.length > 0) {
            data.data.forEach(l => lopSelect.add(new Option(`${l.ten_lop}`, l.lop_id)));
        }
    } catch (e) {}
});

document.getElementById('ma_mon_hoc').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>