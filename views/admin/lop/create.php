<?php
/**
 * View: Form thêm Lớp học (Admin)
 * File: views/admin/lop/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Lớp Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/lop">Quản lý Lớp</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-10 col-lg-11 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin lớp mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/lop/create" id="createLopForm">
                        
                        <!-- Cascade: Khoa → Nghề → Niên khóa -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="khoa_id" class="form-label">
                                        Khoa <span class="text-danger">*</span>
                                    </label>
                                    <select name="khoa_id" id="khoa_id" class="form-select" required>
                                        <option value="">-- Chọn khoa --</option>
                                        <?php foreach ($khoa_list as $k): ?>
                                            <option value="<?= $k['khoa_id'] ?>" 
                                                <?= (isset($_POST['khoa_id']) && $_POST['khoa_id'] == $k['khoa_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($k['ten_khoa']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nghe_id" class="form-label">
                                        Nghề <span class="text-danger">*</span>
                                    </label>
                                    <select name="nghe_id" id="nghe_id" class="form-select" required>
                                        <option value="">-- Chọn khoa trước --</option>
                                    </select>
                                    <div class="form-text">Chọn khoa để hiển thị nghề</div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="nien_khoa_id" class="form-label">
                                        Niên khóa <span class="text-danger">*</span>
                                    </label>
                                    <select name="nien_khoa_id" id="nien_khoa_id" class="form-select" required>
                                        <option value="">-- Chọn nghề trước --</option>
                                    </select>
                                    <div class="form-text">Chọn nghề để hiển thị niên khóa</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Mã lớp -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_lop" class="form-label">
                                        Mã lớp <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_lop" 
                                           name="ma_lop"
                                           value="<?= htmlspecialchars($_POST['ma_lop'] ?? '') ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: CNTT01_CD_2023">
                                    <div class="form-text">Tối đa 20 ký tự, tự động viết hoa</div>
                                </div>
                            </div>

                            <!-- Tên lớp -->
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ten_lop" class="form-label">
                                        Tên lớp <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ten_lop" 
                                           name="ten_lop"
                                           value="<?= htmlspecialchars($_POST['ten_lop'] ?? '') ?>"
                                           maxlength="100"
                                           required
                                           placeholder="VD: Lập trình máy tính 01 - Cao đẳng - 2023">
                                    <div class="form-text">Tối đa 100 ký tự</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Cấp độ -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cap_do_id" class="form-label">
                                        Cấp độ <span class="text-danger">*</span>
                                    </label>
                                    <select name="cap_do_id" id="cap_do_id" class="form-select" required>
                                        <option value="">-- Chọn cấp độ --</option>
                                        <?php foreach ($cap_do_list as $cd): ?>
                                            <option value="<?= $cd['cap_do_id'] ?>" 
                                                <?= (isset($_POST['cap_do_id']) && $_POST['cap_do_id'] == $cd['cap_do_id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($cd['ten_cap_do']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Sĩ số -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="si_so" class="form-label">
                                        Sĩ số <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="si_so" 
                                           name="si_so"
                                           value="<?= htmlspecialchars($_POST['si_so'] ?? '30') ?>"
                                           min="0"
                                           max="200"
                                           required>
                                    <div class="form-text">Số lượng sinh viên (0-200)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ghi chú -->
                        <div class="mb-3">
                            <label for="ghi_chu" class="form-label">Ghi chú</label>
                            <textarea class="form-control" 
                                      id="ghi_chu" 
                                      name="ghi_chu"
                                      rows="3"
                                      placeholder="Nhập ghi chú về lớp..."><?= htmlspecialchars($_POST['ghi_chu'] ?? '') ?></textarea>
                        </div>

                        <!-- Trạng thái -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active"
                                       <?= (!isset($_POST['is_active']) || $_POST['is_active']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    <strong>Kích hoạt lớp</strong>
                                    <small class="text-muted d-block">
                                        Lớp đang hoạt động sẽ hiển thị khi tạo Môn học, Hợp đồng
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/lop" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu lớp
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Cascade: Khoa → Nghề
document.getElementById('khoa_id').addEventListener('change', async function() {
    const khoaId = this.value;
    const ngheSelect = document.getElementById('nghe_id');
    const nienKhoaSelect = document.getElementById('nien_khoa_id');
    
    // Reset
    ngheSelect.innerHTML = '<option value="">-- Chọn nghề --</option>';
    nienKhoaSelect.innerHTML = '<option value="">-- Chọn nghề trước --</option>';
    
    if (!khoaId) return;
    
    try {
        const response = await fetch(`/admin/lop/get-nghe-by-khoa?khoa_id=${khoaId}&is_active=1`);
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(nghe => {
                const option = new Option(`[${nghe.ma_nghe}] ${nghe.ten_nghe}`, nghe.nghe_id);
                ngheSelect.add(option);
            });
        } else {
            ngheSelect.innerHTML = '<option value="">-- Khoa này chưa có nghề --</option>';
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

// Cascade: Nghề → Niên khóa
document.getElementById('nghe_id').addEventListener('change', async function() {
    const ngheId = this.value;
    const nienKhoaSelect = document.getElementById('nien_khoa_id');
    
    // Reset
    nienKhoaSelect.innerHTML = '<option value="">-- Chọn niên khóa --</option>';
    
    if (!ngheId) return;
    
    try {
        const response = await fetch(`/admin/lop/get-nien-khoa-by-nghe?nghe_id=${ngheId}&is_active=1`);
        const result = await response.json();
        
        if (result.success && result.data.length > 0) {
            result.data.forEach(nk => {
                const option = new Option(`${nk.ten_nien_khoa}`, nk.nien_khoa_id);
                nienKhoaSelect.add(option);
            });
        } else {
            nienKhoaSelect.innerHTML = '<option value="">-- Nghề này chưa có niên khóa --</option>';
        }
    } catch (error) {
        console.error('Error:', error);
    }
});

// Auto uppercase mã
document.getElementById('ma_lop').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>