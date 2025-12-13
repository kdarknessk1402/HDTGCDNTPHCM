<?php
/**
 * View: Form thêm Nghề mới (Admin)
 * File: views/admin/nghe/create.php
 */

if (!isLoggedIn()) {
    redirect('/login');
    exit;
}

$pageTitle = 'Thêm Nghề Mới';
require_once __DIR__ . '/../../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4"><?= $pageTitle ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin/nghe">Quản lý Nghề</a></li>
        <li class="breadcrumb-item active">Thêm mới</li>
    </ol>

    <?php displayFlashMessage(); ?>

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-1"></i>
                    Thông tin nghề mới
                </div>
                <div class="card-body">
                    <form method="POST" action="/admin/nghe/create" id="createNgheForm">
                        
                        <!-- Khoa -->
                        <div class="mb-3">
                            <label for="khoa_id" class="form-label">
                                Khoa <span class="text-danger">*</span>
                            </label>
                            <select name="khoa_id" id="khoa_id" class="form-select" required>
                                <option value="">-- Chọn khoa --</option>
                                <?php foreach ($khoa_list as $khoa): ?>
                                    <option value="<?= $khoa['khoa_id'] ?>" 
                                        <?= (isset($_POST['khoa_id']) && $_POST['khoa_id'] == $khoa['khoa_id']) ? 'selected' : '' ?>>
                                        [<?= htmlspecialchars($khoa['ma_khoa']) ?>] 
                                        <?= htmlspecialchars($khoa['ten_khoa']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Chọn khoa quản lý nghề này</div>
                        </div>

                        <div class="row">
                            <!-- Mã nghề -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ma_nghe" class="form-label">
                                        Mã nghề <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="ma_nghe" 
                                           name="ma_nghe"
                                           value="<?= htmlspecialchars($_POST['ma_nghe'] ?? '') ?>"
                                           maxlength="20"
                                           required
                                           placeholder="VD: CNTT01">
                                    <div class="form-text">Tối đa 20 ký tự, không trùng trong khoa</div>
                                </div>
                            </div>

                            <!-- Số năm đào tạo -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="so_nam_dao_tao" class="form-label">
                                        Số năm đào tạo <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="so_nam_dao_tao" 
                                           name="so_nam_dao_tao"
                                           value="<?= htmlspecialchars($_POST['so_nam_dao_tao'] ?? '3') ?>"
                                           min="1"
                                           max="10"
                                           required>
                                    <div class="form-text">Từ 1 đến 10 năm</div>
                                </div>
                            </div>

                            <!-- Thứ tự -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="thu_tu" class="form-label">
                                        Thứ tự hiển thị
                                    </label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="thu_tu" 
                                           name="thu_tu"
                                           value="<?= htmlspecialchars($_POST['thu_tu'] ?? '0') ?>"
                                           min="0">
                                    <div class="form-text">Số càng nhỏ hiển thị càng trước</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tên nghề -->
                        <div class="mb-3">
                            <label for="ten_nghe" class="form-label">
                                Tên nghề <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="ten_nghe" 
                                   name="ten_nghe"
                                   value="<?= htmlspecialchars($_POST['ten_nghe'] ?? '') ?>"
                                   maxlength="100"
                                   required
                                   placeholder="VD: Lập trình máy tính">
                            <div class="form-text">Tối đa 100 ký tự</div>
                        </div>

                        <!-- Mô tả -->
                        <div class="mb-3">
                            <label for="mo_ta" class="form-label">Mô tả</label>
                            <textarea class="form-control" 
                                      id="mo_ta" 
                                      name="mo_ta"
                                      rows="4"
                                      placeholder="Nhập mô tả về nghề đào tạo..."><?= htmlspecialchars($_POST['mo_ta'] ?? '') ?></textarea>
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
                                    <strong>Kích hoạt nghề</strong>
                                    <small class="text-muted d-block">
                                        Nghề đang hoạt động sẽ hiển thị trong danh sách chọn khi tạo Niên khóa, Lớp, Môn học
                                    </small>
                                </label>
                            </div>
                        </div>

                        <hr>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="/admin/nghe" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Lưu nghề mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hướng dẫn -->
            <div class="card border-info mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-info-circle"></i> Hướng dẫn
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Mã nghề phải <strong>duy nhất trong khoa</strong></li>
                        <li>Tên nghề nên rõ ràng, dễ hiểu</li>
                        <li>Số năm đào tạo thường là: 2 năm (Trung cấp), 3 năm (Cao đẳng)</li>
                        <li>Chỉ các nghề <strong>đang hoạt động</strong> mới hiển thị khi tạo dữ liệu khác</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto uppercase mã nghề
document.getElementById('ma_nghe').addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

// Form validation
document.getElementById('createNgheForm').addEventListener('submit', function(e) {
    const khoa = document.getElementById('khoa_id').value;
    const maNghe = document.getElementById('ma_nghe').value.trim();
    const tenNghe = document.getElementById('ten_nghe').value.trim();
    const soNam = document.getElementById('so_nam_dao_tao').value;
    
    if (!khoa) {
        e.preventDefault();
        alert('Vui lòng chọn khoa!');
        document.getElementById('khoa_id').focus();
        return;
    }
    
    if (!maNghe) {
        e.preventDefault();
        alert('Vui lòng nhập mã nghề!');
        document.getElementById('ma_nghe').focus();
        return;
    }
    
    if (!tenNghe) {
        e.preventDefault();
        alert('Vui lòng nhập tên nghề!');
        document.getElementById('ten_nghe').focus();
        return;
    }
    
    if (soNam < 1 || soNam > 10) {
        e.preventDefault();
        alert('Số năm đào tạo phải từ 1 đến 10!');
        document.getElementById('so_nam_dao_tao').focus();
        return;
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>